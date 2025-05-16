<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PDF;

class TicketController extends Controller
{
    public function download($confirmation_token)
    {
        $purchase = Purchase::with('purchaseItems')->where('confirmation_token', $confirmation_token)->firstOrFail();

        $url = env('URL') . 'ticket/paid/' . $purchase->confirmation_token;
        $qrSvg = QrCode::format('svg')->size(150)->generate($url);
        $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);

        $pdf = Pdf::loadView('pdf.ticket', compact('purchase', 'qrBase64'))->setPaper([0, 0, 300, 500]);;

        return $pdf->download('R-tix.pdf');
    }

    public function paid($confirmation_token)
    {
        $purchase = Purchase::with('purchaseItems')->where('confirmation_token', $confirmation_token)->firstOrFail();
        return view('pdf.ticketWeb', compact('purchase'));
    }
}
