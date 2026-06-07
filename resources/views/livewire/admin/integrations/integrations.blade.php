<div>
    <div class="dashboard-page-content">
        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $sections = [
                'Google Integrations' => [
                    'google_console' => ['label' => 'Google Search Console', 'col' => '4', 'type' => 'textarea', 'rows' => 3],
                    'google_analytics' => ['label' => 'Google Analytics', 'col' => '4', 'type' => 'textarea', 'rows' => 3],
                    'google_tag_manager' => ['label' => 'Google Tag Manager', 'col' => '4', 'type' => 'textarea', 'rows' => 3],
                ],
                'SEO Tools' => [
                    'sitemap_submission' => ['label' => 'Sitemap URL / XML Content', 'col' => '6', 'type' => 'textarea', 'rows' => 4],
                    'robots_txt' => ['label' => 'Robots.txt Content', 'col' => '6', 'type' => 'textarea', 'rows' => 4],
                    'meta_tags' => ['label' => 'Meta Tags', 'col' => '4', 'type' => 'textarea', 'rows' => 4],
                    'schema_markup' => ['label' => 'Schema Markup', 'col' => '4', 'type' => 'textarea', 'rows' => 4],
                    'on_page_scripts' => ['label' => 'On-Page Scripts', 'col' => '4', 'type' => 'textarea', 'rows' => 4],
                ],
                'Chat & Communication' => [
                    'live_chat' => ['label' => 'Live Chat Widget Script', 'col' => '6', 'type' => 'textarea', 'rows' => 5],
                    'chatbot_scripts' => ['label' => 'Chatbot Scripts', 'col' => '6', 'type' => 'textarea', 'rows' => 5],
                    'messenger_chat' => ['label' => 'Messenger Integration Script', 'col' => '6', 'type' => 'textarea', 'rows' => 5],
                    'whatsapp_chat' => ['label' => 'WhatsApp Chat Script', 'col' => '6', 'type' => 'textarea', 'rows' => 5],
                ],
                'Tracking & Marketing' => [
                    'facebook_pixel' => ['label' => 'Facebook Pixel Script', 'col' => '4', 'type' => 'textarea', 'rows' => 4],
                    'conversion_tracking' => ['label' => 'Conversion Tracking Codes', 'col' => '4', 'type' => 'textarea', 'rows' => 4],
                    'remarketing_tags' => ['label' => 'Remarketing Tags', 'col' => '4', 'type' => 'textarea', 'rows' => 4],
                ],
            ];
        @endphp

        <div class="row mb-9 align-items-center">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fs-4 mb-0">SEO Tools & Integrations</h2>
                    <p class="text-muted mb-0">Manage external services connected to your website.</p>
                </div>
                <div wire:loading class="text-primary small">
                    <span class="spinner-border spinner-border-sm me-1"></span> Auto-saving...
                </div>
            </div>
        </div>

        <div class="card mb-8 rounded-4 shadow-sm border-0">
            <div class="card-body p-7">
                @foreach($sections as $title => $fields)
                    <div class="mb-10">
                        <div class="d-flex align-items-center mb-6">
                            <h4 class="fs-6 mb-0 text-uppercase fw-bold">{{ $title }}</h4>
                            <div class="flex-grow-1 ms-4 border-bottom"></div>
                        </div>

                        <div class="row g-4">
                            @foreach($fields as $key => $config)
                                <div class="col-md-{{ $config['col'] }}">
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold small text-muted text-uppercase mb-2">
                                            {{ $config['label'] }}
                                        </label>
                                        
                                        @if($config['type'] == 'textarea')
                                            <textarea 
                                                wire:model.live.debounce.1000ms="{{ $key }}" 
                                                class="form-control bg-light" 
                                                rows="{{ $config['rows'] ?? 4 }}"
                                                placeholder="Enter {{ strtolower($config['label']) }}..."></textarea>
                                        @else
                                            <input 
                                                type="text" 
                                                wire:model.live.debounce.1000ms="{{ $key }}" 
                                                class="form-control bg-light"
                                                placeholder="Enter {{ strtolower($config['label']) }}...">
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <!-- WhatsApp Section -->
                <div class="border-top pt-6 mt-4">
                    <div class="mb-4">
                        <h4 class="fs-6 fw-bold mb-1">WhatsApp Channel</h4>
                        <p class="text-muted small">Configure WhatsApp chat button for customer support.</p>
                    </div>

                    <div class="row">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold small text-muted text-uppercase mb-2">Phone Number</label>
                            
                            <div class="input-group border rounded-3 bg-white shadow-sm position-relative" 
                                x-data="{ open: false }">
                                
                                <div class="position-relative">
                                    <button class="btn btn-light border-0 border-end rounded-0 d-flex align-items-center justify-content-center px-3 shadow-none" 
                                            type="button" 
                                            @click="open = !open" 
                                            @click.away="open = false"
                                            style="height: 48px; min-width: 100px;">
                                        <span class="fw-bold">{{ $country_code ?: '+92' }}</span>
                                        <i class="bi bi-chevron-down ms-2 small opacity-50"></i>
                                    </button>
                                    
                                    <div class="shadow-lg border-0 py-0 overflow-hidden bg-white rounded-3" 
                                        x-show="open" 
                                        x-cloak
                                        x-transition
                                        style="width: 300px; max-height: 400px; overflow-y: auto; position: absolute; bottom: 50px; left: 0; z-index: 9999; display: none;">
                                        
                                        <div class="p-3 bg-white sticky-top border-bottom">
                                            <div class="text-uppercase fw-bold text-muted mb-2 small">Search Country</div>
                                            <input type="text" 
                                                wire:model.live.debounce.300ms="searchCountry" 
                                                class="form-control form-control-sm" 
                                                placeholder="Type name or code..." 
                                                @click.stop="">
                                        </div>
                                        
                                        <div style="max-height: 350px; overflow-y: auto;">
                                            @forelse($countries as $country)
                                                <button type="button" 
                                                        wire:click="setCountry('{{ $country->name }}', '{{ $country->code }}')"
                                                        @click="open = false"
                                                        class="dropdown-item d-flex align-items-center justify-content-between py-2 px-3 border-bottom w-100 text-start">
                                                    <span class="small">{{ $country->name }}</span>
                                                    <span class="badge rounded-1 px-2 py-1" style="background-color: #25d366; color: #fff; font-size: 11px;">
                                                        {{ $country->code }}
                                                    </span>
                                                </button>
                                            @empty
                                                <div class="p-3 text-center text-muted small">No results</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                <input type="text" 
                                    wire:model.live="phone_number" 
                                    class="form-control border-0 bg-white shadow-none" 
                                    placeholder="XXXX XXXX XX" 
                                    style="height: 48px;">
                                
                                <div class="bg-light border-start d-flex align-items-center px-3" style="height: 48px;">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" 
                                            type="checkbox" 
                                            wire:model.live="whatsapp_on" 
                                            role="switch">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>