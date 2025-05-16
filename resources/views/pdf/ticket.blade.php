<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>R-Tix Ticket</title>
    <style>
        body {
            font-family: monospace;
            font-size: 12px;
            padding: 0;
            margin: 0;
        }

        .ticket {
            width: 300px;
            border: 1px dashed #000;
            padding: 10px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .label {
            font-size: 10px;
        }

        .value {
            font-weight: bold;
        }

        .row {
            display: flex;
            justify-content: space-between;
        }

        .big-number {
            font-size: 36px;
            font-weight: bold;
            text-align: right;
        }

        .qr {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="ticket">
        <div class="header">{{ $purchase->movie_title }}</div>
        <div class="divider"></div>
        <div class="row">
            <div>
                <div class="label">DATE</div>
                <div class="value">{{ \Carbon\Carbon::parse($purchase->created_at)->format('D, d-M') }}</div>
            </div>
            <div>
                <div class="label">TIME</div>
                <div class="value">{{ $purchase->showtime->start_time ?? '21:40' }}</div>
            </div>
        </div>

        @foreach ($purchase->purchaseItems as $index => $item)
            <div class="divider"></div>
            <div class="row">
                <div>
                    <div class="label">TYPE</div>
                    <div class="value">{{ $item->seat_type }}</div>
                </div>
                <div>
                    <div class="label">SEAT NUMBER</div>
                    <div class="value">{{ $item->seat_number }}</div>
                </div>
                <div class="big-number"> {{ $index + 1 }}</div>
            </div>
            <div class="label">PRICE</div>
            <div class="value">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
        @endforeach

        <div class="divider"></div>
        <div class="label">Total Price</div>
        <div class="value">Rp {{ number_format($purchase->price, 0, ',', '.') }} ( {{ $purchase->status }} )</div>
        <br>
        <div class="label">VOUCHER USED</div>
        <div class="value">{{ $purchase->voucher_code ?? 'None' }}</div>
        <br>
        <div class="divider"></div>
        <div class="qr">
            <img src="{{ $qrBase64 }}" alt="QR Code">
        </div>
    </div>
</body>

</html>
