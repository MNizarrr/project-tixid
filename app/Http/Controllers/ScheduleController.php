<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Cinema;
use App\Models\Movie;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cinemas = Cinema::all();
        $movies = Movie::all();

        //with() : memanggil detail relasi, tidak hanya angka id nya
        //isi with() : dari function relasi di model
        $schedules = Schedule::with(['cinema', 'movie'])->get();
        return view('staff.schedule.index', compact('cinemas', 'movies', 'schedules'));
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
            'cinema_id' => 'required',
            'movie_id' => 'required',
            'price' => 'required|numeric',
            //karna hours arry, yg validasi is itemnya menggunakan (.*)
            // date_format : bentuk item arraynya berupa format waktu H:1
            'hours.*' => 'required|date_format:H:i',
        ], [
            'cinema_id.required' => 'Bioskop harus dipilih',
            'movie_id.required' => 'Film harus dipilih',
            'price.required' => 'Harga harus diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'hours.*.required' => 'Jadwal tayang diisi minimal 1 data',
            'hours.*.date_format' => 'Format jam diisi dengan jam:menit',
        ]);

        // cek apakah data bioskop dan film yg di pilih sudah ada, klo ada ambil jamnya
        $hours = Schedule::where('cinema_id', $request->cinema_id)->where('movie_id', $request->movie_id)->value('hours');
        //value ('hours') : dri schedule cmn ambil bagian hours
        // jika blm ada data bioskop dan film, hours akan null ubah menjadi []
        $hoursBefore = $hours ?? [];
        // gabungkan hours sebelumnya dengan hours yg baru akan di tambahkan
        $mergeHours = array_merge($hoursBefore, $request->hours);
        // jika ada jam duplikat, ambil salah satu
        $newHours = array_unique($mergeHours);

        $createData = Schedule::updateOrCreate([
            'cinema_id' => $request->cinema_id,
            'movie_id' => $request->movie_id,
        ],[
            'price' => $request->price,
            // jam penggabungan sebelum dan baru dari proses di atas
            'hours' => $newHours,
        ]);

        if($createData) {
            return  redirect()->route('staff.schedules.index')->with('success', 'Berhasil Menambahkan Data');
        } else {
            return redirect()->back()->with('error', 'Gagal! Coba lagi');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        //
    }
}
