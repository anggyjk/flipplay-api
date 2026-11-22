<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $query = Media::query()->withCount('pengguna');

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where('judul', 'like', "%{$search}%");
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->query('jenis'));
        }

        if ($genre = trim((string) $request->query('genre', ''))) {
            $query->where('genre', 'like', "%{$genre}%");
        }

        $sortable = ['judul', 'tahun_rilis', 'rating', 'created_at'];
        $sort = in_array($request->query('sort'), $sortable, true) ? $request->query('sort') : 'created_at';
        $order = strtolower((string) $request->query('order', 'desc')) === 'asc' ? 'asc' : 'desc';

        $perPage = min(max((int) $request->query('per_page', 10), 1), 100);

        $media = $query->orderBy($sort, $order)->paginate($perPage)->withQueryString();

        return MediaResource::collection($media)->additional([
            'message' => 'Daftar media FlipPlay berhasil diambil.',
        ]);
    }

    public function store(StoreMediaRequest $request)
    {
        $media = Media::create($request->validated());
        $media->loadCount('pengguna');

        return (new MediaResource($media))
            ->additional(['message' => 'Media berhasil ditambahkan.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Media $media)
    {
        $media->load('pengguna')->loadCount('pengguna');

        return (new MediaResource($media))->additional([
            'message' => 'Detail media berhasil diambil.',
        ]);
    }

    public function update(UpdateMediaRequest $request, Media $media)
    {
        $media->update($request->validated());
        $media->loadCount('pengguna');

        return (new MediaResource($media))->additional([
            'message' => 'Media berhasil diperbarui.',
        ]);
    }

    public function destroy(Media $media)
    {
        $media->pengguna()->detach();
        $media->delete();

        return response()->json(['message' => 'Media berhasil dihapus.']);
    }
}
