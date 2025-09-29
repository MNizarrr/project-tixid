<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;    // unutk menampilkan th di excel
use Maatwebsite\Excel\Concerns\WithMapping;     // untuk menampilkan td di excel
// proses manipulasi tanggal dan waktu
use Carbon\Carbon;

class UserExport implements FromCollection, WithHeadings, WithMapping
{
    private $key = 0;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // menentukan data yg akan dimunculkan di excel
        return User::orderBy('created_at', 'DESC')->get();
    }

    // menentukan th
    public function headings(): array
    {
        return ['No', 'Nama', 'Email', 'Role', 'Tanggal Bergabung'];
    }

    // menentukan td
    public function map($user): array
    {
        return [
            ++$this->key,
            $user->name,
            $user->email,
            $user->role,
            Carbon::parse($user->created_at)->format('d-m-Y'), // format tanggal
        ];
    }
}
