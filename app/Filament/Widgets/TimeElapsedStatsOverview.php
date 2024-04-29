<?php

namespace App\Filament\Widgets;

use App\Models\FinalProject;
use App\Models\Internship;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

class TimeElapsedStatsOverview extends BaseWidget
{
    protected function getColumns(): int
    {
        return count($this->getStats()) === 1 ? 1 : 2;
    }

    public function calculateInternshipElapsedTime(?Internship $record): ?string
    {
        if (!$record) {
            return 'Tidak ada Internship yang sedang berlangsung.';
        }

        if ($record->status == 'Done') {
            return 'Selesai';
        } else {
            $endDate = Carbon::parse($record->end_date);
            $remainingTime = $endDate->diffInDays(now());
            return $remainingTime;
        }
    }

    public function calculateFinalProjectElapsedTime(?FinalProject $record): ?string
    {
        if (!$record) {
            return 'Tidak ada final project yang sedang berlangsung.';
        }

        if ($record->status == 'Done') {
            return 'Selesai';
        } else {
            $startDate = Carbon::parse($record->submitted_at);
            $elapsedTime = $startDate->diffInDays(now());
            return $elapsedTime;
        }
    }

    public function getFinalProjectStatus(?FinalProject $record): string
    {
        if (!$record) {
            return '';
        }

        return match ($record->status) {
            'Ongoing' => 'Seminar Proposal',
            'Finalizing' => 'Seminar Hasil',
            'Publication' => 'Publikasi',
            'Thesis' => 'Ujian Tesis',
            'Done' => 'Wisuda',
            default => ''
        };
    }

    protected function getStats(): array
    {
        $internship = Internship::whereHas('students', function ($query) {
            $query->where('nim', auth()->user()->student?->nim);
        })->first();

        $finalProject = FinalProject::whereHas('student', function ($query) {
            $query->where('nim', auth()->user()->student?->nim);
        })->first();

        $stats = [];

        if ($internship) {
            $remainingTime = $this->calculateInternshipElapsedTime($internship);
            $stats[] = Stat::make(__('Internship Remaining Time'), new HtmlString('<span>' . ($remainingTime === 'Selesai' ? __('Done') : $remainingTime . ' days') . '</span>'))
                ->description(__('Your Internship Status') . ': ' . ($remainingTime === 'Selesai' ? __('Done') : __('Ongoing')));
        }

        if ($finalProject) {
            $elapsedTime = $this->calculateFinalProjectElapsedTime($finalProject);
            $elapsedTimeHtml = $elapsedTime === 'Selesai' ? '<span class="font-bold">Selesai</span>' : '<span>' . $elapsedTime . ' days</span>';
            $stats[] = Stat::make(__('Final Project Elapsed Time'), new HtmlString($elapsedTimeHtml))
                ->description(__('Your Final Project Status') . ': ' . $this->getFinalProjectStatus($finalProject));
        }

        return $stats;
    }
}
