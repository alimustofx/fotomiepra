<?php

namespace App\Http\Controllers;

use App\Imports\StudentPhotosImport;
use App\Models\ImportLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function index()
    {
        $logs = ImportLog::with('user:id,name')
            ->latest()
            ->paginate(10);

        return Inertia::render('Photos/Import', [
            'logs' => $logs,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // Maks 10MB
        ]);

        $import = new StudentPhotosImport();

        try {
            Excel::import($import, $request->file('file'));

            // Catat Log Impor ke Database
            ImportLog::create([
                'filename'      => $request->file('file')->getClientOriginalName(),
                'total_rows'    => $import->successCount + $import->failedCount,
                'success_rows'  => $import->successCount,
                'failed_rows'   => $import->failedCount,
                'error_details' => $import->errors,
                'user_id'       => auth()->id(),
            ]);

            return redirect()->route('photos.import');
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Gagal membaca file Excel. Pastikan format kolom sesuai template: ' . $e->getMessage()]);
        }
    }
}