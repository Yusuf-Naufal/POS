<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Seeder untuk role "User"
        DB::table('users')->insert([
            'nama' => 'Regular User',
            'username' => 'user',
            'email' => 'user@gmail.com',
            'password' => Hash::make('password123'),
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'no_telp' => '081234567890',
            'alamat' => 'Jl. User No. 1',
            'role' => 'User',
            'status' => 'Aktif',
            'catatan' => 'Karyawan',
        ]);

        // Seeder untuk role "Admin"
        DB::table('users')->insert([
            'nama' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'tanggal_lahir' => '1985-05-15',
            'jenis_kelamin' => 'L',
            'no_telp' => '081234567891',
            'alamat' => 'Jl. Admin No. 1',
            'role' => 'Admin',
            'status' => 'Aktif',
            'catatan' => 'Administrator sistem',
        ]);

        // Seeder untuk role "Master"
        DB::table('users')->insert([
            'nama' => 'Master User',
            'username' => 'master',
            'email' => 'master@gmail.com',
            'password' => Hash::make('master123'),
            'tanggal_lahir' => '1980-12-31',
            'jenis_kelamin' => 'P',
            'no_telp' => '081234567892',
            'alamat' => 'Jl. Master No. 1',
            'role' => 'Master',
            'status' => 'Aktif',
            'catatan' => 'Pemilik',
        ]);
    }
}
