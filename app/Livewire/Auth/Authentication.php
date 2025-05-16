<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Purchase;
use Artesaos\SEOTools\Facades\SEOTools;
use Exception;

class Authentication extends Component
{
    public $login, $name, $email, $password, $password_confirmation;
    public $remember = false;

    public $isSignUp = false;

    public function mount()
    {
        SEOTools::setTitle('Authentication | R-Tix');
    }

    public function showSignUp()
    {
        $this->isSignUp = true;
    }

    public function showSignIn()
    {
        $this->isSignUp = false;
    }

    public function signIn() 
    {
        $this->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $credentials = filter_var($this->login, FILTER_VALIDATE_EMAIL)
                ? ['email' => $this->login, 'password' => $this->password]
                : ['name' => $this->login, 'password' => $this->password];
    
            if (Auth::attempt(($credentials), $this->remember)) {
                if (!empty(Session::get('confirmation_token'))) {
                    $purchase = Purchase::where('confirmation_token', Session::get('confirmation_token'))->first();
                    $purchase->update([
                        'user_id' => Auth::id(),
                        'email' => Auth::user()->email,
                    ]);
                }
                DB::commit();
                return redirect()->intended('/');
            }

        } catch (Exception $e) {
            DB::rollback();
            session()->flash('error', 'An error occurred while processing sign in');
            return $this->redirect('/authentication', navigate:true);
        }
        session()->flash('error', 'Login failed. Check credentials.');
    }

    public function signUp()
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password)
            ]);

            if (!empty(Session::get('confirmation_token'))) {
                $purchase = Purchase::where('confirmation_token', Session::get('confirmation_token'))->first();
                $purchase->update([
                    'user_id' => $user->id(),
                    'email' => $user->email,
                ]);
            }
        
            event(new Registered($user));
        
            Auth::login($user);

            DB::commit();
        
            session()->flash('status', 'Please check your email to verify your account.');
            return $this->redirect('/', navigate: true);
        } catch (Exception $e) {
            DB::rollback();
            session()->flash('error', 'An error occurred while processing sign up');
            return $this->redirect('/authentication', navigate:true);
        }
    }

    public function render()
    {
        return view('livewire.auth.authentication')->layout('layouts.auth');
    }
}
