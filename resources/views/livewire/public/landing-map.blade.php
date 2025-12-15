<div wire:poll.60s="refresh" data-public-landing>
    <div class="relative isolate overflow-hidden">
        <div class="absolute inset-0 -z-10 h-full w-full bg-slate-50 dark:bg-slate-900 bg-[radial-gradient(circle_at_top,_rgba(14,165,233,0.1),_transparent_40%),radial-gradient(circle_at_bottom,_rgba(59,130,246,0.1),_transparent_50%)]"></div>

        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
            <div class="grid items-center gap-8 lg:grid-cols-2 lg:gap-12">
                <div class="flex flex-col">
                    <div class="inline-flex items-center gap-x-2 rounded-full bg-blue-100/70 px-3 py-1 text-xs font-semibold text-blue-800 dark:bg-blue-900/70 dark:text-blue-200">
                        <x-heroicon-o-clock class="size-3.5" />
                        {{ __('Realtime supply intelligence') }}
                    </div>
                    <h1 class="mt-6 text-4xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-5xl">
                        {{ __('Distribusi Gas Subsidi') }}
                    </h1>
                    <p class="mt-4 text-lg leading-8 text-slate-600 dark:text-slate-400">
                        {{ __('Pantau stok pangkalan terdekat, temukan jalur distribusi tercepat, dan bantu masyarakat mengakses energi dengan mudah.') }}
                    </p>
                    <div class="mt-8 flex items-center gap-x-4">
                        <flux:button variant="primary" href="{{ route('login') }}" wire:navigate>
                            <span class="inline-flex items-center gap-2">
                                <x-heroicon-o-arrow-right-on-rectangle class="size-5" />
                                {{ __('Login') }}
                            </span>
                        </flux:button>
                        <div class="inline-flex items-center gap-2 text-slate-700 dark:text-slate-300">
                            <span class="text-2xl font-semibold">{{ number_format($this->locations->count()) }}</span>
                            <span class="text-sm">{{ __('lokasi aktif') }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white/60 p-4 shadow-lg backdrop-blur-xl dark:border-slate-800 dark:bg-slate-900/60">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-semibold uppercase tracking-widest text-slate-500 dark:text-slate-400">{{ __('Peta Dinamis') }}</div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-900/70 dark:text-emerald-200">
                            <span class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ __('Siaran langsung') }}
                        </span>
                    </div>
                    <div class="mt-4 h-[60vh] w-full rounded-xl border border-slate-200 dark:border-slate-800" data-public-map>
                        <div class="h-full w-full rounded-xl" data-public-map-canvas wire:ignore></div>
                    </div>
                </div>
            </div>

            <div class="mt-12 rounded-2xl border border-slate-200 bg-white/60 p-4 shadow-lg backdrop-blur-xl dark:border-slate-800 dark:bg-slate-900/60 sm:p-6 lg:p-8">
                <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <div class="text-sm font-semibold uppercase tracking-widest text-slate-500 dark:text-slate-400">{{ __('Cari Lokasi') }}</div>
                        <h2 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">{{ __('Pangkalan terdekat') }}</h2>
                    </div>
                    <div class="w-full max-w-sm">
                        <flux:input wire:model.live="q" :label="__('Search')" placeholder="Cari berdasarkan nama atau alamat..." />
                    </div>
                </div>

                <div class="mt-6">
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @forelse ($this->locations as $location)
                            <div class="flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                                @if ($location->photo_url)
                                    <img src="{{ $location->photo_url }}" alt="{{ $location->name }}" class="h-40 w-full object-cover">
                                @else
                                    <div class="flex h-40 w-full items-center justify-center bg-slate-100 dark:bg-slate-800">
                                        <x-heroicon-o-photo class="size-12 text-slate-400 dark:text-slate-600" />
                                    </div>
                                @endif
                                <div class="flex flex-1 flex-col p-4 md:p-5">
                                    <div class="flex items-start justify-between">
                                        <livewire:shared.stock-badge
                                            :stock="$location->stock"
                                            :threshold="5"
                                            :key="'stock-public-'.$location->id"
                                        />
                                        @if (isset($location->distance))
                                            <div class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ number_format($location->distance, 2) }} km</div>
                                        @endif
                                    </div>

                                    <h3 class="mt-2 text-lg font-bold text-slate-900 dark:text-white">{{ $location->name }}</h3>
                                    <p class="mt-1 line-clamp-2 text-sm text-slate-600 dark:text-slate-400">{{ $location->address }}</p>

                                    <div class="mt-4 flex items-center justify-between gap-2 text-xs text-slate-500 dark:text-slate-400">
                                        <span>{{ __('Update') }} {{ $location->updated_at?->diffForHumans() }}</span>
                                        @if ($location->is_open)
                                            <flux:badge color="green">{{ __('Buka') }}</flux:badge>
                                        @else
                                            <flux:badge color="zinc">{{ __('Tutup') }}</flux:badge>
                                        @endif
                                    </div>
                                </div>
                                <div class="border-t border-slate-200 p-4 dark:border-slate-800">
                                    <flux:button
                                        variant="outline"
                                        size="sm"
                                        type="button"
                                        class="w-full"
                                        data-route-lat="{{ $location->latitude }}"
                                        data-route-lng="{{ $location->longitude }}"
                                        data-route-name="{{ $location->name }}"
                                    >
                                        <span class="inline-flex items-center gap-2 font-semibold">
                                            <x-heroicon-o-map class="size-4" />
                                            {{ __('Lihat Rute') }}
                                        </span>
                                    </flux:button>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full rounded-lg border-2 border-dashed border-slate-200 bg-slate-50 p-12 text-center dark:border-slate-800 dark:bg-slate-900/50">
                                <x-heroicon-o-map-pin class="mx-auto size-12 text-slate-400" />
                                <h3 class="mt-2 text-sm font-medium text-slate-900 dark:text-white">{{ __('Lokasi tidak ditemukan') }}</h3>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Coba ubah kata kunci pencarian Anda.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
