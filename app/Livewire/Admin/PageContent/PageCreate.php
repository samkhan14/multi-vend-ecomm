<?php

namespace App\Livewire\Admin\PageContent;

use App\Models\PageContent;
use Livewire\Component;

class PageCreate extends Component
{
    public $policy_name = '';
    public $title = '';
    public $slug = '';
    public $content = '';
    public $status = 1; // Default: active

    protected $listeners = ['update-quill-content' => 'updateQuillContent'];

    public function updateQuillContent($model, $content)
    {
        if ($model === 'content') {
            $this->content = $content;
        }
    }

    public function updatedTitle($value)
    {
        $this->slug = str()->slug($value);
    }

    // Critical Fix: Handle checkbox state
    public function updatedStatus($value)
    {
        // Jab bhi status update ho, ensure it's 1 or 0
        $this->status = $value ? 1 : 0;
    }

    public function store()
    {
        $this->validate([
            'policy_name' => 'required|string|max:100',
            'slug' => 'required|string|max:255|unique:page_contents,slug',
            'content' => 'required|string|min:10',
            'status' => 'boolean',
        ]);

        PageContent::create([
            'policy_name' => $this->policy_name,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => clean($this->content), 
            'status' => $this->status ? 1 : 0,
        ]);

        $this->reset();
        $this->dispatch('reset-quill');
        $this->dispatch('show-toast', type: 'success', message: 'Page content created successfully!');
    }

    public function render()
    {
        return view('livewire.admin.page-content.page-create');
    }
}