<div>
    <div class="dashboard-page-content">
        <div class="row mb-9 align-items-center">
            <div class="col-12">
                <h2 class="fs-4 mb-0">Payout Requests</h2>
                <p class="mb-0">Manage withdrawal requests and approvals</p>
            </div>
        </div>

        @if ($hasVendorProfile)
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card rounded-4 border-0 shadow-sm">
                        <div class="card-header p-7 bg-transparent border-0">
                            <h5 class="card-title fs-6 mb-0">Create Payout Request</h5>
                        </div>
                        <div class="card-body p-7">
                            <div class="row g-3 align-items-end">
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Amount</label>
                                    <input type="number" wire:model.defer="requestAmount" class="form-control"
                                        step="0.01" min="0.01" placeholder="Enter amount">
                                    @error('requestAmount')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Request Note (Optional)</label>
                                    <input type="text" wire:model.defer="requestNote" class="form-control"
                                        placeholder="Add note for admin">
                                    @error('requestNote')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-2">
                                    <button type="button" wire:click="submitRequest" wire:loading.attr="disabled"
                                        class="btn btn-primary w-100">
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card rounded-4 border-0 shadow-sm">
                    <div class="card-header p-7 bg-transparent">
                        <div class="row align-items-center">
                            <div class="col-12 col-md-6">
                                <h5 class="card-title fs-6 mb-0">Request History</h5>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="d-flex gap-2">
                                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                        placeholder="Search requests...">
                                    <select wire:model.live="statusFilter" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        @if ($canManagePayouts)
                            <div class="row mt-3">
                                <div class="col-12 col-md-6">
                                    <input type="text" wire:model.defer="adminNote" class="form-control"
                                        placeholder="Admin note (used on approve/reject)">
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card-body p-7">
                        <div wire:loading class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>

                        <div class="table-responsive" wire:loading.remove>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Request ID</th>
                                        @if ($canManagePayouts)
                                            <th>Vendor</th>
                                        @endif
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Request Note</th>
                                        <th>Admin Note</th>
                                        <th>Requested At</th>
                                        <th>Processed By</th>
                                        <th>Processed At</th>
                                        @if ($canManagePayouts)
                                            <th class="text-center">Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($payoutRequests as $request)
                                        @php
                                            $statusClass = match ($request->status) {
                                                'approved' => 'bg-success',
                                                'pending' => 'bg-warning',
                                                'rejected' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <tr>
                                            <td>#PR-{{ str_pad((string) $request->id, 6, '0', STR_PAD_LEFT) }}</td>
                                            @if ($canManagePayouts)
                                                <td>{{ $request->vendor->store_name ?? 'N/A' }}</td>
                                            @endif
                                            <td class="text-danger">-{{ formatCurrency($request->amount) }}</td>
                                            <td>
                                                <span class="badge {{ $statusClass }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $request->request_note ?: '-' }}</td>
                                            <td>{{ $request->admin_note ?: '-' }}</td>
                                            <td>{{ $request->created_at?->format('d M Y h:i A') }}</td>
                                            <td>{{ $request->processedBy->name ?? '-' }}</td>
                                            <td>{{ $request->processed_at?->format('d M Y h:i A') ?: '-' }}</td>
                                            @if ($canManagePayouts)
                                                <td class="text-center">
                                                    @if ($request->status === 'pending')
                                                        <div class="d-flex justify-content-center gap-2">
                                                            <button type="button" class="btn btn-sm btn-success"
                                                                wire:click="approveRequest({{ $request->id }})"
                                                                wire:loading.attr="disabled">
                                                                Approve
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                                wire:click="rejectRequest({{ $request->id }})"
                                                                wire:loading.attr="disabled">
                                                                Reject
                                                            </button>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $canManagePayouts ? 10 : 8 }}" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="far fa-inbox fa-3x mb-3 d-block"></i>
                                                    <p class="mb-0">No payout requests found</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $payoutRequests->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

