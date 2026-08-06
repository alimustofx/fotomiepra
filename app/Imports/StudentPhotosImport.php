<?php

namespace App\Imports;

use App\Models\StudentPhoto;
use App\Services\GoogleDriveService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentPhotosImport implements ToCollection, WithHeadingRow
{
    public array $errors = [];
    public int $successCount = 0;
    public int $failedCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // Baris 1 adalah Header Excel

            $nis       = trim($row['nis'] ?? '');
            $nama      = trim($row['nama'] ?? '');
            $kelas     = trim($row['kelas'] ?? '');
            $driveLink = trim($row['drive_link'] ?? $row['link_drive'] ?? $row['link'] ?? '');

            // Validasi Kelengkapan Kolom
            if (empty($nis) || empty($nama) || empty($kelas) || empty($driveLink)) {
                $this->errors[] = "Baris {$rowNumber}: Data tidak lengkap (NIS, Nama, Kelas, dan Link Drive wajib diisi).";
                $this->failedCount++;
                continue;
            }

            // Ekstraksi ID Google Drive
            $drive = GoogleDriveService::resolve($driveLink);
            if (!$drive) {
                $this->errors[] = "Baris {$rowNumber} (NIS {$nis}): Link Google Drive tidak valid atau ID file tidak ditemukan.";
                $this->failedCount++;
                continue;
            }

            // Upsert (Update jika NIS sudah ada, Create jika baru)
            StudentPhoto::updateOrCreate(
                ['nis' => $nis],
                [
                    'nama'                => $nama,
                    'kelas'               => $kelas,
                    'drive_file_id'       => $drive['file_id'],
                    'drive_link_original' => $drive['original_link'],
                    'drive_link_preview'  => $drive['preview_link'],
                    'status'              => 'active',
                    'created_by'          => auth()->id(),
                ]
            );

            $this->successCount++;
        }
    }
}