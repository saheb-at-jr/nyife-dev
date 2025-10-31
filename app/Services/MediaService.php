<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    public static function upload($image)
    {
        Log::info('Starting media upload', [
            'original_name' => $image->getClientOriginalName(),
            'mime_type' => $image->getMimeType(),
            'size_bytes' => $image->getSize(),
        ]);

        try {
            if (config('settings.use_s3_as_storage', false)) {
                Log::info('Uploading media to S3');
                $path = $image->storePublicly('uploads/media/send/' . (auth()->user()->company_id ?? 'unknown'), 's3');
                $imageUrl = Storage::disk('s3')->url($path);
                Log::info('Media uploaded to S3', ['path' => $path, 'url' => $imageUrl]);
            } else {
                Log::info('Uploading media to local storage');
                $path = $image->store(null, 'public');
                $imageUrl = Storage::disk('public')->url($path);
                Log::info('Media uploaded to local storage', ['path' => $path, 'url' => $imageUrl]);
            }

            $name = basename($path);
            Log::info('Media upload completed successfully', ['file_name' => $name]);

            return ['name' => $name, 'path' => $imageUrl];
        } catch (\Exception $e) {
            Log::error('Media upload failed', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
