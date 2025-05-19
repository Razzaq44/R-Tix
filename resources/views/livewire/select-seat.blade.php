<div>

    <div class="w-full md:grid md:grid-cols-2 items-center justify-between mb-3">
        <div class="flex gap-2">
            <flux:button class="" icon="chevron-double-left" onclick="window.history.back();" variant="subtle"
                wire:navigate>Back
            </flux:button>
            <flux:separator vertical />
            <flux:heading size="xl">{{ $showtime->movie->title }}</flux:heading>
        </div>
        <div
            class="flex flex-wrap gap-x-4 md:justify-end items-center mt-4 md:mt-0 overflow-auto no-scrollbar w-full gap-y-2 md:gap-y-0">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-pink-400!"></div>
                <flux:text>SweetBox</flux:text>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-blue-950"></div>
                <flux:text>Available</flux:text>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-blue-600"></div>
                <flux:text>Selected</flux:text>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-gray-400"></div>
                <flux:text>Booked</flux:text>
            </div>
        </div>
    </div>
    <flux:separator />
    <div class="grid grid-rows-10 overflow-auto gap-y-5 no-scrollbar mt-5">
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

                $sortedSeatsInRow = $seatsInRow->sortBy(fn($s) => intval(substr($s->seat->seat_number, 1)))->values();
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
                                <flux:button
                                    wire:click="toggleSeatSelection({{ json_encode($seatIds) }}, '{{ $seatLabels }}')"
                                    wire:key="sweetbox-{{ $seatLabels }}" variant="filled"
                                    class="w-24 {{ collect($seatIds)->intersect($selectedSeats)->count() === count($seatIds) ? 'bg-blue-600!' : 'bg-pink-400!' }} text-white!">
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
                                    class="w-10 bg-gray-400! cursor-not-allowed }}" disabled variant="subtle">
                                    {{ $seat->seat->seat_number }}
                                </flux:button>
                            @else
                                <flux:button
                                    wire:click="toggleSeatSelection([{{ $seat->id }}], '{{ $seat->seat->seat_number }}')"
                                    wire:key="seat-{{ $seat->id }}" variant="filled"
                                    class="w-10 {{ in_array($seat->id, $selectedSeats) ? 'bg-blue-600!' : 'bg-blue-950!' }} text-white!">
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
                                <flux:button wire:click="" wire:key="seat-{{ $seat->id }}"
                                    class="w-10 bg-gray-400! cursor-not-allowed }}" disabled>
                                    {{ $seat->seat->seat_number }}
                                </flux:button>
                            @else
                                <flux:button
                                    wire:click="toggleSeatSelection([{{ $seat->id }}], '{{ $seat->seat->seat_number }}')"
                                    wire:key="seat-{{ $seat->id }}" variant="filled"
                                    class="w-10 {{ in_array($seat->id, $selectedSeats) ? 'bg-blue-600!' : 'bg-blue-950!' }} text-white!">
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
                                <flux:button wire:click="" wire:key="seat-{{ $seat->id }}"
                                    class="w-10 bg-gray-400! cursor-not-allowed }}" disabled>
                                    {{ $seat->seat->seat_number }}
                                </flux:button>
                            @else
                                <flux:button
                                    wire:click="toggleSeatSelection([{{ $seat->id }}], '{{ $seat->seat->seat_number }}')"
                                    wire:key="seat-{{ $seat->id }}" variant="filled"
                                    class="w-10 {{ in_array($seat->id, $selectedSeats) ? 'bg-blue-600!' : 'bg-blue-950!' }} text-white!">
                                    {{ $seat->seat->seat_number }}
                                </flux:button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        @endforeach

    </div>
    {{-- SCREEN --}}
    <div class="w-full flex justify-center items-center bg-slate-300 py-0.5 my-8">
        <flux:heading size="xl" class="uppercase">screen</flux:heading>
    </div>
    <div class="flex flex-row justify-between w-full">
        <div class="flex w-full justify-center items-center flex-col">
            <flux:heading size="lg" class="uppercase">TOTAL PRICE</flux:heading>
            <flux:text>Rp {{ number_format($selectedSeatPrice, 0, ',', '.') }}</flux:text>
        </div>
        <div class="flex justify-center items-center">
            <flux:separator vertical />
        </div>
        <div class="flex w-full justify-center items-center flex-col">
            <flux:heading size="lg" class="uppercase">NUMBER SEAT</flux:heading>
            <div class="flex flex-wrap gap-2">
                @forelse ($seatNumber as $seat)
                    <div class="py-1 px-2 bg-accent rounded shadow-accent-content">
                        <flux:text>{{ $seat }} </flux:text>
                    </div>
                @empty
                    <flux:text>No Seat Selected Yet</flux:text>
                @endforelse
            </div>
        </div>
    </div>
    <div class="w-full flexjustify-center items-center mt-6 gap-4">
        @if (count($selectedSeats) < 1)
            <flux:button class="cursor-not-allowed w-full" disbaled>Confirm Ticket</flux:button>
        @else
            <flux:button variant="primary" wire:click="confirmTicket" class="w-full">Confirm Ticket
            </flux:button>
        @endif
    </div>
</div>
