<x-layouts.auth>
    <div class="relative mx-auto w-full max-w-lg px-4 py-10 sm:px-6">
        <div class="login-panel">
            <x-auth-header :title="__('Masuk ke akun Anda')" :description="__('Gunakan email dan kata sandi yang terdaftar di sistem admin')" />

            <div class="mt-6">
                <!-- Session Status -->
                <x-auth-session-status class="text-center" :status="session('status')" />
            </div>

            <form method="POST" action="{{ route('login.store') }}" class="mt-8 flex flex-col gap-6">
                @csrf

                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-600 dark:text-white/80">{{ __('Alamat Email') }}</label>
                    <flux:input
                        name="email"
                        :value="old('email')"
                        type="email"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="nama@perusahaan.com"
                    >
                        <x-slot:prefix>
                            <span class="inline-flex size-9 items-center justify-center rounded-2xl bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8.25 12 13.5l9-5.25M4.5 19.5h15a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5h-15A1.5 1.5 0 0 0 3 6v12a1.5 1.5 0 0 0 1.5 1.5Z" />
                                </svg>
                            </span>
                        </x-slot:prefix>
                    </flux:input>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-600 dark:text-white/80">{{ __('Kata sandi') }}</label>
                    <flux:input
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        viewable
                    >
                        <x-slot:prefix>
                            <span class="inline-flex size-9 items-center justify-center rounded-2xl bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25a3.75 3.75 0 1 0-7.5 0V9m11.654 11.482-1.086 1.086a2.25 2.25 0 0 1-3.182 0L4.432 13.864a2.25 2.25 0 0 1 0-3.182l1.086-1.086a2.25 2.25 0 0 1 3.182 0l11.204 11.204a2.25 2.25 0 0 1 0 3.182Z" />
                                </svg>
                            </span>
                        </x-slot:prefix>
                    </flux:input>
                </div>

                <div class="flex items-center justify-between gap-4">
                    <flux:checkbox name="remember" :label="__('Ingat saya')" :checked="old('remember')" />
                    @if (Route::has('password.request'))
                        <flux:link class="text-sm font-semibold text-slate-500 dark:text-white/70" :href="route('password.request')" wire:navigate>
                            {{ __('Lupa kata sandi?') }}
                        </flux:link>
                    @endif
                </div>

                <flux:button variant="primary" type="submit" class="w-full rounded-2xl py-4 text-base font-semibold" data-test="login-button">
                    <span class="inline-flex items-center justify-center gap-2">
                        <span class="size-2 rounded-full bg-white animate-pulse"></span>
                        {{ __('Masuk sekarang') }}
                    </span>
                </flux:button>
            </form>
        </div>
    </div>
</x-layouts.auth>
