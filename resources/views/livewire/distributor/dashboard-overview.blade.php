<div class="sg-page">
    <div class="dashboard-hero">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-2">
                <p class="text-xs uppercase tracking-[0.4em] text-white/60">{{ __('Operasi Distributor') }}</p>
                <flux:heading size="xl">{{ __('Distributor Dashboard') }}</flux:heading>
                <flux:subheading class="text-white/70">{{ __('Kelola stok, pantau lokasi, dan pastikan gas siap disalurkan.') }}</flux:subheading>
            </div>
            <div class="flex flex-wrap gap-2">
                <flux:button variant="outline" wire:click="refreshStats" class="rounded-full bg-white/10 text-white hover:bg-white/20">
                    <span class="inline-flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3v6h6M21 21v-6h-6M3 13v8h8M21 11V3h-8" />
                        </svg>
                        {{ __('Refresh data') }}
                    </span>
                </flux:button>
                <flux:button variant="ghost" href="{{ route('distributor.locations') }}" wire:navigate class="rounded-full border-white/30 text-white hover:bg-white/10">
                    {{ __('Kelola lokasi') }}
                </flux:button>
                <flux:button variant="primary" href="{{ route('distributor.locations.create') }}" wire:navigate class="rounded-full">
                    {{ __('Tambah lokasi') }}
                </flux:button>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="stat-card">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-xs uppercase tracking-[0.4em] text-slate-400">{{ __('Lokasi Anda') }}</div>
                    <div class="mt-2 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($totalLocations) }}</div>
                    <div class="mt-2 text-sm text-slate-500 dark:text-white/70">{{ __('Terverifikasi dalam sistem admin') }}</div>
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
                    <div class="text-xs uppercase tracking-[0.4em] text-slate-400">{{ __('Stok total') }}</div>
                    <div class="mt-2 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($totalStock) }}</div>
                    <div class="mt-2 text-sm text-slate-500 dark:text-white/70">{{ __('Siap distribusi hari ini') }}</div>
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
                    <div class="text-xs uppercase tracking-[0.4em] text-slate-400">{{ __('Stok kritis (<= 5)') }}</div>
                    <div class="mt-2 text-3xl font-semibold text-slate-900 dark:text-white">{{ number_format($lowStockCount) }}</div>
                    <div class="mt-2 text-sm text-slate-500 dark:text-white/70">{{ __('Perlu pengisian segera') }}</div>
                </div>
                <span class="sg-icon text-slate-600 dark:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.732 0-2.814-1.874-1.948-3.374L10.052 3.378c.866-1.5 3.03-1.5 3.896 0l7.355 12.748ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </span>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="list-card bg-white dark:bg-white/5">
            <div>
                <div class="text-xs uppercase tracking-[0.35em] text-slate-400">{{ __('Agenda distribusi') }}</div>
                <div class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ __('Proyeksi permintaan mingguan') }}</div>
                <p class="mt-2 text-sm text-slate-500 dark:text-white/70">
                    {{ __('Pastikan setiap lokasi memiliki stok di atas batas minimal sebelum akhir pekan.') }}
                </p>
            </div>
            <flux:button variant="outline" href="{{ route('distributor.locations') }}" wire:navigate class="rounded-full">
                {{ __('Pantau lokasi') }}
            </flux:button>
        </div>

        <div class="list-card bg-white dark:bg-white/5">
            <div>
                <div class="text-xs uppercase tracking-[0.35em] text-slate-400">{{ __('Tindakan cepat') }}</div>
                <div class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ __('Laporkan stok kritis') }}</div>
                <p class="mt-2 text-sm text-slate-500 dark:text-white/70">
                    {{ __('Kirim pembaruan ke admin agar pasokan cadangan segera dikirimkan.') }}
                </p>
            </div>
            <flux:button variant="primary" href="{{ route('distributor.locations.create') }}" wire:navigate class="rounded-full">
                {{ __('Ajukan permintaan') }}
            </flux:button>
        </div>
    </div>
</div>
