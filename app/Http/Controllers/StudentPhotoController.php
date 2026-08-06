<?php

namespace App\Http\Controllers;

use App\Models\StudentPhoto;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentPhotoController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentPhoto::where('status', 'active');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        $photos = $query->latest()->paginate(10)->withQueryString();

        // Mengambil daftar kelas unik untuk dropdown filter
        $classes = StudentPhoto::where('status', 'active')
            ->select('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        return Inertia::render('Photos/Index', [
            'photos'  => $photos,
            'classes' => $classes,
            'filters' => $request->only(['search', 'kelas']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis'        => 'required|string|unique:students_photos,nis',
            'nama'       => 'required|string|max:255',
            'kelas'      => 'required|string|max:100',
            'drive_link' => 'required|url',
        ]);

        $drive = GoogleDriveService::resolve($validated['drive_link']);

        if (!$drive) {
            return back()->withErrors(['drive_link' => 'Link Google Drive tidak valid atau tidak memuat ID file.']);
        }

        StudentPhoto::create([
            'nis'                 => trim($validated['nis']),
            'nama'                => trim($validated['nama']),
            'kelas'               => trim($validated['kelas']),
            'drive_file_id'       => $drive['file_id'],
            'drive_link_original' => $drive['original_link'],
            'drive_link_preview'  => $drive['preview_link'],
            'status'              => 'active',
            'created_by'          => auth()->id(),
        ]);

        return redirect()->route('photos.index');
    }

    public function update(Request $request, StudentPhoto $photo)
    {
        $validated = $request->validate([
            'nis'        => 'required|string|unique:students_photos,nis,' . $photo->id,
            'nama'       => 'required|string|max:255',
            'kelas'      => 'required|string|max:100',
            'drive_link' => 'nullable|url',
        ]);

        $data = [
            'nis'   => trim($validated['nis']),
            'nama'  => trim($validated['nama']),
            'kelas' => trim($validated['kelas']),
        ];

        if (!empty($validated['drive_link'])) {
            $drive = GoogleDriveService::resolve($validated['drive_link']);
            if (!$drive) {
                return back()->withErrors(['drive_link' => 'Link Google Drive tidak valid.']);
            }
            $data['drive_file_id']       = $drive['file_id'];
            $data['drive_link_original'] = $drive['original_link'];
            $data['drive_link_preview']  = $drive['preview_link'];
        }

        $photo->update($data);

        return redirect()->route('photos.index');
    }

    public function destroy(StudentPhoto $photo)
    {
        $photo->delete();

        return redirect()->route('photos.index');
    }
}