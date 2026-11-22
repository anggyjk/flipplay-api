<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'judul' => $this->judul,
            'jenis' => $this->jenis,
            'genre' => $this->genre,
            'tahun_rilis' => $this->tahun_rilis,
            'deskripsi' => $this->deskripsi,
            'rating' => $this->rating,
            'jumlah_di_koleksi' => $this->whenCounted('pengguna'),
            'pengguna' => PenggunaResource::collection($this->whenLoaded('pengguna')),
            'ditambahkan_pada' => $this->whenPivotLoaded('koleksi_media', fn () => $this->pivot->created_at),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
