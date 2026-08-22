<?php

namespace App\Http\Requests;

use App\Models\Peminjamans;
use App\Models\Pengembalians;
use Illuminate\Foundation\Http\FormRequest;

class StorePengembalianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_peminjam' => 'required|exists:peminjamans,id',
            'jumlah_kembali' => 'required|numeric|min:0.01',
            'tanggal_kembali' => 'required|date',
            'kondisi' => 'required|in:Baik,Rusak,Sebagian Rusak,Hilang,Tidak Lengkap',
            'keterangan' => 'nullable|string|max:255',
            'ruangan_id' => 'nullable|exists:ruangans,id',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $peminjamanId = $this->input('id_peminjam');
            $peminjaman = Peminjamans::find($peminjamanId);
            $jumlahKembali = (float) $this->input('jumlah_kembali');

            if (!$peminjaman) {
                return;
            }

            $totalBorrowed = (float) $peminjaman->jumlah;
            $previouslyReturned = (float) Pengembalians::where('id_peminjam', $peminjaman->id)->sum('jumlah');
            $remainingToReturn = $totalBorrowed - $previouslyReturned;

            if ($jumlahKembali > $remainingToReturn) {
                $validator->errors()->add('jumlah_kembali', "Jumlah pengembalian ({$jumlahKembali}) melebihi sisa pinjaman yang belum kembali ({$remainingToReturn}).");
            }
        });
    }

    public function messages(): array
    {
        return [
            'id_peminjam.required' => 'Transaksi peminjaman wajib dipilih.',
            'jumlah_kembali.required' => 'Jumlah yang dikembalikan wajib diisi.',
            'jumlah_kembali.min' => 'Jumlah kembali minimal 0.01.',
            'tanggal_kembali.required' => 'Tanggal kembali wajib diisi.',
            'kondisi.required' => 'Kondisi barang saat pengembalian wajib dipilih.',
            'kondisi.in' => 'Kondisi barang tidak valid.',
        ];
    }
}
