<?php

namespace App\Models;

use Database\Factories\MediaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model
{
    /** @use HasFactory<MediaFactory> */
    use HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'judul',
        'jenis',
        'genre',
        'tahun_rilis',
        'deskripsi',
        'rating',
    ];

    protected function casts(): array
    {
        return [
            'tahun_rilis' => 'integer',
            'rating' => 'float',
        ];
    }

    public function pengguna(): BelongsToMany
    {
        return $this->belongsToMany(Pengguna::class, 'koleksi_media', 'media_id', 'pengguna_id')
            ->withTimestamps();
    }
}
