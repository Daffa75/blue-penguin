<?php

namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;

class FinalProjectsExport implements FromView, WithEvents, Responsable
{
    use Exportable;

    private $data;
    private $fileName;
    private $url;

    public function __construct($data, $url)
    {
        $this->url = $url;

        if ($url == 'final-projects') {
            $this->fileName = now()->format('Y-m-d_his') . '-final_projects.xlsx';
        } else {
            $this->fileName = now()->format('Y-m-d_his') . '-final_projects_s2.xlsx';
        }

        $this->data = $data;

    }

    public function view(): View
    {
        $data['data_final_projects'] = $this->data;

        $data['url'] = $this->url;

        return view('exports.final-projects', $data);
    }

    public function registerEvents(): array
    {
        Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
        });

        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $range = 'A1:' . $highestColumn . $highestRow;

                foreach (range('A', $highestColumn) as $column) {
                    if ($column != 'C') {
                        $sheet->getColumnDimension($column)
                            ->setAutoSize(true);
                    } else {
                        $sheet->getColumnDimension($column)
                            ->setWidth(25);
                    }
                }

                $event->sheet->styleCells(
                    $range,
                    [
                        'font' => [
                            'name' => 'Arial',
                            'size' => 10,
                        ],
                    ]
                );

                // for header styling
                $event->sheet->styleCells(
                    'A1:' . $highestColumn . 1,
                    [
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['argb' => '9CC2E5']
                        ]
                    ]
                );

                $borderRange = $highestColumn . $highestRow;
                $event->sheet->styleCells(
                    'A1:' . $borderRange,
                    [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ],
                        ],
                    ]
                );
            }
        ];
    }
}
