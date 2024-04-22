<?php

namespace App\Filament\Widgets;

use App\Models\FinalProject;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class QuotaOverview extends BaseWidget
{

  // protected static string $view = 'filament.widgets.quota-overview';

  protected function getColumns(): int
  {
    return 2;
  }

  protected function getStats(): array
  {
    $lecturer = auth()->user()->lecturer;

    if ($lecturer) {


      $quota = $lecturer->quota;

      $finalProjectCount = FinalProject::where('status', 'Ongoing')
        ->whereHas('lecturers', function (Builder $query) {
          $query->where('nip', auth()->user()->lecturer?->nip)
            ->whereIn('role', ['supervisor 1', 'supervisor 2']);
        })->count();

      $remainingQuota = $quota - $finalProjectCount;

      $color = $remainingQuota <= 0 ? 'danger' : ($remainingQuota < 5 ? 'warning' : 'okay');

      $description = '';
      if ($remainingQuota <= 0) {
        $description = __('Your supervising quota is full');
      } elseif ($remainingQuota < 5) {
        $description = __('Your supervising quota is almost full');
      }
    } else {
      $quota = 0;
      $remainingQuota = 0;
      $color = 'danger';
      $description = __('You are not logged in as a lecturer');
    }

    return [
      Stat::make((__('Supervising Quota')), $quota),
      Stat::make((__('Remaining Quota')), $remainingQuota)
        ->color($color)
        ->descriptionColor($color)
        ->description($description),
    ];
  }
}
