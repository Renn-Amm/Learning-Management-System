<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Student Enrollment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #FFFFFF;
            color: #000000;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #FFFFFF;
            border: 2px solid #000000;
            border-radius: 8px;
            padding: 30px;
        }
        h1 {
            color: #000000;
            border-bottom: 2px solid #000000;
            padding-bottom: 10px;
        }
        .info-block {
            background-color: #FFFFFF;
            border: 1px solid #000000;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .info-label {
            font-weight: bold;
            color: #000000;
        }
        .info-value {
            color: #000000;
            margin-top: 5px;
        }
        .button {
            display: inline-block;
            background-color: #000000;
            color: #FFFFFF;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 4px;
            margin-top: 20px;
            border: 2px solid #000000;
        }
        .button:hover {
            background-color: #FFFFFF;
            color: #000000;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #000000;
            font-size: 12px;
            color: #000000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>New Student Enrollment</h1>
        
        <p>Hello,</p>
        
        <p>A new student has enrolled in your course!</p>
        
        <div class="info-block">
            <div class="info-label">Student Name:</div>
            <div class="info-value">{{ $student->name }}</div>
        </div>
        
        <div class="info-block">
            <div class="info-label">Student Email:</div>
            <div class="info-value">{{ $student->email }}</div>
        </div>
        
        <div class="info-block">
            <div class="info-label">Course Name:</div>
            <div class="info-value">{{ $course->title }}</div>
        </div>
        
        <div class="info-block">
            <div class="info-label">Enrollment Time:</div>
            <div class="info-value">{{ $enrollmentTime->format('M d, Y h:i A') }}</div>
        </div>
        
        <a href="{{ route('courses.show', $course->id) }}" class="button">
            View Course & Student Progress
        </a>
        
        <div class="footer">
            <p>This is an automated notification from your Learning Management System.</p>
            <p>You received this email because you are the instructor for this course.</p>
        </div>
    </div>
</body>
</html>
