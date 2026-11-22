<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KoleksiApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_media_bisa_ditambahkan_ke_koleksi_pengguna(): void
    {
        $pengguna = Pengguna::factory()->create();
        $media = Media::factory()->create(['judul' => 'Inception']);

        $response = $this->postJson("/api/pengguna/{$pengguna->id}/koleksi/{$media->id}");

        $response->assertCreated()
            ->assertJsonPath('message', 'Media berhasil ditambahkan ke koleksi pengguna.')
            ->assertJsonPath('data.jumlah_koleksi', 1)
            ->assertJsonPath('data.koleksi.0.id', $media->id);

        $this->assertDatabaseHas('koleksi_media', [
            'pengguna_id' => $pengguna->id,
            'media_id' => $media->id,
        ]);
    }

    public function test_media_yang_sama_tidak_boleh_ditambahkan_dua_kali(): void
    {
        $pengguna = Pengguna::factory()->create();
        $media = Media::factory()->create();
        $pengguna->koleksi()->attach($media);

        $response = $this->postJson("/api/pengguna/{$pengguna->id}/koleksi/{$media->id}");

        $response->assertStatus(409)
            ->assertJsonPath('message', 'Media sudah ada di koleksi pengguna.');

        $this->assertDatabaseCount('koleksi_media', 1);
    }

    public function test_koleksi_media_pengguna_bisa_dilihat(): void
    {
        $pengguna = Pengguna::factory()->create();
        $media = Media::factory()->count(2)->create();
        $pengguna->koleksi()->attach($media);

        $response = $this->getJson("/api/pengguna/{$pengguna->id}/koleksi");

        $response->assertOk()
            ->assertJsonPath('message', 'Koleksi media pengguna berhasil diambil.')
            ->assertJsonPath('data.jumlah_koleksi', 2)
            ->assertJsonCount(2, 'data.koleksi');
    }

    public function test_media_bisa_dihapus_dari_koleksi_pengguna(): void
    {
        $pengguna = Pengguna::factory()->create();
        $media = Media::factory()->create();
        $pengguna->koleksi()->attach($media);

        $response = $this->deleteJson("/api/pengguna/{$pengguna->id}/koleksi/{$media->id}");

        $response->assertOk()
            ->assertJsonPath('message', 'Media berhasil dihapus dari koleksi pengguna.')
            ->assertJsonPath('data.jumlah_koleksi', 0);

        $this->assertDatabaseMissing('koleksi_media', [
            'pengguna_id' => $pengguna->id,
            'media_id' => $media->id,
        ]);
    }

    public function test_menghapus_media_yang_tidak_ada_di_koleksi_mengembalikan_404(): void
    {
        $pengguna = Pengguna::factory()->create();
        $media = Media::factory()->create();

        $response = $this->deleteJson("/api/pengguna/{$pengguna->id}/koleksi/{$media->id}");

        $response->assertNotFound()
            ->assertJsonPath('message', 'Media tidak ditemukan di koleksi pengguna.');
    }

    public function test_menambahkan_media_atau_pengguna_yang_tidak_ada_mengembalikan_404(): void
    {
        $pengguna = Pengguna::factory()->create();
        $media = Media::factory()->create();

        $this->postJson("/api/pengguna/999999/koleksi/{$media->id}")->assertNotFound();
        $this->postJson("/api/pengguna/{$pengguna->id}/koleksi/999999")->assertNotFound();
    }
}
