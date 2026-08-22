<?php

namespace App\Http\Requests;

use App\Models\Barangs;
use App\Models\BarangRuangans;
use App\Models\InventoryItem;
use Illuminate\Foundation\Http\FormRequest;

class StorePeminjamanRequest extends FormRequest
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
            'jumlah' => 'required|numeric|min:0.01',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'nama_peminjam' => 'required|string|max:255',
            'ruangan_id' => 'nullable|exists:ruangans,id',
        ];

        if ($isSerialized) {
            $rules['inventory_item_id'] = 'required|exists:inventory_items,id';
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
                    $validator->errors()->add('inventory_item_id', 'Unit serial tidak sesuai dengan master barang.');
                    return;
                }

                if ($item->status !== 'available') {
                    $validator->errors()->add('inventory_item_id', "Unit serial {$item->serial_number} sedang dalam status '{$item->status}' dan tidak dapat dipinjam.");
                    return;
                }

                if ($jumlah > (float) $item->current_quantity) {
                    $validator->errors()->add('jumlah', "Jumlah pinjam ({$jumlah}) melebihi saldo unit serial {$item->serial_number} (" . (float) $item->current_quantity . ").");
                }
            } else {
                if ($jumlah > (float) $barang->stok) {
                    $validator->errors()->add('jumlah', "Jumlah pinjam ({$jumlah}) melebihi total stok barang yang tersedia (" . (float) $barang->stok . ").");
                }

                $ruanganId = $this->input('ruangan_id');
                if ($ruanganId) {
                    $room = BarangRuangans::where('barang_id', $barang->id)->where('ruangan_id', $ruanganId)->first();
                    $roomStock = $room ? (float) $room->stok : 0;
                    if ($jumlah > $roomStock) {
                        $validator->errors()->add('jumlah', "Jumlah pinjam ({$jumlah}) melebihi stok di ruangan yang dipilih ({$roomStock}).");
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'id_barang.required' => 'Barang wajib dipilih.',
            'id_barang.exists' => 'Barang tidak ditemukan.',
            'inventory_item_id.required' => 'Unit serial wajib dipilih untuk barang serial.',
            'jumlah.required' => 'Jumlah pinjam wajib diisi.',
            'jumlah.min' => 'Jumlah pinjam minimal 0.01.',
            'tanggal_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            'tanggal_kembali.required' => 'Tanggal rencana kembali wajib diisi.',
            'tanggal_kembali.after_or_equal' => 'Tanggal rencana kembali harus sama atau setelah tanggal pinjam.',
            'nama_peminjam.required' => 'Nama peminjam wajib diisi.',
        ];
    }
}
