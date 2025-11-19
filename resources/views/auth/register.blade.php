<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-center mb-4 text-black">Create Account</h2>
        <div class="grid grid-cols-2 gap-4 mb-6">
            <button type="button" onclick="setRegisterType('student')" id="studentBtn" class="px-4 py-3 border-2 border-black rounded font-semibold bg-black text-white">
                Register as Student
            </button>
            <button type="button" onclick="setRegisterType('teacher')" id="teacherBtn" class="px-4 py-3 border-2 border-black rounded font-semibold bg-white text-black hover:bg-black hover:text-white">
                Register as Teacher
            </button>
        </div>
        <div id="roleInfo" class="mb-4 p-3 bg-white border border-black rounded text-sm">
            <p class="text-black"><strong id="roleText">Students</strong> can enroll in courses and track their progress.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <input type="hidden" name="role" id="roleInput" value="student">

        <div class="mb-4">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mb-4">
            <a class="underline text-sm text-black hover:text-black rounded-md focus:outline-none" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        function setRegisterType(type) {
            const studentBtn = document.getElementById('studentBtn');
            const teacherBtn = document.getElementById('teacherBtn');
            const roleInput = document.getElementById('roleInput');
            const roleText = document.getElementById('roleText');
            const roleInfo = document.getElementById('roleInfo');

            if (type === 'student') {
                studentBtn.className = 'px-4 py-3 border-2 border-black rounded font-semibold bg-black text-white';
                teacherBtn.className = 'px-4 py-3 border-2 border-black rounded font-semibold bg-white text-black hover:bg-black hover:text-white';
                roleInput.value = 'student';
                roleText.textContent = 'Students';
                roleInfo.innerHTML = '<p class="text-black"><strong>Students</strong> can enroll in courses and track their progress.</p>';
            } else {
                teacherBtn.className = 'px-4 py-3 border-2 border-black rounded font-semibold bg-black text-white';
                studentBtn.className = 'px-4 py-3 border-2 border-black rounded font-semibold bg-white text-black hover:bg-black hover:text-white';
                roleInput.value = 'teacher';
                roleText.textContent = 'Teachers';
                roleInfo.innerHTML = '<p class="text-black"><strong>Teachers</strong> can create courses, add lessons, and view student progress.</p>';
            }
        }
    </script>
</x-guest-layout>
