<?php

namespace App\Exports;

use App\Models\Setting\ShopSetting;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class ExportPayoutRequests extends DefaultValueBinder implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithCustomValueBinder
{
    protected $payouts;
    protected ?string $debitAccount = null;
    protected ?string $debitBank = null;

    public function __construct($payouts)
    {
        $this->payouts = $payouts;

        if (method_exists($this->payouts, 'load')) {
            $this->payouts->load([
                'payoutBeneficiary.bank:id,name',
                'payoutBeneficiary.mobileWallet:id,name',
            ]);
        }

        $settings = ShopSetting::whereIn('key', ['payout_debit_account', 'payout_debit_bank'])
            ->pluck('value', 'key');

        $this->debitAccount = $settings['payout_debit_account'] ?? null;
        $this->debitBank = $settings['payout_debit_bank'] ?? 'Eastern Bank Ltd';
    }

    public function collection()
    {
        return $this->payouts;
    }

    /**
     * 🔥 THIS IS THE REAL FIX
     * Force account & routing values as STRING
     */
    public function bindValue(Cell $cell, $value)
    {
        if (in_array($cell->getColumn(), ['A', 'E', 'H'])) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function map($payout): array
    {
        $beneficiary = $payout->payoutBeneficiary;

        $debitAccount = $this->debitAccount;
        $voucher = $payout->request_id ?? null;
        $batch = $payout->request_id ?? null;
        $beneficiaryName = $beneficiary->account_holder_name ?? null;
        $creditAccount = $beneficiary->account_number ?? null;

        $methodName = null;
        if ($beneficiary) {
            if ($beneficiary->bank) {
                $methodName = $beneficiary->bank->name ?? null;
            } elseif ($beneficiary->mobileWallet) {
                $methodName = $beneficiary->mobileWallet->name ?? null;
            }
        }

        $txnType = null;
        if ($methodName) {
            $debitBank = $this->debitBank ?? '';
            $txnType = strcasecmp(trim($debitBank), trim($methodName)) === 0
                ? 'EBLACT'
                : 'EBLBFT';
        }

        $bankName = $methodName;
        $routing = $beneficiary->routing_number ?? null;
        $payAmount = $payout->amount !== null ? (float) $payout->amount : null;
        $remarks = 'Ecommerce COD Payment';

        return [
            $debitAccount,     // A → forced STRING
            $voucher,
            $batch,
            $beneficiaryName,
            $creditAccount,    // E → forced STRING
            $txnType,
            $bankName,
            $routing,          // H → forced STRING
            $payAmount,
            $remarks,
        ];
    }

    public function headings(): array
    {
        return [
            'Debit Account',
            'Voucher',
            'BATCH',
            'Beneficiary Name',
            'Credit Account/Card',
            'Txn Type',
            'Bank Name',
            'Routing No',
            'Pay Amount',
            'Remarks',
        ];
    }

    public function title(): string
    {
        return 'Payout Requests';
    }
}