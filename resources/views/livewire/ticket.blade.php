<div class="">
    @foreach ($purchases as $purchase)
        <div class="grid grid-cols-1 md:grid-cols-4 shadow-xl gap-6 p-8 rounded mb-8">
            @guest
                <div class="flex gap-2 md:col-span-4 items-center">
                    <flux:icon variant="solid" class="text-yellow-500" icon="exclamation-triangle">
                    </flux:icon>
                    <flux:description>
                        You are not logged in. Please be aware that your ticket will not be
                        accessible for 24 hours following payment confirmation. Ensure you download your ticket promptly.
                    </flux:description>
                </div>
            @endguest

            <div class="flex flex-col md:col-span-3 gap-6">
                <flux:heading size="xl" class="uppercase">{{ $purchase->movie_title }}</flux:heading>
                <div class="flex flex-col gap-4 uppercase">
                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex flex-col">
                            <flux:label>SEAT NUMBER</flux:label>
                            <div class="flex gap-x-2">
                                @foreach ($purchase->purchaseItems as $item)
                                    <flux:text>{{ $item->seat_number }}</flux:text>
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
                <flux:button variant="primary" href="{{ route('ticket.download', $purchase->confirmation_token) }}"
                    target="_blank" class="">
                    Download Ticket
                </flux:button>
            </div>
            <div class="w-full flex justify-center items-center">
                {!! QrCode::size(200)->generate($purchase->confirmation_token) !!}
            </div>
        </div>
    @endforeach
</div>
