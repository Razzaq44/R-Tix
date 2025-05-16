<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ShowingSeats;
use Carbon\Carbon;
use App\Models\Showtime;
use App\Models\Movie;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Support\Facades\Auth;

class Profile extends Component
{
    public $email;

    public function mount()
    {
        SEOTools::setTitle('Profile | R-Tix');

        $this->email = Auth::user()->email;
    }

    public function render()
    {
        return view('livewire.profile')->layout('layouts.app');
    }
}

