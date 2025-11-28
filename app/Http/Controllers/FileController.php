<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    use AuthorizesRequests;
    
    // Download lesson file with authorization check
    public function download(Request $request, $file)
    {
        $file = basename($file);
        
        // Find lesson by filename in attachment path
        $lesson = Lesson::where('attachment', 'LIKE', "%{$file}%")->first();
        
        if (!$lesson) {
            abort(404, 'File not found. Please contact the course instructor.');
        }
        
        // Check authorization
        $this->authorize('downloadFile', $lesson);
        
        // Get the filepath from database
        $filepath = $lesson->attachment;
        
        // Get full system path to file
        $fullPath = Storage::disk('private')->path($filepath);
        
        // Verify file exists
        if (!file_exists($fullPath)) {
            abort(404, 'File not found in storage. The file may have been deleted.');
        }
        
        // Get original filename with extension
        $downloadName = $lesson->attachment_name ?? basename($filepath);
        
        // Ensure filename has extension
        if (!pathinfo($downloadName, PATHINFO_EXTENSION)) {
            $extension = pathinfo($filepath, PATHINFO_EXTENSION);
            $downloadName .= '.' . $extension;
        }
        
        // Get file extension and MIME type
        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
        ];
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
        
        // Create response with BinaryFileResponse for better header control
        $response = new BinaryFileResponse($fullPath);
        $response->setContentDisposition(
            'attachment',
            $downloadName,
            iconv('UTF-8', 'ASCII//TRANSLIT', $downloadName)
        );
        
        // Set headers
        $response->headers->set('Content-Type', $mimeType);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Content-Security-Policy', "default-src 'none'");
        $response->headers->set('X-Download-Options', 'noopen');
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        
        return $response;
    }
}
