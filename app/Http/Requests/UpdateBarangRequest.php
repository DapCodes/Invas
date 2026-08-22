<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBarangRequest extends FormRequest
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
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama barang wajib diisi.',
            'merek.required' => 'Merek barang wajib diisi.',
            'satuan_id.required' => 'Satuan barang wajib dipilih.',
            'satuan_id.exists' => 'Satuan barang tidak valid.',
        ];
    }
}
