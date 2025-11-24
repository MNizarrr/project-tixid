<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Promo;
use App\Models\TicketPayment;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // ticket aktif : sudah di bayar dan berlaku di hari ini atau besok
        $ticketActive = Ticket::whereHas('ticketPayment', function($q) {
            $date = now()->format('Y-m-d');
            $q->whereDate('paid_date', '>=', $date);
        })->where('user_id', Auth::user()->id)->get();
        // ticket tidak aktif : Sudah di bayar dan berlaku di hari kemarin (terlewat)
        // tiket sesuai dengan akun yang login
        $ticketNonActive = Ticket::whereHas('ticketPayment', function($q) {
            $date = now()->format('Y-m-d');
            $q->whereDate('paid_date', '<', $date);
        })->where('user_id', Auth::user()->id)->get();
        return view('ticket.index', compact('ticketActive', 'ticketNonActive'));
    }

    public function chart() {
        // ambil data tanggal untul sumbu x dan y jumlah tiket untuk sumbu y
        $tickets = Ticket::whereHas('ticketPayment', function($q) {
            // ambil yg paid_date naya udah bukan (<>) null (udah di bayar)
            $q->where('paid_date', '<>', NULL);
        })->get()->groupBy(function($ticket) {
            // groupBy : mengelompokan data tiket berdasarkan tgl pembayran, untuk dihitung jumlah tiket di tiap tgl nya
            return \Carbon\Carbon::parse($ticket->ticketPayment->paid_date)->format('Y-m-d');
        })->toArray(); //data di sajikan dalam bentuk array agar bisa menggunakan fungsi-fungsi array
        $labels = array_keys($tickets); //array_keys() : ambil index array
        $data = [];
        // sumbu y mengambil jumlah value bukan isi value, jd gunakan count() untuk ambil jumlah valuenya
        foreach ($tickets as $value) {
            // simpan jumlah value ke array diatas
            array_push($data, count($value));
        }
        // dd($tickets);
        // di proses lewat js, jd gunakan response()->json()
        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
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
        if ($request->promo_id != null) {
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

    public function updateStatusPayment(Request $request, $ticketId)
    {
        $updateData = TicketPayment::where('ticket_id', $ticketId)->update([
            'status' => 'paid-off',
            'paid_date' => now()
        ]);
        if ($updateData) {
            Ticket::where('id', $ticketId)->update(['activated' => 1]);
        }
        return redirect()->route('tickets.payment.proof', $ticketId);
    }

    public function proofPayment($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['schedule', 'schedule.cinema', 'schedule.movie', 'promo', 'ticketPayment'])->first();

        return view('schedule.proof-payment', compact('ticket'));
    }

    public function exportPdf($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['schedule', 'schedule.cinema', 'schedule.movie', 'promo', 'ticketPayment'])->first()->toArray();

        view()->share('ticket', $ticket);

        $pdf = Pdf::loadView('schedule.export-pdf', $ticket);
        $fileName = 'TICKET' . $ticket['id'] . '.pdf';
        return $pdf->download($fileName);
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
