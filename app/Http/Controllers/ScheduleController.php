<?php

namespace App\Http\Controllers;

use App\Exports\ScheduleExport;
use App\Models\Schedule;
use App\Models\Cinema;
use App\Models\Movie;
use Illuminate\Http\Request;
use App\Exports\UserExport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

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

public function datatables()
{
    $schedules = Schedule::query()->with(['cinema', 'movie'])->get();
    return DataTables::of($schedules)
    ->addIndexColumn()
    ->addColumn('cinema_name', function($schedule){
        return $schedule->cinema->name;
    })
    ->addColumn('movie_title', function($schedule){
        return $schedule->movie->title;
    })
    ->addColumn('price', function($schedule){
        return 'Rp. ' . number_format($schedule->price, 0, ',', '.');
    })
    ->addColumn('hours', function($schedule){
        if(is_array($schedule->hours)) {
            $list = '<ul>';
            foreach ($schedule->hours as $hours) {
                $list .='<li>' . $hours . '<li>';
            }
            $list .='</ul>';
            return $list;
        }
    })
    ->addColumn('action', function ($schedule) {
            $btnEdit = '<a href="' . route('staff.schedules.edit', $schedule->id) . '" class="btn btn-primary">Edit</a>';
            $btnDelete = '
            <form action="' . route('staff.schedules.delete', $schedule->id) . '" method="POST"> ' .
                @csrf_field() .
                @method_field('DELETE') . '
                <button type="submit" class="btn btn-danger ms-2">Hapus</button>
            </form>';

            return '<div class="d-flex justify-content-center align-items-center gap-2">' . $btnEdit . $btnDelete . '</div>';
        })
        ->rawColumns(['cinema_name', 'movie_title','price', 'hours', 'action'])
        ->make(true);
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
        ], [
            'price' => $request->price,
            // jam penggabungan sebelum dan baru dari proses di atas
            'hours' => $newHours,
        ]);

        if ($createData) {
            return redirect()->route('staff.schedules.index')->with('success', 'Berhasil Menambahkan Data');
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
    public function edit(Schedule $schedule, $id)
    {
        $schedule = Schedule::where('id', $id)->with(['cinema', 'movie'])->first();
        return view('staff.schedule.edit', compact('schedule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule, $id)
    {
        $request->validate([
            'price' => 'required|numeric',
            'hours.*' => 'required|date_format:H:i'
        ], [
            'price.required' => 'Harga harus di isi',
            'price.numeric' => 'harga harus di isi dengan angka',
            'hours.*.required' => 'Jam tayang harus di isi',
            'hours.*.date_format' => 'Jam tayang harus di isi dengan jam:menit'
        ]);

        $updateData = Schedule::where('id', $id)->update([
            'price' => $request->price,
            'hours' => $request->hours
        ]);

        if ($updateData) {
            return redirect()->route('staff.schedules.index')->with('success', 'Berhasil mengubah data!');
        } else {
            return redirect()->back()->with('error', 'Gagal! coba lagi');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule, $id)
    {
        Schedule::where('id', $id)->delete();
        return redirect()->route('staff.schedules.index')->with('success', 'Berhasil menghapus data');
    }

    public function export()
    {
        $fileName = "data-jadwalTiket.xlsx";
        return Excel::download(new ScheduleExport, $fileName);
    }

    public function trash()
    {
        // onlytrashed() -> filter data yang sudah di hapus, delete_at BUKAN NULL
        $scheduleTrash = Schedule::with(['cinema', 'movie'])->onlyTrashed()->get();
        return view('staff.schedule.trash', compact('scheduleTrash'));
    }

    public function restore($id)
    {
        // restore()-> mengembalikan data yang sudah di hapus (menghapus nilai tanggal pada delete_at)
        $schedule = Schedule::onlyTrashed()->find($id);
        $schedule->restore();
        return redirect()->route('staff.schedules.index')->with('success', 'Berhasil mengambil data!');
    }

    public function deletePermanent($id)
    {
        $schedule = Schedule::onlyTrashed()->find($id);
        $schedule->forceDelete();
        return redirect()->back()->with('success', 'Berhasil menghapus seutuhnya!');
    }
}
