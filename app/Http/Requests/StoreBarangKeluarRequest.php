<?php

namespace App\Http\Requests;

use App\Models\Barangs;
use App\Models\BarangRuangans;
use App\Models\InventoryItem;
use Illuminate\Foundation\Http\FormRequest;

class StoreBarangKeluarRequest extends FormRequest
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
            'tanggal_keluar' => 'required|date',
            'keterangan' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0.01',
        ];

        if ($isSerialized) {
            $rules['inventory_item_id'] = 'required|exists:inventory_items,id';
        } else {
            $rules['ruangan_id'] = 'nullable|exists:ruangans,id';
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $barangId = $this->input('id_barang');
            $barang = Barangs::find($barangId);
            $jumlah = (float) $this->input('jumlah');

            if (!$barang) {
                return;
            }

            if ($barang->has_serial_number) {
                $itemId = $this->input('inventory_item_id');
                $item = InventoryItem::find($itemId);

                if (!$item) {
                    $validator->errors()->add('inventory_item_id', 'Unit serial wajib dipilih.');
                    return;
                }

                if ($item->barang_id != $barang->id) {
                    $validator->errors()->add('inventory_item_id', 'Unit serial tidak sesuai dengan barang yang dipilih.');
                    return;
                }

                if (!in_array($item->status, ['available', 'in_use'])) {
                    $validator->errors()->add('inventory_item_id', "Unit serial {$item->serial_number} sedang dalam status '{$item->status}' dan tidak dapat dikeluarkan.");
                    return;
                }

                if ($jumlah > (float) $item->current_quantity) {
                    $validator->errors()->add('jumlah', "Jumlah keluar ({$jumlah}) melebihi saldo quantity yang tersedia pada serial {$item->serial_number} (" . (float) $item->current_quantity . ").");
                }
            } else {
                // Non-serialized stock check
                if ($jumlah > (float) $barang->stok) {
                    $validator->errors()->add('jumlah', "Jumlah keluar ({$jumlah}) melebihi total stok barang yang tersedia (" . (float) $barang->stok . ").");
                }

                $ruanganId = $this->input('ruangan_id');
                if ($ruanganId) {
                    $room = BarangRuangans::where('barang_id', $barang->id)->where('ruangan_id', $ruanganId)->first();
                    $roomStock = $room ? (float) $room->stok : 0;
                    if ($jumlah > $roomStock) {
                        $validator->errors()->add('jumlah', "Jumlah keluar ({$jumlah}) melebihi stok di ruangan yang dipilih ({$roomStock}).");
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'id_barang.required' => 'Barang harus dipilih.',
            'id_barang.exists' => 'Barang tidak valid.',
            'inventory_item_id.required' => 'Unit serial number wajib dipilih untuk barang serial.',
            'inventory_item_id.exists' => 'Unit serial yang dipilih tidak ditemukan.',
            'jumlah.required' => 'Jumlah barang keluar wajib diisi.',
            'jumlah.numeric' => 'Jumlah barang keluar harus berupa angka.',
            'jumlah.min' => 'Jumlah barang keluar minimal 0.01.',
            'tanggal_keluar.required' => 'Tanggal keluar wajib diisi.',
            'keterangan.required' => 'Keterangan pengeluaran barang wajib diisi.',
        ];
    }
}
