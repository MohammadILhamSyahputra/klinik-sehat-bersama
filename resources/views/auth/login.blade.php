<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Masuk — {{ config('app.name', 'Klinik Sehat Bersama') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-slate-100">
        <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">
            <div class="w-full max-w-5xl bg-white rounded-2xl shadow-xl overflow-hidden grid grid-cols-1 lg:grid-cols-12 border border-gray-100">

                <!-- Kolom Kiri: Judul & Keterangan Klinik -->
                <div class="lg:col-span-6 bg-gradient-to-br from-teal-700 via-teal-600 to-emerald-600 text-white p-8 sm:p-10 lg:p-12 flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute -top-16 -right-16 w-56 h-56 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
                    <div class="absolute -bottom-20 -left-16 w-64 h-64 rounded-full bg-emerald-400/20 blur-2xl pointer-events-none"></div>

                    <div class="relative z-10">
                        <div class="inline-flex items-center gap-3 mb-8">
                            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-white/15 backdrop-blur-sm border border-white/20 shadow-sm">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <span class="text-sm font-semibold tracking-wider uppercase text-teal-100">Sistem Informasi Klinik</span>
                        </div>

                        <h1 class="text-3xl sm:text-4xl font-bold leading-tight tracking-tight">
                            Klinik Sehat Bersama
                        </h1>
                        <p class="mt-3 text-teal-50 text-sm sm:text-base leading-relaxed">
                            Platform terpadu untuk pengelolaan layanan kesehatan pasien, antrian pemeriksaan dokter, rekam medis elektronik, penebusan resep apotek, hingga pembayaran kasir dan klaim asuransi.
                        </p>

                        <div class="mt-8 space-y-3 text-sm text-teal-50">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-xs font-bold">✓</span>
                                <span>Pendaftaran pasien & manajemen jadwal dokter</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-xs font-bold">✓</span>
                                <span>Pemeriksaan medis, diagnosis ICD-10 & resep digital</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 flex-shrink-0 w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-xs font-bold">✓</span>
                                <span>Penagihan kasir, klaim asuransi & laporan keuangan</span>
                            </div>
                        </div>
                    </div>

                    <div class="relative z-10 mt-10 pt-6 border-t border-white/15 text-xs text-teal-100/90 flex items-center justify-between">
                        <span>Jl. Flamboyan No. 1, Kab Ngawi</span>
                        <span>WIB (UTC+7)</span>
                    </div>
                </div>

                <!-- Kolom Kanan: Form Login -->
                <div class="lg:col-span-6 p-8 sm:p-10 lg:p-12 flex flex-col justify-center bg-white">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Masuk ke Akun Anda</h2>
                        <p class="text-sm text-gray-500 mt-1">Silakan masukkan kredensial petugas, dokter, atau kasir.</p>
                    </div>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <x-input-label for="email" :value="__('Email Petugas / Dokter / Kasir')" class="font-medium text-gray-700" />
                            <x-text-input id="email" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@klinik.com" />
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

                        <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-500 space-y-1">
                            <p class="font-semibold text-gray-600">Akun Pengujian Demo (Seeder):</p>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1.5 pt-1">
                                <div class="bg-gray-50 rounded-md px-2.5 py-1.5 border border-gray-100">
                                    <span class="block text-[11px] text-gray-400">Petugas</span>
                                    <span class="font-mono text-teal-700">petugas@klinik.com</span>
                                </div>
                                <div class="bg-gray-50 rounded-md px-2.5 py-1.5 border border-gray-100">
                                    <span class="block text-[11px] text-gray-400">Dokter</span>
                                    <span class="font-mono text-teal-700">dokter@klinik.com</span>
                                </div>
                                <div class="bg-gray-50 rounded-md px-2.5 py-1.5 border border-gray-100">
                                    <span class="block text-[11px] text-gray-400">Kasir</span>
                                    <span class="font-mono text-teal-700">kasir@klinik.com</span>
                                </div>
                            </div>
                            <p class="text-[11px] text-gray-400 pt-1">Kata sandi semua akun demo: <span class="font-mono text-teal-700">password</span></p>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </body>
</html>
