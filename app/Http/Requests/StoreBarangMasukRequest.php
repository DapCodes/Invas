<?php

namespace App\Http\Requests;

use App\Models\Barangs;
use Illuminate\Foundation\Http\FormRequest;

class StoreBarangMasukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $barangId = $this->input('id_barang');
        $barang = $barangId ? Barangs::find($barangId) : null;
        $isSerialized = $barang && $barang->has_serial_number;

        $rules = [
            'id_barang' => 'required|exists:barangs,id',
            'tanggal_masuk' => 'required|date',
            'keterangan' => 'required|string|max:255',
            'ruangan_id' => 'nullable|exists:ruangans,id',
        ];

        if ($isSerialized) {
            $rules['serials'] = 'required|array|min:1';
            $rules['serials.*.serial_number'] = 'required|string|max:255';
            $rules['serials.*.quantity'] = 'required|numeric|min:0.01';
            $rules['serials.*.ruangan_id'] = 'nullable|exists:ruangans,id';
        } else {
            $rules['jumlah'] = 'required|numeric|min:0.01';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'id_barang.required' => 'Barang harus dipilih.',
            'id_barang.exists' => 'Barang tidak valid.',
            'jumlah.required' => 'Jumlah barang masuk harus diisi.',
            'jumlah.numeric' => 'Jumlah barang masuk harus berupa angka.',
            'jumlah.min' => 'Jumlah barang masuk minimal 0.01.',
            'tanggal_masuk.required' => 'Tanggal masuk harus diisi.',
            'keterangan.required' => 'Keterangan harus diisi.',
            'serials.required' => 'Minimal satu unit serial harus dimasukkan untuk barang serial.',
            'serials.*.serial_number.required' => 'Nomor seri wajib diisi.',
            'serials.*.quantity.required' => 'Kuantitas unit serial wajib diisi.',
            'serials.*.quantity.min' => 'Kuantitas unit serial minimal 0.01.',
        ];
    }
}
