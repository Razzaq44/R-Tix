<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Showtime;
use App\Models\Seat;
use App\Models\ShowingSeats;
use Carbon\Carbon;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Support\Str;
use Exception;

class SelectSeat extends Component
{
    public $showtime, $seats, $selectedSeatPrice, $movieName, $showtimeId;
    public array $selectedSeats = [];
    public $seatNumber = [];

    public function render()
    {
        return view('livewire.select-seat')->layout('layouts.app');
    }

    public function mount(Showtime $showtime)
    {
        $this->showtime = $showtime;
        $this->seats = ShowingSeats::where('showtime_id', $showtime->id)->with(['seat' => fn ($query) => $query->orderBy('seat_number')])->get();
        $this->showtimeId = $showtime->id;

        SEOTools::setTitle($showtime->movie->title . ' | R-Tix');
        SEOTools::setDescription('Pesan tiket film ' . $showtime->movie->title . ', pilih kursi, dan bayar online dengan mudah. Booking tiket bioskop tanpa antre.');
        SEOTools::metatags()->addKeyword([
            'booking tiket bioskop',
            'tiket film online',
            'pesan tiket film',
            'nonton bioskop tanpa antre',
            'movie booking',
            $showtime->movie->title,
            $showtime->movie->title . ' hari ini',
        ]);

        SEOTools::setCanonical(url()->current());
        SEOTools::opengraph()->setUrl(url()->current());

        $this->movieName = $showtime->movie->title;
    }

    public function toggleSeatSelection(array $seatIds, $seatNumber)
    {
        foreach ($seatIds as $seatId) {
            if (in_array($seatId, $this->selectedSeats)) {
                $this->selectedSeats = array_filter($this->selectedSeats, fn($s) => $s != $seatId);
                $this->seatNumber = array_filter($this->seatNumber, fn($s) => $s != $seatNumber);
            } else {
                $this->selectedSeats[] = $seatId;
                $this->seatNumber[] = $seatNumber;
            }
        }

        $this->selectedSeats = array_values(array_unique($this->selectedSeats));
        $this->seatNumber = array_values(array_unique($this->seatNumber));

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
            session()->flash('error', 'An error occurred while processing your purchase');
            return $this->redirect('/', navigate:true);
        }
    }
}
