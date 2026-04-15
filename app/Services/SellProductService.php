<?php

namespace App\Services;

use Exception;
use App\Enums\AccountTypes;
use Illuminate\Support\Str;
use App\Models\Account\Account;
use App\Models\Product\Product;
use App\Models\Sell\SellProduct;
use App\Models\Stock\StockOrder;
use Illuminate\Support\Facades\DB;
use App\Models\Stock\StockInventory;
use App\Models\Sell\SellProductDetail;
use App\Models\Product\ProductVariation;
use App\Exceptions\InsufficientException;
use App\Models\Merchant\MerchantTransaction;
use App\Models\Sell\SellPayment;
use App\Models\Supplier\SupplierPurchasePayment;

class SellProductService
{
    /**
     * @throws Exception
     */
    public function validateProduct($product, $merchantId): void
    {
        if (! Product::where('id', $product['product_id'])->where('merchant_id', $merchantId)->exists()) {
            throw new Exception('Product not found');
        }
    }

    /**
     * @throws Exception
     */
    public function processStock($product, $sellProductDetail, $merchantId): float|int
    {
        $totalProfit      = 0;
        $isVariation      = ! is_null($product['product_variation_id']);
        $stockInventories = StockInventory::where('merchant_id', $merchantId)
            ->when(
                $isVariation,
                function ($query) use ($product) {
                    return $query->where('product_variation_id', $product['product_variation_id']);
                },
                function ($query) use ($product) {
                    return $query->where('product_id', $product['product_id']);
                },
            )
            ->orderBy('id', 'asc')
            ->get();

        $remainingQty = $product['sale_qty'];

        foreach ($stockInventories as $stockInventory) {
            if ($remainingQty <= 0) {
                break;
            }

            $stockOrders = StockOrder::where('stock_inventory_id', $stockInventory->id)
                ->when(! empty($product['serial_number']), function ($query) use ($product) {
                    return $query->whereIn('uuid', $product['serial_number']);
                })
                ->whereNull('sell_product_detail_id')
                ->orderBy('id')
                ->get();

            foreach ($stockOrders as $stockOrder) {
                if ($remainingQty <= 0) {
                    break;
                }

                $stockOrder->update([
                    'type'                   => 1,
                    'sell_product_detail_id' => $sellProductDetail->id,
                ]);

                $remainingQty -= 1;
            }

            $totalProfit += ($product['unit_cost'] - $stockInventory->purchase_price) * $product['sale_qty'];
        }

        if ($remainingQty > 0) {
            throw new InsufficientException('Not enough stock available across all inventories.');
        }

        $this->decrementStock($product);

        return $totalProfit;
    }

    public function decrementStock($product): void
    {
        if (! $product['product_variation_id']) {
            Product::find($product['product_id'])->decrement('total_stock_qty', $product['sale_qty']);
        } else {
            $productVariation = ProductVariation::find($product['product_variation_id']);
            $productVariation->decrement('total_stock_qty', $product['sale_qty']);
            $productVariation->product->decrement('total_stock_qty', $product['sale_qty']);
        }
    }

    public function handleTransaction($saleProduct, $accountId = null): void
    {
        $calculateTotal = $this->calculateTotal($saleProduct);

        $uuid = Str::uuid();
        $saleProduct->update([
            'payment_status' => $saleProduct->due_amount == 0 ? 2 : 1,
        ]);

        $totalAmount            = $saleProduct->total_amount;
        $totalPurchasePrice     = $calculateTotal->totalPurchasePrice;
        $totalItemsDiscount     = $saleProduct->total_items_discount;
        $totalDiscountAmount    = $saleProduct->total_discount_amount;
        $adjustedDiscountAmount = $saleProduct->adjusted_discount_amount;
        $totalItemsVat          = $saleProduct->total_items_vat;
        $totalSaleVatAmount     = $saleProduct->total_sale_vat_amount;
        $totalShippingCost      = $saleProduct->total_shipping_cost;
        $paidAmount             = $saleProduct->paid_amount;

        $amountDifference = $totalAmount      - $totalPurchasePrice;
        $grossProfit      = $amountDifference - $totalItemsDiscount - $totalDiscountAmount - $adjustedDiscountAmount;

        // Now calculate net profit
        // $taxes = $totalItemsVat + $totalSaleVatAmount;
        $operatingExpenses = 0;
        $taxes             = 0;
        $otherCosts        = 0;

        $netProfit = $grossProfit - $operatingExpenses - $taxes - $otherCosts;

        $this->updateAccountBalance($saleProduct->merchant_id, $totalAmount, AccountTypes::ASSET->value, 'SPRC', 'debit', 'increment', $uuid);
        $this->updateAccountBalance($saleProduct->merchant_id, $totalAmount, AccountTypes::INCOME->value, 'REVE', 'credit', 'increment', $uuid);
        $this->updateAccountBalance($saleProduct->merchant_id, $totalPurchasePrice, AccountTypes::INVENTORY->value, 'INAS', 'credit', 'decrement', $uuid);
        $this->updateAccountBalance($saleProduct->merchant_id, $totalPurchasePrice, AccountTypes::SALE->value, 'COGS', 'debit', 'increment', $uuid);

        if ($totalItemsDiscount > 0) {
            $this->updateAccountBalance($saleProduct->merchant_id, $totalItemsDiscount, AccountTypes::ASSET->value, 'SPRC', 'credit', 'decrement', $uuid);
            $this->updateAccountBalance($saleProduct->merchant_id, $totalItemsDiscount, AccountTypes::EXPENSE->value, 'PROD', 'debit', 'increment', $uuid);
        }

        if ($totalItemsVat > 0) {
            $this->updateAccountBalance($saleProduct->merchant_id, $totalItemsVat, AccountTypes::ASSET->value, 'SPRC', 'debit', 'increment', $uuid);
            $this->updateAccountBalance($saleProduct->merchant_id, $totalItemsVat, AccountTypes::LIABILITIES->value, 'NOTP', 'credit', 'increment', $uuid);
        }

        if ($totalDiscountAmount > 0) {
            $this->updateAccountBalance($saleProduct->merchant_id, $totalDiscountAmount, AccountTypes::ASSET->value, 'SPRC', 'credit', 'decrement', $uuid);
            $this->updateAccountBalance($saleProduct->merchant_id, $totalDiscountAmount, AccountTypes::EXPENSE->value, 'PROD', 'debit', 'increment', $uuid);
        }

        if ($totalSaleVatAmount > 0) {
            $this->updateAccountBalance($saleProduct->merchant_id, $totalSaleVatAmount, AccountTypes::ASSET->value, 'SPRC', 'debit', 'increment', $uuid);
            $this->updateAccountBalance($saleProduct->merchant_id, $totalSaleVatAmount, AccountTypes::LIABILITIES->value, 'NOTP', 'credit', 'increment', $uuid);
        }

        if ($adjustedDiscountAmount > 0) {
            $this->updateAccountBalance($saleProduct->merchant_id, $adjustedDiscountAmount, AccountTypes::ASSET->value, 'SPRC', 'credit', 'decrement', $uuid);
            $this->updateAccountBalance($saleProduct->merchant_id, $adjustedDiscountAmount, AccountTypes::EXPENSE->value, 'PROD', 'debit', 'increment', $uuid);
        }

        if ($totalShippingCost > 0) {
            $this->updateAccountBalance($saleProduct->merchant_id, $totalShippingCost, AccountTypes::ASSET->value, 'SPRC', 'debit', 'increment', $uuid);
            $this->updateAccountBalance($saleProduct->merchant_id, $totalShippingCost, AccountTypes::INCOME->value, 'REVE', 'credit', 'increment', $uuid);
        }

        if ($paidAmount > 0) {
            $this->updateAccountBalance($saleProduct->merchant_id, $paidAmount, AccountTypes::ASSET->value, 'SPRC', 'debit', 'decrement', $uuid);
            if ($accountId) {
                $this->updateAccountBalanceById($saleProduct->merchant_id, $paidAmount, $accountId, null, 'credit', 'increment');
            } else {
                $this->updateAccountBalance($saleProduct->merchant_id, $paidAmount, AccountTypes::CASH->value, null, 'credit', 'increment', $uuid);
            }
        }

        $this->updateAccountBalance($saleProduct->merchant_id, $saleProduct->grand_total, AccountTypes::SALE->value, 'SALE', 'debit', 'increment', $uuid);

        $this->updateAccountBalance($saleProduct->merchant_id, $netProfit, AccountTypes::SALE->value, 'NETP', 'credit', 'increment', $uuid);
        $this->updateAccountBalance($saleProduct->merchant_id, $grossProfit, AccountTypes::SALE->value, 'GRPF', 'credit', 'increment', $uuid);
    }

    public function updateAccountBalance($merchantId, $amount, $accountType, $uucode = null, $type = 'credit', $method = 'increment', $uuid = null): void
    {
        $account = Account::where('merchant_id', $merchantId)
            ->where('account_type', $accountType)
            ->when($uucode, function ($query, $uucode) {
                $query->where('uucode', $uucode);
            })
            ->first();

        $account->{$method}('balance', $amount);

        MerchantTransaction::create(
            attributes: [
                'uuid'        => $uuid,
                'merchant_id' => $merchantId,
                'account_id'  => $account->id,
                'amount'      => $method === 'increment' ? $amount : -$amount,
                'date'        => now(),
                'type'        => $type,
            ],
        );
    }
    public function updateAccountBalanceById($merchantId, $amount, $accountId, $uucode = null, $type = 'credit', $method = 'increment'): void
    {
        $account = Account::where('merchant_id', $merchantId)
            ->where('id', $accountId)
            ->first();

        $account->{$method}('balance', $amount);

        MerchantTransaction::create(
            attributes: [
                'merchant_id' => $merchantId,
                'account_id'  => $account->id,
                'amount'      => $method === 'increment' ? $amount : -$amount,
                'date'        => now(),
                'type'        => $type,
            ],
        );
    }

    public function generateUniqueInvoiceNo(): int
    {
        do {
            $invoiceNo = mt_rand(10000000, 99999999);

            $exists = DB::table('sell_products')->where('invoice_no', $invoiceNo)->exists();
        } while ($exists);

        return $invoiceNo;
    }

    public function createSaleProduct($data)
    {
        return SellProduct::create([
            'merchant_id' => $data->merchant_id,
            'customer_id' => $data->customer_id,
            'invoice_no'  => $this->generateUniqueInvoiceNo(),
            'sale_date'   => $data->sale_date,
            'due_date'    => $data->due_date,
            'sold_from'   => $data->sold_from,
            'sell_status_id' => 2,
        ]);
    }

    /**
     * @throws Exception
     */
    public function processProducts($products, $saleProduct, $request): void
    {
        $totalItem             = 0;
        $totalAmount           = 0;
        $productDiscountAmount = 0;
        $productVatAmount      = 0;
        $subTotal              = 0;
        $merchantId            = $saleProduct->merchant_id;

        foreach ($products as $product) {
            $this->validateProduct($product, $request->merchant_id);
            $totalItem += $product['sale_qty'];
            $singleTotalAmount = $product['unit_cost'] * $product['sale_qty'];
            $totalAmount += $singleTotalAmount;
            $singleProductDiscountAmount = discountCalculation($product['unit_cost'], $product['product_discount_percentage']) * $product['sale_qty'];
            $productDiscountAmount += $singleProductDiscountAmount;
            $singleProductVatAmount = taxCalculation($singleTotalAmount - $singleProductDiscountAmount, $product['product_vat']);
            $productVatAmount += $singleProductVatAmount;
            $singleSubTotal = $singleTotalAmount - $singleProductDiscountAmount + $singleProductVatAmount;
            $subTotal += $singleSubTotal;

            $productData = Product::find($product['product_id']);

            $sellProductDetail = SellProductDetail::create([
                'sell_product_id'             => $saleProduct->id,
                'product_id'                  => $product['product_id'],
                'variation_id'                => $product['variation_id'] ?? null,
                'sale_qty'                    => $product['sale_qty'],
                'unit_cost'                   => $product['unit_cost'],
                'product_discount_percentage' => $product['product_discount_percentage'],
                'product_vat_percentage'      => $product['product_vat'],
                'sub_total'                   => $singleSubTotal,
                'warranty'                    => $productData->warranty ?? null,
            ]);

            $this->processStock($product, $sellProductDetail, $merchantId);
        }

        $totalDiscountAmount = discountCalculation($subTotal, $request->total_discount_percentage);
        $totalPurchaseVat    = taxCalculation($subTotal - $totalDiscountAmount, $request->total_sale_vat_percentage);

        $grandTotal = $subTotal - $totalDiscountAmount + $totalPurchaseVat + $request->total_shipping_cost - $request->adjusted_discount_amount;

        if ($grandTotal < $request->current_paid_amount) {
            throw new InsufficientException('Paid amount cannot be greater than grand total');
        }

        $saleProduct->update([
            'total_item'                => $totalItem,
            'total_items_discount'      => $productDiscountAmount,
            'total_items_vat'           => $productVatAmount,
            'total_discount_percentage' => $request->total_discount_percentage,
            'total_discount_amount'     => $totalDiscountAmount,
            'total_sale_vat_percentage' => $request->total_sale_vat_percentage,
            'total_sale_vat_amount'     => $totalPurchaseVat,
            'total_shipping_cost'       => $request->total_shipping_cost,
            'sub_total'                 => $subTotal,
            'grand_total'               => $grandTotal,
            'total_amount'              => $totalAmount,
            'paid_amount'               => $request->current_paid_amount,
            'due_amount'                => $grandTotal - $request->current_paid_amount,
            'adjusted_discount_amount'  => $request->adjusted_discount_amount ?? 0,
        ]);
    }

    public function calculateTotal($saleProduct): object
    {
        $totalPurchasePrice = 0;
        $saleProductDetails = $saleProduct->sell_product_details;

        foreach ($saleProductDetails as $detail) {
            $purchase_price = StockOrder::where(['type' => 1, 'sell_product_detail_id' => $detail->id])->sum('purchase_price');
            $totalPurchasePrice += $purchase_price;
        }

        return (object) [
            'totalPurchasePrice' => $totalPurchasePrice,
        ];
    }

    public function getPercentageValue(float $value, float $total): float
    {
        return ($value / $total) * 100;
    }
    public function createSellPayment($saleProduct, array $data = []): ?SellPayment
    {

        if ($saleProduct->paid_amount <= 0) {
            return null;
        }
        $merchantId = $saleProduct->merchant_id;

        $grandTotal    = $saleProduct->grand_total;
        $paidAmount    = $saleProduct->paid_amount;
        $dueAmount     = $saleProduct->due_amount;
        $newPaidAmount = $paidAmount;


        $status = match (true) {
            $newPaidAmount == 0 => SupplierPurchasePayment::$FULL_DUE,
            $newPaidAmount >= $grandTotal => SupplierPurchasePayment::$FULL_PAID,
            default => SupplierPurchasePayment::$PARTIAL_PAID,
        };


        $sellPayment = $saleProduct->sellPayments()->create([
            'merchant_id'     => $merchantId,
            'customer_id'     => $saleProduct->customer_id,
            'sell_product_id' => $saleProduct->id,
            'paid_status'     => $status,
            'paid_amount'     => $paidAmount,
            'due_amount'      => $dueAmount,
            'total_amount'    => $grandTotal,
        ]);


        $sellPayment->sellPaymentDetails()->create([
            'account_id'          => $data['account_id'] ?? null,
            'current_paid_amount' => $paidAmount,
            'date'                => $data['date'] ?? now(),
            'note'                => $data['note'] ?? null,
            'ref_no'              => $data['ref_no'] ?? null,
        ]);

        return $sellPayment;
    }
}
