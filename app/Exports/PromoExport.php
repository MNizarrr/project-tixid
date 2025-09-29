<?php

namespace App\Exports;

use App\Models\Promo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;    // unutk menampilkan th di excel
use Maatwebsite\Excel\Concerns\WithMapping;     // untuk menampilkan td di excel
// proses manipulasi tanggal dan waktu
use Carbon\Carbon;

class PromoExport implements FromCollection, WithHeadings, WithMapping
{
    private $key = 0;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // menentukan data yg akan dimunculkan di excel
        return Promo::orderBy('created_at', 'DESC')->get();
    }

    // menentukan th
    public function headings(): array
    {
        return ['No', 'Kode Promo', 'Total Potongan',];
    }

    // menentukan td
    public function map($promo): array
    {
        return [
            ++$this->key,
            $promo->promo_code,
            $promo->type === 'percent'
            ? $promo->discount . '%'
            : 'Rp. ' . number_format($promo->discount, 0, ',', '.'),
        ];
    }
}
