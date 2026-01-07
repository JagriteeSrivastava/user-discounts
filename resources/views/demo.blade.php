@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Discount Dashboard</h2>
        <p class="text-muted mb-0">Manage user-specific discounts and simulate price calculations.</p>
    </div>
    <div class="badge bg-white text-dark shadow-sm border rounded-pill px-4 py-2">
        <i class="fas fa-user-circle me-2 text-primary"></i>
        <span class="fw-semibold">{{ $user->name }}</span>
    </div>
</div>

<div class="row g-4">
    <!-- Calculation Summary -->
    <div class="col-lg-4">
        <div class="card h-100 border-0 shadow-sm p-4">
            <h5 class="fw-bold mb-4 text-dark">Price Simulation</h5>
            <div class="bg-indigo-50 p-4 rounded-4 text-center mb-4 border border-indigo-100">
                <small class="text-indigo-600 d-block mb-1 fw-semibold text-uppercase letter-spacing-1">Subtotal</small>
                <h4 class="text-muted text-decoration-line-through mb-1">$100.00</h4>
                <h1 class="mb-0 text-indigo-900 fw-black display-5">${{ number_format($finalPrice, 2) }}</h1>
                <div class="mt-2">
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                        Optimized Price
                    </span>
                </div>
            </div>

            <h6 class="fw-bold mb-3 text-dark d-flex align-items-center">
                <i class="fas fa-ticket-alt me-2 text-primary"></i>
                Active Discounts
            </h6>
            <div class="list-group list-group-flush rounded-3 overflow-hidden border mb-4">
                @forelse($userDiscounts->where('is_revoked', false) as $ud)
                    <div class="list-group-item d-flex justify-content-between align-items-center list-group-item-action py-3">
                        <div>
                            <div class="fw-bold text-dark">{{ $ud->discount->code }}</div>
                            <div class="d-flex align-items-center">
                                <span class="badge {{ $ud->discount->type === 'percentage' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }} rounded-pill border-0 me-2" style="font-size: 0.7rem;">
                                    {{ $ud->discount->type === 'percentage' ? $ud->discount->value . '%' : '$' . $ud->discount->value }}
                                </span>
                                <small class="text-muted">Used: {{ $ud->usage_count }}/{{ $ud->discount->usage_limit_per_user }}</small>
                            </div>
                        </div>
                        <form action="{{ route('demo.revoke') }}" method="POST">
                            @csrf
                            <input type="hidden" name="code" value="{{ $ud->discount->code }}">
                            <button class="btn btn-sm btn-outline-danger border-0 rounded-circle" title="Revoke Discount">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="list-group-item text-center py-4 text-muted small">
                        No active discounts for this user.
                    </div>
                @endforelse
            </div>

            <!-- Revoked History -->
            @if($userDiscounts->where('is_revoked', true)->isNotEmpty())
                <h6 class="fw-bold mb-2 text-muted small text-uppercase letter-spacing-1">
                    Recently Revoked
                </h6>
                <div class="list-group list-group-flush rounded-3 overflow-hidden border border-dashed">
                    @foreach($userDiscounts->where('is_revoked', true)->take(3) as $ud)
                        <div class="list-group-item d-flex justify-content-between align-items-center py-2 opacity-75">
                            <small class="fw-bold text-muted text-decoration-line-through">{{ $ud->discount->code }}</small>
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill border-0" style="font-size: 0.6rem;">Inactive</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Main Content: Discounts & Activity -->
    <div class="col-lg-8">
        <!-- Coupon Codes -->
        <div class="card border-0 shadow-sm p-4 mb-4">
            <h5 class="fw-bold mb-4 text-dark">Available Coupons</h5>
            <div class="row g-3">
                @foreach($discounts as $d)
                @php
                    $uDisc = $userDiscounts->where('discount_id', $d->id)->first();
                    $isApplied = $uDisc && !$uDisc->is_revoked;
                    $isLimitReached = $uDisc && $uDisc->usage_count >= $d->usage_limit_per_user;
                @endphp
                <div class="col-md-6">
                    <div class="coupon-box border rounded-4 p-3 d-flex align-items-center justify-content-between transition-all {{ $isApplied ? 'border-primary bg-primary-subtle bg-opacity-10' : '' }}">
                        <div class="d-flex align-items-center">
                            <div class="icon-box {{ $isApplied ? 'bg-primary' : 'bg-primary-subtle' }} rounded-3 p-2 me-3">
                                <i class="fas fa-tag {{ $isApplied ? 'text-white' : 'text-primary' }}"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">{{ $d->code }}</div>
                                <div class="small text-muted">{{ $d->name }} • Limit: {{ $d->usage_limit_per_user }}</div>
                            </div>
                        </div>
                        <form action="{{ route('demo.assign') }}" method="POST">
                            @csrf
                            <input type="hidden" name="code" value="{{ $d->code }}">
                            @if($isApplied)
                                <span class="badge bg-success text-white py-2 px-3 rounded-pill shadow-sm">
                                    <i class="fas fa-check me-1"></i> Active
                                </span>
                            @elseif($isLimitReached)
                                <span class="badge bg-secondary text-white py-2 px-3 rounded-pill">
                                    Max Used
                                </span>
                            @else
                                <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                    Apply
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Simulation Products -->
        <div class="card border-0 shadow-sm p-4 mb-4">
            <h5 class="fw-bold mb-4 text-dark">Simulation Products</h5>
            <div class="row g-3">
                @php
                    $samples = [
                        ['name' => 'Premium Headphones', 'price' => 100, 'img' => '🎧'],
                        ['name' => 'Smart Watch Pro', 'price' => 100, 'img' => '⌚'],
                        ['name' => 'Laptop Case', 'price' => 100, 'img' => '💼']
                    ];
                @endphp
                @foreach($samples as $sample)
                <div class="col-md-4">
                    <div class="card border border-light bg-light-subtle rounded-4 text-center p-3 h-100 shadow-sm transition-all">
                        <div class="display-4 mb-3">{{ $sample['img'] }}</div>
                        <h6 class="fw-bold text-dark mb-1">{{ $sample['name'] }}</h6>
                        <div class="text-muted small mb-3">Base: ${{ $sample['price'] }}</div>
                        <div class="h4 fw-black text-primary mb-0">${{ number_format($finalPrice, 2) }}</div>
                        <small class="text-success fw-bold">Discounted Price</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Activity (Audit Log) -->
        <div class="card border-0 shadow-sm p-4 overflow-hidden">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-dark">Recent Activity (Package Audits)</h5>
                <span class="badge bg-light text-muted border rounded-pill">Real-time Logs</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-uppercase text-muted letter-spacing-1">
                            <th>Event</th>
                            <th>Discount</th>
                            <th>Details</th>
                            <th class="text-end">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($audits as $audit)
                        <tr>
                            <td>
                                @if($audit->event === 'assigned')
                                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">Assigned</span>
                                @elseif($audit->event === 'revoked')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Revoked</span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Applied</span>
                                @endif
                            </td>
                            <td class="fw-bold text-dark">{{ $audit->discount->code }}</td>
                            <td>
                                @if($audit->event === 'applied')
                                    <small class="text-muted">
                                        Subtotal: ${{ number_format($audit->metadata['price_before'] ?? 0, 2) }} 
                                        <i class="fas fa-arrow-right mx-1 small"></i> 
                                        <strong>${{ number_format($audit->metadata['price_after'] ?? 0, 2) }}</strong>
                                    </small>
                                @else
                                    <small class="text-muted">User state updated</small>
                                @endif
                            </td>
                            <td class="text-end small text-muted">
                                {{ $audit->created_at->diffForHumans() }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No activity recorded yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-indigo-50 { background-color: #f5f3ff; }
    .bg-indigo-100 { background-color: #ede9fe; }
    .text-indigo-600 { color: #4f46e5; }
    .text-indigo-900 { color: #1e1b4b; }
    .letter-spacing-1 { letter-spacing: 0.1em; }
    .fw-black { font-weight: 900; }
    .bg-blue-100 { background-color: #dbeafe; }
    .text-blue-700 { color: #1d4ed8; }
    .bg-green-100 { background-color: #dcfce7; }
    .text-green-700 { color: #15803d; }
    .coupon-box:hover { border-color: #4f46e5 !important; background-color: #f5f3ff; }
    .transition-all { transition: all 0.3s ease; }
    .letter-spacing-1 { letter-spacing: 0.05rem; }
    .fw-black { font-weight: 900; }
</style>
@endsection
