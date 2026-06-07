<?php

namespace App\Livewire\Admin\SocialLinks;

use Livewire\Component;
use App\Models\SocialLink;

class SocialLinksEdit extends Component
{
    public $linkId; // Renamed from $id to avoid conflict with internal Livewire properties
    public $platform;
    public $url;
    public $icon_class;
    public $is_active;

    // The mount method runs once when the component is first loaded
    public function mount($id)
    {
        $link = SocialLink::findOrFail($id);
        
        $this->linkId = $link->id;
        $this->platform = $link->platform;
        $this->url = $link->url;
        $this->icon_class = $link->icon_class;
        $this->is_active = $link->is_active;
    }

    public function update()
    {
        $this->validate([
            'platform' => 'required|string|max:255',
            'url' => 'required|url',
        ]);

        $icons = [
            'facebook'  => 'fab fa-facebook',
            'instagram' => 'fab fa-instagram',
            'twitter'   => 'fab fa-twitter',
            'x'         => 'fab fa-twitter',
            'linkedin'  => 'fab fa-linkedin',
            'youtube'   => 'fab fa-youtube',
            'tiktok'    => 'fab fa-tiktok',
            'whatsapp'  => 'fab fa-whatsapp',
        ];

        $key = strtolower(trim($this->platform));
        
        if (array_key_exists($key, $icons)) {
            $this->icon_class = $icons[$key];
        }

        $link = SocialLink::findOrFail($this->linkId);
        $link->update([
            'platform' => $this->platform,
            'url' => $this->url,
            'icon_class' => $this->icon_class,
            'is_active' => $this->is_active,
        ]);

        $this->dispatch('show-toast', type: 'success', message: 'Social Link Updated Successfully!');
        
        return redirect()->route('admin.social-links.index');
    }

    public function render()
    {
        return view('livewire.admin.social-links.social-links-edit');
    }
}