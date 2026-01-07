<?php

namespace SmartDiscounts\UserDiscounts\Models;

use Illuminate\Database\Eloquent\Model;

class UserDiscount extends Model
{
    protected $fillable = [
        'user_id',
        'discount_id',
        'usage_count',
        'is_revoked',
        'assigned_at'
    ];

    protected $casts = [
        'is_revoked' => 'boolean',
        'assigned_at' => 'datetime',
    ];

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
}
