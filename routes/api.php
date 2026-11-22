<?php

use App\Http\Controllers\Api\KoleksiController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\PenggunaController;
use Illuminate\Support\Facades\Route;

Route::apiResource('media', MediaController::class)->parameters(['media' => 'media']);

Route::apiResource('pengguna', PenggunaController::class)->only(['index', 'store', 'show']);

Route::prefix('pengguna/{pengguna}/koleksi')->name('pengguna.koleksi.')->group(function () {
    Route::get('/', [KoleksiController::class, 'index'])->name('index');
    Route::post('{media}', [KoleksiController::class, 'store'])->name('store');
    Route::delete('{media}', [KoleksiController::class, 'destroy'])->name('destroy');
});
