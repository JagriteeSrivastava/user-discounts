<?php

namespace SmartDiscounts\UserDiscounts\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountAudit extends Model
{
    protected $fillable = [
        'user_id',
        'discount_id',
        'event',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    const UPDATED_AT = null;

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
}
