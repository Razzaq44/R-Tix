<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Artesaos\SEOTools\Facades\SEOTools;

class ResetPassword extends Component
{
    public $email, $password, $password_confirmation, $token;

    public function render()
    {
        return view('livewire.auth.reset-password')->layout('layouts.auth');
    }

    public function mount($token)
    {
        $this->token = $token;
        SEOTools::setTitle('Reset Your Password | R-Tix');
    }

    public function resetPassword()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('success', 'Password reset successfully.');
            return $this->redirect('/authentication', navigate:true);
        } else {
            session()->flash('error', 'Reset failed.');
        }
    }

}
