<?php

namespace App\Http\Controllers;

use App\Models\ImportLog;
use App\Models\StudentPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total Arsip Foto
        $totalPhotos = StudentPhoto::where('status', 'active')->count();

        // 2. Total Kelas Unique
        $totalClasses = StudentPhoto::where('status', 'active')
            ->distinct('kelas')
            ->count('kelas');

        // 3. Distribusi Foto per Kelas (untuk Bar Chart)
        $classDistribution = StudentPhoto::where('status', 'active')
            ->select('kelas', DB::raw('count(*) as total'))
            ->groupBy('kelas')
            ->orderBy('kelas')
            ->get();

        // 4. 5 Entri Terbaru
        $latestEntries = StudentPhoto::where('status', 'active')
            ->latest()
            ->take(5)
            ->get(['id', 'nis', 'nama', 'kelas', 'created_at']);

        // 5. Riwayat Impor Terakhir
        $latestImport = ImportLog::latest()->first();

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalPhotos'       => $totalPhotos,
                'totalClasses'      => $totalClasses,
                'classDistribution' => $classDistribution,
                'latestEntries'     => $latestEntries,
                'latestImport'      => $latestImport,
            ],
        ]);
    }
}