<?php

namespace App\Livewire\Presensi;

use Livewire\Component;
use App\Models\User;

class Approval extends Component
{
    public $id;

    public function mount($id)
    {
        $this->id = $id;
        $user = User::find($id);
        if ((bool) $user->status_approved) {
            return redirect()->route('login');
        }
    }

    public function render()
    {
        return view('livewire.presensi.approval')->layout('components.layouts.auth');
    }
}
