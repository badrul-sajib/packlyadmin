<?php

namespace App\Services;

use App\Models\Customer\Customer;
use App\Models\Merchant\Merchant;
use App\Models\Supplier\Supplier;
use App\Models\Unit\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserDefaultDataService
{
    public function insert(Merchant $merchant): void
    {
        $attributes = [
            'color' => ['red', 'blue', 'green', 'black', 'white', 'yellow', 'pink', 'purple', 'gray'],
            'size'  => ['S', 'M', 'L', 'XL', 'XXL'],
            'ram'   => ['2GB', '4GB', '8GB', '16GB', '32GB'],
            'rom'   => ['32GB', '64GB', '128GB', '256GB', '512GB', '1TB'],
        ];

        foreach ($attributes as $attribute => $values) {

            $id = DB::table('attributes')->insertGetId([
                'name'        => ucfirst($attribute),
                'merchant_id' => $merchant->id,
                'slug'        => Str::slug($attribute),
                'status'      => 1,
                'added_by'    => $merchant->id,
            ]);

            foreach ($values as $value) {
                DB::table('attribute_options')->insert([
                    'merchant_id'     => $merchant->id,
                    'attribute_id'    => $id,
                    'attribute_value' => ucfirst($value),
                    'slug'            => Str::slug($value),
                    'status'          => 1,
                    'added_by'        => $merchant->id,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }

        $accounts = [
            // Asset
            1 => [
                'Advance Tax'             => ['ADTX', 'Tax paid in advance, recorded as an asset until settled.'],
                'Prepaid Expenses'        => ['PRPE', 'Expenses paid in advance and recognized as assets until consumed.'],
                'Employee Advance'        => ['EMAD', 'Funds given to employees as an advance to be adjusted later.'],
                'Furniture and Equipment' => ['FUEQ', 'Value of office furniture, tools, and equipment owned by the business.'],
                'Payable Receivable'      => ['PYRC', 'Temporary balance of pending payments and receivables.'],
                'Sale Payable Receivable' => ['SPRC', 'Receivables and payables arising from sales transactions.'],
            ],
            // Bank
            2 => [],
            // Inventory
            3 => [
                'Inventory Asset'  => ['INAS', 'Raw materials and stock available for sale.'],
                'Work in Progress' => ['WIPG', 'Partially completed goods in the production process.'],
                'Finished Goods'   => ['FNGD', 'Completed goods ready for sale.'],
            ],
            // Supplier
            4 => [
                'Supplies' => ['SUPP', 'Office and business supplies purchased for daily operations.'],
            ],
            // Loan / Liability
            5 => [
                'Tax Payable'                 => ['TAPY', 'Outstanding tax obligations owed to the government.'],
                'Unearned Revenue'            => ['UNRV', 'Revenue received before delivering goods or services.'],
                'Employee Loans'              => ['EMLO', 'Loans granted to employees repayable over time.'],
                'Credit Card Liabilities'     => ['CCLI', 'Outstanding balances on company credit cards.'],
                'Dimension Adjustments'       => ['DIAD', 'Adjustments related to accounting dimensions.'],
                'Opening Balance Adjustments' => ['OBAD', 'Adjustments made during system opening balance entry.'],
            ],
            // Income
            6 => [
                'Revenue' => ['REVE', 'Income earned from sales or services.'],
            ],
            // Purchase
            7 => [
                'Inventory Purchases' => ['INPU', 'Purchases of goods for resale or production.'],
                'Equipment Purchases' => ['EQPU', 'Acquisition of office and factory equipment.'],
                'Vendor Prepayments'  => ['VEPR', 'Advance payments made to suppliers or vendors.'],
            ],
            // Expense
            8 => [
                'Lodging'                     => ['LODG', 'Accommodation and lodging expenses for business activities.'],
                'Purchase Discounts'          => ['PUDS', 'Discounts received on purchases.'],
                'Office Supplies'             => ['OFSP', 'Stationery and consumables used in operations.'],
                'Advertising And Marketing'   => ['ADMK', 'Expenses for promotion and marketing campaigns.'],
                'Bank Fees and Charges'       => ['BFCH', 'Bank service charges and transaction fees.'],
                'Credit Card Charges'         => ['CCCH', 'Interest or fees on business credit cards.'],
                'Travel Expense'              => ['TRVE', 'Costs of travel for business purposes.'],
                'Telephone Expense'           => ['TEEX', 'Expenses related to business phone and communication.'],
                'Automobile Expense'          => ['AUEX', 'Fuel, repairs, and maintenance of company vehicles.'],
                'IT and Internet Expenses'    => ['ITIE', 'Software, hosting, and internet costs.'],
                'Rent Expense'                => ['REXP', 'Office or warehouse rent payments.'],
                'Janitorial Expense'          => ['JEXP', 'Cleaning and maintenance service costs.'],
                'Postage'                     => ['POST', 'Mailing and courier service charges.'],
                'Bad Debt'                    => ['BADE', 'Unrecoverable receivables written off.'],
                'Printing and Stationery'     => ['PRST', 'Printing and office stationery expenses.'],
                'Salaries and Employee Wages' => ['SEWG', 'Payments to staff and employees.'],
                'Uncategorized'               => ['UNCA', 'Miscellaneous expenses not categorized elsewhere.'],
                'Meals and Entertainment'     => ['MAEN', 'Food and hospitality expenses for business.'],
                'Depreciation Expense'        => ['DEEX', 'Reduction in value of assets due to wear and tear.'],
                'Consultant Expense'          => ['COEX', 'Professional consultancy service costs.'],
                'Repairs and Maintenance'     => ['REMA', 'Repairs and upkeep of assets.'],
                'Other Expenses'              => ['OTEX', 'Miscellaneous operational expenses.'],
                'Delivery Cost'               => ['DELC', 'Expenses for product delivery to customers.'],
                'Vat/Taxes'                   => ['VATT', 'VAT and other business-related taxes.'],
                'Product Discount'            => ['PROD', 'Discounts offered on product sales.'],
                'Commission'                  => ['COMM', 'Commission payments to agents or sales staff.'],
                'Shipping Fine'               => ['SHFI', 'Penalties related to shipping issues.'],
            ],
            // Equity
            9 => [
                'Retained Earnings'      => ['RENR', 'Accumulated profits reinvested into the business.'],
                'Owner\'s Capital'       => ['OWCA', 'Initial and additional investments from owners.'],
                'Opening Balance Offset' => ['OBOF', 'Balancing entry for opening balances.'],
                'Drawings'               => ['DRAW', 'Withdrawals made by owners from business funds.'],
            ],
            // Cash
            10 => [
                'Petty Cash' => ['PCAH', 'Small amount of cash kept on hand for daily expenses.'],
            ],
            // Loss
            11 => [
                'Loss' => ['LOSS', 'Recorded losses from business operations.'],
            ],
            // Sales & COGS
            12 => [
                'Sale'               => ['SALE', 'Revenue generated from product sales.'],
                'Cost of Goods Sold' => ['COGS', 'Direct costs of producing goods sold.'],
                'Gross Profit'       => ['GRPF', 'Revenue minus cost of goods sold.'],
                'Net Profit'         => ['NETP', 'Profit after all expenses and taxes.'],
                'Shipping Cost'      => ['SHPC', 'Costs incurred in delivering products.'],
                'Cod Cost'           => ['CDCO', 'Cash on delivery service charges.'],
                'Packaging Cost'     => ['PACO', 'Expenses for product packaging.'],
                'Other Cost'         => ['OTCO', 'Miscellaneous costs related to sales.'],
                'Discount'           => ['DISC', 'Discounts given on sales transactions.'],
            ],
            // Payables
            13 => [
                'Accounts payable' => ['ACPA', 'Outstanding balances owed to suppliers.'],
                'Note payable'     => ['NOTP', 'Written promise to pay a certain amount in future.'],
            ],
        ];

        foreach ($accounts as $key => $values) {
            foreach ($values as $accountName => $info) {
                [$uucode, $description] = $info;
                DB::table('accounts')->insert([
                    'merchant_id'  => $merchant->id,
                    'uucode'       => $uucode,
                    'account_type' => $key,
                    'slug'         => Str::lower(Str::replace(' ', '_', $accountName)),
                    'name'         => $accountName,
                    'code'         => $this->generateCode($accountName),
                    'balance'      => 0.00,
                    'status'       => 1,
                    'description'  => $description,
                ]);
            }
        }
        // warehouse create
        DB::table('warehouses')->insert([
            'merchant_id' => $merchant->id,
            'name'        => 'Local Warehouse',
            'phone'       => $merchant->phone,
            'address'     => $merchant->address ?? 'NA',
            'status'      => 1,
        ]);

        $units = [
            'Piece',
            'Pack',
            'Box',
            'KG',
            'Liter',
        ];

        foreach ($units as $unit) {

            $unitModel = new Unit([
                'merchant_id' => $merchant->id,
                'name'        => $unit,
                'status'      => 1,
                'slug'        => Str::lower(Str::replace(' ', '_', $unit)),
                'added_by'    => $merchant->user_id,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            $unitModel->save();
        }

        // customer
        Customer::create([
            'merchant_id'      => $merchant->id,
            'name'             => 'WalkIn Customer',
            'email'            => 'N/A',
            'phone'            => 'N/A',
            'address'          => 'N/A',
            'customer_type_id' => 1,
        ]);

        // supplier create
        Supplier::create([
            'merchant_id' => $merchant->id,
            'name'        => 'Guest Supplier',
            'email'       => 'N/A',
            'phone'       => 'N/A',
        ]);
    }

    private function generateCode($string): string
    {
        $words = explode(' ', $string);
        $code  = '';

        foreach ($words as $word) {
            $code .= strtoupper($word[0]);
        }

        return $code;
    }
}
