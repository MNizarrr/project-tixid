<?php

namespace App\Exports;

use App\Models\Movie;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;    // unutk menampilkan th di excel
use Maatwebsite\Excel\Concerns\WithMapping;     // untuk menampilkan td di excel
// proses manipulasi tanggal dan waktu
use Carbon\Carbon;

class MovieExport implements FromCollection, WithHeadings, WithMapping
{
    private $key = 0;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // menentukan data yg akan dimunculkan di excel
        return Movie::orderBy('created_at', 'DESC')->get();
    }

    // menentukan th
    public function headings(): array
    {
        return ['No', 'Judul', 'Durasi', 'Genre', 'Sutradara', 'Usia Minimal', 'Poster', 'Sinopsis', 'Status Aktif'];
    }

    // menentukan td
    public function map($movie):array
    {
        return [
            ++$this->key,
            $movie->title,
            Carbon::parse($movie->duration)->format('H') . "Jam" . Carbon::parse($movie->duration)->format("i") . "Menit",
            $movie->genre,
            $movie->director,
            $movie->age_rating . "+",
            // poster berupa url -> aset()
            asset("storage") . "/" . $movie->poster,
            $movie->description,
            // jika activated 1 munculkan 'aktif', tidak muncul 'non-aktif'
            $movie->activated == 1 ? 'Aktif' : 'Non-Aktif'
        ];
    }
}
