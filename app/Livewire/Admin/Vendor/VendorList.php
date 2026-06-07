<?php

namespace App\Livewire\Admin\Vendor;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;

class VendorList extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 20;
    public $statusFilter = 'all';
    public $selectedUser = null;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $vendors = User::role('Vendor')
            ->with('vendor')
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                      ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->when($this->statusFilter !== 'all', function($query) {
                $query->where('user_status', $this->statusFilter === 'active' ? 1 : 0);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.vendor.vendor-list', [
            'customerUser' => $vendors
        ]);
    }

    public function toggleStatus($userId)
    {
        $user = User::findOrFail($userId);
        $user->user_status = !$user->user_status;
        $user->save();
    }

    public function viewUserDetails($userId)
    {
        $this->selectedUser = User::with('vendor')->findOrFail($userId);
    }

    public function gotoPage($page)
    {
        $this->setPage($page);
    }

    public function nextPage()
    {
        $this->nextPage();
    }

    public function previousPage()
    {
        $this->previousPage();
    }
}
