<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Promo;
use App\Models\TicketPayment;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'schedule_id' => 'required',
            'rows_of_seats' => 'required',
            'quantity' => 'required',
            'total_price' => 'required',
            'tax' => 'required',
            'hour' => 'required',
        ]);
        $createData = Ticket::create([
            'user_id' => $request->user_id,
            'schedule_id' => $request->schedule_id,
            'rows_of_seats' => $request->rows_of_seats,
            'quantity' => $request->quantity,
            'total_price' => $request->total_price,
            'tax' => $request->tax,
            'hour' => $request->hour,
            'date' => now(),
            'activated' => 0,
        ]);
        // karna fungsi ini di jalankan lewat js,
        return response()->json([
            'message' => 'Berhasil membuat data tiket',
            'data' => $createData
        ]);
    }

    public function orderPage($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['schedule', 'schedule.cinema', 'schedule.movie'])->first();
        $promos = Promo::where('activated', 1)->get();
        return view('schedule.order', compact('ticket', 'promos'));
    }

    public function createQrcode(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required'
        ]);

        $ticket = Ticket::find($request['ticket_id']);
        $kodeQr = 'TICKET-' . $ticket['id'];

        // format : svg/png/jpg/jpeg (bentuk gambar qrcode)
        // size : ukuran gambar, margin : margin ke kotak luar qrcode
        // generate : isi qrcode yang akan di buat
        $qrcode = QrCode::format('svg')->size(30)->margin(2)->generate($kodeQr);

        $filename = $kodeQr . '.svg'; // nama file qrcode yg akan di simpan
        $folder = 'qrcode/' . $filename; // lokasi gambar
        // simpan gambar ke storage dengan visibility public. put (lokasi, file)
        Storage::disk('public')->put($folder, $qrcode);

        $createData = TicketPayment::create([
            'ticket_id' => $ticket['id'],
            'qrcode' => $folder, // di db di simpan lokasi gambar qr
            'booked_date' => now(),
            'status' => 'process'
        ]);

        // update promo_id pada tickets jika ada promo yang di pilih (bukan null)
        if($request->promo_id != null) {
            $promo = Promo::find($request->promo_id);
            if ($promo['type'] == 'percent') {
                $discount = $ticket['total_price'] * $promo['discount'] / 100;
            } else {
                $discount = $promo['discount'];
            }
            $totalPriceNew = $ticket['total_price'] - $discount;
            $ticket->update([
                'total_price' => $totalPriceNew,
                'promo_id' => $request->promo_id
        ]);
        }
        return response()->json([
            'message' => 'Berhasil membuat data pembayaran dan update promo tiket!',
            'data' => $ticket
        ]);
    }

    public function paymentPage($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with('ticketPayment')->first();
        // dd(vars: $ticket);
        return view('schedule.payment', compact('ticket'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        //
    }
}
