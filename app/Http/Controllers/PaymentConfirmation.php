<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class PaymentConfirmation extends Controller
{
    public function pay(Request $request)
    {
        $request->validate([
            'confirmation_token' => 'required|string',
        ]);

        $token = $request->input('confirmation_token');

        DB::beginTransaction();

        try {
            $purchase = Purchase::where('confirmation_token', $token)
                ->where('status', 'pending')
                ->first();

            if (!$purchase) {
                return redirect('/')->with('error', 'Invalid or already paid token.');
            }

            $purchase->update(['status' => 'paid']);

            DB::commit();

            session()->flash('success', 'Payment confirmed successfully.');
            return view('payment.success');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect('/')->with('error', 'Failed to confirm payment.');
        }
    }
}
