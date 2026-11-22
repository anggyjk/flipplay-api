<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_daftar_media_bisa_diambil_dan_difilter_berdasarkan_judul(): void
    {
        Media::factory()->create(['judul' => 'Inception']);
        Media::factory()->create(['judul' => 'Interstellar']);

        $response = $this->getJson('/api/media?search=incep');

        $response->assertOk()
            ->assertJsonPath('message', 'Daftar media FlipPlay berhasil diambil.')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.judul', 'Inception')
            ->assertJsonStructure([
                'message',
                'data' => [
                    ['id', 'judul', 'jenis', 'genre', 'tahun_rilis', 'deskripsi', 'rating', 'jumlah_di_koleksi', 'created_at', 'updated_at'],
                ],
                'links',
                'meta',
            ]);
    }

    public function test_media_baru_bisa_ditambahkan(): void
    {
        $payload = [
            'judul' => 'Dune: Part Two',
            'jenis' => 'film',
            'genre' => 'Sci-Fi',
            'tahun_rilis' => 2024,
            'deskripsi' => 'Paul Atreides bersatu dengan Fremen untuk membalas dendam.',
            'rating' => 8.6,
        ];

        $response = $this->postJson('/api/media', $payload);

        $response->assertCreated()
            ->assertJsonPath('message', 'Media berhasil ditambahkan.')
            ->assertJsonPath('data.judul', 'Dune: Part Two')
            ->assertJsonPath('data.jumlah_di_koleksi', 0);

        $this->assertDatabaseHas('media', ['judul' => 'Dune: Part Two']);
    }

    public function test_judul_media_wajib_diisi(): void
    {
        $response = $this->postJson('/api/media', [
            'judul' => '',
            'jenis' => 'film',
            'genre' => 'Action',
            'tahun_rilis' => 2024,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('judul')
            ->assertJsonPath('errors.judul.0', 'Judul media wajib diisi.');
    }

    public function test_judul_media_tidak_boleh_duplikat(): void
    {
        Media::factory()->create(['judul' => 'Inception']);

        $response = $this->postJson('/api/media', [
            'judul' => 'Inception',
            'jenis' => 'film',
            'genre' => 'Sci-Fi',
            'tahun_rilis' => 2010,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('judul')
            ->assertJsonPath('errors.judul.0', 'Judul media sudah terdaftar. Gunakan judul lain.');
    }

    public function test_detail_media_menampilkan_pengguna_yang_mengoleksi(): void
    {
        $media = Media::factory()->create(['judul' => 'Inception']);
        $pengguna = Pengguna::factory()->create(['nama' => 'Andi Pratama']);
        $pengguna->koleksi()->attach($media);

        $response = $this->getJson("/api/media/{$media->id}");

        $response->assertOk()
            ->assertJsonPath('message', 'Detail media berhasil diambil.')
            ->assertJsonPath('data.jumlah_di_koleksi', 1)
            ->assertJsonPath('data.pengguna.0.id', $pengguna->id)
            ->assertJsonPath('data.pengguna.0.nama', 'Andi Pratama');
    }

    public function test_media_bisa_diperbarui(): void
    {
        $media = Media::factory()->create(['judul' => 'Judul Lama']);

        $response = $this->patchJson("/api/media/{$media->id}", [
            'judul' => 'Judul Baru',
            'rating' => 9.5,
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Media berhasil diperbarui.')
            ->assertJsonPath('data.judul', 'Judul Baru')
            ->assertJsonPath('data.rating', 9.5);

        $this->assertDatabaseHas('media', ['id' => $media->id, 'judul' => 'Judul Baru']);
    }

    public function test_update_media_tidak_boleh_memakai_judul_media_lain(): void
    {
        Media::factory()->create(['judul' => 'Inception']);
        $media = Media::factory()->create(['judul' => 'Interstellar']);

        $response = $this->patchJson("/api/media/{$media->id}", ['judul' => 'Inception']);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('judul')
            ->assertJsonPath('errors.judul.0', 'Judul media sudah terdaftar. Gunakan judul lain.');
    }

    public function test_media_bisa_dihapus_beserta_relasi_koleksinya(): void
    {
        $media = Media::factory()->create();
        $pengguna = Pengguna::factory()->create();
        $pengguna->koleksi()->attach($media);

        $response = $this->deleteJson("/api/media/{$media->id}");

        $response->assertOk()->assertJsonPath('message', 'Media berhasil dihapus.');

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        $this->assertDatabaseMissing('koleksi_media', ['media_id' => $media->id]);
    }
}
