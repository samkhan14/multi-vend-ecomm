<?php

namespace App\Livewire\Admin\About;

use Livewire\Component;
use App\Models\AboutContent;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class About extends Component
{
    use WithFileUploads;

    public $title, $content, $aboutId, $image, $current_image;

    protected $rules = [
        'title' => 'required|min:5',
        'content' => 'required',
        'image' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,webp',
    ];

    public function mount()
    {
        $about = AboutContent::first();
        if ($about) {
            $this->aboutId = $about->id;
            $this->title = $about->title;
            $this->content = $about->content;
            $this->current_image = $about->image;
        }
    }

    public function save()
    {
        $this->validate();

        $imagePath = $this->current_image;

        if ($this->image) {
            // Delete old image if exists (same as category code)
            if ($this->current_image && Storage::disk('public')->exists($this->current_image)) {
                Storage::disk('public')->delete($this->current_image);
            }

          
            $imageName = time() . '.' . $this->image->getClientOriginalExtension();
            $imagePath = 'about-images/' . $imageName;
            $this->image->storeAs('about-images', $imageName, 'public');
        }

        // Update or create about content
        $about = AboutContent::updateOrCreate(
            ['id' => $this->aboutId],
            [
                'title' => $this->title, 
                'content' => $this->content, 
                'image' => $imagePath
            ]
        );

        $this->aboutId = $about->id;
        $this->current_image = $imagePath;
        $this->image = null;
        
        $this->dispatch('show-toast', 
            type: 'success', 
            message: 'About Page updated successfully!'
        );
    }

    public function render()
    {
        return view('livewire.admin.about.about')->layout('layouts.admin');
    }
}