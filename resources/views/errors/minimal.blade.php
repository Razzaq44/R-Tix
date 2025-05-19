@extends('layouts.app')

@section('R-Tix', 'Error Found')

@section('content')
    <div class="text-center flex w-full h-full justify-center items-center flex-col">
        <flux:text class="text-xl mb-8">There was a problem processing your request. We've been notified and are working on
            it. Please try again in a few minutes.</flux:text>
        <flux:button href="/" wire:navigate class="hover:bg-transparent! underline" variant="ghost">Back To Dashboard
        </flux:button>
    </div>
@endsection
