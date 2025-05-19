@extends('layouts.app')

@section('R-Tix', 'Page Not Found')

@section('content')
    <div class="text-center flex w-full h-full justify-center items-center flex-col">
        <flux:heading size="xl" class="text-red-500">404</flux:heading>
        <flux:text class="text-xl mb-8">We can't seem to find the page you're looking for.</flux:text>
        <flux:button href="/" wire:navigate class="hover:bg-transparent! underline" variant="ghost">Back To Dashboard
        </flux:button>
    </div>
@endsection
