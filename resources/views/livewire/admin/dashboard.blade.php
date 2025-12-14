<div class="sg-page">
    <div class="dashboard-hero">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-3">
                <p class="text-xs uppercase tracking-[0.4em] text-white/60">{{ __('Command Center') }}</p>
                <flux:heading size="xl">{{ __('Admin Dashboard') }}</flux:heading>
                <flux:subheading class="text-white/70">
                    {{ __('Sinkronisasi data pengguna, distributor, dan stok setiap detik untuk keputusan cepat.') }}
                </flux:subheading>
                <div class="dashboard-hero__actions">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/20 px-4 py-2 text-xs uppercase tracking-[0.3em]">
                        <span class="size-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                        {{ __('Realtime feed') }}
                    </span>
                    <span class="inline-flex items-center gap-2 text-xs text-white/70">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12a9 9 0 1 0 9-9v9H3Z" />
                        </svg>
                        {{ __('Terakhir diperbarui') }} {{ now()->diffForHumans() }}
                    </span>
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="stat-card-accent">
                    <div class="text-xs uppercase tracking-[0.4em] text-white/80">{{ __('Total pengguna') }}</div>
                    <div class="mt-3 text-4xl font-semibold">{{ number_format($totalUsers) }}</div>
                    <div class="mt-2 text-sm text-white/80">{{ __('Distributor aktif:') }} {{ number_format($totalDistributors) }}</div>
                </div>
                <div class="stat-card bg-white/10 text-white">
                    <div class="text-xs uppercase tracking-[0.4em] text-white/70">{{ __('Stok agregat') }}</div>
                    <div class="mt-3 text-4xl font-semibold">{{ number_format($totalStock) }}</div>
                    <div class="mt-2 text-sm text-white/70">{{ __('Unit gas tersinkron') }}</div>
                </div>
            </div>
        </div>
        <div class="mt-6 flex flex-wrap gap-3">
            <flux:button variant="ghost" wire:click="refreshStats" size="sm" class="rounded-full border border-white/30 bg-white/10 text-white hover:bg-white/20">
                <span class="inline-flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m4.5 4.5 15 15m0-15-15 15" />
                    </svg>
                    {{ __('Refresh data') }}
                </span>
            </flux:button>
            <flux:button variant="ghost" href="{{ route('admin.reports.export') }}" wire:navigate size="sm" class="rounded-full border border-white/30 text-white hover:bg-white/10">
                {{ __('Export laporan') }}
            </flux:button>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="stat-card">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-xs uppercase tracking-[0.4em] text-slate-400">{{ __('Lokasi aktif') }}</div>
                    <div class="mt-2 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($totalLocations) }}</div>
                    <div class="mt-2 text-sm text-slate-500 dark:text-white/70">{{ __('Seluruh distributor terdaftar') }}</div>
                </div>
                <span class="sg-icon text-slate-600 dark:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                </span>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-xs uppercase tracking-[0.4em] text-slate-400">{{ __('Total stok') }}</div>
                    <div class="mt-2 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($totalStock) }}</div>
                    <div class="mt-2 text-sm text-slate-500 dark:text-white/70">{{ __('Unit siap distribusi') }}</div>
                </div>
                <span class="sg-icon text-slate-600 dark:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25M21 7.5v9.75L12 22.5m0-9.75L3 7.5m9 5.25v9.75m0-9.75 9-5.25M3 17.25 12 22.5" />
                    </svg>
                </span>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-xs uppercase tracking-[0.4em] text-slate-400">{{ __('Distribusi harian') }}</div>
                    <div class="mt-2 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($totalDistributors) }}</div>
                    <div class="mt-2 text-sm text-slate-500 dark:text-white/70">{{ __('Distributor aktif / hari') }}</div>
                </div>
                <span class="sg-icon text-slate-600 dark:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3 3" />
                    </svg>
                </span>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <a class="list-card bg-white dark:bg-white/5" href="{{ route('admin.locations') }}" wire:navigate>
            <span class="sg-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
            </span>
            <div class="min-w-0">
                <div class="font-semibold text-slate-900 dark:text-white">{{ __('Lokasi distribusi') }}</div>
                <div class="mt-1 text-sm text-slate-500 dark:text-white/70">{{ __('Pantau stok dan status buka tutup') }}</div>
            </div>
            <span class="ms-auto text-xs uppercase tracking-[0.3em] text-slate-400">{{ __('Detail') }}</span>
        </a>

        <a class="list-card bg-white dark:bg-white/5" href="{{ route('admin.reports.export') }}" wire:navigate>
            <span class="sg-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
            </span>
            <div class="min-w-0">
                <div class="font-semibold text-slate-900 dark:text-white">{{ __('Ekspor laporan') }}</div>
                <div class="mt-1 text-sm text-slate-500 dark:text-white/70">{{ __('Ambil CSV stok per distributor') }}</div>
            </div>
            <span class="ms-auto text-xs uppercase tracking-[0.3em] text-slate-400">{{ __('Detail') }}</span>
        </a>
    </div>
</div>
