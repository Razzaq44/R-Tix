<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Password;
use Artesaos\SEOTools\Facades\SEOTools;

class ForgotPassword extends Component
{
    public $email;

    public function render()
    {
        return view('livewire.auth.forgot-password')->layout('layouts.auth');
    }

    public function mount()
    {
        SEOTools::setTitle('Forget Password | R-Tix');
    }

    public function sendResetPasswordLink()
    {
        $this->validate(['email' => 'required|email']);
        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('success', 'Password reset link sent!');
        } else {
            session()->flash('error', 'Failed to send reset link.');
        }
    }
}
