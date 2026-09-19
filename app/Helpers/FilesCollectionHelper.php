<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FilesCollectionHelper
{
    public static function findFiles(string $directoryPath, string $finder = '', ?string $disk = null)
    {
        $storage = Storage::disk($disk ?? config('filesystems.default'));

        $allFiles = $storage->files($directoryPath);

        if (empty($allFiles)) {
            return collect();
        }

        return collect($allFiles)
            ->filter(function ($filePath) use ($finder) {
                $fileName = basename($filePath);

                if (pathinfo($fileName, PATHINFO_EXTENSION) !== 'webp') {
                    return false;
                }

                if (empty($finder)) {
                    return true;
                }

                return Str::contains($fileName, $finder);
            })
            ->map(function ($filePath) use ($storage) {
                $pngFilePath = preg_replace('/\.webp$/i', '.png', $filePath);

                return [
                    'name' => basename($filePath),
                    'url' => $storage->url($filePath),
                    'download_url' => $storage->exists($pngFilePath)
                        ? $storage->url($pngFilePath)
                        : null,
                ];
            });
    }
}
