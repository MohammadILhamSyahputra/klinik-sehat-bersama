<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tambah 1 Petugas Administrasi
        User::updateOrCreate(
            ['email' => 'petugas2@klinik.com'],
            [
                'name' => 'Andi Pratama (Petugas Admin)',
                'password' => Hash::make('password'),
                'peran' => User::PERAN_PETUGAS,
                'no_telp' => '081234567891',
            ]
        );

        // Tambah 2 Kasir
        User::updateOrCreate(
            ['email' => 'kasir2@klinik.com'],
            [
                'name' => 'Dewi Lestari (Kasir)',
                'password' => Hash::make('password'),
                'peran' => User::PERAN_KASIR,
                'no_telp' => '081234567892',
            ]
        );

        User::updateOrCreate(
            ['email' => 'kasir3@klinik.com'],
            [
                'name' => 'Rudi Hartono (Kasir)',
                'password' => Hash::make('password'),
                'peran' => User::PERAN_KASIR,
                'no_telp' => '081234567893',
            ]
        );
    }
}

