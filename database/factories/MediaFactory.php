<?php

namespace Database\Factories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'judul' => ucwords(fake()->unique()->words(3, true)),
            'jenis' => fake()->randomElement(['film', 'musik', 'serial', 'buku']),
            'genre' => fake()->randomElement(['Action', 'Drama', 'Comedy', 'Sci-Fi', 'Romance', 'Horror', 'Jazz', 'Pop']),
            'tahun_rilis' => fake()->numberBetween(1950, (int) date('Y')),
            'deskripsi' => fake()->sentence(12),
            'rating' => fake()->randomFloat(1, 0, 10),
        ];
    }
}
