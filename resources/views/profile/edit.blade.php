<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-black leading-tight">
            Profile Settings
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-white border-2 border-black text-black px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white border border-black sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-image-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white border border-black sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-name-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white border border-black sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white border border-black sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white border border-black sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
