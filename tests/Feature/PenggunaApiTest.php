<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenggunaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_daftar_pengguna_bisa_diambil(): void
    {
        $pengguna = Pengguna::factory()->create(['nama' => 'Andi Pratama']);
        $media = Media::factory()->create();
        $pengguna->koleksi()->attach($media);

        $response = $this->getJson('/api/pengguna');

        $response->assertOk()
            ->assertJsonPath('message', 'Daftar pengguna FlipPlay berhasil diambil.')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.nama', 'Andi Pratama')
            ->assertJsonPath('data.0.jumlah_koleksi', 1);
    }

    public function test_pengguna_baru_bisa_ditambahkan_dan_email_disimpan_huruf_kecil(): void
    {
        $response = $this->postJson('/api/pengguna', [
            'nama' => 'Andi Pratama',
            'email' => 'Andi@Example.COM',
        ]);

        $response->assertCreated()
            ->assertJsonPath('message', 'Pengguna berhasil ditambahkan.')
            ->assertJsonPath('data.email', 'andi@example.com');

        $this->assertDatabaseHas('pengguna', ['email' => 'andi@example.com']);
    }

    public function test_email_pengguna_tidak_boleh_duplikat(): void
    {
        Pengguna::factory()->create(['email' => 'andi@example.com']);

        $response = $this->postJson('/api/pengguna', [
            'nama' => 'Andi Lain',
            'email' => 'ANDI@example.com',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('email')
            ->assertJsonPath('errors.email.0', 'Email sudah terdaftar.');
    }

    public function test_detail_pengguna_menampilkan_koleksi_media(): void
    {
        $pengguna = Pengguna::factory()->create();
        $media = Media::factory()->create(['judul' => 'Inception']);
        $pengguna->koleksi()->attach($media);

        $response = $this->getJson("/api/pengguna/{$pengguna->id}");

        $response->assertOk()
            ->assertJsonPath('message', 'Detail pengguna berhasil diambil.')
            ->assertJsonPath('data.jumlah_koleksi', 1)
            ->assertJsonPath('data.koleksi.0.judul', 'Inception')
            ->assertJsonStructure([
                'data' => [
                    'koleksi' => [
                        ['id', 'judul', 'ditambahkan_pada'],
                    ],
                ],
            ]);
    }
}
