<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FileUploadService
{
    private const MAX_IMAGE_SIZE = 2048;
    private const MAX_FILE_SIZE = 5120;
    
    private const ALLOWED_IMAGE_MIMES = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
    private const ALLOWED_FILE_MIMES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain',
    ];
    
    private const BLOCKED_EXTENSIONS = [
        'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar', 'sh', 'php', 'py', 'rb', 'pl'
    ];

    public function validateImage(UploadedFile $file): array
    {
        $errors = [];
        
        if ($file->getSize() > self::MAX_IMAGE_SIZE * 1024) {
            $errors[] = 'Image must not exceed 2MB';
        }
        
        if (!in_array($file->getMimeType(), self::ALLOWED_IMAGE_MIMES)) {
            $errors[] = 'Image must be JPEG, PNG, GIF, or WebP';
        }
        
        if ($this->isBlockedExtension($file)) {
            $errors[] = 'File type not allowed';
        }
        
        if (!$this->isValidImageContent($file)) {
            $errors[] = 'Invalid image file';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    public function validateFile(UploadedFile $file): array
    {
        $errors = [];
        
        if ($file->getSize() > self::MAX_FILE_SIZE * 1024) {
            $errors[] = 'File must not exceed 5MB';
        }
        
        $allowedMimes = array_merge(self::ALLOWED_IMAGE_MIMES, self::ALLOWED_FILE_MIMES);
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            $errors[] = 'File type not allowed';
        }
        
        if ($this->isBlockedExtension($file)) {
            $errors[] = 'File extension not allowed';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    public function storeImage(UploadedFile $file, string $directory = 'images', string $disk = 'private'): ?string
    {
        $validation = $this->validateImage($file);
        
        if (!$validation['valid']) {
            Log::warning('Image upload validation failed', [
                'errors' => $validation['errors'],
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
            return null;
        }
        
        try {
            $path = $file->store($directory, $disk);
            
            Log::info('Image uploaded successfully', [
                'path' => $path,
                'disk' => $disk,
            ]);
            
            return $path;
        } catch (\Exception $e) {
            Log::error('Image upload failed', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function storeFile(UploadedFile $file, string $directory = 'files', string $disk = 'private'): ?string
    {
        $validation = $this->validateFile($file);
        
        if (!$validation['valid']) {
            Log::warning('File upload validation failed', [
                'errors' => $validation['errors'],
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
            return null;
        }
        
        try {
            $path = $file->store($directory, $disk);
            
            Log::info('File uploaded successfully', [
                'path' => $path,
                'disk' => $disk,
            ]);
            
            return $path;
        } catch (\Exception $e) {
            Log::error('File upload failed', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function deleteFile(string $path, string $disk = 'private'): bool
    {
        try {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
                
                Log::info('File deleted successfully', [
                    'path' => $path,
                    'disk' => $disk,
                ]);
                
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('File deletion failed', [
                'error' => $e->getMessage(),
                'path' => $path,
            ]);
            return false;
        }
    }

    private function isBlockedExtension(UploadedFile $file): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());
        return in_array($extension, self::BLOCKED_EXTENSIONS);
    }

    private function isValidImageContent(UploadedFile $file): bool
    {
        try {
            $imageInfo = @getimagesize($file->getRealPath());
            return $imageInfo !== false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
