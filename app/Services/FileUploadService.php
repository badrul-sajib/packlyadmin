<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    public static function getDisk(?string $disk = null): string
    {
        // if provided, check if it exists in config/filesystems.php
        $availableDisks = array_keys(config('filesystems.disks'));

        if ($disk && in_array($disk, $availableDisks)) {
            return $disk;
        }

        // fallback to default disk from config
        return config('filesystems.default', 'public');
    }

    public static function handleUpload(
        Request $request,
        string $fieldName,
        ?string $disk = null,
        ?string $oldFilePath = null,
        string $folder = 'uploads'
    ): ?string {
        $disk = self::getDisk($disk);

        if ($request->hasFile($fieldName)) {
            // Delete old file if it exists
            if ($oldFilePath && Storage::disk($disk)->exists($oldFilePath)) {
                Storage::disk($disk)->delete($oldFilePath);
            }

            $file     = $request->file($fieldName);
            $filePath = $folder.'/'.now()->format('Ymd_His').'_'.Str::uuid().'.'.$file->getClientOriginalExtension();
            $result   = Storage::disk($disk)->put($filePath, file_get_contents($file), 'public');

            if (! $result) {
                throw new Exception('Media upload failed.');
            }

            return $filePath;

        }

        // Keep old file if no new file uploaded
        return $oldFilePath;
    }

    public static function getFileUrl(?string $filePath, ?string $disk = null): ?string
    {
        if (! $filePath) {
            return null;
        }

        $disk = self::getDisk($disk);

        return Storage::disk($disk)->url($filePath);
    }

    public static function deleteFile(?string $filePath, ?string $disk = null): bool
    {
        if (! $filePath) {
            return false;
        }

        $disk = self::getDisk($disk);

        if (Storage::disk($disk)->exists($filePath)) {
            return Storage::disk($disk)->delete($filePath);
        }

        return false;
    }

    public static function resolveFileUrl(?string $filePath, ?string $disk = null): ?string
    {
        if (! $filePath) {
            return null;
        }

        $disk = self::getDisk($disk);

        // If disk is s3 → direct URL
        if ($disk === 's3') {
            return Storage::disk($disk)->url($filePath);
        }

        // Otherwise build remote URL (or APP_URL)
        $baseUrl = config('app.url');

        return rtrim($baseUrl, '/').'/storage/'.$filePath;
    }
}
