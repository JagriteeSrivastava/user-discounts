@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('discounts.index') }}" class="text-decoration-none text-muted small">
        <i class="fas fa-arrow-left me-1"></i> Back to List
    </a>
    <h2 class="fw-bold mt-2">Create New Coupon</h2>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 pt-5">
            <form action="{{ route('discounts.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Coupon Code</label>
                        <input type="text" name="code" class="form-control rounded-3 py-2" placeholder="e.g. SUMMER50" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Display Name</label>
                        <input type="text" name="name" class="form-control rounded-3 py-2" placeholder="e.g. Summer Special" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Discount Type</label>
                        <select name="type" class="form-select rounded-3 py-2" required>
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount ($)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Value</label>
                        <input type="number" step="0.01" name="value" class="form-control rounded-3 py-2" placeholder="0.00" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Per-User Usage Limit</label>
                        <input type="number" name="usage_limit_per_user" class="form-control rounded-3 py-2" value="1" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">Expires At (Optional)</label>
                        <input type="date" name="expires_at" class="form-control rounded-3 py-2">
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch p-3 bg-light rounded-3">
                            <input class="form-check-input ms-0 me-3" type="checkbox" name="active" value="1" id="activeSwitch" checked>
                            <label class="form-check-label fw-semibold text-dark" for="activeSwitch">Coupon Active</label>
                            <div class="small text-muted ms-5">If inactive, the coupon will be ignored by the engine.</div>
                        </div>
                    </div>

                    <div class="col-12 mt-4 text-end">
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">
                            Create Coupon
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 bg-primary bg-gradient text-white p-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i> Tips</h5>
            <ul class="small mb-0 opacity-90 ps-3">
                <li class="mb-2"><strong>Codes</strong> should be uppercase and unique.</li>
                <li class="mb-2"><strong>Percentage</strong> discounts are capped by the system config (currently 50%).</li>
                <li class="mb-2"><strong>Usage limits</strong> ensure users don't abuse the discount.</li>
                <li><strong>Expiry</strong> date will automatically disable the code after that day.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
