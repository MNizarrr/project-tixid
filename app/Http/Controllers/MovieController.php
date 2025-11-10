<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use League\CommonMark\Extension\DescriptionList\Node\Description;
use Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MovieExport;
use Yajra\DataTables\Facades\DataTables;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies = Movie::all();
        return view('admin.movie.index', compact('movies'));
    }

    public function datatables()
    {
        $movies = Movie::query();
        // datatables::of mengambil data dari query model movie, keseluruhan field
        return DataTables::of($movies)
        ->addIndexColumn()
        ->addColumn('poster_img', function ($movie) {
            $url = asset('storage/' . $movie->poster);
            return '<img src="' . $url .'" width="70">';
        })
        ->addColumn('activated_badge', function($movie) {
            if($movie->activated){
                return '<span class="badge badge-success">Aktif</span>';
            } else {
                return '<span class="badge badge-danger">Non-Aktif</span>';
            }
        })
        ->addColumn('action', function ($movie) {
            $btnDetail = '<button class="btn btn-secondary me-2" onclick=\'showModal(' . json_encode($movie) . ')\'>Detail</button>';
            $btnEdit = '<a href="' . route('admin.movies.edit', $movie->id) . '" class="btn btn-primary me-2">Edit</a>';
            $btnDelete = '
            <form action="' . route('admin.movies.delete', $movie->id) . '" method="POST">
                ' . csrf_field() . method_field('DELETE') . '
                <button class="btn btn-danger me-2">Hapus</button>
            </form>';
            $btnNonAktif = '';
            if($movie->activated) {
                $btnNonAktif = '<form action="' . route('admin.movies.nonactive', $movie->id) . '" method="POST" class="me-2">' . csrf_field() . method_field('PUT') . '<button type="submit" class="btn btn-warning">Non-Aktif</button></form>';
            }
            return '<div class="d-flex justify-content-center align-items-center gap-2">' . $btnDetail . $btnEdit . $btnDelete . $btnNonAktif . '</div>';
        })
        ->rawColumns(['poster_img', 'activated_badge', 'action'])
        ->make(true);
    }

    public function home()
    {
        // where ('field', 'operator', 'value') : mencari data
        // operator : = / < / <= / > / >= / <> / !=
        // orderBy('field', 'ASC/DESC') : : mengurutkan data
        // ASC : a-z, 0-9 terlama-terbaru, DESC : 9-0, z-a, tebaru-terlama
        // limit(angka) : mengambil hanya beberapa data
        // get() : ambil hasil proses filter
        $movies = Movie::where('activated', 1)->orderBy('created_at', 'DESC')->limit(3)->get();
        return view('home', compact('movies'));
    }

    public function homeMovies(Request $request)
    {
        // ambil $request input search
        $nameMovie = $request->search_movie;
        // cek jika input name="search_movie" tidak kosong
        if($nameMovie != ""){
            // LIKE : mencari kata yang mengandung teks tertentu
            // % didepan : mencari kata belakang, % di belakang : mencari data di depan, % depan belakang : mencari di depan tengah belakang
            $movies = Movie::where('title', 'LIKE', '%' . $nameMovie . '%')->where('activated', 1)->orderBy('created_at', 'DESC')->get();
        }else{
            $movies = Movie::where('activated', 1)->orderBy('created_at', 'DESC')->get();
        }
        $movies = Movie::where('activated', 1)->orderBy('created_at', 'DESC')->get();
        return view('movies', compact('movies'));
    }

    public function movieSchedule($movie_id, Request $request)
    {
        $sortPrice = $request->sort_price ?: 'ASC';

        if ($sortPrice){
             $movie = Movie::where('id', $movie_id)->with(['schedules' => function($q)
                use($sortPrice){
                    // karna mau ngurutkan berdasar price di table schedules. schedule itu ada di relasi jd gunakan fungsi anonim
                    // $q : query eloquent, mewakili model relasi (model schedule)
                $q->orderBy('price', $sortPrice);
             }, 'schedules.cinema'])->first();
        }else{
            $movie = Movie::where('id', $movie_id)->with(['schedules', 'schedules.cinema'])->first();
        }

        $sortAlfabet = $request->sort_alfabet;
        if ($sortAlfabet == 'ASC') {
            $movie->schedules = $movie->schedules->sortBy(function($schedule){
                return $schedule->cinema->name;
            })->values();
        }elseif($sortAlfabet == 'DESC') {
            $movie->schedules = $movie->schedules->sortByDesc(function($schedule){
                return $schedule->cinema->name;
            })->values();
        }
        // ambil data movie bersama schedule  dan cinema
        // karn cinema adanya relasi dengan schedule bukan movie, jd gunakan schedule.cinema
        // schedules : mengambil relasi schedules
        // schedules.cinema : ambil relasi cinema dari schedule
        // first() : karna mau ambil 1 film
        return view('auth.schedule.detail', compact('movie', 'sortPrice'));
    }

    public function nonActive($id)
    {
        $movies = Movie::findOrFail($id);
        $nonAktifData = $movies->update(['activated' => 0]);
        if ($nonAktifData) {
            return redirect()->route('admin.movies.index')->with('success', 'Berhasil menambahkan data!');
        } else {
            return redirect()->back()->with('error', 'Gagal! Silahkan voba lagi.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.movie.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => 'required',
            'duration' => 'required',
            'genre' => 'required',
            'director' => 'required',
            'age_rating' => 'required',
            //mimes : Memastikan ekstensi (jenis file) yg di upload
            'poster' => 'required|mimes:jpg,jpeg,png,svg,webp',
            'description' => 'required|min:10',
        ], [
            'title.required' => 'Judul film harus di isi',
            'duration.required' => 'Durasi film harus di isi',
            'genre.required' => 'Genre film harus di isi',
            'director.required' => 'Sutradara film harus di isi',
            'age_rating.required' => 'Usia minimal haus di isi',
            'poster.required' => 'Poster film harus di isi',
            'poster.mimes' => 'Poster harus berupa jpg/jpeg/png/svg/webp',
            'description.required' => 'Sinopsis film harus di isi',
            'description.min' => 'Sinopsi harus di isi minimal 10 karakter'
        ]);
        //ambil file nya
        $poster = $request->file('poster');
        // baut nama nari untuk file nya
        // formas file baru yang di harapkan acak : <acak>-poster.jpg
        // getClientOriginalExtension() : mengambil ekstensi file yang di upload
        $namaFile = Str::random(10) . "-poster." . $poster->getClientOriginalExtension();
        //simpan file ke folder storage : store AS("namasubfolder", namafile, "visibility")
        $path = $poster->storeAs("poster", $namaFile, "public");
        $createData = Movie::create([
            'title' => $request->title,
            'duration' => $request->duration,
            'genre' => $request->genre,
            'director' => $request->director,
            'age_rating' => $request->age_rating,
            //poster di isi dengan hasil storeAS hasil penyimpanan file di storage sebelumnya
            'poster' => $path,
            'description' => $request->description,
            'activated' => 1
        ]);
        if ($createData) {
            return redirect()->route('admin.movies.index')->with('success', 'Berhasil menambahkan data!');
        } else {
            return redirect()->back()->with('error', 'Gagal! Silahkan voba lagi.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Movie $movie, $id)
    {
        $movie = Movie::find($id);
        return view('admin.movie.edit', compact('movie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movie $movie, $id)
    {
        $request->validate([
            'title' => 'required',
            'duration' => 'required',
            'genre' => 'required',
            'director' => 'required',
            'age_rating' => 'required',
            'description' => 'required|min:10',
            'poster' => 'mimes:jpg,jpeg,png,svg,webp',
        ], [
            'title.required' => 'Judul film harus di isi',
            'duration.required' => 'Durasi film harus di isi',
            'genre.required' => 'Genre film harus di isi',
            'director.required' => 'Sutradara film harus di isi',
            'age_rating.required' => 'Usia minimal haus di isi',
            'poster.mimes' => 'Poster harus berupa jpg/jpeg/png/svg/webp',
            'description.required' => 'Sinopsis film harus di isi',
            'description.min' => 'Sinopsi harus di isi minimal 10 karakter'
        ]);
        //ambil data sebelummya
        $movie = Movie::find($id);

        //jika input file poster di isi
        if ($request->hasFile('poster')) {
            $filePath = storage_path('app.public/' . $movie->poster);
            //jika file ada di storage path
            if (file_exists('poster')) {
                //hapus file lama
                unlink($filePath);
            }
            $file = $request->file('poster');
            $fileName = 'poster-' . Str::random(10) . '.' .
                $file->getClientOriginalExtension();
            $path = $file->storeAs('poster', $fileName, 'public');
        }

        $updateData = $movie->update([
            'title' => $request->title,
            'duration' => $request->duration,
            'genre' => $request->genre,
            'director' => $request->director,
            'age_rating' => $request->age_rating,
            'poster' => $request->hasFile('poster') ? $path : $movie->poster,
            'description' => $request->description,
            'activated' => 1
        ]);

        if ($updateData) {
            return redirect()->route('admin.movies.index')->with('success', 'Berhasil memperbarui data!');
        } else {
            return redirect()->back()->with('error', 'Gagal, silahkan coba lagi.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $movie = Movie::findOrFail($id);

        // Hapus sementara (soft delete), file tidak dihapus
        $movie->delete();

        return redirect()->route('admin.movies.index')->with('success', 'Film dihapus sementara!');
    }


    public function export()
    {
        // name file yg akan di download
        // ekstensi antara xlsx/csv
        $fileName = "data-film.xlsx";
        // proses download
        return Excel::download(new MovieExport(), $fileName);
    }

    public function trash()
    {
        // onlytrashed() -> filter data yang sudah di hapus, delete_at BUKAN NULL
        $movieTrash = Movie::onlyTrashed()->get();
        return view('admin.movie.trash', compact('movieTrash'));
    }

    public function restore($id)
    {
        // restore()-> mengembalikan data yang sudah di hapus (menghapus nilai tanggal pada delete_at)
        $movie = Movie::onlyTrashed()->find($id);
        $movie->restore();
        return redirect()->route('admin.movies.index')->with('success', 'Berhasil mengambil data!');
    }

    public function deletePermanent($id)
    {
        $movie = Movie::onlyTrashed()->findOrFail($id);

        // Hapus file poster jika ada
        if ($movie->poster && file_exists(storage_path('app/public/' . $movie->poster))) {
            unlink(storage_path('app/public/' . $movie->poster));
        }

        $movie->forceDelete();

        return redirect()->back()->with('success', 'Film dihapus permanen beserta posternya!');
    }

}
