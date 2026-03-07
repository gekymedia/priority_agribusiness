@extends('layouts.app')

@section('title', 'Payment Settings')

@section('content')
<div class="page-header">
    <h1 class="page-title">Payment Settings</h1>
    <p class="page-subtitle">Configure Hubtel and Paystack for the public egg store. When both are configured, <strong>Hubtel takes precedence</strong>. Values saved here override .env when set.</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0 list-unstyled">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(isset($activeGateway) && $activeGateway)
    <div class="alert alert-info mb-4">
        <span class="me-2">Active payment gateway for front store:</span>
        <span class="badge bg-primary">{{ strtoupper($activeGateway) }}</span>
    </div>
@endif

<form action="{{ route('payment-settings.update') }}" method="POST">
    @csrf
    @method('PUT')

    {{-- Hubtel --}}
    <div class="agri-card mb-4">
        <div class="agri-card-body">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-success bg-opacity-10 text-success p-3">
                        <i class="fas fa-mobile-alt fa-lg"></i>
                    </div>
                    <div>
                        <h2 class="h5 mb-1">Hubtel</h2>
                        <p class="text-muted small mb-0">Client ID/Secret or API Key/Secret from Hubtel. When both Hubtel and Paystack are configured, Hubtel is used for the front store.</p>
                    </div>
                </div>
                @if($channels['hubtel']['configured'])
                    <span class="badge bg-success">Configured</span>
                @else
                    <span class="badge bg-warning text-dark">Not configured</span>
                @endif
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="hubtel_client_id" class="form-label">Client ID</label>
                    <input type="text" name="hubtel_client_id" id="hubtel_client_id" class="form-control" value="{{ old('hubtel_client_id', $settings['hubtel_client_id'] ?? '') }}" placeholder="From Hubtel dashboard">
                </div>
                <div class="col-md-6">
                    <label for="hubtel_client_secret" class="form-label">Client secret</label>
                    <div class="input-group">
                        <input type="password" name="hubtel_client_secret" id="hubtel_client_secret" class="form-control" value="{{ old('hubtel_client_secret', $settings['hubtel_client_secret'] ?? '') }}" placeholder="Leave blank to keep current">
                        <button type="button" class="btn btn-outline-secondary toggle-secret" data-target="hubtel_client_secret"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="hubtel_api_key" class="form-label">API key (alternative)</label>
                    <input type="text" name="hubtel_api_key" id="hubtel_api_key" class="form-control" value="{{ old('hubtel_api_key', $settings['hubtel_api_key'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label for="hubtel_api_secret" class="form-label">API secret (alternative)</label>
                    <div class="input-group">
                        <input type="password" name="hubtel_api_secret" id="hubtel_api_secret" class="form-control" value="{{ old('hubtel_api_secret', $settings['hubtel_api_secret'] ?? '') }}" placeholder="Leave blank to keep current">
                        <button type="button" class="btn btn-outline-secondary toggle-secret" data-target="hubtel_api_secret"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
                <div class="col-12">
                    <label for="hubtel_merchant_account_number" class="form-label">Merchant account number</label>
                    <input type="text" name="hubtel_merchant_account_number" id="hubtel_merchant_account_number" class="form-control" value="{{ old('hubtel_merchant_account_number', $settings['hubtel_merchant_account_number'] ?? '') }}" placeholder="Optional">
                </div>
            </div>
        </div>
    </div>

    {{-- Paystack --}}
    <div class="agri-card mb-4">
        <div class="agri-card-body">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-warning bg-opacity-10 text-warning p-3">
                        <i class="fas fa-credit-card fa-lg"></i>
                    </div>
                    <div>
                        <h2 class="h5 mb-1">Paystack</h2>
                        <p class="text-muted small mb-0">Public and secret keys from Paystack dashboard. Used when Hubtel is not configured.</p>
                    </div>
                </div>
                @if($channels['paystack']['configured'])
                    <span class="badge bg-success">Configured</span>
                @else
                    <span class="badge bg-warning text-dark">Not configured</span>
                @endif
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="paystack_public_key" class="form-label">Public key</label>
                    <input type="text" name="paystack_public_key" id="paystack_public_key" class="form-control" value="{{ old('paystack_public_key', $settings['paystack_public_key'] ?? '') }}" placeholder="pk_live_... or pk_test_...">
                </div>
                <div class="col-md-6">
                    <label for="paystack_secret_key" class="form-label">Secret key</label>
                    <div class="input-group">
                        <input type="password" name="paystack_secret_key" id="paystack_secret_key" class="form-control" value="{{ old('paystack_secret_key', $settings['paystack_secret_key'] ?? '') }}" placeholder="Leave blank to keep current">
                        <button type="button" class="btn btn-outline-secondary toggle-secret" data-target="paystack_secret_key"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="paystack_base_url" class="form-label">Base URL</label>
                    <input type="url" name="paystack_base_url" id="paystack_base_url" class="form-control" value="{{ old('paystack_base_url', $settings['paystack_base_url'] ?? '') }}" placeholder="https://api.paystack.co">
                </div>
                <div class="col-md-6">
                    <label for="paystack_webhook_secret" class="form-label">Webhook secret</label>
                    <div class="input-group">
                        <input type="password" name="paystack_webhook_secret" id="paystack_webhook_secret" class="form-control" value="{{ old('paystack_webhook_secret', $settings['paystack_webhook_secret'] ?? '') }}" placeholder="Leave blank to keep current">
                        <button type="button" class="btn btn-outline-secondary toggle-secret" data-target="paystack_webhook_secret"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Egg market pricing (public store) --}}
    <div class="agri-card mb-4">
        <div class="agri-card-body">
            <h2 class="h5 mb-3"><i class="fas fa-egg me-2"></i>Egg market pricing (public store)</h2>
            <p class="text-muted small mb-4">Set prices for the public egg store. Customers can buy by crate or by piece.</p>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="egg_market_price_per_crate" class="form-label">Price per crate (₵)</label>
                    <input type="number" name="egg_market_price_per_crate" id="egg_market_price_per_crate" class="form-control" step="0.01" min="0" value="{{ old('egg_market_price_per_crate', $settings['egg_market_price_per_crate'] ?? '') }}" placeholder="e.g. 45.00">
                </div>
                <div class="col-md-4">
                    <label for="egg_market_price_per_piece" class="form-label">Price per piece (₵)</label>
                    <input type="number" name="egg_market_price_per_piece" id="egg_market_price_per_piece" class="form-control" step="0.01" min="0" value="{{ old('egg_market_price_per_piece', $settings['egg_market_price_per_piece'] ?? '') }}" placeholder="e.g. 2.00">
                </div>
                <div class="col-md-4">
                    <label for="egg_market_eggs_per_crate" class="form-label">Eggs per crate</label>
                    <input type="number" name="egg_market_eggs_per_crate" id="egg_market_eggs_per_crate" class="form-control" min="1" value="{{ old('egg_market_eggs_per_crate', $settings['egg_market_eggs_per_crate'] ?? '30') }}">
                </div>
                <div class="col-12">
                    <label for="egg_market_batch_id" class="form-label">Layer batch for market sales (optional)</label>
                    <select name="egg_market_batch_id" id="egg_market_batch_id" class="form-select">
                        <option value="">— None (for display only) —</option>
                        @foreach(\App\Models\BirdBatch::whereIn('purpose', ['egg_production', 'layer'])->orderBy('arrival_date', 'desc')->get() as $batch)
                            <option value="{{ $batch->id }}" {{ old('egg_market_batch_id', $settings['egg_market_batch_id'] ?? '') == $batch->id ? 'selected' : '' }}>{{ $batch->batch_code }} - {{ $batch->farm->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">If set, sold eggs will be recorded against this batch in Egg Sales.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Save payment settings
        </button>
        <a href="{{ route('settings.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>

@push('scripts')
<script>
document.querySelectorAll('.toggle-secret').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var id = this.getAttribute('data-target');
        var input = document.getElementById(id);
        var icon = this.querySelector('i');
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
});
</script>
@endpush
@endsection
