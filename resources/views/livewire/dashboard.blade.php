<div class="">
    @php
        use Carbon\Carbon;
        $date = $activeTab === 'Today' ? now()->toDateString() : $activeTab;
    @endphp
    <div
        class="w-full h-96 shadow bg-black rounded-lg flex justify-center items-center bg-[url(https://img.freepik.com/premium-photo/flying-popcorn-3d-glasses-film-reel-clapboard-yellow-background-cinema-movie-concept-3d_989822-1302.jpg)] bg-contain">
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
                        <flux:heading size="xl" class="text-lg font-bold">{{ $movie['title'] }}</flux:heading>
                        @php
                            $groupedShowtimes = collect($movie['showing'])->groupBy('type');
                        @endphp

                        @foreach ($groupedShowtimes as $type => $showtimes)
                            <div class="mt-3 grid grid-cols-2 gap-3 items-center">
                                <flux:heading class="uppercase w-fit">{{ $type }}</flux:heading>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($showtimes as $showtime)
                                        @php
                                            $showDateTime = \Carbon\Carbon::parse(
                                                $date . ' ' . $showtime['time'],
                                            )->addMinutes(15);
                                        @endphp
                                        @if ($showDateTime->isFuture())
                                            <flux:button wire:click="goToSelectSeat({{ $showtime['id'] }})"
                                                wire:navigate>
                                                {{ $showtime['time'] }}
                                            </flux:button>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endif
    </div>
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
