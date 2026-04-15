<?php

namespace App\Exports;

use App\Models\Shop\ShopSetting;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MFSPayoutExport extends DefaultValueBinder implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithStyles,
    WithCustomValueBinder
{
    protected $method;
    protected $date;
    protected $dmCode;
    protected $sourceWallet;
    protected $comment;

    public function __construct($method, $date = null)
    {
        $this->method = $method;
        $this->date = $date;

        $settings = ShopSetting::whereIn('key', [
            'payout_dm_code',
            'payout_source_wallet',
            'payout_comment'
        ])->pluck('value', 'key');

        $this->dmCode = $settings['payout_dm_code'] ?? '';
        $this->sourceWallet = $settings['payout_source_wallet'] ?? '';
        $this->comment = $settings['payout_comment'] ?? '';
    }

    public function collection()
    {
        $request = request()->duplicate();
        $request->merge([
            'perPage' => 999999,
        ]);

        if ($this->date) {
            $request->merge(['date' => $this->date]);
        }

        $payouts = app(\App\Services\PayoutRequestService::class)->getPayoutRequests($request);

        return $payouts->getCollection();
    }

    public function map($payout): array
    {
        return [
            $payout->request_id ?? 'N/A',
            $this->dmCode,
            $this->sourceWallet,
            $payout->payoutBeneficiary->account_number ?? 'N/A',
            $payout->amount,
            $this->comment,
        ];
    }

    public function headings(): array
    {
        return [
            'PAYOUT_REQUEST_ID',
            'DM_CODE',
            'SOURCE_WALLET',
            'BENEFICIARY_WALLET',
            'PRINCIPAL_AMOUNT',
            'COMMENT',
        ];
    }

    public function title(): string
    {
        return ucfirst($this->method) . " Payout Export";
    }

    public function bindValue(Cell $cell, $value)
    {
        // Force wallets as strings to prevent scientific notation or stripping leading zeros
        if (in_array($cell->getColumn(), ['A', 'C', 'D'])) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4A5568']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
