<div class="w-full" wire:poll.60s="refresh" data-public-landing>
    <div class="max-w-6xl mx-auto px-4 py-6">
        <div class="flex items-start justify-between gap-3 mb-4">
            <div>
                <flux:heading size="lg">{{ __('Distribusi Gas Subsidi') }}</flux:heading>
                <flux:subheading>{{ __('Cari lokasi dan stok pangkalan terdekat') }}</flux:subheading>
            </div>
            <a href="{{ route('login') }}" class="text-sm text-primary underline underline-offset-4">{{ __('Login') }}</a>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-700 shadow-sm">
                <div class="h-[40vh] md:h-[45vh] lg:h-[50vh] w-full" data-public-map>
                    <div class="w-full h-full" data-public-map-canvas wire:ignore></div>
                </div>
            </div>

            <div class="rounded-2xl bg-white/95 dark:bg-zinc-900/95 border border-zinc-200 dark:border-zinc-700 shadow-md backdrop-blur p-4">
                <div class="grid gap-3">
                    <flux:input wire:model.live="q" :label="__('Search')" placeholder="nama / alamat" />
                </div>

                <div class="mt-4 max-h-[45vh] overflow-auto space-y-3">
                    @forelse ($this->locations as $location)
                        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-sm p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="font-semibold">{{ $location->name }}</div>
                                    <div class="text-sm text-zinc-500 mt-1">{{ str($location->address)->limit(120) }}</div>
                                    <div class="text-xs text-zinc-500 mt-1">{{ __('Last updated:') }} {{ $location->updated_at?->diffForHumans() }}</div>
                                </div>

                                <div class="text-right">
                                    <livewire:shared.stock-badge :stock="$location->stock" :threshold="5" :key="'stock-public-'.$location->id" />
                                    <div class="text-xs text-zinc-500 mt-1">
                                        @if (isset($location->distance))
                                            {{ number_format($location->distance, 2) }} km
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 flex items-center justify-between">
                                <div>
                                    @if ($location->is_open)
                                        <flux:badge color="green">{{ __('Open') }}</flux:badge>
                                    @else
                                        <flux:badge color="zinc">{{ __('Closed') }}</flux:badge>
                                    @endif
                                </div>

                                <flux:button
                                    variant="outline"
                                    size="sm"
                                    type="button"
                                    data-route-lat="{{ $location->latitude }}"
                                    data-route-lng="{{ $location->longitude }}"
                                    data-route-name="{{ $location->name }}"
                                >
                                    {{ __('Petunjuk Arah') }}
                                </flux:button>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-sm p-4">
                            <div class="text-sm text-zinc-500">
                                {{ __('No locations found.') }}
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
