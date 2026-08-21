<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Exports\KaryawanExport;
use RealRashid\SweetAlert\Facades\Alert;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

Carbon::setLocale('id');

class KaryawanController extends Controller
{
     
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->input('search'); // Menerima input pencarian
        $exportType = $request->input('export'); // Menerima jenis ekspor (excel atau pdf)

        // Query untuk mendapatkan data karyawan berdasarkan filter keyword
        $karyawanQuery = User::where('is_admin', 0) // Mengambil data user yang bukan admin
            ->when($keyword, function ($query) use ($keyword) {
                // Menambahkan filter pencarian berdasarkan nama atau email
                $query->where('name', 'like', "%$keyword%")
                    ->orWhere('email', 'like', "%$keyword%")
                    ->orWhere('status_user', 'like', "%$keyword%");
            });

        // Ambil hasil query sesuai filter
        $users = $karyawanQuery->get();

        // Jika pengguna memilih untuk mengekspor ke Excel
        if ($exportType == 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(new KaryawanExport($users), 'laporan-data-karyawan.xlsx');
        }

        // Jika pengguna memilih untuk mengekspor ke PDF
        if ($exportType == 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.karyawan', ['users' => $users]);
            return $pdf->download('laporan-data-karyawan.pdf');
        }

        $users = $karyawanQuery->orderBy('id', 'desc')->paginate(10);

        // Menampilkan halaman daftar karyawan dengan data yang sudah difilter
        return view('karyawan.index', compact('users', 'keyword'));
    }

    


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('karyawan.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'status_user' => 'required|string|max:4',
        ],
        [
            'name.required' => 'Nama tidak boleh kosong',
            'email.required' => 'Email tidak boleh kosong',
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Password minimal 8 karakter',
            'status_user.required' => 'Status Petugas tidak boleh kosong',
            'status_user.max' => 'Silahkan pilih status (RPL, TBSM, TKRO, Umum)'
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->status_user = $request->status_user;
        $user->is_admin = 0;
        $user->save();

        Alert::success('Berhasil!', 'Data Berhasil Ditambahkan');
        return redirect()->route('karyawan.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $karyawan = User::findOrFail($id);
        return view('karyawan.edit', compact('karyawan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

     public function update(Request $request, $id)
     {


         $user = User::findOrFail($id);
     
         // Validasi input (password tidak wajib diisi)
         $request->validate([
             'name' => 'required|string|max:255',
             'email' => 'required|email|max:255|unique:users,email,' . $user->id,
             'password' => 'nullable|string|min:6|confirmed',
         ],[
            'name.required' => 'Nama tidak boleh kosong',
            'email.required' => 'Email tidak boleh kosong',
            'password.required' => 'Password tidak boleh kosong',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
         ]);
     
         // Update data
         $user->name = $request->name;
         $user->email = $request->email;
     
         // Hanya update password jika field password diisi
         if ($request->filled('password')) {
             $user->password = Hash::make($request->password);
         }

        // Update status_user jika ada perubahan
        if ($request->has('status_user')) {
            $user->status_user = $request->status_user;
        }
     
         $user->save();
     
         Alert::success('Berhasil!', 'Data Berhasil Diubah');
         return redirect()->route('karyawan.index');
     }
     


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        Alert::success('Berhasil!', 'Data Berhasil Dihapus');
        return redirect()->route('karyawan.index');
    }
}
