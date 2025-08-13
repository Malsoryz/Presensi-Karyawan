<?php

namespace App\Livewire;

use App\Models\Config;
use Livewire\Component;
use Carbon\Carbon;

class DateTime extends Component
{
    public Carbon $datetime;
    
    public function mount(): void
    {
        $now = now(Config::timezone())->locale('Id');
        $this->datetime = $now;
    }

    public function render()
    {
        return view('livewire.date-time');
    }
}
