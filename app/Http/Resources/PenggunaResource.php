<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PenggunaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'email' => $this->email,
            'jumlah_koleksi' => $this->whenCounted('koleksi'),
            'koleksi' => MediaResource::collection($this->whenLoaded('koleksi')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
