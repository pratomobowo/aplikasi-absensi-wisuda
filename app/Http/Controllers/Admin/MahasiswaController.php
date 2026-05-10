<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MahasiswaExport;
use App\Exports\MahasiswaTemplateExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMahasiswaRequest;
use App\Http\Requests\UpdateMahasiswaRequest;
use App\Imports\MahasiswaImport;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Mahasiswa::with('graduationTickets');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('npm', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('program_studi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('program_studi')) {
            $query->where('program_studi', $request->input('program_studi'));
        }

        $mahasiswas = $query->latest()->paginate(20)->withQueryString();
        $programStudis = Mahasiswa::distinct()->pluck('program_studi', 'program_studi');

        return view('admin.mahasiswa.index', compact('mahasiswas', 'programStudis'));
    }

    public function create()
    {
        return view('admin.mahasiswa.create');
    }

    public function store(StoreMahasiswaRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password'] ?? $data['npm']);

        if ($request->hasFile('foto_wisuda')) {
            $path = $request->file('foto_wisuda')->store('graduation-photos', 'public');
            $data['foto_wisuda'] = basename($path);
        }

        Mahasiswa::create($data);

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function edit(Mahasiswa $mahasiswa)
    {
        return view('admin.mahasiswa.edit', compact('mahasiswa'));
    }

    public function update(UpdateMahasiswaRequest $request, Mahasiswa $mahasiswa)
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('foto_wisuda')) {
            if ($mahasiswa->foto_wisuda) {
                Storage::disk('public')->delete('graduation-photos/' . $mahasiswa->foto_wisuda);
            }
            $path = $request->file('foto_wisuda')->store('graduation-photos', 'public');
            $data['foto_wisuda'] = basename($path);
        }

        $mahasiswa->update($data);

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Mahasiswa berhasil diperbarui.');
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        if ($mahasiswa->foto_wisuda) {
            Storage::disk('public')->delete('graduation-photos/' . $mahasiswa->foto_wisuda);
        }

        $mahasiswa->delete();

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Mahasiswa berhasil dihapus.');
    }

    public function resetPassword(Mahasiswa $mahasiswa)
    {
        $mahasiswa->update([
            'password' => bcrypt($mahasiswa->npm),
            'password_changed_at' => null,
        ]);

        return redirect()->route('admin.mahasiswa.index')->with('success', "Password {$mahasiswa->nama} berhasil direset ke NPM.");
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        Excel::import(new MahasiswaImport, $request->file('file'));

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil diimport.');
    }

    public function export(Request $request)
    {
        $fileName = 'Mahasiswa-' . now()->format('Y-m-d-His') . '.xlsx';
        return Excel::download(new MahasiswaExport, $fileName);
    }

    public function downloadTemplate()
    {
        return Excel::download(new MahasiswaTemplateExport, 'template-mahasiswa.xlsx');
    }
}