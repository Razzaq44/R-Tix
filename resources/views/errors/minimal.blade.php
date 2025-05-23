<!DOCTYPE html>
<html lang="en">

<head>
    <title>Error</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    @vite('resources/css/app.css')
    @livewireStyles
</head>

<body>
    <div class="text-center flex w-full h-full justify-center items-center flex-col">
        <flux:text class="text-xl mb-8">There was a problem processing your request. We've been notified and are working
            on
            it. Please try again in a few minutes.</flux:text>
    </div>
    @fluxScripts
    @livewireScripts
</body>

</html>
