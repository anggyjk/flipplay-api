<?php

namespace App\Models;

use Database\Factories\PenggunaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pengguna extends Model
{
    /** @use HasFactory<PenggunaFactory> */
    use HasFactory;

    protected $table = 'pengguna';

    protected $fillable = [
        'nama',
        'email',
    ];

    public function koleksi(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'koleksi_media', 'pengguna_id', 'media_id')
            ->withTimestamps();
    }
}
