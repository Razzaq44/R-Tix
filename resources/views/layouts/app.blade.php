<!doctype html>
<html class="scroll-smooth">

<head>
    {!! app('seotools')->generate() !!}
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    @vite('resources/css/app.css')
    {{-- @fluxAppearance --}}
    @livewireStyles
</head>

<body class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <flux:header container class="bg-gray-100 dark:bg-gray-950">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
        <flux:brand href="/" logo="" name="R-Tix" class="max-lg:hidden dark:hidden" wire:navigate />
        <flux:spacer />
        <flux:navbar class="-mb-px max-lg:hidden">
            <flux:navbar.item icon="home" href="/" class="@if (request->is('/')) font-bold @endif"
                wire:navigate>Home</flux:navbar.item>
            <flux:separator vertical variant="subtle" class="my-2" />
            <flux:navbar.item icon="shopping-bag" href="/order-details" wire:navigate>Order
            </flux:navbar.item>
            <flux:separator vertical variant="subtle" class="my-2" />
            <flux:navbar.item icon="ticket" href="/ticket" wire:navigate>Ticket</flux:navbar.item>
        </flux:navbar>
        <flux:spacer />
        <flux:dropdown position="top" align="start">
            @if (auth()->user())
                <flux:profile avatar="" color="orange" circle :chevron="false"
                    avatar:name="{{ auth()->user()->name }}" />
                <flux:menu>
                    <flux:menu.item href="/profile" icon="user" wire:navigate>Profile</flux:menu.item>
                    <flux:menu.separator />
                    <livewire:auth.logout>
                </flux:menu>
            @else
                <flux:avatar size="sm" color="orange" circle avatar:name="" href="/authentication" wire:navigate />
            @endif
        </flux:dropdown>
    </flux:header>
    <flux:sidebar stashable sticky
        class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border rtl:border-r-0 rtl:border-l border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />
        <flux:brand href="#" logo="https://fluxui.dev/img/demo/logo.png" name="Acme Inc."
            class="px-2 dark:hidden" />
        <flux:brand href="#" logo="https://fluxui.dev/img/demo/dark-mode-logo.png" name="Acme Inc."
            class="px-2 hidden dark:flex" />
        <flux:navlist variant="outline">
            <flux:navlist.item icon="home" href="/" class="@if (request->is('/')) font-bold @endif"
                wire:navigate>Home</flux:navlist.item>
            <flux:navlist.item icon="inbox" href="/profile" wire:current="" wire:navigate>Inbox</flux:navlist.item>
            <flux:navlist.item icon="document-text" href="#">Documents</flux:navlist.item>
            <flux:navlist.item icon="calendar" href="#">Calendar</flux:navlist.item>
            <flux:navlist.group expandable heading="Favorites" class="max-lg:hidden">
                <flux:navlist.item href="#">Marketing site</flux:navlist.item>
                <flux:navlist.item href="#">Android app</flux:navlist.item>
                <flux:navlist.item href="#">Brand guidelines</flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>
        <flux:spacer />
        <flux:navlist variant="outline">
            <flux:navlist.item icon="cog-6-tooth" href="#">Settings</flux:navlist.item>
            <flux:navlist.item icon="information-circle" href="#">Help</flux:navlist.item>
        </flux:navlist>
    </flux:sidebar>

    <flux:main container>
        @if (session('error'))
            <div class="">
                <flux:callout variant="danger" color="rose" icon="x-circle" heading="{{ session('error') }}"
                    class="mb-8" />
            </div>
        @elseif (session('success'))
            <div class="">
                <flux:callout variant="success" color="teal" icon="check-circle" heading="{{ session('success') }}"
                    class="mb-8" />
            </div>
        @elseif (session('warning'))
            <div class="">
                <flux:callout variant="warning" color="yellow" icon="exclamation-circle"
                    heading="{{ session('warning') }}" class="mb-8" />
            </div>
        @endif
        @if (!empty($slot))
            {{ $slot }}
        @else
            @yield('content')
        @endif
    </flux:main>

    @fluxScripts
    @livewireScripts
</body>

</html>
