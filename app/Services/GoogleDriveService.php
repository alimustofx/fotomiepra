<?php

namespace App\Services;

class GoogleDriveService
{
    /**
     * Ekstraksi FILE_ID dari berbagai format URL Google Drive.
     */
    public static function extractFileId(string $url): ?string
    {
        // 1. Format: https://drive.google.com/file/d/FILE_ID/view?usp=sharing
        if (preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        // 2. Format: https://drive.google.com/open?id=FILE_ID atau uc?id=FILE_ID
        if (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        // 3. Fallback jika admin langsung input FILE_ID mentah (panjang min 25 karakter)
        $trimmed = trim($url);
        if (preg_match('/^[a-zA-Z0-9_-]{25,}$/', $trimmed)) {
            return $trimmed;
        }

        return null;
    }

    /**
     * Mengolah URL mentah menjadi array data link yang terstruktur.
     */
    public static function resolve(string $url): ?array
    {
        $fileId = self::extractFileId($url);

        if (!$fileId) {
            return null;
        }

        return [
            'file_id' => $fileId,
            'preview_link' => "https://drive.google.com/thumbnail?id={$fileId}&sz=w1000",
            'original_link' => "https://drive.google.com/file/d/{$fileId}/view",
        ];
    }
}