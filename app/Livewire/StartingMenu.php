<?php

namespace App\Livewire;

use Closure;
use Livewire\Attributes\Layout;
use Livewire\Component;

class StartingMenu extends Component
{
    public function render()
    {
        $icons = [
            'admin' => 'heroicon-m-users',
            'lecturer' => 'phosphor-graduation-cap-fill',
            'student' => 'phosphor-student-fill',
        ];
        $panels = filament()->getPanels();
        $labels = [
            'lecturer' => 'Lecturer',
            'student' => 'Student',
        ];
        return view('livewire.starting-menu', [
            "panels" => $panels,
            "icons" => $icons,
            "labels" => $labels
        ]);
    }
}
