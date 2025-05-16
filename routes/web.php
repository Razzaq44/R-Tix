<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Authentication;
use App\Livewire\Auth\SignIn;
use App\Livewire\Dashboard;
use App\Livewire\Profile;
use App\Livewire\OrderDetails;
use App\Livewire\Ticket;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\ForgotPassword;
use App\Http\Controllers\PaymentConfirmation;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/', Dashboard::class)->name('dashboard');
Route::get('/profile', Profile::class)->name('profile');
Route::get('/ticket', Ticket::class)->name('ticket');
Route::get('/order-details', OrderDetails::class)->name('order.details');
Route::get('/forgot-password', ForgotPassword::class)->name('forget.password');
Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');

Route::get('/payment/confirm', function() {
    return view('payment.confirm');});
Route::post('/payment/confirm', [PaymentConfirmation::class, 'pay']);
Route::get('/ticket/download/{confirmation_token}', [TicketController::class, 'download'])->name('ticket.download');
Route::get('/ticket/paid/{confirmation_token}', [TicketController::class, 'paid'])->name('ticket.view');


// Auth Routes
Route::get('/authentication', Authentication::class)->name('auth');
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

//Email Verification
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function () {
    request()->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');