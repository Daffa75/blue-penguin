<?php

namespace App\Filament\Lecturer\Resources\FinalProjectResource\Widgets;

use App\Models\FinalProject;
use Filament\Support\RawJs;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class FinalProjectStudentS1Chart extends ApexChartWidget
{
    protected static string $chartId = 'finalProjectStudentChart';
    private static $finalProjects;

    public function getHeading(): ?string
    {
        return (__('Your Students Status'));
    }
    public function getDescription(): ?string
    {
        return (__('Your students that has not yet complete their Final Project'));
    }
    public int $height = 840;
    protected function getContentHeight(): ?int
    {
        return $this->height;
    }
    protected int | string | array $columnSpan = 'full';

    public function getStudentFinalProject()
    {
        $userNip = auth()->user()->lecturer?->nip;

        $projects = FinalProject::with([
            'lecturers' => function ($query) use ($userNip) {
                $query->select('nip', 'role')
                    ->where('nip', $userNip)
                    ->whereIn('role', ['supervisor 1', 'supervisor 2']);
            },
            'student:id,name,nim'
        ])
            ->whereHas('lecturers', function (Builder $query) use ($userNip) {
                $query->where('nip', $userNip)
                    ->whereIn('role', ['supervisor 1', 'supervisor 2']);
            })
            ->where('status', 'Ongoing')
            ->whereHas('student', function ($query) {
                $query->where('nim', 'like', 'D121%')
                    ->orWhere('nim', 'like', 'D421%');
            })
            ->orderBy('submitted_at')
            ->get();

        return $projects->map(function ($result) {
            $lecturerRole = $result->lecturers->first?->pivot->role;
            $diffDays = now()->diffInDays(Carbon::createFromFormat('Y-m-d', $result->submitted_at));

            if ($lecturerRole === 'supervisor 1') {
                $labelColor = '#037ffc';
            } else {
                $labelColor = '#9ca3af';
            }

            if ($diffDays >= 540) {
                $color = '#FF0000D8';
            } elseif ($diffDays >= 180) {
                $color = '#facc15d8';
            } else {
                $color = '#00FF2FD8';
            }

            return [
                'days' => $diffDays,
                'name' => $result->student->name,
                'color' => $color,
                'labelColor' => $labelColor,
            ];
        });
    }

    protected function getOptions(): array
    {
        if (!self::$finalProjects) {
            self::$finalProjects = $this->getStudentFinalProject();
        }

        $data = self::$finalProjects->pluck('days');
        $labels = self::$finalProjects->pluck('name');
        $colors = self::$finalProjects->pluck('color');
        $labelColors = self::$finalProjects->pluck('labelColor');
        $this->height = 33.65 * count($data) + 312;
        return [
            'grid' => [
                'show' => false,
                'borderColor' => '#90A4AE',
            ],
            'colors' => $colors,
            'chart' => [
                'type' => 'bar',
                'toolbar' => [
                    'tools' => [
                        'download' => false,
                    ],
                ],
                'height' => $this->getContentHeight(),
            ],
            'series' => [
                [
                    'name' => __('Days after Proposal'),
                    'data' => $data,
                ],
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
                        'colors' => $labelColors,
                    ],
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'distributed' => true,
                    'borderRadius' => 3,
                    'horizontal' => true,
                ],
            ],
            'legend' => [
                'customLegendItems' => [
                    __('Less than 180 days'),
                    __('Between 180 to 540 days'),
                    __('More than 540 days'),
                    __('Supervisor 1'),
                    __('Supervisor 2'),
                ],
                'markers' => [
                    'fillColors' => [
                        '#00ff2f',
                        '#facc15',
                        '#ff0000',
                        '#037ffc',
                        '#9ca3af',
                    ]
                ],
            ],
        ];
    }
}
