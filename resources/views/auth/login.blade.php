<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-center mb-4 text-black">Choose Login Type</h2>
        <div class="grid grid-cols-2 gap-4 mb-6">
            <button type="button" onclick="setLoginType('student')" id="studentBtn" class="px-4 py-3 border-2 border-black rounded font-semibold bg-black text-white">
                Login as Student
            </button>
            <button type="button" onclick="setLoginType('teacher')" id="teacherBtn" class="px-4 py-3 border-2 border-black rounded font-semibold bg-white text-black hover:bg-black hover:text-white">
                Login as Teacher
            </button>
        </div>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div id="studentInfo" class="mb-6 p-4 bg-white border border-black rounded text-sm">
            <p class="font-semibold text-black mb-2">Student Demo Account:</p>
            <p class="text-black"><strong>Email:</strong> student1@example.com</p>
            <p class="text-black"><strong>Password:</strong> password</p>
        </div>

        <div id="teacherInfo" class="mb-6 p-4 bg-white border border-black rounded text-sm hidden">
            <p class="font-semibold text-black mb-2">Teacher Demo Account:</p>
            <p class="text-black"><strong>Email:</strong> teacher@example.com</p>
            <p class="text-black"><strong>Password:</strong> password</p>
        </div>

        <div class="mb-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mb-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-black" name="remember">
                <span class="ms-2 text-sm text-black">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mb-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-black hover:text-black rounded-md focus:outline-none" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button>
                {{ __('Log in') }}
            </x-primary-button>
        </div>

        <div class="text-center mt-4">
            <span class="text-sm text-black">Don't have an account?</span>
            <a href="{{ route('register') }}" class="text-sm text-black hover:text-black underline font-semibold">
                Register
            </a>
        </div>
    </form>

    <script>
        function setLoginType(type) {
            const studentBtn = document.getElementById('studentBtn');
            const teacherBtn = document.getElementById('teacherBtn');
            const studentInfo = document.getElementById('studentInfo');
            const teacherInfo = document.getElementById('teacherInfo');
            const emailInput = document.getElementById('email');

            if (type === 'student') {
                studentBtn.className = 'px-4 py-3 border-2 border-black rounded font-semibold bg-black text-white';
                teacherBtn.className = 'px-4 py-3 border-2 border-black rounded font-semibold bg-white text-black hover:bg-black hover:text-white';
                studentInfo.classList.remove('hidden');
                teacherInfo.classList.add('hidden');
                emailInput.value = 'student1@example.com';
            } else {
                teacherBtn.className = 'px-4 py-3 border-2 border-black rounded font-semibold bg-black text-white';
                studentBtn.className = 'px-4 py-3 border-2 border-black rounded font-semibold bg-white text-black hover:bg-black hover:text-white';
                teacherInfo.classList.remove('hidden');
                studentInfo.classList.add('hidden');
                emailInput.value = 'teacher@example.com';
            }
        }
    </script>
</x-guest-layout>
