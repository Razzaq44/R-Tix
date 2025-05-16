<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\ShowingSeats;
use App\Models\Purchase;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Artesaos\SEOTools\Facades\SEOTools;

class OrderDetails extends Component
{
    
    public $selectedSeatIds;
    public $showtimeId;
    public $totalPrice;
    public $seatNumber = [];
    public $movieName;
    public $url;
    public $showQr = false;
    public $email = '';
    public string $voucher = '';
    public $confirmation_token;
    public $expiresAt;

    protected $rules = [
            'email' => 'required|email',
        ];

    public function render()
    {
        return view('livewire.order-details')->layout('layouts.app');
    }

    public function mount()
    {
        SEOTools::setTitle('Order Details | R-Tix');

        $this->selectedSeatIds = Session::get('selected_seats');
        $this->showtimeId = Session::get('showtime_id');
        $this->expiresAt = Session::get('expires_at');
    
        if (!$this->selectedSeatIds || !$this->showtimeId) {
            session()->flash('error', 'No seats selected.');
            return $this->redirect('/', navigate:true);
        }

        if (!$this->expiresAt || now()->greaterThan($this->expiresAt)) {
            $this->cancelOrder();
        }
        
        $this->totalPrice = Session::get('total_price');
        $this->movieName = Session::get('movie_name');
        $this->confirmation_token = Session::get('confirmation_token');
        $this->seatNumber = ShowingSeats::with('seat')
            ->whereIn('id', $this->selectedSeatIds)
            ->get();

        $this->url = env('URL') . 'payment/confirm?confirmation_token=' . $this->confirmation_token;
    }

    public function checkPaymentStatus()
    {
        $confirmationToken = Session::get('confirmation_token');

        $purchase = Purchase::where('confirmation_token', $confirmationToken)->first();

        if ($purchase && $purchase->status === 'paid') {
            Session::forget(['total_price', 'movie_name', 'selected_seats', 'showtime_id']);
            session()->flash('success', 'Payment confirmed successfully.');
            return $this->redirect('/ticket', navigate:true);
        } elseif ($purchase && $purchase->status === 'pending') {
            if (!$this->expiresAt || now()->greaterThan($this->expiresAt)) {
                $this->cancelOrder();
            }
        }

    }

    public function generateQr()
    {
        if (!auth()->check()) {
            $this->validateOnly('email');
        } else {
            $this->email = auth()->user()->email;
        }

        $purchase = Purchase::where('confirmation_token', $this->confirmation_token)->first();
        $purchase->update(['email' => $this->email]);

        return $this->showQr = true;
    }

    public function applyVoucher()
    {
        if($this->voucher !== '') {
            $voucher = Voucher::where('code', strtoupper($this->voucher))->first();
            $discount = $voucher->discount_amount;
            if ($voucher->discount_type === 'percent') {
                $this->totalPrice = $this->totalPrice - ($this->totalPrice * ($discount / 100));
            } else {
                $this->totalPrice = $this->totalPrice - $discount;
            }
            $purchase = Purchase::where('confirmation_token', $this->confirmation_token)->first();
            $purchase->update([
                'voucher_id' => $voucher->id,
                'voucher_code' => $voucher->code,
                'price' => $this->totalPrice,
            ]);
        }
    }

    public function cancelOrder()
    {
        DB::beginTransaction();

        try {
            $purchase = Purchase::with('purchaseItems.showingSeat')
                ->where('confirmation_token', $this->confirmation_token)
                ->first();
        
            if (!$purchase) {
                session()->flash('error', 'Purchase not found.');
                return $this->redirect('/', navigate: true);
            }
        
            $showingSeatIds = $purchase->purchaseItems->pluck('showingSeat.id')->filter()->all();
        
            if (!empty($showingSeatIds)) {
                ShowingSeats::whereIn('id', $showingSeatIds)->update(['is_booked' => false]);
            }
        
            $purchase->delete();
        
            Session::forget([
                'total_price',
                'movie_name',
                'selected_seats',
                'showtime_id'
            ]);
        
            session()->flash('warning', 'Your order has been canceled.');
        
            DB::commit();
        
            return $this->redirect('/', navigate: true);
        
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'An error occurred while canceling the order.');
            return $this->redirect('/', navigate: true);
        }
        
    }
}
