<?php

namespace App\Filament\Widgets;

use App\Models\FinalProject;
use App\Models\Lecturer;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class FinalProjectLecturerGraph extends ApexChartWidget
{
    protected static string $chartId = 'finalProjectStudentApexChart';
    private static $finalProjects;


    /**
     * Widget Title
     *
     * @var string|null
     */
    protected function getHeading(): ?string
    {
        return __('Mentoring Chart');
    }

    public int $height = 3240;
    protected function getContentHeight(): ?int
    {
        return $this->height;
    }

    public function placeholder(): View
    {
        return view('components.loading-section', [
            'columnSpan' => $this->getColumnSpan(),
            'columnStart' => $this->getColumnStart(),
        ]);
    }
    protected int | string | array $columnSpan = 'full';

    public function getLecturerFinalProject()
    {
        $results = DB::select("
        SELECT lecturers.name, lecturers.nip, 
            CASE 
                WHEN students.nim LIKE 'D121%' OR students.nim LIKE 'D421%' THEN 'S1' ELSE 'S2'
            END AS degree,
            COUNT(final_projects.id) as final_projects_count
        FROM lecturers
        LEFT JOIN final_project_lecturer ON lecturers.id = final_project_lecturer.lecturer_id
        LEFT JOIN final_projects ON final_project_lecturer.final_project_id = final_projects.id
        LEFT JOIN students ON final_projects.student_id = students.id
        WHERE LENGTH(lecturers.nip) = 18
            AND final_projects.status = 'Ongoing'
            AND final_project_lecturer.role IN ('supervisor 1', 'supervisor 2')
        GROUP BY lecturers.name, lecturers.nip, degree
        HAVING final_projects_count > 0
        ORDER BY final_projects_count DESC, lecturers.name;
    ");

        $s1Counts = [];
        $s2Counts = [];
        $totalCounts = [];


        foreach ($results as $result) {
            $degree = $result->degree;
            $count = $result->final_projects_count;

            if ($degree === 'S1') {
                $s1Counts[$result->name] = $count;
            } elseif ($degree === 'S2') {
                $s2Counts[$result->name] = $count;
            }

            $totalCounts[$result->name] = ($totalCounts[$result->name] ?? 0) + $count;
        }

        arsort($totalCounts);

        $combinedCounts = [];

        foreach (array_keys($totalCounts) as $name) {
            $combinedCounts[] = [
                'name' => $name,
                'countS1' => $s1Counts[$name] ?? 0,
                'countS2' => $s2Counts[$name] ?? 0,
            ];
        }

        return $combinedCounts;
    }

    protected function getOptions(): array
    {
        if (!self::$finalProjects) {
            self::$finalProjects = $this->getLecturerFinalProject();
        }

        $dataS1 = array_column(self::$finalProjects, 'countS1');
        $dataS2 = array_column(self::$finalProjects, 'countS2');
        $labels = array_column(self::$finalProjects, 'name');

        $this->height = 33.65 * count($dataS1) + 312;

        return [
            'grid' => [
                'show' => false,
            ],
            'chart' => [
                'type' => 'bar',
                'stacked' => true,
                'height' => $this->getContentHeight(),
            ],
            'series' => [
                [
                    'name' => 'Mentoring Final Project S1',
                    'data' => $dataS1,
                    'color' => '#ff9966',
                ],
                [
                    'name' => 'Mentoring Final Project S2',
                    'data' => $dataS2,
                    'color' => '#66ffcc',
                ]
            ],
            'xaxis' => [
                'categories' => $labels,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    // 'borderRadius' => 3,
                    'horizontal' => true,
                ],
            ],
        ];
    }
}
