<?php

namespace SmartDiscounts\UserDiscounts\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DiscountApplied
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public $user;
    public $discount;
    public $priceBefore;
    public $priceAfter;

    public function __construct($user, $discount, $priceBefore, $priceAfter)
    {
        $this->user = $user;
        $this->discount = $discount;
        $this->priceBefore = $priceBefore;
        $this->priceAfter = $priceAfter;
    }
}
