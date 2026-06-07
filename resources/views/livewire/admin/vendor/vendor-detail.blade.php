<div>
    <div class="dashboard-page-content">
        <div class="card mb-5 rounded-4 card-brand">
            <div class="card-header p-15" style="background-color: #B88539"></div>
            <div class="card-body p-7">
                <div class="row">
                    <div class="col-xl col-lg flex-grow-0 mb-xl-0 mb-7" style="flex-basis: 230px">
                        <div class="img-thumbnail shadow w-100 bg-body position-relative text-center mt-n20 py-3 px-4">
                            @if($vendor->user && $vendor->user->image)
                                <img class="lazy-image img-fluid" src="{{ asset('storage/' . $vendor->user->image) }}"
                                    alt="{{ $vendor->store_name }} Logo" width="180" height="180">
                            @else
                                <img class="lazy-image img-fluid" src="{{ asset('assets/images/dashboard/avatar-1.png') }}"
                                    alt="{{ $vendor->store_name }} Logo" width="180" height="180">
                            @endif
                        </div>
                    </div>

                    <div class="col-xl col-lg">
                        <h3 class="fs-4 mb-0">{{ $vendor->store_name ?? 'N/A' }}</h3>
                        <p>{{ $vendor->address ?? 'N/A' }}, {{ $vendor->city ?? 'N/A' }}, {{ $vendor->country ?? 'N/A' }}</p>
                        @if($vendor->user)
                            <p class="text-muted">Owner: {{ $vendor->user->name ?? 'N/A' }}</p>
                        @endif
                    </div>

                    <div class="col-xl-4 text-xl-end">
                        <div class="d-inline-block my-4">
                            <span class="badge bg-{{ $vendor->status == 1 ? 'success' : 'danger' }} fs-13px px-3 py-2">
                                {{ $vendor->status == 1 ? 'Approved' : 'Pending' }}
                            </span>
                            @if($vendor->is_block ?? false)
                                <span class="badge bg-danger fs-13px px-3 py-2 ms-2">
                                    Blocked
                                </span>
                            @endif
                        </div>
                        <div class="mt-3">
                            @if($vendor->status == 0)
                                <button wire:click="approveVendor" class="btn btn-success btn-sm me-2">
                                    <i class="fas fa-check me-1"></i> Approve
                                </button>
                                <button wire:click="rejectVendor" class="btn btn-danger btn-sm">
                                    <i class="fas fa-times me-1"></i> Reject
                                </button>
                            @else
                                @if($vendor->is_block ?? false)
                                    <button wire:click="unblockVendor" class="btn btn-warning btn-sm">
                                        <i class="fas fa-unlock me-1"></i> Unblock
                                    </button>
                                @else
                                    <button wire:click="blockVendor" class="btn btn-danger btn-sm">
                                        <i class="fas fa-ban me-1"></i> Block
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>

                </div>

                <hr class="my-7">
                <div class="row">
                    <div class="col-md-12 col-lg-4 col-xl-2 mb-8 mb-lg-0">
                        <article class="border p-6 rounded">
                            <p class="mb-0 text-muted">Total sales:</p>
                            <h5 class="text-success">{{ $totalSales }}</h5>
                            <p class="mb-0 text-muted">Revenue:</p>
                            <h5 class="text-success mb-0">{{ formatCurrency($totalRevenue, 2) }}</h5>
                        </article>
                    </div>

                    <div class="col-sm-6 col-lg-4 col-xl-3 mb-sm-0 mb-7">
                        <h6 class="fs-18px mb-4">Contacts</h6>
                        <p class="mb-0">
                            @if($vendor->user)
                                Manager: {{ $vendor->user->name ?? 'N/A' }} <br>
                            @endif
                            @if($vendor->user && $vendor->user->email)
                                {{ $vendor->user->email }} <br>
                            @endif
                            @if($vendor->phone)
                                {{ $vendor->phone }}
                            @endif
                        </p>
                    </div>

                    <div class="col-sm-6 col-lg-4 col-xl-3">
                        <h6 class="fs-18px mb-4">Address</h6>
                        <p class="mb-0">
                            Country: {{ $vendor->country ?? 'N/A' }} <br>
                            Address: {{ $vendor->address ?? 'N/A' }} <br>
                            City: {{ $vendor->city ?? 'N/A' }}
                        </p>
                    </div>

                    <div class="col-sm-6 col-xl-4">
                        <h6 class="fs-18px mb-4">Documents</h6>
                        <div class="document-list">
                            @forelse($vendor->documents as $document)
                                <div class="document-item mb-3 p-3 border rounded">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $document->document_type)) }}</h6>
                                            @if($document->document_type == 'ntn_certificate')
                                                <small class="text-muted">NTN Number: {{ $document->document_number ?? 'N/A' }}</small>
                                            @else
                                                <small class="text-muted">{{ $document->document_number ?? 'N/A' }}</small>
                                            @endif
                                        </div>
                                        @if($document->document_type != 'ntn_number')
                                            <a href="{{ asset('storage/' . $document->document_file_path) }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                        @endif    
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">No documents found</p>
                            @endforelse
                        </div>
                    </div>

                      <div class="mt-4">
    <div class="card-header">
        <h5><i class="fas fa-university"></i> Bank Account Information</h5>
    </div>
    <div class="card-body">
        @if($vendor->bankDetail)
            <table class="table table-bordered">
                <tr>
                    <th width="30%">Account Title:</th>
                    <td>{{ $vendor->bankDetail->account_title }}</td>
                </tr>
                <tr>
                    <th>IBAN Number:</th>
                    <td>{{ $vendor->bankDetail->iban_number }}</td>
                </tr>
                <tr>
                    <th>Bank Name:</th>
                    <td>{{ $vendor->bankDetail->bank_name }}</td>
                </tr>
            </table>
        @else
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle"></i> Vendor has not added bank details yet.
            </div>
        @endif
    </div>
</div>


                </div>

            </div>
        </div>

        <div class="card mb-4 rounded-4">
            <div class="card-body p-7">
                <h2 class="card-title fs-4 mb-6">Products by seller</h2>
                <div class="row mx-n5">
                    @forelse($products as $product)
                        <div class="col-sm-6 col-xl-custom px-5">
                            <div class="card rounded-xl card-product mb-7">
                                {{-- <a href="{{ route('admin.product.detail', $product->product_slug) }}" title="{{ $product->product_name }}"> --}}
                                    @if($product->thumbnail_image)
                                        <img src="{{ asset('storage/' . $product->thumbnail_image) }}"
                                            alt="{{ $product->product_name }}" class="lazy-image card-img-top" width="270" height="360">
                                    @else
                                        <img src="{{ asset('assets/images/products/product-01-270x360.jpg') }}"
                                            alt="{{ $product->product_name }}" class="lazy-image card-img-top" width="270" height="360">
                                    @endif
                                </a>
                                <div class="card-body p-6">
                                    {{-- <a href="{{ route('admin.product.detail', $product->product_slug) }}" class="cart-title">{{ $product->product_name }}</a> --}}
                                    <div class="price mb-4 text-primary fw-500">
                                        @if($product->product_discount > 0)
                                            <span class="text-decoration-line-through text-muted">${{ number_format($product->product_price, 2) }}</span>
                                            {{ formatCurrency($product->product_price - $product->product_discount, 2) }}
                                        @else
                                            {{ formatCurrency($product->product_price, 2) }}
                                        @endif
                                    </div>
                                    @if($product->category)
                                        <small class="text-muted">{{ $product->category->category_name }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <h4>No products found</h4>
                                <p>This vendor hasn't added any products yet.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{ $products->links() }}
    </div>
</div>
