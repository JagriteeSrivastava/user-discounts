<?php

namespace SmartDiscounts\UserDiscounts\Traits;

use SmartDiscounts\UserDiscounts\Models\UserDiscount;
use SmartDiscounts\UserDiscounts\Services\DiscountService;

trait HasDiscounts
{
    public function discounts()
    {
        return $this->hasMany(UserDiscount::class);
    }

    public function assignDiscount($code)
    {
        return (new DiscountService())->assign($this, $code);
    }

    public function revokeDiscount($code)
    {
        (new DiscountService())->revoke($this, $code);
    }

    public function applyDiscounts($price)
    {
        return (new DiscountService())->apply($this, $price);
    }
}
