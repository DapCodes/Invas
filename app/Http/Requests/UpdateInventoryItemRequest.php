<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $itemId = $this->route('inventory_item') ?? $this->id;

        return [
            'serial_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('inventory_items', 'serial_number')->ignore($itemId),
            ],
            'status' => 'required|in:available,borrowed,in_use,out,damaged,lost,maintenance,depleted',
            'ruangan_id' => 'nullable|exists:ruangans,id',
            'keterangan' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'serial_number.required' => 'Nomor seri wajib diisi.',
            'serial_number.unique' => 'Nomor seri sudah digunakan.',
            'status.in' => 'Status tidak valid.',
        ];
    }
}
