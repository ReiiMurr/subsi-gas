<div class="flex flex-col gap-6">
        <div>
            <flux:heading size="xl">{{ __('Export Reports') }}</flux:heading>
            <flux:subheading>{{ __('Download a CSV of current stock per location') }}</flux:subheading>
        </div>

        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-sm p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="text-sm text-zinc-500">
                    {{ __('CSV includes distributor, coordinates, and stock fields.') }}
                </div>

                <flux:button variant="primary" wire:click="download">
                    {{ __('Download CSV') }}
                </flux:button>
            </div>
        </div>
</div>
