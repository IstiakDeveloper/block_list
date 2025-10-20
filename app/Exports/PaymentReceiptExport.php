<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Collection;

class PaymentReceiptExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;
    protected $reportType;

    public function __construct($data, string $reportType)
    {
        $this->data = $data;
        $this->reportType = $reportType;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return match($this->reportType) {
            'transaction_summary' => [
                'Date',
                'From Branch',
                'To Branch',
                'Quantity',
                'Processed By'
            ],
            'branch_stock' => [
                'Branch Code',
                'Branch Name',
                'Total Receipts',
                'Used Receipts',
                'Available Receipts',
                'Usage %'
            ],
            'distribution_details' => [
                'Date',
                'Branch',
                'Officer Name',
                'Officer PIN',
                'Quantity',
                'Distributed By'
            ],
            'daily_collection' => [
                'Date',
                'Branch',
                'Total Distributed',
                'Number of Officers',
                'Average Per Officer'
            ]
        };
    }

    public function map($row): array
    {
        return match($this->reportType) {
            'transaction_summary' => [
                $row->created_at->format('d/m/Y'),
                $row->fromBranch?->branch_name ?? 'System',
                $row->toBranch->branch_name,
                $row->quantity,
                $row->user->name
            ],
            'branch_stock' => [
                $row->branch->branch_code,
                $row->branch->branch_name,
                $row->total_receipts,
                $row->used_receipts,
                $row->available_receipts,
                $this->calculateUsagePercentage($row->used_receipts, $row->total_receipts) . '%'
            ],
            'distribution_details' => [
                $row->created_at->format('d/m/Y'),
                $row->branch->branch_name,
                $row->officer->name,
                $row->officer->pin_number,
                $row->quantity,
                $row->user->name
            ],
            'daily_collection' => [
                $row->date,
                $row->branch->branch_name,
                $row->total_distributed,
                $row->officers_count,
                round($row->total_distributed / $row->officers_count, 2)
            ]
        };
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'E2E8F0',
                ],
            ],
        ]);

        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $sheet;
    }

    private function calculateUsagePercentage($used, $total): int
    {
        if (!$total) return 0;
        return round(($used / $total) * 100);
    }
}
