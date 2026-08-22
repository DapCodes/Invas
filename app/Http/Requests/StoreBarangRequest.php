<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'merek' => 'required|string|max:255',
            'satuan_id' => 'required|exists:units,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'has_serial_number' => 'nullable|boolean',
            'stok' => 'nullable|numeric|min:0',
            'ruangan_id' => 'nullable|exists:ruangans,id',
            'serials' => 'nullable|array',
            'serials.*.serial_number' => 'required_with:serials|string|distinct|unique:inventory_items,serial_number',
            'serials.*.quantity' => 'nullable|numeric|min:0.01',
            'serials.*.ruangan_id' => 'nullable|exists:ruangans,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama barang wajib diisi.',
            'merek.required' => 'Merek barang wajib diisi.',
            'satuan_id.required' => 'Satuan barang wajib dipilih.',
            'satuan_id.exists' => 'Satuan barang tidak valid.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
            'serials.*.serial_number.required_with' => 'Nomor seri wajib diisi jika menambahkan unit serial.',
            'serials.*.serial_number.distinct' => 'Terdapat nomor seri yang duplikat dalam form.',
            'serials.*.serial_number.unique' => 'Nomor seri :input sudah digunakan oleh item lain.',
        ];
    }
}
