<?php

namespace App\Livewire\Admin\Integrations;

use Livewire\Component;
use App\Models\Integration;
use App\Models\Country;

class Integrations extends Component
{
    // Google & SEO
    public $google_console, $google_analytics, $google_tag_manager;
    public $sitemap_submission, $robots_txt, $meta_tags, $schema_markup, $on_page_scripts;
    
    // Chat & Communication
    public $live_chat, $whatsapp_chat, $messenger_chat, $chatbot_scripts;
    
    // Tracking
    public $facebook_pixel, $conversion_tracking, $remarketing_tags;
    
    // WhatsApp
    public $country_code, $phone_number, $whatsapp_on;
    public $searchCountry = '';
    public $selectedCountryName = '';

    public function mount()
    {
        $data = Integration::first();
        if ($data) {
            // Fill all properties
            $this->google_console = $data->google_console;
            $this->google_analytics = $data->google_analytics;
            $this->google_tag_manager = $data->google_tag_manager;
            $this->sitemap_submission = $data->sitemap_submission;
            $this->robots_txt = $data->robots_txt;
            $this->meta_tags = $data->meta_tags;
            $this->schema_markup = $data->schema_markup;
            $this->on_page_scripts = $data->on_page_scripts;
            $this->live_chat = $data->live_chat;
            $this->whatsapp_chat = $data->whatsapp_chat;
            $this->messenger_chat = $data->messenger_chat;
            $this->chatbot_scripts = $data->chatbot_scripts;
            $this->facebook_pixel = $data->facebook_pixel;
            $this->conversion_tracking = $data->conversion_tracking;
            $this->remarketing_tags = $data->remarketing_tags;
            $this->country_code = $data->country_code;
            $this->phone_number = $data->phone_number;
            $this->whatsapp_on = (bool) $data->whatsapp_on;
            
            // Look up country name
            $current = Country::where('code', $this->country_code)->first();
            $this->selectedCountryName = $current ? $current->name : 'Select Country';
        } else {
            $this->country_code = "+92";
            $this->selectedCountryName = "Pakistan";
            $this->whatsapp_on = false;
        }
    }

    public function updated($propertyName)
    {
        // Skip searchCountry field from auto-save
        if ($propertyName === 'searchCountry') return;
        
        // Save to database
        $data = Integration::first() ?: new Integration();
        $data->{$propertyName} = $this->{$propertyName};
        $data->save();
        
        // Dispatch toast notification
        $this->dispatch('show-toast', 
            type: 'success', 
            message: ucfirst(str_replace('_', ' ', $propertyName)) . ' updated!'
        );
    }

    public function setCountry($name, $code)
    {
        $this->country_code = $code;
        $this->selectedCountryName = $name;
        $this->searchCountry = '';
        
        $data = Integration::first() ?: new Integration();
        $data->country_code = $this->country_code;
        $data->save();
        
        $this->dispatch('show-toast', 
            type: 'success', 
            message: 'Country code updated to ' . $code . ' (' . $name . ')'
        );
    }

    public function render()
    {
        $countries = Country::where('status', 1)
            ->when($this->searchCountry, function($query) {
                $query->where('name', 'like', '%' . $this->searchCountry . '%')
                      ->orWhere('code', 'like', '%' . $this->searchCountry . '%');
            })
            ->orderBy('name', 'asc')
            ->get();

        return view('livewire.admin.integrations.integrations', [
            'countries' => $countries
        ])->layout('layouts.admin');
    }
}