<?php

namespace App\Jobs;

use App\Enums\AccountTypes;
use App\Models\Account\Account;
use App\Models\Merchant\MerchantTransaction;
use App\Models\Product\Product;
use App\Models\Stock\StockInventory;
use App\Models\Stock\StockOrder;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class CreateStockOrders implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 600;

    protected $productId;
    protected $merchantId;
    protected $stockQty;
    protected $prices;
    protected $paymentDate;

    public function __construct(int $productId, int $merchantId, int $stockQty, array $prices, ?string $paymentDate = null)
    {
        $this->productId   = $productId;
        $this->merchantId  = $merchantId;
        $this->stockQty    = $stockQty;
        $this->prices      = $prices;
        $this->paymentDate = $paymentDate;
    }

    public function handle(): void
    {
        \Illuminate\Support\Facades\Log::info("Job Started for Product ID: {$this->productId}, Qty: {$this->stockQty}");
        
        DB::transaction(function () {
            
            $product = Product::find($this->productId);
            if (! $product) {
               throw new Exception("Product ID {$this->productId} not found during stock creation.");
            }

            // 1. Create Stock Inventory
            $stockData = [
                'merchant_id'      => $this->merchantId,
                'product_id'       => $this->productId,
                'purchase_price'   => $this->prices['purchase_price'] ?? 0,
                'e_price'          => $this->prices['e_price'] ?? 0,
                'e_discount_price' => $this->prices['e_discount_price'] ?? 0,
                'regular_price'    => $this->prices['regular_price'] ?? 0,
                'discount_price'   => $this->prices['discount_price'] ?? 0,
                'wholesale_price'  => $this->prices['wholesale_price'] ?? 0,
                'stock_qty'        => $this->stockQty,  
            ];

            // Update Product Total Stock
            $product->increment('total_stock_qty', $this->stockQty);
            
            $stockInventory = StockInventory::create($stockData);

            // 2. Update Accounts & Merchant Transactions
            $uuid = Str::uuid();
            $paymentDate = $this->paymentDate ?? now();
            $amount = $this->stockQty * ($this->prices['purchase_price'] ?? 0);

            $accounts = [
                'inventory' => Account::where(['merchant_id' => $this->merchantId, 'account_type' => AccountTypes::INVENTORY->value, 'uucode' => 'INAS'])->first(),
                'purchases' => Account::where(['merchant_id' => $this->merchantId, 'account_type' => AccountTypes::PURCHASE->value, 'uucode' => 'INPU'])->first(),
                'capital'   => Account::where(['merchant_id' => $this->merchantId, 'account_type' => AccountTypes::EQUITY->value, 'uucode' => 'OWCA'])->first(),
            ];

            if (in_array(null, $accounts, true)) {
                 throw new Exception("Missing required account(s) for merchant ID {$this->merchantId}.");
            }

            foreach ($accounts as $account) {
                $account->increment('balance', $amount);
            }

            $transactions = [
                [
                    'account' => $accounts['inventory'],
                    'type'    => 'debit',
                    'reason'  => 'Opening stock in the inventory',
                ],
                [
                    'account' => $accounts['purchases'],
                    'type'    => 'debit',
                    'reason'  => 'Opening stock in the inventory',
                ],
                [
                    'account' => $accounts['capital'],
                    'type'    => 'credit',
                    'reason'  => 'Owner capital as opening stock',
                ],
            ];

            foreach ($transactions as $txn) {
                MerchantTransaction::create([
                    'uuid'        => $uuid,
                    'merchant_id' => $this->merchantId,
                    'account_id'  => $txn['account']->id,
                    'amount'      => $amount,
                    'date'        => $paymentDate,
                    'type'        => $txn['type'],
                    'reason'      => $txn['reason'],
                ]);
            }

            // 3. Create Stock Orders (Chunked)
             $chunkSize = 1000;
             $batches   = ceil($this->stockQty / $chunkSize);
             $remaining = $this->stockQty;

             $baseData = [
                'stock_inventory_id' => $stockInventory->id,
                'purchase_price'     => $this->prices['purchase_price'] ?? 0,
                'created_at'         => now(),
                'updated_at'         => now(),
             ];
             for ($i = 0; $i < $batches; $i++) {
                $currentBatchSize = ($remaining > $chunkSize) ? $chunkSize : $remaining;
                $data = [];

                for ($j = 0; $j < $currentBatchSize; $j++) {
                    $row = $baseData;
                    $row['uuid'] = (string) Str::uuid();
                    $data[] = $row;
                }

                StockOrder::insert($data);
                unset($data);
                $remaining -= $currentBatchSize;
             }

             
             \Illuminate\Support\Facades\Log::info("Stock Orders Inserted. Job Complete.");
        });

    }
}
