<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin Besar',
            'email' => 'siadmin@gmail.com',
            'role' => 'admin',
            'password' => Hash::make('adminorangkaya'),
        ]);

        User::create([
            'name' => 'King Kita',
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            'password' => Hash::make('123123qwe'),
        ]);

        User::create([
            'name' => 'Adminn',
            'email' => 'admin1@gmail.com',
            'role' => 'admin',
            'password' => Hash::make('123123123'),
        ]);
    }
}
