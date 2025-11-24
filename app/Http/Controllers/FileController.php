<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function download(Request $request, $file)
    {
        // Prevent directory traversal attacks
        $file = basename($file);
        
        // Find the lesson that owns this file
        $lesson = Lesson::where('attachment', 'LIKE', "%{$file}%")->first();
        
        if (!$lesson) {
            abort(404, 'File not found');
        }
        
        // Check authorization
        $user = auth()->user();
        $course = $lesson->course;
        
        // Teachers can download their own course files
        if ($user->isTeacher() && $course->teacher_id === $user->id) {
            return $this->downloadFile($lesson->attachment);
        }
        
        // Students can download if enrolled
        if ($user->isStudent() && $course->isEnrolledBy($user->id)) {
            return $this->downloadFile($lesson->attachment);
        }
        
        abort(403, 'Unauthorized access to this file');
    }
    
    private function downloadFile($filepath)
    {
        if (!Storage::disk('private')->exists($filepath)) {
            abort(404, 'File not found');
        }
        
        return Storage::disk('private')->download($filepath);
    }
}
