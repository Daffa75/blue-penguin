<?php

namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use \Maatwebsite\Excel\Sheet;

class InventarisExport implements FromView, WithDrawings, WithEvents, Responsable
{
    use Exportable;

    private $data;
    private $countData;
    private $fileName;

    public function __construct($data)
    {
        $this->fileName = now()->format('Y-m-d_his') . '-inventaris.xlsx';

        $this->data = $data;

        $this->countData = $data->count();
    }

    public function view(): View
    {
        $data['list_inventaris'] = $this->data;

        return view('exports.inventaris', $data);
    }

    public function drawings()
    {
        // for unhas logo
        $drawing_unhas_logo = new Drawing();
        $drawings = [];

        $drawing_unhas_logo->setName('UNHAS Logo');
        $drawing_unhas_logo->setPath(public_path('assets/images/unhas-logo.png'));
        $drawing_unhas_logo->setHeight(70);
        $drawing_unhas_logo->setCoordinates('C1');
        $drawing_unhas_logo->setOffsetX(30);

        // for inventaris images
        $list_inventaris = $this->data;

        foreach ($list_inventaris as $index => $inventaris) {
            $mediaItems = $inventaris->getMedia("*");

            for ($i = 0; $i < count($mediaItems); $i++) {
                $collectionName = $mediaItems[$i]->collection_name;
                $id = $mediaItems[$i]->id;
                $fileName = $mediaItems[$i]->getDownloadFilename();

                $imageUrl = "storage/" . $id . '/' . $fileName;

                if (!$imageResource = @imagecreatefromstring(file_get_contents($imageUrl))) {
                    throw new \Exception('The image URL cannot be converted into an image resource.');
                }

                $drawing = new MemoryDrawing();

                $drawing->setName('Image ' . ($index + 1));
                $drawing->setDescription('Image ' . ($index + 1));
                $drawing->setImageResource($imageResource);
                $drawing->setHeight(120);

                if ($collectionName === 'inventory/images/distribution') {
                    $drawing->setCoordinates('I' . ($index + 11));
                } elseif ($collectionName === 'inventory/images/physique') {
                    $drawing->setCoordinates('J' . ($index + 11));
                } else {
                    $drawing->setCoordinates('K' . ($index + 11));
                }

                $drawing->setOffsetX(10);
                $drawing->setOffsetY(10);

                $drawings[] = $drawing;
            }
        }

        $drawings[] = $drawing_unhas_logo;

        return $drawings;
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

                $columns = [
                    'A' => 5,
                    'B' => 25,
                    'C' => 15,
                    'D' => 15,
                    'E' => 15,
                    'F' => 10,
                    'G' => 10,
                    'H' => 25,
                    'I' => 25,
                    'J' => 25,
                    'K' => 25,
                ];
                foreach ($columns as $column => $width) {
                    $sheet->getColumnDimension($column)->setWidth($width);
                }

                $event->sheet->styleCells(
                    $range,
                    [
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],
                        'font' => [
                            'name' => 'Arial',
                            'size' => 10,
                        ],
                    ]
                );

                // for number format currency
                $event->sheet->styleCells(
                    'D1:D' . $highestRow,
                    [
                        'numberFormat' => [
                            'formatCode' => '#,##0',
                        ],
                    ]
                );

                // for header styling
                $event->sheet->styleCells(
                    'A1:K9',
                    [
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ]
                );

                $event->sheet->styleCells(
                    'I10:K10',
                    [
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                    ]
                );

                // for border styling
                $borderRange = $highestColumn . 10 + $this->countData;
                $event->sheet->styleCells(
                    'A9:' . $borderRange,
                    [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ],
                        ],
                    ]
                );
            },
        ];
    }
}
