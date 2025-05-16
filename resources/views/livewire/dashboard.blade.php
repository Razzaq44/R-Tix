<div class="">
    @php
        use Carbon\Carbon;
        $date = $activeTab === 'Today' ? now()->toDateString() : $activeTab;
    @endphp
    <div class="w-full h-68 bg-black rounded-lg flex justify-center items-center">
        <flux:heading size="lg">SPACE ADS AVAILABLE</flux:heading>
    </div>
    <div class="mt-6">
        <flux:heading size="xl">🎬 Showtimes</flux:heading>

        @if (empty($showtimes))
            <flux:heading size="xl">No showtimes found</flux:heading>
        @else
            <div class="flex gap-4 overflow-auto">
                @foreach ($showtimes as $showtime)
                    <div class="mt-4">
                        <flux:button wire:click="setActiveTabDay('{{ $showtime['day'] }}')" class=""
                            variant="{{ $activeTab === $showtime['day'] ? 'primary' : 'outline' }}">
                            {{ Carbon::parse($showtime['day'])->format('D') }}
                        </flux:button>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 w-full">
                @foreach ($movies[$date]['movies'] ?? [] as $movie)
                    <div class="p-4 rounded shadow mb-4">
                        <flux:heading size="lg" class="text-lg font-bold">{{ $movie['title'] }}</flux:heading>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($movie['showing'] as $showtime)
                                @php
                                    $showDateTime = Carbon::parse($date . ' ' . $showtime['time'])->subMinutes(-15);
                                @endphp
                                @if ($showDateTime->isFuture())
                                    <flux:button wire:click="openModal({{ $showtime['id'] }})">
                                        {{ strtoupper($showtime['type']) }} - {{ $showtime['time'] }}
                                    </flux:button>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    <flux:modal wire:model.self="bookingModal" class="w-full">
        <div class="p-8">
            <div class="w-full flex items-center justify-center mb-6">
                <flux:heading size="xl">{{ $movieName }}</flux:heading>
            </div>
            <div class="grid grid-rows-10 overflow-auto gap-y-5">
                @php
                    $preferredOrder = range('A', 'Z');

                    $groupedByRow = collect($seats)
                        ->groupBy(fn($s) => substr($s->seat->seat_number, 0, 1))
                        ->sortKeysUsing(function ($a, $b) use ($preferredOrder) {
                            return array_search($a, $preferredOrder) <=> array_search($b, $preferredOrder);
                        });
                @endphp

                @foreach ($groupedByRow as $row => $seatsInRow)
                    @php

                        $sortedSeatsInRow = $seatsInRow
                            ->sortBy(fn($s) => intval(substr($s->seat->seat_number, 1)))
                            ->values();
                    @endphp
                    @if ($row === 'A')
                        <div class="grid grid-cols-30 gap-x-4 items-center">
                            @php
                                $sortedSeats = $seatsInRow
                                    ->sortBy(fn($s) => intval(substr($s->seat->seat_number, 1)))
                                    ->values();
                            @endphp
                            @for ($i = 0; $i < $sortedSeats->count(); $i += 2)
                                @php
                                    $pair = $sortedSeats->slice($i, 2);
                                    $seatLabels = $pair->map(fn($s) => $s->seat->seat_number)->join(' | ');
                                    $seatIds = $pair->pluck('id')->toArray();
                                    $isDisabled = $pair->contains(fn($s) => $s->is_booked);
                                @endphp
                                <div class="col-span-2">
                                    @if ($isDisabled === true)
                                        <flux:button wire:click="" wire:key="sweetbox-{{ $seatLabels }}"
                                            class="w-24 cursor-not-allowed bg-gray-400!" disabled>
                                            {{ $seatLabels }}
                                        </flux:button>
                                    @else
                                        <flux:button wire:click="toggleSeatSelection({{ json_encode($seatIds) }})"
                                            wire:key="sweetbox-{{ $seatLabels }}"
                                            class="w-24 {{ collect($seatIds)->intersect($selectedSeats)->count() === count($seatIds) ? 'bg-yellow-400!' : 'bg-red-400!' }}">
                                            {{ $seatLabels }}
                                        </flux:button>
                                    @endif
                                </div>
                            @endfor
                        </div>
                    @else
                        <div
                            class="grid grid-cols-[repeat(8,auto)_1fr_repeat(14,auto)_1fr_repeat(8,auto)] gap-x-2 items-center">
                            {{-- KIRI --}}
                            @foreach ($sortedSeatsInRow->slice(0, 8) as $seat)
                                <div>
                                    @if ($seat->is_booked)
                                        <flux:button wire:click="" wire:key="seat-{{ $seat->id }}"
                                            class="w-10 bg-gray-400! cursor-not-allowed }}" disabled>
                                            {{ $seat->seat->seat_number }}
                                        </flux:button>
                                    @else
                                        <flux:button wire:click="toggleSeatSelection([{{ $seat->id }}])"
                                            wire:key="seat-{{ $seat->id }}"
                                            class="w-10 {{ in_array($seat->id, $selectedSeats) ? 'bg-yellow-400!' : 'bg-green-400!' }}">
                                            {{ $seat->seat->seat_number }}
                                        </flux:button>
                                    @endif
                                </div>
                            @endforeach

                            {{-- JALAN KIRI --}}
                            <div class="rounded w-10"></div>

                            {{-- TENGAH --}}
                            @foreach ($sortedSeatsInRow->slice(8, 14) as $seat)
                                <div>
                                    @if ($seat->is_booked)
                                        <flux:button wire:click="bookSeat({{ $seat->id }})"
                                            wire:key="seat-{{ $seat->id }}"
                                            class="w-10 {{ $seat->is_booked ? 'bg-gray-400! cursor-not-allowed' : 'bg-green-500! hover:bg-green-600' }}"
                                            disabled>
                                            {{ $seat->seat->seat_number }}
                                        </flux:button>
                                    @else
                                        <flux:button wire:click="bookSeat({{ $seat->id }})"
                                            wire:key="seat-{{ $seat->id }}"
                                            class="w-10 {{ $seat->is_booked ? 'bg-gray-400! cursor-not-allowed' : 'bg-green-500! hover:bg-green-600' }}">
                                            {{ $seat->seat->seat_number }}
                                        </flux:button>
                                    @endif
                                </div>
                            @endforeach

                            {{-- JALAN KANAN --}}
                            <div class="rounded w-10"></div>

                            {{-- KANAN --}}
                            @foreach ($sortedSeatsInRow->slice(22, 8) as $seat)
                                <div>
                                    @if ($seat->is_booked)
                                        <flux:button wire:click="bookSeat({{ $seat->id }})"
                                            wire:key="seat-{{ $seat->id }}"
                                            class="w-10 {{ $seat->is_booked ? 'bg-gray-400! cursor-not-allowed' : 'bg-green-500! hover:bg-green-600' }}"
                                            disabled>
                                            {{ $seat->seat->seat_number }}
                                        </flux:button>
                                    @else
                                        <flux:button wire:click="bookSeat({{ $seat->id }})"
                                            wire:key="seat-{{ $seat->id }}"
                                            class="w-10 {{ $seat->is_booked ? 'bg-gray-400! cursor-not-allowed' : 'bg-green-500! hover:bg-green-600' }}">
                                            {{ $seat->seat->seat_number }}
                                        </flux:button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endforeach

                {{-- SCREEN --}}
                <div class="w-full flex justify-center items-center">
                    <div class="w-3/4 h-8 border rounded-xl bg-gray-300 text-center items-center my-8">
                        <flux:heading size="lg">screen</flux:heading>
                    </div>
                </div>
            </div>
            <div class="w-full mt-8">
                Rp {{ number_format($selectedSeatPrice, 0, ',', '.') }}
            </div>
            <div class="w-full justify-center items-center mt-6">
                @if (count($selectedSeats) < 1)
                    <flux:button class="cursor-not-allowed" disbaled>Confirm Ticket</flux:button>
                @else
                    <flux:button variant="primary" wire:click="confirmTicket">Confirm Ticket</flux:button>
                @endif
            </div>
        </div>
    </flux:modal>
    <flux:modal wire:model.self="replaceModal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Warning!</flux:heading>
                <flux:text class="mt-2">You are not logged in. This action will result in the deletion of any tickets
                    you have previously booked or bought. Additionally, please be aware that your ticket will not be
                    accessible for 24 hours following payment confirmation. Ensure you download your ticket promptly.
                    You can view your ticket in the <flux:link href="/ticket" class="text-blue-400!">ticket tab
                    </flux:link>. If you wish to retain your ticket,
                    please register or
                    log in first, and we will securely store it for you.
                </flux:text>
            </div>
            <div class="flex justify-center items-center gap-x-2">
                <flux:button variant="filled" wire:click="closeReplaceModal">Yes
                </flux:button>
                <flux:button href="/authentication" variant="primary">Register</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
