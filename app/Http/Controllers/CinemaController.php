<?php

namespace App\Http\Controllers;

use App\Exports\CinemaExport;
use App\Models\Cinema;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class CinemaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //model::all() -> mengambil semua data di model
        $cinemas = Cinema::all();
        //compact() -> mengirim data ke blade, nama compact sama dengan nama variable
        return view('admin.cinema.index', compact('cinemas'));
    }

    public function datatables()
    {
        $cinemas = Cinema::query();
        return DataTables::of($cinemas)
            ->addIndexColumn()
            ->addColumn('action', function ($cinema) {
                $btnEdit = '<a href="' . route('admin.cinemas.edit', $cinema->id) . '" class="btn btn-primary">Edit</i></a>';
                $btnDelete = '
            <form action="' . route('admin.cinemas.delete', $cinema->id) . '" method="POST">
            ' . csrf_field() . method_field('DELETE') . '
                <button class="btn btn-danger">Hapus</button>
            </form>';

                return '<div class="d-flex justify-content-center align-items-center gap-2">' . $btnEdit . $btnDelete . '</div>';
            })
            ->rawColumns(['name', 'location', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.cinema.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'location' => 'required|min:10',
        ], [
            'name.required' => 'Nama biokop harus di isi',
            'location.required' => 'Lokasi harus di isi',
            'location.min' => 'Lokasi harus di isi setidaknya 10 karakter'
        ]);

        $createData = Cinema::create([
            'name' => $request->name,
            'location' => $request->location,
        ]);

        if ($createData) {
            return redirect()->route('admin.cinemas.index')->with('success', 'Berhasil membuat data baru!');
        } else {
            return redirect()->back()->with('error', 'Gagal, silakan coba lagi');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Cinema $cinema)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //Cinema::find() => mencari data di tabel cinema berdasarkan id
        $cinema = Cinema::find($id);
        return view('admin.cinema.edit', compact('cinema'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'location' => 'required|min:10',
        ], [
            'name.required' => 'Nama bioskop wajib di isi',
            'location.required' => 'Lokasi bioskop harus di isi',
            'location.min' => 'Lokasi bioskop harus di isi minimal 10 karakter'
        ]);

        $updateData = Cinema::where('id', $id)->update([
            'name' => $request->name,
            'location' => $request->location,
        ]);

        if ($updateData) {
            return redirect()->route('admin.cinema.index')->with('success', 'Berhasil mengubah data');
        } else {
            return redirect()->back()->with('error', 'Gagal! silahkan coba lagi');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $schedules = Schedule::where('cinema_id', $id)->count();
        if ($schedules) {
            return redirect()->route('admin.cinemas.index')->with('error', 'Tidak dapat menghapus data bioskop! Data tertaut dengan jadwal tayang');
        }

        Cinema::where('id', $id)->delete();
        return redirect()->route('admin.cinema.index')->with('success', 'Berhasil menghapus data!');
    }

    public function export()
    {
        // name file yg akan di download
        // ekstensi antara xlsx/csv
        $fileName = "data-bioskop.xlsx";
        // proses download
        return Excel::download(new CinemaExport, $fileName);
    }

    public function trash()
    {
        // onlytrashed() -> filter data yang sudah di hapus, delete_at BUKAN NULL
        $cinemaTrash = Cinema::onlyTrashed()->get();
        return view('admin.cinema.trash', compact('cinemaTrash'));
    }
    public function restore($id)
    {
        // restore()-> mengembalikan data yang sudah di hapus (menghapus nilai tanggal pada delete_at)
        $cinema = Cinema::onlyTrashed()->find($id);
        $cinema->restore();
        return redirect()->route('admin.cinemas.index')->with('success', 'Berhasil mengambil data!');
    }

    public function deletePermanent($id)
    {
        $cinema = Cinema::onlyTrashed()->find($id);
        $cinema->forceDelete();
        return redirect()->back()->with('success', 'Berhasil menghapus seutuhnya!');
    }

    public function cinemaList()
    {
        $cinemas = Cinema::all();
        return view('schedule.cinemas', compact('cinemas'));
    }

    public function cinemaSchedules($cinema_id)
    {
        $schedules = Schedule::where('cinema_id', $cinema_id)->with('movie')->whereHas('movie', function ($q) {
            $q->where('activated', 1);
        })->get();
        return view('schedule.cinema-schedules', compact('schedules'));
    }
}
