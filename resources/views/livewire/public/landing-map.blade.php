<div class="sg-gradient-bg" wire:poll.60s="refresh" data-public-landing>
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 opacity-40">
            <div class="h-full w-full bg-[radial-gradient(circle_at_top,_rgba(14,165,233,0.25),_transparent_50%),radial-gradient(circle_at_bottom,_rgba(59,130,246,0.2),_transparent_55%)]"></div>
        </div>

        <div class="relative z-10 mx-auto flex max-w-6xl flex-col gap-8 px-4 py-10 lg:px-6 lg:py-14">
            <div class="glass-panel">
                <div class="hero-badge">
                    <span class="inline-flex size-5 items-center justify-center rounded-full bg-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6l3 3" />
                        </svg>
                    </span>
                    {{ __('Realtime supply intelligence') }}
                </div>
                <div class="mt-6 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div class="space-y-4">
                        <div class="hero-heading">{{ __('Distribusi Gas Subsidi') }}</div>
                        <div class="hero-subheading max-w-2xl">
                            {{ __('Pantau stok pangkalan terdekat, temukan jalur distribusi tercepat, dan bantu masyarakat mengakses energi dengan mudah.') }}
                        </div>
                    </div>
                    <div class="flex flex-col gap-3 text-sm text-white/80">
                        <div class="inline-flex items-center gap-2 text-white">
                            <span class="text-4xl font-semibold">{{ number_format($this->locations->count()) }}</span>
                            <span>{{ __('lokasi aktif') }}</span>
                        </div>
                        <flux:button variant="primary" href="{{ route('login') }}" wire:navigate class="rounded-2xl px-6 py-3 text-base font-semibold">
                            <span class="inline-flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-9A2.25 2.25 0 0 0 2.25 5.25v13.5A2.25 2.25 0 0 0 4.5 21h9a2.25 2.25 0 0 0 2.25-2.25V15m3-3 3-3m0 0-3-3m3 3H9" />
                                </svg>
                                {{ __('Login') }}
                            </span>
                        </flux:button>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="glass-panel overflow-hidden">
                    <div class="flex items-center justify-between">
                        <div class="text-sm uppercase tracking-[0.4em] text-white/60">{{ __('Peta Dinamis') }}</div>
                        <span class="inline-flex items-center gap-1 text-xs text-white/70">
                            <span class="size-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            {{ __('Siaran langsung') }}
                        </span>
                    </div>
                    <div class="mt-4 h-[60vh] lg:h-[70vh] w-full rounded-2xl border border-white/10" data-public-map>
                        <div class="h-full w-full rounded-2xl" data-public-map-canvas wire:ignore></div>
                    </div>
                </div>

                <div class="glass-panel-dark">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-xs uppercase tracking-[0.35em] text-slate-500 dark:text-white/60">{{ __('Cari Lokasi') }}</div>
                            <div class="mt-1 text-lg font-semibold text-slate-900 dark:text-white">{{ __('Pangkalan terdekat') }}</div>
                        </div>
                        <flux:badge color="cyan" class="px-4 py-1 text-xs uppercase tracking-[0.3em]">
                            {{ __('Live') }}
                        </flux:badge>
                    </div>
                    <div class="mt-4">
                        <flux:input wire:model.live="q" :label="__('Search')" placeholder="nama / alamat" />
                    </div>

                    <div class="mt-5 max-h-[50vh] space-y-4 overflow-auto pr-2">
                        @forelse ($this->locations as $location)
                            <div class="list-card bg-white/60 dark:bg-white/5 dark:text-white flex-col">
                                @if ($location->photo_url)
                                    <div class="w-full overflow-hidden rounded-2xl">
                                        <img
                                            src="{{ $location->photo_url }}"
                                            alt="{{ $location->name }}"
                                            class="h-48 w-full object-cover"
                                        >
                                    </div>
                                @endif
                                <div class="rounded-2xl bg-slate-900/5 p-3 text-slate-700 dark:bg-white/10 dark:text-white">
                                    <livewire:shared.stock-badge
                                        :stock="$location->stock"
                                        :threshold="5"
                                        :key="'stock-public-'.$location->id"
                                    />
                                    <div class="mt-2 text-xs text-slate-500 dark:text-white/60">
                                        @if (isset($location->distance))
                                            {{ number_format($location->distance, 2) }} km
                                        @else
                                            {{ __('Lokasi baru') }}
                                        @endif
                                    </div>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="text-base font-semibold">{{ $location->name }}</div>
                                    <div class="mt-1 text-sm text-slate-500 dark:text-white/70">
                                <span class="line-clamp-2">
                                    {{ str($location->address)->limit(120) }}
                                </span>
                                    </div>
                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-slate-400 dark:text-white/60">
                                        <span>{{ __('Update') }} {{ $location->updated_at?->diffForHumans() }}</span>
                                        @if ($location->is_open)
                                            <flux:badge color="green">{{ __('Open') }}</flux:badge>
                                        @else
                                            <flux:badge color="zinc">{{ __('Closed') }}</flux:badge>
                                        @endif
                                    </div>
                                </div>

                                <flux:button
                                    variant="ghost"
                                    size="sm"
                                    type="button"
                                    class="rounded-full border border-slate-300 text-slate-700 dark:border-white/20 dark:text-white"
                                    data-route-lat="{{ $location->latitude }}"
                                    data-route-lng="{{ $location->longitude }}"
                                    data-route-name="{{ $location->name }}"
                                >
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-[0.2em]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.712 4.709a41.34 41.34 0 0 1 0 14.582c-.116.842-.84 1.459-1.69 1.459H8.978c-.85 0-1.574-.617-1.69-1.459a41.34 41.34 0 0 1 0-14.582c.116-.842.84-1.459 1.69-1.459h6.044c.85 0 1.574.617 1.69 1.459Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 8h4m-4 4h4m-4 4h4" />
                                        </svg>
                                        {{ __('Rute') }}
                                    </span>
                                </flux:button>
                            </div>
                        @empty
                            <div class="list-card justify-center text-center text-sm text-slate-500 dark:text-white/60">
                                {{ __('No locations found.') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
