<?php

namespace App\Livewire\Admin\SocialLinks;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SocialLink;

class SocialLinks extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleStatus($id)
    {
        $link = SocialLink::find($id);
        if ($link) {
            $link->is_active = !$link->is_active;
            $link->save();
            $this->dispatch('show-toast', type: 'success', message: 'Status Updated Successfully!');
        }
    }

    public function delete($id)
    {
        SocialLink::find($id)->delete();
        $this->dispatch('show-toast', type: 'success', message: 'Social Link Deleted!');
    }

    public function render()
{
    $links = SocialLink::query()
        ->where('platform', 'like', '%' . $this->search . '%')
        ->orderBy('id', 'desc')
        ->paginate($this->perPage);

    return view('livewire.admin.social-links.social-links', [
        'links' => $links
    ]);
}
}