<?php

namespace App\Exports;

use App\Models\Cinema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;    // unutk menampilkan th di excel
use Maatwebsite\Excel\Concerns\WithMapping;     // untuk menampilkan td di excel
// proses manipulasi tanggal dan waktu
use Carbon\Carbon;

class CinemaExport implements FromCollection, WithHeadings, WithMapping
{
    private $key = 0;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // menentukan data yg akan dimunculkan di excel
        return Cinema::orderBy('created_at', 'DESC')->get();
    }

    // menentukan th
    public function headings(): array
    {
        return ['No', 'Nama', 'Lokasi',];
    }

    // menentukan td
    public function map($cinema):array
    {
        return [
            ++$this->key,
            $cinema->name,
            $cinema->location,
        ];
    }
}
