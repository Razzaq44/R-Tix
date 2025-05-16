<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Logout extends Component
{
    public function render()
    {
        return view('livewire.auth.logout');
    }

    public function logout()
    {
        Auth::logout();

        session()->invalidate();
        session()->regenerateToken();

        session()->flash('success', 'Logout Successfully');
        return $this->redirect('/', navigate:true);
    }
}
