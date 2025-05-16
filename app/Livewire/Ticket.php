<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Purchase;
use Illuminate\Support\Facades\Session;
use Artesaos\SEOTools\Facades\SEOTools;

class Ticket extends Component
{
    public $confirmation_token;
    public $purchases;

    public function render()
    {
        return view('livewire.ticket')->layout('layouts.app');
    }

    public function mount()
    {
        SEOTools::setTitle('Your Ticket | R-Tix');
        if (!empty(Session::get('confirmation_token')) && !auth()->user()) {
            session()->flash('error', 'No seats selected.');
            return $this->redirect('/', navigate:true);
        } elseif (auth()->user()) {
            if (Purchase::with('purchaseItems')->where('user_id', auth()->id())->first() !== null) {
                $this->purchases = Purchase::with('purchaseItems')->where('user_id', auth()->id())->first()->get();
            } else {
                session()->flash('error', 'You Don`t have any orders recorded');
                return $this->redirect('/', navigate:true);
            }
        } else {
            $this->confirmation_token = Session::get('confirmation_token');
            $this->purchases = Purchase::with('purchaseItems')->where('confirmation_token', $this->confirmation_token)->where('status', 'paid')->first();
        }
    }
}
