<?php

namespace App\Http\Requests;

use App\Models\Media;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $media = $this->route('media');
        $mediaId = $media instanceof Media ? $media->getKey() : $media;

        return [
            'judul' => ['sometimes', 'required', 'string', 'max:150', Rule::unique('media', 'judul')->ignore($mediaId)],
            'jenis' => ['sometimes', 'required', 'string', Rule::in(['film', 'musik', 'serial', 'buku'])],
            'genre' => ['sometimes', 'required', 'string', 'max:100'],
            'tahun_rilis' => ['sometimes', 'required', 'integer', 'min:1900', 'max:'.(int) date('Y')],
            'deskripsi' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'rating' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:10'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'judul.required' => 'Judul media wajib diisi.',
            'judul.max' => 'Judul media maksimal 150 karakter.',
            'judul.unique' => 'Judul media sudah terdaftar. Gunakan judul lain.',
            'jenis.in' => 'Jenis media harus salah satu dari: film, musik, serial, buku.',
            'genre.required' => 'Genre media wajib diisi.',
            'tahun_rilis.integer' => 'Tahun rilis harus berupa angka.',
            'tahun_rilis.min' => 'Tahun rilis minimal 1900.',
            'tahun_rilis.max' => 'Tahun rilis tidak boleh melebihi tahun sekarang.',
            'deskripsi.max' => 'Deskripsi maksimal 2000 karakter.',
            'rating.numeric' => 'Rating harus berupa angka.',
            'rating.min' => 'Rating minimal 0.',
            'rating.max' => 'Rating maksimal 10.',
        ];
    }
}
