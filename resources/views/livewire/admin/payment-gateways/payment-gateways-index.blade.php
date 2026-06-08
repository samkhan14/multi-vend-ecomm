<div>
    <div class="dashboard-page-content">
        <div class="row mb-9 align-items-center">
            <div class="col-12">
                <h2 class="fs-4 mb-1">Payment Gateways</h2>
                <p class="text-muted mb-0">Enable checkout methods and configure NOWPayments crypto payments.</p>
            </div>
        </div>

        <div class="card mb-6 rounded-4 shadow-sm border-0">
            <div class="card-body p-7">
                <h5 class="mb-4">Environment Status</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small">API Key</div>
                            <div class="fw-semibold">{{ $apiKeyConfigured ? 'Configured' : 'Missing' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small">IPN Secret</div>
                            <div class="fw-semibold">{{ $ipnSecretConfigured ? 'Configured' : 'Missing' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small">Sandbox Mode</div>
                            <div class="fw-semibold">{{ $sandboxEnabled ? 'Enabled' : 'Disabled' }}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="button" class="btn btn-outline-primary" wire:click="testConnection" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="testConnection">Test NOWPayments Connection</span>
                        <span wire:loading wire:target="testConnection">Testing...</span>
                    </button>
                    @if($connectionMessage)
                        <div class="alert alert-{{ $connectionStatus === 'success' ? 'success' : 'danger' }} mt-3 mb-0">
                            {{ $connectionMessage }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <form wire:submit.prevent="save">
            <div class="card rounded-4 shadow-sm border-0">
                <div class="card-body p-7">
                    <div class="mb-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="codEnabled" wire:model="codEnabled">
                            <label class="form-check-label fw-semibold" for="codEnabled">Cash on Delivery (COD)</label>
                        </div>
                        <small class="text-muted">Allow customers to pay when the order is delivered.</small>
                        @error('codEnabled') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <hr>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="nowpaymentsEnabled" wire:model="nowpaymentsEnabled">
                            <label class="form-check-label fw-semibold" for="nowpaymentsEnabled">NOWPayments (Crypto)</label>
                        </div>
                        <small class="text-muted">Redirect customers to NOWPayments hosted invoice page. Requires API key and IPN secret in `.env`.</small>
                        @error('nowpaymentsEnabled') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label">Invoice Fiat Currency</label>
                            <input type="text" class="form-control" wire:model="nowpaymentsPriceCurrency" placeholder="usd">
                            <small class="text-muted">Used when your store base currency is not supported by NOWPayments (e.g. PKR → USD).</small>
                            @error('nowpaymentsPriceCurrency') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Default Pay Currency (optional)</label>
                            <input type="text" class="form-control" wire:model="nowpaymentsPayCurrency" placeholder="usdttrc20">
                        </div>
                        <div class="col-md-4 d-flex flex-column justify-content-center gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="nowpaymentsIsFixedRate" wire:model="nowpaymentsIsFixedRate">
                                <label class="form-check-label" for="nowpaymentsIsFixedRate">Fixed rate exchange</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="nowpaymentsFeePaidByUser" wire:model="nowpaymentsFeePaidByUser">
                                <label class="form-check-label" for="nowpaymentsFeePaidByUser">Fee paid by customer</label>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4 mb-0">
                        Set your IPN callback URL in NOWPayments dashboard to:
                        <code>{{ route('webhooks.nowpayments') }}</code>
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">Save Gateway Settings</span>
                            <span wire:loading wire:target="save">Saving...</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
