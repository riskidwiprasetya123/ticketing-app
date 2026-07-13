<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EventFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'lokasi' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'tikets' => 'required|array|min:1',
            'tikets.*.tipe' => 'required|in:reguler,premium',
            'tikets.*.harga' => 'required|numeric|min:0',
            'tikets.*.stok' => 'required|integer|min:0',
            'tikets.*.id' => 'nullable|exists:tikets,id',
        ];

        // Saat CREATE
        if ($this->isMethod('post')) {
            $rules['tanggal_waktu'] = 'required|date|after:now';
        }

        // Saat EDIT
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['tanggal_waktu'] = 'required|date';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul event wajib diisi.',
            'judul.max' => 'Judul event maksimal 255 karakter.',

            'deskripsi.required' => 'Deskripsi event wajib diisi.',

            'lokasi.required' => 'Lokasi event wajib diisi.',
            'lokasi.max' => 'Lokasi maksimal 255 karakter.',

            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.exists' => 'Kategori tidak valid.',

            'tanggal_waktu.required' => 'Tanggal dan waktu wajib diisi.',
            'tanggal_waktu.after' => 'Tanggal dan waktu harus setelah waktu sekarang.',

            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus JPG, JPEG, atau PNG.',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.',

            'tikets.required' => 'Minimal harus ada satu tiket.',
            'tikets.array' => 'Data tiket tidak valid.',
            'tikets.min' => 'Minimal satu tiket harus ditambahkan.',

            'tikets.*.tipe.required' => 'Tipe tiket wajib dipilih.',
            'tikets.*.tipe.in' => 'Tipe tiket harus reguler atau premium.',

            'tikets.*.harga.required' => 'Harga tiket wajib diisi.',
            'tikets.*.harga.numeric' => 'Harga tiket harus berupa angka.',
            'tikets.*.harga.min' => 'Harga tiket tidak boleh kurang dari 0.',

            'tikets.*.stok.required' => 'Stok tiket wajib diisi.',
            'tikets.*.stok.integer' => 'Stok tiket harus berupa angka bulat.',
            'tikets.*.stok.min' => 'Stok tiket tidak boleh kurang dari 0.',
        ];
    }
}
