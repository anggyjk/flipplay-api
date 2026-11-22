<?php

namespace Database\Seeders;

use App\Models\Media;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $daftarMedia = [
            [
                'judul' => 'Inception',
                'jenis' => 'film',
                'genre' => 'Sci-Fi',
                'tahun_rilis' => 2010,
                'deskripsi' => 'Pencuri mimpi profesional menjalankan misi menanam ide di alam bawah sadar targetnya.',
                'rating' => 9.0,
            ],
            [
                'judul' => 'Interstellar',
                'jenis' => 'film',
                'genre' => 'Sci-Fi',
                'tahun_rilis' => 2014,
                'deskripsi' => 'Sekelompok astronot menjelajahi lubang cacing demi mencari rumah baru bagi umat manusia.',
                'rating' => 8.7,
            ],
            [
                'judul' => 'The Dark Knight',
                'jenis' => 'film',
                'genre' => 'Action',
                'tahun_rilis' => 2008,
                'deskripsi' => 'Batman menghadapi Joker, musuh yang ingin menjerumuskan Gotham ke dalam kekacauan.',
                'rating' => 9.0,
            ],
            [
                'judul' => 'Thriller',
                'jenis' => 'musik',
                'genre' => 'Pop',
                'tahun_rilis' => 1982,
                'deskripsi' => 'Album ikonis Michael Jackson yang mengubah arah musik pop dunia.',
                'rating' => 9.2,
            ],
            [
                'judul' => 'Random Access Memories',
                'jenis' => 'musik',
                'genre' => 'Electronic',
                'tahun_rilis' => 2013,
                'deskripsi' => 'Album Daft Punk yang memadukan elektronik modern dengan nuansa disko klasik.',
                'rating' => 8.8,
            ],
            [
                'judul' => 'Stranger Things',
                'jenis' => 'serial',
                'genre' => 'Sci-Fi',
                'tahun_rilis' => 2016,
                'deskripsi' => 'Anak-anak di Hawkins menghadapi kekuatan supranatural dari dunia terbalik.',
                'rating' => 8.7,
            ],
            [
                'judul' => 'Breaking Bad',
                'jenis' => 'serial',
                'genre' => 'Crime',
                'tahun_rilis' => 2008,
                'deskripsi' => 'Guru kimia berubah menjadi produsen narkoba demi masa depan keluarganya.',
                'rating' => 9.5,
            ],
            [
                'judul' => 'Bumi Manusia',
                'jenis' => 'buku',
                'genre' => 'Historical Fiction',
                'tahun_rilis' => 1980,
                'deskripsi' => 'Kisah Minke dan Annelies yang berlatar belakang kolonialisme di Hindia Belanda.',
                'rating' => 8.5,
            ],
        ];

        foreach ($daftarMedia as $media) {
            Media::updateOrCreate(['judul' => $media['judul']], $media);
        }
    }
}
