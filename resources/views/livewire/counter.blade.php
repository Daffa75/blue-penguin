<?php 

use App\Models\FinalProject;
use Illuminate\Database\Eloquent\Builder;

$countSupervisor1 = FinalProject::where('status', 'Ongoing')
    ->whereHas('lecturers', function (Builder $query) {
        $query->where('nip', auth()->user()->lecturer?->nip)
            ->where('role', 'supervisor 1');
    });
    
$countSupervisor2 = FinalProject::where('status', 'Ongoing')
    ->whereHas('lecturers', function (Builder $query) {
        $query->where('nip', auth()->user()->lecturer?->nip)
            ->where('role', 'supervisor 2');
    });

if ($selectedOption === 's1') {
    // take only from students that contain D121 or D421 on the early of his nim
    $countSupervisor1->whereHas('student', function (Builder $query) {
            $query->where('nim', 'like', 'D121%')
                ->orWhere('nim', 'like', 'D421%');
        });

    $countSupervisor2->whereHas('student', function (Builder $query) {
            $query->where('nim', 'like', 'D121%')
                ->orWhere('nim', 'like', 'D421%');
        });

} elseif ($selectedOption === 's2') {
    // take only from students that not contain D122 or D422 on the early of his nim
    $countSupervisor1->whereDoesntHave('student', function ($query) {
            $query->where('nim', 'like', 'D121%')
                ->orWhere('nim', 'like', 'D421%');
        });
    
    $countSupervisor2->whereDoesntHave('student', function ($query) {
            $query->where('nim', 'like', 'D121%')
                ->orWhere('nim', 'like', 'D421%');
        });

}

$countSupervisor1 = $countSupervisor1->count();
$countSupervisor2 = $countSupervisor2->count();

$finalProjectTotal = $countSupervisor1 + $countSupervisor2;
$supervisor1 = $countSupervisor1;
$supervisor2 = $countSupervisor2;

?>

<x-filament::section>
    <div class="flex flex-col">
        <div class="flex justify-end mb-5">
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model="selectedOption" wire:change="setSelectedOption($event.target.value)">
                    <option value="all">{{ __('All') }}</option>
                    <option value="s1">{{ __('S1') }}</option>
                    <option value="s2">{{ __('S2') }}</option>
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <x-filament::section>
                <section class="flex flex-col gap-2">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Final Project Total') }}</span>
                    <span class="text-3xl font-semibold">{{ $finalProjectTotal }}</span>
                </section>
            </x-filament::section>

            <x-filament::section>
                <section class="flex flex-col gap-2">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Supervisor 1') }}</span>
                    <span class="text-3xl font-semibold">{{ $supervisor1 }}</span>
                </section>
            </x-filament::section>

            <x-filament::section>
                <section class="flex flex-col gap-2">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Supervisor 2') }}</span>
                    <span class="text-3xl font-semibold">{{ $supervisor2 }}</span>
                </section>
            </x-filament::section>
        </div>
    </div>
</x-filament::section>
