@extends('layouts.auth')

@section('content')
    <div class="flex w-full justify-center items-center h-full">
        <div class="shadow-xl p-8 rounded max-w-80">
            <div class="flex flex-col gap-6">
                <flux:heading size="xl" class="uppercase">{{ $purchase->movie_title }}</flux:heading>
                <div class="flex flex-col gap-4 uppercase">
                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex flex-col">
                            <flux:label>SEAT NUMBER</flux:label>
                            <div class="flex gap-x-2 flex-wrap w-fit">
                                @foreach ($purchase->purchaseItems as $item)
                                    <flux:text>{{ $item->seat_number }}
                                    </flux:text>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <flux:label>STATUS</flux:label>
                            <flux:text>{{ $purchase->status }}</flux:text>
                        </div>
                        <div class="flex flex-col">
                            <flux:label>VOUCHER</flux:label>
                            <flux:text>{{ $purchase->voucher_code }}</flux:text>
                        </div>
                        <div class="flex flex-col">
                            <flux:label>PRICE</flux:label>
                            <flux:text>Rp. {{ number_format($purchase->price, 0, ',', '.') }}</flux:text>
                        </div>
                    </div>
                </div>
                <div class="w-full flex justify-center items-center">
                    {!! QrCode::size(200)->generate($purchase->confirmation_token) !!}
                </div>
                <flux:button variant="primary" href="{{ route('ticket.download', $purchase->confirmation_token) }}"
                    target="_blank" class="">
                    Download Ticket
                </flux:button>
            </div>
        </div>
    </div>
@endsection
