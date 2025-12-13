<?php

namespace App\Livewire\Public;

use App\Models\Location;
use Livewire\Component;

class LandingMap extends Component
{
    public string $q = '';

    public function updatedQ(): void
    {
        $this->broadcastLocations();
    }

    public function refresh(): void
    {
        $this->broadcastLocations();
    }

    public function getLocationsProperty()
    {
        $query = Location::query();

        return $query
            ->when($this->q !== '', function ($q) {
                $q->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->q.'%')
                        ->orWhere('address', 'like', '%'.$this->q.'%');
                });
            })
            ->orderByDesc('updated_at')
            ->limit(30)
            ->get();
    }

    private function broadcastLocations(): void
    {
        $locations = $this->locations->map(function ($location) {
            return [
                'id' => $location->id,
                'name' => $location->name,
                'address' => $location->address,
                'latitude' => (float) $location->latitude,
                'longitude' => (float) $location->longitude,
                'stock' => (int) $location->stock,
                'is_open' => (bool) $location->is_open,
                'distance' => isset($location->distance) ? (float) $location->distance : null,
                'updated_at' => optional($location->updated_at)->toISOString(),
                'updated_human' => optional($location->updated_at)?->diffForHumans(),
            ];
        })->values()->all();

        $this->dispatch('public-locations-updated', locations: $locations, center: [
            'latitude' => null,
            'longitude' => null,
        ]);
    }

    public function mount(): void
    {
        $this->broadcastLocations();
    }

    public function render()
    {
        return view('livewire.public.landing-map')
            ->layout('components.layouts.public', ['title' => __('Distribusi Gas Subsidi')]);
    }
}
