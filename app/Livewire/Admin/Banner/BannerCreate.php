<?php

namespace App\Livewire\Admin\Banner;

use App\Models\Banner;
use Livewire\Component;
use Livewire\WithFileUploads;

class BannerCreate extends Component
{
    use WithFileUploads;

    // Form Fields
    public $title, $type, $tagline, $description, $link, $alt;
    public $status = true;
    public $banner_video_status = false;
    
    // Date Fields for Offer Banner
    public $start_date;
    public $end_date;

    // Media Fields
    public $image;
    public $mobile_image;
    public $banner_video;

    /**
     * Validation Rules
     */
    protected function rules()
    {
        return [
            'title'       => 'nullable|string|max:255',
            'type'        => 'required|string|max:255',
            'tagline'     => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'link'        => 'nullable|url',
            'alt'         => 'nullable|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'banner_video' => 'nullable|mimes:mp4,avi,mov,mpeg,webm|max:50000',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
        ];
    }

    /**
     * Store Function
     */
    public function store()
    {
        $this->validate();

        try {
            // Handle file uploads
            $desktopPath = $this->image ? $this->image->store('banners', 'public') : null;
            $mobilePath = $this->mobile_image ? $this->mobile_image->store('banners', 'public') : null;
            $videoPath = $this->banner_video ? $this->banner_video->store('banners/videos', 'public') : null;

            // Create banner with all fields
            Banner::create([
                'title' => $this->title ?: null,
                'type' => $this->type,
                'tagline' => $this->tagline,
                'description' => $this->description,
                'link' => $this->link,
                'alt' => $this->alt ?: '',
                'status' => $this->status,
                'banner_video_status' => $this->banner_video_status,
                'image' => $desktopPath,
                'mob_banner_image' => $mobilePath,
                'banner_video' => $videoPath,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
            ]);

            // Reset form
            $this->reset([
                'title', 'type', 'tagline', 'description', 'link', 'alt', 
                'image', 'mobile_image', 'banner_video', 'start_date', 'end_date'
            ]);
            $this->status = true;
            $this->banner_video_status = false;

            $this->dispatch('show-toast', type: 'success', message: 'Banner Created Successfully');
            return redirect()->route('admin.banner');

        } catch (\Exception $e) {
            $this->dispatch('show-toast', type: 'error', message: 'Error: ' . $e->getMessage());
        }
    }

    public function removeImage()
    {
        $this->image = null;
    }

    public function removeMobileImage()
    {
        $this->mobile_image = null;
    }

    public function removeVideo()
    {
        $this->banner_video = null;
    }

    public function render()
    {
        return view('livewire.admin.banner.banner-create');
    }
}