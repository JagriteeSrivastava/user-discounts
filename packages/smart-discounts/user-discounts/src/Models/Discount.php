<?php

namespace SmartDiscounts\UserDiscounts\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'value',
        'usage_limit_per_user',
        'active',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'active' => 'boolean',
    ];

    public function userDiscounts()
    {
        return $this->hasMany(UserDiscount::class);
    }
}
