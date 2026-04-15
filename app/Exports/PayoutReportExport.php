<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PayoutReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    protected $method;
    protected $fromDate;
    protected $toDate;

    public function __construct($method, $fromDate, $toDate)
    {
        $this->method = $method;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function collection()
    {
        // Reuse service logic to fetch data (all pages, no pagination)
        $request = request()->duplicate();
        $request->merge([
            'from_date' => $this->fromDate,
            'to_date' => $this->toDate,
            'perPage' => 999999, // Fetch all
        ]);

        $payouts = app(\App\Services\PayoutRequestService::class)->getPayoutRequestsForMethod($request, $this->method);

        return $payouts->getCollection();
    }

    public function map($payout): array
    {
        $beneficiary = $payout->payoutBeneficiary;
        $isMobile = $beneficiary->payout_beneficiary_type_id == 1;
        $accountName = $isMobile ? ($beneficiary->mobileWallet->name ?? 'N/A') : ($beneficiary->account_holder_name ?? 'N/A');
        $branch = $isMobile ? '—' : ($beneficiary->branch_name ?? 'N/A');
        $accountNo = $beneficiary->account_number ?? 'N/A';

        // Modern format: Full details (9 columns to match UI, excluding status as per request)
        return [
            $payout->id,
            $payout->request_id ?? 'N/A',
            $payout->created_at->format('d/m/Y H:i'),
            $payout->merchant->name ?? 'N/A',
            $payout->merchant->phone ?? 'N/A',
            $accountName,
            $branch,
            $accountNo,
            '৳ ' . number_format($payout->amount),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Payment ID',
            'Date & Time',
            'Merchant Name',
            'Contact',
            'Account Holder',
            'Branch',
            'Account No.',
            'Amount',
        ];
    }

    public function title(): string
    {
        return "{$this->method} Payout Report ({$this->fromDate} to {$this->toDate})";
    }

    public function styles(Worksheet $sheet)
    {
        // Header row styling only
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '667EEA']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Page setup
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageMargins()->setHeader(0.5)->setFooter(0.5);

                // Auto-size columns
                foreach (range('A', $highestColumn) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Apply borders to data range
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E9ECEF'],
                        ],
                    ],
                ]);

                // Align amount column (I for 9th column)
                $sheet->getStyle("I2:I{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Add summary at bottom
                $totalRow = $highestRow + 2;
                $sheet->setCellValue("A" . $totalRow, 'Grand Total:');
                $sheet->setCellValue("I" . $totalRow, '৳ ' . number_format($this->getTotalAmount()));
                $sheet->getStyle("A" . $totalRow . ":I" . $totalRow)->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle("I" . $totalRow)->getFont()->getColor()->setARGB('FF28A745');
                $sheet->getStyle("A" . $totalRow . ":I" . $totalRow)->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
            },
        ];
    }

    private function getTotalAmount()
    {
        // Calculate total from service
        $request = request()->duplicate();
        $request->merge(['from_date' => $this->fromDate, 'to_date' => $this->toDate]);
        $summary = app(\App\Services\PayoutRequestService::class)->getPayoutSummary($request, $this->method);
        return $summary['total'];
    }
}