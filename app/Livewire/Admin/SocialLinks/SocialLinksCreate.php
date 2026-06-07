<?php

namespace App\Livewire\Admin\SocialLinks;

use Livewire\Component;
use App\Models\SocialLink;

class SocialLinksCreate extends Component
{
    public $platform;
    public $url;
    public $icon_class;
    public $is_active = true;

    protected $rules = [
        'platform' => 'required|string|max:255',
        'url' => 'required|url',
        'icon_class' => 'required|string',
    ];

    public function save()
    {
            $this->validate([
            'platform' => 'required',
            'url' => 'required|url',
        ]);

        $icons = [
            'facebook'  => 'fab fa-facebook',
            'instagram' => 'fab fa-instagram',
            'twitter'   => 'fab fa-twitter',
            'linkedin'  => 'fab fa-linkedin',
            'youtube'   => 'fab fa-youtube',
            'tiktok'    => 'fab fa-tiktok',
            'whatsapp'  => 'fab fa-whatsapp',
        ];

        $platformKey = strtolower($this->platform);

        $finalIcon = $icons[$platformKey] ?? 'fas fa-share-alt';

        SocialLink::create([
            'platform' => $this->platform,
            'url' => $this->url,
            'icon_class' => $finalIcon,
            'is_active' => $this->is_active,
        ]);

        $this->dispatch('show-toast', type: 'success', message: 'Social Link Created Successfully!');
        return redirect()->route('admin.social-links.index');
    }

    public function render()
    {
        return view('livewire.admin.social-links.social-links-create');
    }
}