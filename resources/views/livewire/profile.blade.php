<div>
    <div class="w-full h-full shadow-lg rounded-lg p-8">
        <div class="w-full flex justify-center items-center gap-2 flex-col">
            <flux:avatar size="xl" circle name="{{ auth()->user()->name }}" />
            <flux:heading class="uppercase font-bold">{{ auth()->user()->name }}</flux:heading>
        </div>
        <flux:heading class="mt-4">Email</flux:heading>

        <flux:input wire:model="email" disabled />
    </div>
</div>
