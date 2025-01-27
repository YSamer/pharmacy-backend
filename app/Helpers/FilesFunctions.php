<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function storeFile(?UploadedFile $file, ?string $directory = 'files'): ?string
{
    if (isset($file) && $file instanceof UploadedFile) {
        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs($directory, $fileName);
        return $filePath;
    } else {
        return null;
    }
}

function deleteFile(string $filePath): bool
{
    return Storage::delete($filePath);
}