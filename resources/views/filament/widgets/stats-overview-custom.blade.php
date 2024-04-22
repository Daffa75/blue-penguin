@php

  use App\Models\FinalProject;
  use Filament\Widgets\StatsOverviewWidget\Stat;

  $All = FinalProject::count();
  $S1Count = FinalProject::whereHas('student', function ($query) {
      $query->where('nim', 'like', 'D121%')
          ->orWhere('nim', 'like', 'D421%');
  })->count();
  $S2Count = FinalProject::whereDoesntHave('student', function ($query) {
      $query->where('nim', 'like', 'D121%')
          ->orWhere('nim', 'like', 'D421%');
  })->count();

@endphp

<div 
  x-data="{
    selectedOption: 'all',
    count: <?php echo json_encode($All) ?>,
    updateCount: function() {
      // Lakukan pembaruan berdasarkan nilai selectedOption
      if (this.selectedOption === 'all') {
        this.count = <?php echo json_encode($All) ?>;
      } else if (this.selectedOption === 's1') {
        this.count = <?php echo json_encode($S1Count) ?>;
      } else if (this.selectedOption === 's2') {
        this.count = <?php echo json_encode($S2Count) ?>;
      }
    }
  }"
>
  <x-filament::section>
    <div class="flex justify-between">
      <section class="flex flex-col gap-2">
        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Final Project') }}</span>
        <span class="text-3xl font-semibold" x-text="count"></span>
      </section>
      
      <section>
        <x-filament::input.wrapper>
          <x-filament::input.select x-model="selectedOption" @change="updateCount">
            <option value="all">{{ __('All') }}</option>
            <option value="s1">S1</option>
            <option value="s2">S2</option>
          </x-filament::input.select>
        </x-filament::input.wrapper>
      </section>
    </div>
  </x-filament::section>
</div>
