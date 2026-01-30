<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Course Created</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; border: 2px solid #000; border-radius: 8px; padding: 30px;">
        <h1 style="color: #000; margin-top: 0;">New Course Created</h1>
        
        <p>A new course has been created on {{ config('app.name') }}.</p>
        
        <div style="background-color: #fff; border: 1px solid #000; border-radius: 4px; padding: 20px; margin: 20px 0;">
            <h2 style="margin-top: 0; color: #000;">Course Details</h2>
            
            <p><strong>Course Title:</strong><br>{{ $course->title }}</p>
            
            <p><strong>Description:</strong><br>{{ $course->description }}</p>
            
            <p><strong>Category:</strong> {{ $course->category->name }}</p>
            
            <p><strong>Level:</strong> {{ ucfirst($course->level) }}</p>
            
            @if($course->skills->isNotEmpty())
                <p><strong>Skills:</strong><br>
                    @foreach($course->skills as $skill)
                        <span style="display: inline-block; background-color: #e9ecef; padding: 4px 8px; margin: 2px; border-radius: 4px; font-size: 14px;">{{ $skill->name }}</span>
                    @endforeach
                </p>
            @endif
            
            <p><strong>Created by:</strong><br>
                {{ $teacher->name }}<br>
                <a href="mailto:{{ $teacher->email }}">{{ $teacher->email }}</a>
            </p>
            
            <p><strong>Created at:</strong> {{ $course->created_at->format('F j, Y g:i A') }}</p>
        </div>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="{{ route('courses.show', $course) }}" 
               style="display: inline-block; background-color: #000; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 4px; font-weight: bold;">
                View Course
            </a>
        </div>
        
        <p style="margin-top: 30px; font-size: 12px; color: #666; text-align: center;">
            This is an automated notification from {{ config('app.name') }}
        </p>
    </div>
</body>
</html>
