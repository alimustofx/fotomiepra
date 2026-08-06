<?php

namespace App\Http\Controllers;

use App\Models\SearchLog;
use App\Models\StudentPhoto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    // Tampilan Awal (GET /)
    public function index()
    {
        return Inertia::render('Welcome', [
            'student'     => null,
            'hasSearched' => false,
            'searchedNis' => null,
        ]);
    }

    // Proses Pencarian (POST /)
    public function search(Request $request)
    {
        $nis = $request->input('nis');
        $student = null;
        $hasSearched = false;

        if ($nis !== null && $nis !== '') {
            $hasSearched = true;
            $nisClean = trim($nis);

            // Cari data siswa yang statusnya active
            $student = StudentPhoto::where('nis', $nisClean)
                ->where('status', 'active')
                ->first(['id', 'nis', 'nama', 'kelas', 'drive_link_preview', 'drive_link_original']);

            // Catat log pencarian ke database (PRD 6.8 & 9)
            SearchLog::create([
                'nis_searched' => $nisClean,
                'found'        => $student !== null,
                'ip_address'   => $request->ip(),
            ]);
        }

        return Inertia::render('Welcome', [
            'student'     => $student,
            'hasSearched' => $hasSearched,
            'searchedNis' => $nis,
        ]);
    }
}