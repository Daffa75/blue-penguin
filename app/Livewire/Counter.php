<?php

namespace App\Livewire;

use Livewire\Component;

class Counter extends Component
{
    public $selectedOption = 'all';

    public function setSelectedOption($value)
    {
        $this->selectedOption = $value;
    }

    public function render()
    {
        return view('livewire.counter');
    }
}
