<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Pengguna::firstOrNew(['username' => 'sawung@gmail.com']);

        $admin->forceFill([
            'nama_pengguna' => 'Administrator',
            'no_telpon'     => '081234567890',
            'alamat'        => 'NAT Laundry',
            'password'      => 'Seta120406',
            'role'          => 'admin',
        ])->save();
    }
}