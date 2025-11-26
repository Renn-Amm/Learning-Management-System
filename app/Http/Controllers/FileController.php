<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    use AuthorizesRequests;
    /**
     * Download a file with authorization check.
     * Authorization: Students can download if enrolled, teachers if they own the course.
     * Security: Prevents directory traversal attacks with basename().
     */
    public function download(Request $request, $file)
    {
        // Prevent directory traversal attacks
        $file = basename($file);
        
        // Find the lesson that owns this file
        $lesson = Lesson::where('attachment', 'LIKE', "%{$file}%")->first();
        
        if (!$lesson) {
            abort(404, 'File not found');
        }
        
        // Authorization: Use policy to check if user can download lesson files
        $this->authorize('downloadFile', $lesson);
        
        return $this->downloadFile($lesson->attachment);
    }
    
    private function downloadFile($filepath)
    {
        if (!Storage::disk('private')->exists($filepath)) {
            abort(404, 'File not found');
        }
        
        return Storage::disk('private')->download($filepath);
    }
}
