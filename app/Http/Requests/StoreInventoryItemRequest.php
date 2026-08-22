<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'barang_id' => 'required|exists:barangs,id',
            'serial_number' => 'required|string|max:255|unique:inventory_items,serial_number',
            'initial_quantity' => 'nullable|numeric|min:0.01',
            'ruangan_id' => 'nullable|exists:ruangans,id',
            'tanggal_masuk' => 'nullable|date',
            'keterangan' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'barang_id.required' => 'Barang harus dipilih.',
            'serial_number.required' => 'Nomor seri wajib diisi.',
            'serial_number.unique' => 'Nomor seri sudah terdaftar dalam sistem.',
            'initial_quantity.min' => 'Quantity minimal 0.01.',
        ];
    }
}
