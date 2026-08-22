<?php

namespace App\Http\Requests;

use App\Models\Barangs;
use App\Models\BarangRuangans;
use App\Models\InventoryItem;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:serialized,non_serialized',
            'inventory_item_id' => 'required_if:type,serialized|nullable|exists:inventory_items,id',
            'barang_id' => 'required_if:type,non_serialized|nullable|exists:barangs,id',
            'from_ruangan_id' => 'required_if:type,non_serialized|nullable|exists:ruangans,id',
            'to_ruangan_id' => 'required|exists:ruangans,id',
            'quantity' => 'required_if:type,non_serialized|nullable|numeric|min:0.01',
            'keterangan' => 'nullable|string|max:255',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $fromRoom = $this->input('from_ruangan_id');
            $toRoom = $this->input('to_ruangan_id');

            if ($fromRoom && $fromRoom == $toRoom) {
                $validator->errors()->add('to_ruangan_id', 'Ruangan tujuan tidak boleh sama dengan ruangan asal.');
            }

            if ($this->input('type') === 'non_serialized') {
                $barangId = $this->input('barang_id');
                $qty = (float) $this->input('quantity');

                if ($barangId && $fromRoom) {
                    $sourceRoom = BarangRuangans::where('barang_id', $barangId)->where('ruangan_id', $fromRoom)->first();
                    $avail = $sourceRoom ? (float) $sourceRoom->stok : 0;
                    if ($qty > $avail) {
                        $validator->errors()->add('quantity', "Jumlah transfer ({$qty}) melebihi stok di ruangan asal ({$avail}).");
                    }
                }
            }
        });
    }
}
