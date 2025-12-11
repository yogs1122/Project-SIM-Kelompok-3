<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <h2 class="text-lg font-semibold text-blue-900 mb-3">Pilih Tipe Akun</h2>
        <div class="space-y-2">
            <label class="flex items-center p-3 border border-blue-300 rounded cursor-pointer hover:bg-blue-100" for="role_user">
                <input id="role_user" type="radio" name="role" value="user" class="rounded border-gray-300 text-blue-600" {{ old('role', 'user') === 'user' ? 'checked' : '' }} />
                <span class="ms-3 font-medium text-gray-800">👤 User Biasa</span>
            </label>
            <label class="flex items-center p-3 border border-purple-300 rounded cursor-pointer hover:bg-purple-100" for="role_umkm">
                <input id="role_umkm" type="radio" name="role" value="umkm" class="rounded border-gray-300 text-purple-600" {{ old('role') === 'umkm' ? 'checked' : '' }} />
                <span class="ms-3 font-medium text-gray-800">🏪 Pedagang UMKM</span>
            </label>
            <label class="flex items-center p-3 border border-red-300 rounded cursor-pointer hover:bg-red-100" for="role_admin">
                <input id="role_admin" type="radio" name="role" value="admin" class="rounded border-gray-300 text-red-600" {{ old('role') === 'admin' ? 'checked' : '' }} />
                <span class="ms-3 font-medium text-gray-800">🛡️ Administrator</span>
            </label>
        </div>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
