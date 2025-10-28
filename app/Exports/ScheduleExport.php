<?php

namespace App\Exports;

use App\Models\Schedule;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;    // unutk menampilkan th di excel
use Maatwebsite\Excel\Concerns\WithMapping;     // untuk menampilkan td di excel

class ScheduleExport implements FromCollection, WithHeadings, WithMapping
{
    private $key = 0;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // menentukan data yg akan dimunculkan di excel
        return Schedule::orderBy('created_at', 'DESC')->get();
    }

    // menentukan th
    public function headings(): array
    {
        return ['No', 'Nama bioskop', 'Judul Film', 'Harga', 'Jadwal tayang',];
    }

    // menentukan td
    public function map($schedule): array
    {
        return [
            ++$this->key,
            $schedule->cinema->name,
            $schedule->movie->title,
            $schedule->price
            = 'Rp. ' . number_format($schedule->price, 0, ',', '.'),
            implode(' ', (array) $schedule->hours),
        ];
    }
}
