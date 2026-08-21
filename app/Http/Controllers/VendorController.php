<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\VendorExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

Carbon::setLocale('id');

class VendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $keyword = $request->input('search');
        $exportType = $request->input('export');

        $vendorQuery = Vendor::query();

        if ($keyword) {
            $vendorQuery->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('code', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        if ($exportType) {
            $vendors = $vendorQuery->withCount('barangs')->orderBy('name', 'asc')->get();

            if ($exportType === 'excel') {
                return Excel::download(new VendorExport($vendors), 'laporan-data-vendor.xlsx');
            }

            if ($exportType === 'pdf') {
                $pdf = Pdf::loadView('pdf.vendor', compact('vendors'));
                return $pdf->download('laporan-data-vendor.pdf');
            }
        }

        $vendors = $vendorQuery->withCount('barangs')->orderBy('name', 'asc')->paginate(10);

        return view('vendor.index', compact('vendors', 'keyword'));
    }

    public function create()
    {
        return view('vendor.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:vendors,code',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama Vendor tidak boleh kosong.',
            'code.unique' => 'Kode Vendor sudah digunakan.',
            'email.email' => 'Format email tidak valid.',
        ]);

        try {
            $code = $request->code;
            if (!$code) {
                $lastRecord = Vendor::latest('id')->first();
                $lastId = $lastRecord ? $lastRecord->id : 0;
                $code = 'VND-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
            }

            Vendor::create([
                'name' => $request->name,
                'code' => $code,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'description' => $request->description,
            ]);

            Alert::success('Berhasil!', 'Vendor berhasil ditambahkan');
            return redirect()->route('vendor.index');
        } catch (\Exception $e) {
            Alert::error('Gagal!', 'Gagal menambahkan vendor: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show($id)
    {
        $vendor = Vendor::with('barangs')->withCount('barangs')->findOrFail($id);
        return view('vendor.show', compact('vendor'));
    }

    public function edit($id)
    {
        $vendor = Vendor::findOrFail($id);
        return view('vendor.edit', compact('vendor'));
    }

    public function update(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:vendors,code,' . $id,
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama Vendor tidak boleh kosong.',
            'code.unique' => 'Kode Vendor sudah digunakan.',
            'email.email' => 'Format email tidak valid.',
        ]);

        try {
            $code = $request->code;
            if (!$code) {
                $code = $vendor->code ?? ('VND-' . str_pad($id, 4, '0', STR_PAD_LEFT));
            }

            $vendor->update([
                'name' => $request->name,
                'code' => $code,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'description' => $request->description,
            ]);

            Alert::success('Berhasil!', 'Vendor berhasil diperbarui');
            return redirect()->route('vendor.index');
        } catch (\Exception $e) {
            Alert::error('Gagal!', 'Gagal memperbarui vendor: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $vendor = Vendor::findOrFail($id);
            $vendor->delete();

            Alert::success('Berhasil!', 'Vendor berhasil dihapus');
            return redirect()->route('vendor.index');
        } catch (\Exception $e) {
            Alert::error('Gagal!', 'Gagal menghapus vendor: ' . $e->getMessage());
            return redirect()->route('vendor.index');
        }
    }

    public function exportExcel(Request $request)
    {
        $keyword = $request->input('search');
        $vendorQuery = Vendor::query();

        if ($keyword) {
            $vendorQuery->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('code', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        $vendors = $vendorQuery->withCount('barangs')->orderBy('name', 'asc')->get();
        return Excel::download(new VendorExport($vendors), 'laporan-data-vendor.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $keyword = $request->input('search');
        $vendorQuery = Vendor::query();

        if ($keyword) {
            $vendorQuery->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('code', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        $vendors = $vendorQuery->withCount('barangs')->orderBy('name', 'asc')->get();
        $pdf = Pdf::loadView('pdf.vendor', compact('vendors'));
        return $pdf->download('laporan-data-vendor.pdf');
    }
}
