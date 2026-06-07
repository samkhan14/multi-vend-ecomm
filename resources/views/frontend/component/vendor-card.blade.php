@foreach ($vendors as $index => $vendor)
    @php
        $previewProducts = $vendor->products()->where('status', 1)->latest()->take(4)->get();
        $totalProducts = $vendor->products()->where('status', 1)->count();
        
        $profileImage = $vendor->user->image ?? null;
        $vendorName = $vendor->store_name ?? $vendor->user->name ?? 'Vendor';
        
        $bgColors = [
            '#fee7e7', // Light Pink
            '#fff3d8', // Light Yellow  
            '#e1f7e3', // Light Green
            '#e6f0fa', // Light Blue
            '#f3e8fe', // Light Purple
            '#fee9d1', // Light Peach
            '#d9f0f7', // Light Cyan
            '#fce4f0', // Light Rose
        ];
        
        $bgColor = $bgColors[$index % count($bgColors)];
    @endphp

    <div class="col-xxl-4 col-lg-4 col-sm-6 mb-4">
        <div class="vendor-card text-center" style="background: {{ $bgColor }};">
            <div class="vendor-logo">
                @if($profileImage)
                    <img src="{{ asset('storage/' . $profileImage) }}" 
                         alt="{{ $vendorName }}"
                         class="vendor-profile-img">
                @else
                    <span>{{ substr($vendorName, 0, 2) }}</span>
                @endif
            </div>

            <h5 class="vendor-title">
                <a href="{{ route('vendor.show', $vendor->store_slug) }}">
                    {{ $vendorName }}
                </a>
            </h5>

            <p class="text-muted small delivery-time">
                <i class="ph ph-home me-1"></i>  {{ $vendor->city }} 
            </p>

            @if($totalProducts > 0)
                <a href="{{ route('vendor.show', $vendor->store_slug) }}" class="offer-badge text-decoration-none">
                    <i class="ph ph-package me-1"></i> {{ $totalProducts }} Products Available
                </a>
            @else
                <span class="offer-badge">
                    <i class="ph ph-package me-1"></i> No Products Yet
                </span>
            @endif

            @if($previewProducts->count() > 0)
                <div class="d-flex justify-content-center gap-2 mt-3">
                    @foreach($previewProducts as $product)
                        <div class="preview-circle">
                            @if($product->thumbnail_image)
                                <img src="{{ asset('storage/' . $product->thumbnail_image) }}" 
                                     alt="{{ $product->product_name }}">
                            @else
                                <span style="font-size: 10px;">P</span>
                            @endif
                        </div>
                    @endforeach
                    
                    @if($totalProducts > 4)
                        <div class="preview-circle more">
                            +{{ $totalProducts - 4 }}
                        </div>
                    @endif
                </div>
            @else
                <div class="text-muted small mt-3">
                    <i class="ph ph-image"></i> No products to display
                </div>
            @endif
        </div>
    </div>
@endforeach