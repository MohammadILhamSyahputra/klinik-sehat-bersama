<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-teal-100 text-teal-600 mb-3 shadow-inner">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Klinik Sehat Bersama</h2>
        <p class="text-sm text-gray-500 mt-1">Sistem Informasi Layanan Pasien & Rekam Medis</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Petugas / Dokter / Kasir')" class="font-medium text-gray-700" />
            <x-text-input id="email" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@kliniksehat.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Kata Sandi')" class="font-medium text-gray-700" />
            <x-text-input id="password" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Ingat Saya') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-teal-600 hover:text-teal-700 hover:underline font-medium" href="{{ route('password.request') }}">
                    {{ __('Lupa kata sandi?') }}
                </a>
            @endif
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-teal-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-teal-700 active:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                {{ __('Masuk ke Sistem') }}
            </button>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-500 text-center space-y-1">
            <p class="font-semibold text-gray-600">Akun Pengujian Demo (Seeder):</p>
            <p>Petugas: <span class="font-mono text-teal-700">petugas@klinik.com</span> / sandi: <span class="font-mono text-teal-700">password</span></p>
            <p>Dokter: <span class="font-mono text-teal-700">dokter@klinik.com</span> / sandi: <span class="font-mono text-teal-700">password</span></p>
            <p>Kasir: <span class="font-mono text-teal-700">kasir@klinik.com</span> / sandi: <span class="font-mono text-teal-700">password</span></p>
        </div>
    </form>
</x-guest-layout>
