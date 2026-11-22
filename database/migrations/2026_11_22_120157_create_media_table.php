<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 150)->unique();
            $table->string('jenis', 20);
            $table->string('genre', 100);
            $table->unsignedSmallInteger('tahun_rilis');
            $table->text('deskripsi')->nullable();
            $table->decimal('rating', 3, 1)->nullable();
            $table->timestamps();

            $table->index('jenis');
            $table->index('tahun_rilis');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
