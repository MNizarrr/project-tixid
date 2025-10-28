<?php

namespace App\Http\Controllers;

use App\Exports\CinemaExport;
use App\Models\Cinema;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
}
