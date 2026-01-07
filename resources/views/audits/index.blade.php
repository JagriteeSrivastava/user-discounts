@extends('layouts.app')

@section('content')
<div class="mb-5">
    <h2 class="fw-bold mb-1">Audit Logs</h2>
    <p class="text-muted">Full history of discount assignments, revocations, and applications.</p>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr class="small text-uppercase text-muted letter-spacing-1">
                    <th class="ps-4">Timestamp</th>
                    <th>User</th>
                    <th>Event</th>
                    <th>Discount Code</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                @forelse($audits as $audit)
                <tr>
                    <td class="ps-4 text-muted small">
                        {{ $audit->created_at->format('M d, Y H:i:s') }}
                        <div class="small opacity-50">{{ $audit->created_at->diffForHumans() }}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2 font-monospace" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                ID{{ $audit->user_id }}
                            </div>
                            <span class="small fw-semibold">Demo User</span>
                        </div>
                    </td>
                    <td>
                        @if($audit->event === 'assigned')
                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">Assigned</span>
                        @elseif($audit->event === 'revoked')
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Revoked</span>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Applied</span>
                        @endif
                    </td>
                    <td class="fw-bold text-dark">
                        {{ $audit->discount->code ?? 'N/A' }}
                    </td>
                    <td>
                        @if($audit->event === 'applied')
                            <div class="small">
                                <span class="text-muted">Price:</span> 
                                <span class="text-decoration-line-through text-muted">${{ number_format($audit->metadata['price_before'] ?? 0, 2) }}</span>
                                <i class="fas fa-arrow-right mx-1 text-primary" style="font-size: 0.7rem;"></i>
                                <span class="fw-bold text-indigo-600">${{ number_format($audit->metadata['price_after'] ?? 0, 2) }}</span>
                            </div>
                        @else
                            <small class="text-muted">Package state updated successfully.</small>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        No audit logs found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($audits->hasPages())
    <div class="card-footer bg-white border-top-0 py-3">
        {{ $audits->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

<style>
    .text-indigo-600 { color: #6366f1; }
    .letter-spacing-1 { letter-spacing: 0.05rem; }
</style>
@endsection
