<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EduHub LMS - Professional Learning Management System</title>
    
    <!-- Favicon with cache busting -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS & Alpine.js via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.1/dist/cdn.min.js"></script>
</head>
<body class="antialiased font-sans">
    <div class="min-h-screen bg-white">
        <nav class="bg-white border-b border-black">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-black">EduHub LMS</h1>
                    </div>
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-black hover:underline px-3 py-2">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-black hover:underline px-3 py-2">Login</a>
                            <a href="{{ route('register') }}" class="bg-black text-white px-4 py-2 rounded-md hover:bg-white hover:text-black border border-black">Register</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <main>
            <div class="relative overflow-hidden">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
                    <div class="text-center">
                        <h1 class="text-5xl font-bold text-black mb-6">
                            Welcome to EduHub LMS
                        </h1>
                        <p class="text-xl text-black mb-8 max-w-2xl mx-auto">
                            Empowering educators and learners with a modern, intuitive platform.
                            Transform your teaching and learning experience today.
                        </p>
                        <div class="flex justify-center gap-4">
                            @auth
                                <a href="{{ route('dashboard') }}" class="bg-black text-white px-8 py-3 rounded-lg font-semibold text-lg hover:bg-white hover:text-black border-2 border-black">
                                    Go to Dashboard
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="bg-black text-white px-8 py-3 rounded-lg font-semibold text-lg hover:bg-white hover:text-black border-2 border-black">
                                    Get Started
                                </a>
                                <a href="{{ route('login') }}" class="bg-white hover:bg-black text-black hover:text-white px-8 py-3 rounded-lg font-semibold text-lg border-2 border-black">
                                    Sign In
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white p-8 rounded-lg border border-black">
                        <div class="w-12 h-12 bg-white border border-black rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-black mb-2">Rich Course Content</h3>
                        <p class="text-black">Access comprehensive courses with structured lessons and progress tracking.</p>
                    </div>

                    <div class="bg-white p-8 rounded-lg border border-black">
                        <div class="w-12 h-12 bg-white border border-black rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-black mb-2">For Teachers & Students</h3>
                        <p class="text-black">Teachers can create courses while students can enroll and learn at their own pace.</p>
                    </div>

                    <div class="bg-white p-8 rounded-lg border border-black">
                        <div class="w-12 h-12 bg-white border border-black rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-black mb-2">Track Progress</h3>
                        <p class="text-black">Monitor your learning progress and complete courses to achieve your goals.</p>
                    </div>
                </div>
            </div>
        </main>

        <footer class="bg-white border-t border-black mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <p class="text-center text-black">
                    &copy; {{ date('Y') }} EduHub LMS. All rights reserved.
                </p>
            </div>
        </footer>
    </div>
</body>
</html>
