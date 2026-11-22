<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePenggunaRequest;
use App\Http\Resources\PenggunaResource;
use App\Models\Pengguna;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $perPage = min(max((int) $request->query('per_page', 10), 1), 100);

        $pengguna = Pengguna::query()
            ->withCount('koleksi')
            ->orderBy('nama')
            ->paginate($perPage)
            ->withQueryString();

        return PenggunaResource::collection($pengguna)->additional([
            'message' => 'Daftar pengguna FlipPlay berhasil diambil.',
        ]);
    }

    public function store(StorePenggunaRequest $request)
    {
        $data = $request->validated();
        $data['email'] = strtolower($data['email']);

        $pengguna = Pengguna::create($data);
        $pengguna->loadCount('koleksi');

        return (new PenggunaResource($pengguna))
            ->additional(['message' => 'Pengguna berhasil ditambahkan.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Pengguna $pengguna)
    {
        $pengguna->load('koleksi')->loadCount('koleksi');

        return (new PenggunaResource($pengguna))->additional([
            'message' => 'Detail pengguna berhasil diambil.',
        ]);
    }
}
