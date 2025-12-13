<div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Distributor Dashboard') }}</flux:heading>
                <flux:subheading>{{ __('Manage your locations and stock') }}</flux:subheading>
            </div>
            <div class="flex gap-2">
                <flux:button variant="outline" href="{{ route('distributor.locations') }}" wire:navigate>{{ __('Locations') }}</flux:button>
                <flux:button variant="primary" href="{{ route('distributor.locations.create') }}" wire:navigate>{{ __('Add Location') }}</flux:button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="p-4 rounded-2xl shadow-sm bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700">
                <div class="text-sm text-zinc-500">{{ __('Your Locations') }}</div>
                <div class="text-3xl font-semibold mt-1">{{ number_format($totalLocations) }}</div>
            </div>

            <div class="p-4 rounded-2xl shadow-sm bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700">
                <div class="text-sm text-zinc-500">{{ __('Total Stock') }}</div>
                <div class="text-3xl font-semibold mt-1">{{ number_format($totalStock) }}</div>
            </div>

            <div class="p-4 rounded-2xl shadow-sm bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700">
                <div class="text-sm text-zinc-500">{{ __('Low Stock (<= 5)') }}</div>
                <div class="text-3xl font-semibold mt-1">{{ number_format($lowStockCount) }}</div>
            </div>
        </div>
</div>
