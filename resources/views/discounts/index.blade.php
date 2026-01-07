@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Coupon Management</h2>
        <p class="text-muted">Create and manage your discount codes here.</p>
    </div>
    <a href="{{ route('discounts.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i> Create New Coupon
    </a>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr class="small text-uppercase text-muted">
                    <th class="ps-4">Code</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Limit</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($discounts as $d)
                <tr>
                    <td class="ps-4">
                        <span class="badge bg-indigo-50 text-indigo-600 px-3 py-2 rounded-pill fw-bold border border-indigo-100">
                            {{ $d->code }}
                        </span>
                    </td>
                    <td class="fw-semibold text-dark">
                        {{ $d->name }}
                        @if(in_array($d->code, ['WELCOME10', 'SAVE5']))
                            <div class="small text-muted fw-normal" style="font-size: 0.7rem;">(By Default Coupon)</div>
                        @endif
                    </td>
                    <td>
                        <span class="text-capitalize">{{ $d->type }}</span>
                    </td>
                    <td>
                        {{ $d->type === 'percentage' ? $d->value . '%' : '$' . number_format($d->value, 2) }}
                    </td>
                    <td>{{ $d->usage_limit_per_user }} per user</td>
                    <td>
                        @if($d->active)
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Active</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Inactive</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('discounts.edit', $d) }}" class="btn btn-sm btn-outline-primary border-0 rounded-circle" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(!in_array($d->code, ['WELCOME10', 'SAVE5']))
                                <form action="{{ route('discounts.destroy', $d) }}" method="POST" onsubmit="return confirm('Delete this coupon?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger border-0 rounded-circle" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        No coupons found. Create your first one to get started!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    .bg-indigo-50 { background-color: #f5f3ff; }
    .text-indigo-600 { color: #4f46e5; }
</style>
@endsection
