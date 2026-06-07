<div>
    <div class="dashboard-page-content">

        <div class="row mb-9 align-items-center justify-content-between">
            <div class="col-sm-6 mb-8 mb-sm-0">
                <h2 class="fs-4 mb-0">Vendors List</h2>
            </div>
        </div>

        <div class="card mb-4 rounded-4 p-7">
            <div class="card-header bg-transparent px-0 pt-0 pb-7">
                <div class="row align-items-center justify-content-between">
                    <div class="col-md-4 col-12 mr-auto mb-md-0 mb-6">
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search..."
                            class="form-control bg-input border-0">
                    </div>
                    <div class="col-md-8">
                        <div class="row justify-content-end flex-nowrap d-flex">
                            <div class="col-lg-3 col-md-6 col-6">
                                <select class="form-select" wire:model.live="perPage">
                                    <option value="20">Show 20</option>
                                    <option value="30">Show 30</option>
                                    <option value="40">Show 40</option>
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-6 col-6">
                                <select class="form-select" wire:model.live="statusFilter">
                                    <option value="all">Status: All</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body px-0 pt-7 pb-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-nowrap mb-0 table-borderless">
                        <thead class="table-light">
                            <tr>
                                <th class="align-middle" scope="col">Image</th>
                                <th class="align-middle" scope="col">Name</th>
                                <th class="align-middle" scope="col">Store Name</th>
                                <th class="align-middle" scope="col">Email</th>
                                <th class="align-middle" scope="col">Registered</th>
                                {{-- <th class="align-middle" scope="col">Status</th> --}}
                                <th class="align-middle text-center" scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customerUser as $user)
                                <tr wire:key="user-{{ $user->id }}">
                                    <td>
                                        @php
                                            $avatarUrl =
                                                'https://ui-avatars.com/api/?name=' .
                                                urlencode($user->name ?? 'User') .
                                                '&background=4e7661&color=fff';
                                            $imagePath = trim((string) ($user->image ?? ''));
                                            $imageUrl = $avatarUrl;

                                            if ($imagePath !== '') {
                                                if (preg_match('~^https?://~i', $imagePath)) {
                                                    $imageUrl = $imagePath;
                                                } elseif (str_starts_with($imagePath, 'storage/')) {
                                                    $imageUrl = asset($imagePath);
                                                } else {
                                                    $imageUrl = asset('storage/' . ltrim($imagePath, '/'));
                                                }
                                            }
                                        @endphp

                                        <img src="{{ $imageUrl }}"
                                            onerror="this.onerror=null;this.src='{{ $avatarUrl }}';"
                                            alt="{{ $user->name }}" width="60" height="60"
                                            class="rounded-pill object-fit-cover">
                                    </td>
                                    <td>
                                        <div>
                                            <a href="#" class="text-dark fw-semibold">{{ $user->name }}</a>
                                            <span class="d-block fs-13px text-muted">ID#{{ $user->id }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $user->vendor->store_name ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->created_at->format('d.m.Y') }}</td>
                                    {{-- <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle" type="checkbox" role="switch"
                                                id="status-{{ $user->id }}"
                                                wire:click="toggleStatus({{ $user->id }})"
                                                @if ($user->user_status) checked @endif>
                                            <label
                                                class="form-check-label ms-3 fw-semibold {{ $user->user_status ? 'text-success' : 'text-muted' }}"
                                                for="status-{{ $user->id }}">
                                                {{ $user->user_status ? 'Active' : 'Inactive' }}
                                            </label>
                                        </div>
                                    </td> --}}
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            <a href="{{ route('admin.vendor.detail', $user->vendor->id ?? $user->id) }}"
                                                class="btn btn-primary fs-13px btn-xs py-4">
                                                <i class="far fa-eye me-2"></i> Details
                                            </a>

                                            @if ($user->vendor)
                                                <a href="{{ route('admin.vendor.orders', $user->vendor->id) }}"
                                                    class="btn btn-outline-primary fs-13px btn-xs py-4">
                                                    <i class="far fa-list-alt me-2"></i> Orders
                                                </a>
                                            @else
                                                <button type="button" class="btn btn-outline-secondary fs-13px btn-xs py-4"
                                                    disabled>
                                                    <i class="far fa-list-alt me-2"></i> Orders
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <p class="text-muted mb-0">No vendors found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if ($customerUser->hasPages())
            <nav aria-label="Page navigation example" class="mt-6 mb-4">
                <ul class="pagination justify-content-start">
                    @if ($customerUser->onFirstPage())
                        <li class="page-item disabled mx-3">
                            <span class="page-link"><i class="far fa-chevron-left"></i></span>
                        </li>
                    @else
                        <li class="page-item mx-3">
                            <button type="button" class="page-link" wire:click="previousPage"
                                wire:loading.attr="disabled">
                                <i class="far fa-chevron-left"></i>
                            </button>
                        </li>
                    @endif

                    @foreach ($customerUser->links()->elements[0] as $page => $url)
                        @if ($page == $customerUser->currentPage())
                            <li class="page-item active mx-3" aria-current="page">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item mx-3">
                                <button type="button" class="page-link"
                                    wire:click="gotoPage({{ $page }})">{{ $page }}</button>
                            </li>
                        @endif
                    @endforeach

                    @if ($customerUser->hasMorePages())
                        <li class="page-item mx-3">
                            <button type="button" class="page-link" wire:click="nextPage" wire:loading.attr="disabled">
                                <i class="far fa-chevron-right"></i>
                            </button>
                        </li>
                    @else
                        <li class="page-item disabled mx-3">
                            <span class="page-link"><i class="far fa-chevron-right"></i></span>
                        </li>
                    @endif
                </ul>
            </nav>
        @endif
    </div>
</div>
