<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Showtime;
use App\Models\ShowingSeats;
use App\Models\Seat;
use App\Models\Movie;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Artesaos\SEOTools\Facades\SEOTools;
use Exception;

class Dashboard extends Component
{
    public $showtimes = [];
    public $activeTab;
    public $movies = [];
    public $selectedShowtimeId;
    public $seats = [];
    public $bookingModal = false;
    public $replaceModal = false;
    public $showtimeId;
    public array $selectedSeats = [];
    public $selectedSeatPrice;
    public $movieName;
    
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
            $this->bookingModal($showtimeId);
        }
    }

    public function bookingModal($showtimeId)
    {
        $this->seats = ShowingSeats::where('showtime_id', $showtimeId)->with(['seat' => fn ($query) => $query->orderBy('seat_number')])->get();
        $this->movieName = ShowingSeats::with('showtime.movie')
            ->where('showtime_id', $showtimeId)
            ->first()
            ?->showtime
            ?->movie
            ?->title;

        $this->bookingModal = true;
        $this->selectedSeats = [];
        $this->selectedSeatPrice = 0;  
    }

    public function toggleSeatSelection(array $seatIds)
    {
        foreach ($seatIds as $seatId) {
            if (in_array($seatId, $this->selectedSeats)) {
                $this->selectedSeats = array_filter($this->selectedSeats, fn($s) => $s != $seatId);
            } else {
                $this->selectedSeats[] = $seatId;
            }
        }
        $this->selectedSeats = array_values(array_unique($this->selectedSeats));
        $this->calculatePrice();
    }

    public function getSeatPrice(Seat $seat)
    {
        $isWeekend = in_array(Carbon::now()->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]);

        if ($seat->type === 'sweetbox') {
            return $isWeekend ? 50000 : 40000;
        }

        // Regular Seat
        return $isWeekend ? 40000 : 35000;
    }

    public function calculatePrice()
    {
        $total = 0;
        foreach ($this->selectedSeats as $showingSeatId) {
            $seat = ShowingSeats::with('seat')->findOrFail($showingSeatId);
            $total += $this->getSeatPrice($seat->seat);
        }

        $this->selectedSeatPrice = $total;
    }

    public function confirmTicket()
    {
        if (count($this->selectedSeats) < 1) {
            return;
        }

        if (Session::get('selected_seat') !== null) {
            session()->flash('warning', 'Finish or cancel your order first please!');
            return $this->redirect('/', navigate:true);
        }

        DB::beginTransaction();

        try{
            $seatDetails = [];

            $showingSeats = ShowingSeats::with('seat')
                ->whereIn('id', $this->selectedSeats)
                ->lockForUpdate()
                ->get();

            foreach ($showingSeats as $showingSeat) {    
                if ($showingSeat->is_booked) {
                    session()->flash('error', 'Seats already booked.');
                    return $this->redirect('/', navigate:true);
                }
                
                $price = $this->getSeatPrice($showingSeat->seat);
                $seatDetails[] = [
                    'showing_seat_id' => $showingSeat->id,
                    'price' => $price,
                    'seat_number' => $showingSeat->seat->seat_number,
                    'seat_type' => $showingSeat->seat->type,
                ];
                
                $showingSeat->is_booked = true;
                $showingSeat->save();
            }

            $confirmation_token = (string) Str::uuid();
            
            $purchase = Purchase::create([
                'user_id' => auth()->check() ? auth()->id() : null,
                'email' => auth()->check() ? auth()->user()->email : null,
                'price' => $this->selectedSeatPrice,
                'status' => 'pending',
                'voucher_id' => null,
                'confirmation_token' => $confirmation_token,
                'movie_title' => $this->movieName,
            ]);

            foreach ($seatDetails as $seat) {
                PurchaseItem::create([
                    'purchase_id'      => $purchase->id,
                    'showing_seat_id'  => $seat['showing_seat_id'],
                    'price'            => $seat['price'],
                    'seat_number'      => $seat['seat_number'],
                    'seat_type'        => $seat['seat_type'],
                ]);
            }

            DB::commit();

            Session::put('selected_seats', $this->selectedSeats);
            Session::put('showtime_id', $this->showtimeId);
            Session::put('total_price', $this->selectedSeatPrice);
            Session::put('movie_name', $this->movieName);
            Session::put('confirmation_token', $confirmation_token);
            Session::put('expires_at', Carbon::now()->addMinutes(5));
    
            return $this->redirect('/order-details', navigate:true);
        } catch (Exception $e) {
            DB::rollback();
            session()->flash('error', 'An error occurred while processing your purchase: ' . $e->getMessage());
            return $this->redirect('/', navigate:true);
        }
    }

    public function closeReplaceModal()
    {
        $this->replaceModal = false;
        $this->bookingModal($this->showtimeId);
    }
}
