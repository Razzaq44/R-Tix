<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchase;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        DB::beginTransaction();

        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name'     => $googleUser->getName(),
                    'email'    => $googleUser->getEmail(),
                    'password' => bcrypt(uniqid()),
                    'email_verified_at' => now(),
                ]);
            }

            
            Auth::login($user, true);
            
            if (!empty(Session::get('confirmation_token'))) {
                $purchase = Purchase::where('confirmation_token', Session::get('confirmation_token'))->first();
                $purchase->update([
                    'user_id' => $user->id(),
                    'email' => $user->email,
                ]);
            }

            DB::commit();
            return redirect('/');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect('/authentication')->with('error', 'Failed to login with Google.');
        }
    }
}
