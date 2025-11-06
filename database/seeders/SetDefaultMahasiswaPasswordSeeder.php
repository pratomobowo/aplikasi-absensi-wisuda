<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SetDefaultMahasiswaPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set password sama dengan NPM untuk semua mahasiswa yang belum punya password
        Mahasiswa::whereNull('password')
            ->orWhere('password', '')
            ->chunk(100, function ($mahasiswas) {
                foreach ($mahasiswas as $mahasiswa) {
                    $mahasiswa->update([
                        'password' => Hash::make($mahasiswa->npm)
                    ]);
                }
            });

        $this->command->info('Default passwords set for all mahasiswa (password = NPM)');
    }
}
