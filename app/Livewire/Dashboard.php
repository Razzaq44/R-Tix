<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Showtime;
use App\Models\ShowingSeats;
use App\Models\Seat;
use App\Models\Movie;
use Illuminate\Support\Facades\Session;
use Artesaos\SEOTools\Facades\SEOTools;
use Exception;

class Dashboard extends Component
{
    public $showtimes;
    public $activeTab;
    public $movies;
    public $replaceModal = false;
    public $showtimeId;
    
    public function render()
    {
        return view('livewire.dashboard')->layout('layouts.app');
    }

    public function mount($activeTab = null)
    {
        SEOTools::setTitle('Pesan Tiket Bioskop Online | R-Tix');
        SEOTools::setDescription('Pesan tiket film terbaru, pilih kursi, dan bayar online dengan mudah. Booking tiket bioskop tanpa antre.');
        SEOTools::metatags()->addKeyword([
            'booking tiket bioskop',
            'tiket film online',
            'pesan tiket film',
            'nonton bioskop tanpa antre',
            'movie booking'
        ]);

        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());

        $selectedDate = $activeTab ?? now()->toDateString();
        $this->activeTab = $selectedDate;

        $days = Showtime::select('show_date')
            ->distinct()
            ->orderBy('show_date')
            ->get()
            ->map(function ($item) {
                return [
                    'day' => Carbon::parse($item->show_date)->format('Y-m-d'),
                ];
            })
            ->toArray();

        $this->showtimes = $days;

        $this->fetchMoviesForDay($this->activeTab);
    }

    public function fetchMoviesForDay($selectedDate)
    {
        $movies = Movie::whereHas('showtimes', function ($query) use ($selectedDate) {
            $query->where('show_date', $selectedDate);
        })
        ->with(['showtimes' => function ($query) use ($selectedDate) {
            $query->where('show_date', $selectedDate)->orderBy('time');
        }])
        ->get();

        $this->movies = [
            $selectedDate => [
                'day' => $selectedDate,
                'movies' => $movies->map(function ($movie) {
                    return [
                        'title' => $movie->title,
                        'showing' => $movie->showtimes->map(function ($showtime) {
                            return [
                                'id' => $showtime->id,
                                'type' => $showtime->type,
                                'time' => $showtime->time,
                            ];
                        }),
                    ];
                }),
            ],
        ];
    }

    public function setActiveTabDay($day)
    {
        $this->activeTab = $day;
        $this->mount($day);
    }

    public function openModal($showtimeId)
    {
        $this->showtimeId = $showtimeId;
        if (!auth()->user() && Session::get('confirmation_token')) {
            $this->replaceModal = true;
        } else {
            $this->goToSelectSeat($showtimeId);
        }
    }

    public function closeReplaceModal()
    {
        $this->replaceModal = false;
        $this->goToSelectSeat($this->showtimeId);
    }

    public function goToSelectSeat($showtimeId)
    {
        $showtime = Showtime::findOrFail($showtimeId);
        return $this->redirectRoute('select.seat', ['showtime' => $showtime->slug], navigate:true);
    }
}
