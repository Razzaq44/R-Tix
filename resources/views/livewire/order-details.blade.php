<div wire:poll="checkPaymentStatus">
    <div class="flex justify-between items-center w-full">
        <flux:heading size="xl">Payment</flux:heading>
    </div>
    <flux:field class="flex justify-center items-center w-full mt-3">
        <div class="space-y-6 w-full rounded shadow-lg p-8">
            <div class="">
                <flux:heading size="lg">Order Details</flux:heading>
                <flux:description class="">This order will expire in {{ $expiresAt }}</flux:description>
                <table>
                    <tr>
                        <td>
                            <flux:text>Movie</flux:text>
                        </td>
                        <td class="px-4">
                            <flux:text>:</flux:text>
                        </td>
                        <td>
                            <flux:text>{{ $movieName }}</flux:text>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <flux:text>Seat Number</flux:text>
                        </td>
                        <td class="px-4">
                            <flux:text>:</flux:text>
                        </td>
                        <td>
                            <div class="flex gap-1">
                                @foreach ($seatNumber as $seat)
                                    <flux:text>{{ $seat->seat->seat_number }}</flux:text>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <flux:text>Total</flux:text>
                        </td>
                        <td class="px-4">
                            <flux:text>:</flux:text>
                        </td>
                        <td>
                            <div class="flex">
                                <flux:text>Rp. {{ number_format($totalPrice, 0, ',', '.') }}</flux:text>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            @if (!auth()->check())
                <div class="">
                    <flux:input wire:model="email" badge="Required" label="E-mail" placeholder="example@test.com"
                        class="" required />
                    <flux:description class="text-red-500/60!">You haven't login yet, so you must fills with your email
                    </flux:description>
                </div>
            @endif
            <div class="flex flex-col gap-y-2">
                <flux:label badge="Optional">Voucher</flux:label>
                <flux:input.group>
                    <flux:input placeholder="HAPYYWEEKEND20" wire:model="voucher" class="" />
                    <flux:button variant="primary" wire:click="applyVoucher">Apply</flux:button>
                </flux:input.group>
            </div>

            <flux:radio.group wire:model="payment" label="Select your payment method">
                <flux:radio value="QRIS" label="QRIS" checked />
                <div class="">
                    <flux:radio value="TF" label="Bank transfer" disabled />
                    <flux:description class="text-yellow-500/60!">Under Maintenance</flux:description>
                </div>
            </flux:radio.group>
            <div class="flex justify-center items-center w-full flex-col gap-y-6">
                <div class="flex justify-center gap-4 items-center">
                    <flux:button wire:click="generateQr">Generate QR</flux:button>
                    <flux:modal.trigger name="cancel-order">
                        <flux:button variant="danger">Cancel</flux:button>
                    </flux:modal.trigger>
                </div>
                @if ($showQr)
                    {!! QrCode::size(200)->generate($url) !!}
                    <flux:description class="">Note: scan and just click continue. This only dummy QR 😁
                    </flux:description>
                @endif

            </div>
        </div>
    </flux:field>
    <flux:modal name="cancel-order" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Cancel Your Order</flux:heading>
                <flux:text class="mt-2">Are you sure want to cancel this order?</flux:text>
            </div>
            <div class="flex justify-center items-center gap-x-2">
                <flux:button type="submit" variant="primary" wire:click="cancelOrder">Yes</flux:button>
                <flux:modal.close>
                    <flux:button type="" variant="filled">No</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>
</div>
