<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Admin Dashboard') }}</flux:heading>
        <flux:button variant="outline" wire:click="refreshStats">{{ __('Refresh') }}</flux:button>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="p-4 rounded-2xl shadow-sm bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700">
            <div class="text-sm text-zinc-500">{{ __('Users') }}</div>
            <div class="text-3xl font-semibold mt-1">{{ number_format($totalUsers) }}</div>
            <div class="text-xs text-zinc-500 mt-2">{{ __('Distributors:') }} {{ number_format($totalDistributors) }}</div>
        </div>

        <div class="p-4 rounded-2xl shadow-sm bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700">
            <div class="text-sm text-zinc-500">{{ __('Locations') }}</div>
            <div class="text-3xl font-semibold mt-1">{{ number_format($totalLocations) }}</div>
            <div class="text-xs text-zinc-500 mt-2">{{ __('All distributor locations') }}</div>
        </div>

        <div class="p-4 rounded-2xl shadow-sm bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700">
            <div class="text-sm text-zinc-500">{{ __('Total Stock') }}</div>
            <div class="text-3xl font-semibold mt-1">{{ number_format($totalStock) }}</div>
            <div class="text-xs text-zinc-500 mt-2">{{ __('Units available across all locations') }}</div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <a class="rounded-2xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4 shadow-sm hover:shadow-md transition" href="{{ route('admin.users') }}" wire:navigate>
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-semibold">{{ __('Manage Users') }}</div>
                    <div class="text-sm text-zinc-500">{{ __('Assign roles, edit, delete') }}</div>
                </div>
                <flux:icon.users />
            </div>
        </a>

        <a class="rounded-2xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4 shadow-sm hover:shadow-md transition" href="{{ route('admin.locations') }}" wire:navigate>
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-semibold">{{ __('Locations') }}</div>
                    <div class="text-sm text-zinc-500">{{ __('View and manage locations') }}</div>
                </div>
                <flux:icon.map-pin />
            </div>
        </a>

        <a class="rounded-2xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4 shadow-sm hover:shadow-md transition" href="{{ route('admin.reports.export') }}" wire:navigate>
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-semibold">{{ __('Export Reports') }}</div>
                    <div class="text-sm text-zinc-500">{{ __('CSV stock report') }}</div>
                </div>
                <flux:icon.download />
            </div>
        </a>
    </div>
</div>
