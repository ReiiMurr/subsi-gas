<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Password;
use Livewire\Component;
use Livewire\WithPagination;

class DistributorList extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function resendInvite(int $userId): void
    {
        $user = User::query()
            ->where('role', 'distributor')
            ->findOrFail($userId);

        Password::broker()->sendResetLink(['email' => $user->email]);

        session()->flash('status', 'Undangan berhasil dikirim ulang.');
    }

    public function toggleActive(int $userId): void
    {
        $user = User::query()
            ->where('role', 'distributor')
            ->findOrFail($userId);

        $user->is_active = ! (bool) $user->is_active;
        $user->save();

        session()->flash('status', $user->is_active ? 'Distributor diaktifkan.' : 'Distributor dinonaktifkan.');
    }

    public function deleteDistributor(int $userId): void
    {
        $user = User::query()
            ->where('role', 'distributor')
            ->findOrFail($userId);

        $user->delete();

        session()->flash('status', 'Distributor berhasil dihapus.');

        $this->resetPage();
    }

    public function getDistributorsProperty()
    {
        return User::query()
            ->where('role', 'distributor')
            ->when($this->search !== '', function ($q) {
                $q->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('phone', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.admin.distributor-list')
            ->layout('components.layouts.app', ['title' => __('Distributor Management')]);
    }
}
