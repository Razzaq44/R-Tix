@extends('layouts.app')

@section('content')
    <div class="w-full h-full flex justify-center items-center flex-col">
        <flux:heading>Confirming Your Payment</flux:heading>
        <flux:text>Please Be Patient</flux:text>
        <div class="loader"></div>
    </div>
    <form id="payment-form" action="{{ url('/payment/confirm') }}" method="POST">
        @csrf
        <input type="hidden" name="confirmation_token" id="confirmation_token">
    </form>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const confirmationToken = urlParams.get('confirmation_token');

        if (confirmationToken) {
            document.getElementById('confirmation_token').value = confirmationToken;
            document.getElementById('payment-form').submit();
        } else {
            alert('Invalid QR Code');
            window.close();
        }
    });
</script>

<style>
    .loader {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: inline-block;
        position: relative;
        border: 3px solid;
        border-color: #FFF #FFF transparent transparent;
        box-sizing: border-box;
        animation: rotation 1s linear infinite;
    }

    .loader::after,
    .loader::before {
        content: '';
        box-sizing: border-box;
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        margin: auto;
        border: 3px solid;
        border-color: transparent transparent #FF3D00 #FF3D00;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        box-sizing: border-box;
        animation: rotationBack 0.5s linear infinite;
        transform-origin: center center;
    }

    .loader::before {
        width: 32px;
        height: 32px;
        border-color: #FFF #FFF transparent transparent;
        animation: rotation 1.5s linear infinite;
    }

    @keyframes rotation {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes rotationBack {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(-360deg);
        }
    }
</style>
