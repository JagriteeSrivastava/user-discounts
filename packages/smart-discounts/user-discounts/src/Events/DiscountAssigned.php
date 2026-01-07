<?php

namespace SmartDiscounts\UserDiscounts\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DiscountAssigned
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public $user;
    public $discount;

    public function __construct($user, $discount)
    {
        $this->user = $user;
        $this->discount = $discount;
    }
}
