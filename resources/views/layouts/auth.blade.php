<!doctype html>
<html>

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
    <flux:main container>
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
