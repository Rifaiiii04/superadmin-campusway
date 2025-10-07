<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SchoolController extends Controller
{
    public function index()
    {
        try {
            $schools = School::paginate(10);
            
            return Inertia::render('SuperAdmin/Schools', [
                'title' => 'Manajemen Sekolah',
                'schools' => $schools,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching schools: ' . $e->getMessage());
            return Inertia::render('SuperAdmin/Schools', [
                'title' => 'Manajemen Sekolah',
                'schools' => [
                    'data' => [],
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                ],
                'error' => 'Gagal memuat data sekolah'
            ]);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'npsn' => 'required|string|unique:schools,npsn',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            School::create([
                'npsn' => $request->npsn,
                'name' => $request->name,
                'password' => bcrypt($request->password),
            ]);

            return redirect()->back()->with('success', 'Sekolah berhasil ditambahkan');
        } catch (\Exception $e) {
            \Log::error('Error creating school: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menambahkan sekolah'])->withInput();
        }
    }

    public function show(School $school)
    {
        return Inertia::render('SuperAdmin/SchoolDetail', [
            'title' => 'Detail Sekolah',
            'school' => $school,
        ]);
    }

    public function update(Request $request, School $school)
    {
        $validator = Validator::make($request->all(), [
            'npsn' => 'required|string|unique:schools,npsn,' . $school->id,
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $updateData = [
                'npsn' => $request->npsn,
                'name' => $request->name,
            ];

            if ($request->password) {
                $updateData['password'] = bcrypt($request->password);
            }

            $school->update($updateData);

            return redirect()->back()->with('success', 'Sekolah berhasil diperbarui');
        } catch (\Exception $e) {
            \Log::error('Error updating school: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal memperbarui sekolah'])->withInput();
        }
    }

    public function destroy(School $school)
    {
        try {
            $school->delete();
            return redirect()->back()->with('success', 'Sekolah berhasil dihapus');
        } catch (\Exception $e) {
            \Log::error('Error deleting school: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menghapus sekolah']);
        }
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $file = $request->file('file');
            $csvData = array_map('str_getcsv', file($file->getRealPath()));
            $header = array_shift($csvData);

            $imported = 0;
            $errors = [];

            foreach ($csvData as $row) {
                if (count($row) >= 3) {
                    try {
                        School::create([
                            'npsn' => $row[0],
                            'name' => $row[1],
                            'password' => bcrypt($row[2]),
                        ]);
                        $imported++;
                    } catch (\Exception $e) {
                        $errors[] = "Baris " . ($imported + count($errors) + 1) . ": " . $e->getMessage();
                    }
                }
            }

            if ($imported > 0) {
                return redirect()->back()->with('success', "Berhasil mengimport {$imported} sekolah");
            } else {
                return back()->withErrors(['error' => 'Tidak ada data yang berhasil diimport']);
            }
        } catch (\Exception $e) {
            \Log::error('Error importing schools: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal mengimport sekolah: ' . $e->getMessage()]);
        }
    }
}
