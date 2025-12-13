<div class="flex flex-col gap-6">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <flux:heading size="xl">{{ __('My Locations') }}</flux:heading>
                <flux:subheading>{{ __('Manage your locations and stock') }}</flux:subheading>
            </div>

            <div class="flex gap-2">
                <div class="w-full md:w-[320px]">
                    <flux:input wire:model.live="search" :label="__('Search')" placeholder="name / address" />
                </div>
                <div class="self-end">
                    <flux:button variant="primary" href="{{ route('distributor.locations.create') }}" wire:navigate>{{ __('Add') }}</flux:button>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-950/40 text-zinc-500">
                        <tr>
                            <th class="text-left p-3">{{ __('Name') }}</th>
                            <th class="text-left p-3">{{ __('Stock') }}</th>
                            <th class="text-left p-3">{{ __('Open') }}</th>
                            <th class="text-right p-3">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach ($this->locations as $location)
                            <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40 transition">
                                <td class="p-3">
                                    <div class="font-medium">{{ $location->name }}</div>
                                    <div class="text-xs text-zinc-500 mt-0.5">{{ str($location->address)->limit(70) }}</div>
                                </td>
                                <td class="p-3 font-semibold">{{ number_format($location->stock) }}</td>
                                <td class="p-3">
                                    <flux:button variant="outline" size="sm" wire:click="toggleOpen({{ $location->id }})">
                                        {{ $location->is_open ? __('Open') : __('Closed') }}
                                    </flux:button>
                                </td>
                                <td class="p-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <flux:button variant="outline" size="sm" href="{{ route('distributor.locations.edit', $location) }}" wire:navigate>{{ __('Edit') }}</flux:button>
                                        <flux:button variant="danger" size="sm" wire:click="deleteLocation({{ $location->id }})">{{ __('Delete') }}</flux:button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {{ $this->locations->links() }}
            </div>
        </div>
</div>
