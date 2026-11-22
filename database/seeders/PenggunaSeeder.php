<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Pengguna;
use Illuminate\Database\Seeder;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        $andi = Pengguna::updateOrCreate(
            ['email' => 'andi@flipplay.test'],
            ['nama' => 'Andi Pratama']
        );

        $siti = Pengguna::updateOrCreate(
            ['email' => 'siti@flipplay.test'],
            ['nama' => 'Siti Rahma']
        );

        $budi = Pengguna::updateOrCreate(
            ['email' => 'budi@flipplay.test'],
            ['nama' => 'Budi Santoso']
        );

        $andi->koleksi()->syncWithoutDetaching(
            Media::whereIn('judul', ['Inception', 'Interstellar', 'Thriller'])->pluck('id')
        );

        $siti->koleksi()->syncWithoutDetaching(
            Media::whereIn('judul', ['Stranger Things', 'Bumi Manusia'])->pluck('id')
        );

        $budi->koleksi()->syncWithoutDetaching(
            Media::whereIn('judul', ['The Dark Knight', 'Breaking Bad', 'Random Access Memories'])->pluck('id')
        );
    }
}
