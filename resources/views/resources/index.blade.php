<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Resources - {{ config('app.name') }}</title>
    @livewireStyles
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background: #f5f5f5;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
        <div style="background: white; padding: 20px; margin-bottom: 20px; border: 1px solid #ddd;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h1 style="margin: 0;">Learning Resources</h1>
                <a href="{{ route('dashboard') }}" style="padding: 10px 20px; background: #333; color: white; text-decoration: none;">
                    Back to Dashboard
                </a>
            </div>
            <p style="color: #666; margin: 0;">
                Explore courses and discover recommended books from our external library integration.
            </p>
        </div>

        <div style="margin-bottom: 40px;">
            <h2 style="margin-bottom: 20px;">Course Search</h2>
            @livewire('course-search')
        </div>

        <div style="margin-bottom: 40px;">
            <h2 style="margin-bottom: 20px;">Book Recommendations</h2>
            <p style="color: #666; margin-bottom: 20px;">
                Powered by OpenLibrary API - Search for books by subject to enhance your learning.
            </p>
            @livewire('book-recommendations')
        </div>
    </div>

    @livewireScripts
</body>
</html>
