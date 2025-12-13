<?php

namespace App\Livewire\Distributor;

use App\Models\Location;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class LocationTable extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function toggleOpen(int $locationId): void
    {
        $location = Location::query()->where('distributor_id', auth()->id())->findOrFail($locationId);
        $this->authorize('update', $location);

        $location->is_open = ! $location->is_open;
        $location->save();
    }

    public function deleteLocation(int $locationId): void
    {
        $location = Location::query()->where('distributor_id', auth()->id())->findOrFail($locationId);
        $this->authorize('delete', $location);

        $location->delete();
        $this->resetPage();
    }

    public function getLocationsProperty()
    {
        return Location::query()
            ->where('distributor_id', auth()->id())
            ->when($this->search !== '', function ($q) {
                $q->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('address', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.distributor.location-table')
            ->layout('components.layouts.app', ['title' => __('My Locations')]);
    }
}
