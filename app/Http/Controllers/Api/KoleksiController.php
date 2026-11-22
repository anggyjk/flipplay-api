<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PenggunaResource;
use App\Models\Media;
use App\Models\Pengguna;

class KoleksiController extends Controller
{
    public function index(Pengguna $pengguna)
    {
        $pengguna->load('koleksi')->loadCount('koleksi');

        return (new PenggunaResource($pengguna))->additional([
            'message' => 'Koleksi media pengguna berhasil diambil.',
        ]);
    }

    public function store(Pengguna $pengguna, Media $media)
    {
        if ($pengguna->koleksi()->where('media_id', $media->getKey())->exists()) {
            return response()->json([
                'message' => 'Media sudah ada di koleksi pengguna.',
                'errors' => [
                    'media' => ['Media yang sama tidak boleh ditambahkan dua kali ke koleksi pengguna yang sama.'],
                ],
            ], 409);
        }

        $pengguna->koleksi()->attach($media->getKey());
        $pengguna->load('koleksi')->loadCount('koleksi');

        return (new PenggunaResource($pengguna))
            ->additional(['message' => 'Media berhasil ditambahkan ke koleksi pengguna.'])
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(Pengguna $pengguna, Media $media)
    {
        if (! $pengguna->koleksi()->where('media_id', $media->getKey())->exists()) {
            return response()->json([
                'message' => 'Media tidak ditemukan di koleksi pengguna.',
            ], 404);
        }

        $pengguna->koleksi()->detach($media->getKey());
        $pengguna->load('koleksi')->loadCount('koleksi');

        return (new PenggunaResource($pengguna))->additional([
            'message' => 'Media berhasil dihapus dari koleksi pengguna.',
        ]);
    }
}
