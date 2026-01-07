<?php

namespace Tests\Unit;

use Tests\TestCase;
use SmartDiscounts\UserDiscounts\Models\Discount;
use SmartDiscounts\UserDiscounts\Models\UserDiscount;
use SmartDiscounts\UserDiscounts\Services\DiscountService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DiscountUsageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that usage cap is strictly enforced.
     */
    public function test_usage_cap_enforcement_logic()
    {
        $user = \App\Models\User::factory()->create();
        $discount = Discount::create([
            'code' => 'LIMIT2',
            'name' => 'Limit 2',
            'type' => 'fixed',
            'value' => 10,
            'usage_limit_per_user' => 2,
            'active' => true
        ]);

        $service = new DiscountService();
        $user->assignDiscount('LIMIT2');

        // Initial check
        $this->assertTrue($service->eligibleFor($user, $discount));

        // Use once
        $service->apply($user, 100);
        $this->assertTrue($service->eligibleFor($user, $discount));

        // Use twice
        $service->apply($user, 100);
        $this->assertFalse($service->eligibleFor($user, $discount), "Should not be eligible after 2 uses");
    }

    /**
     * Test that percentage caps are applied correctly.
     */
    public function test_percentage_cap_logic()
    {
        \Illuminate\Support\Facades\Config::set('discounts.max_percentage_cap', 50);

        $user = \App\Models\User::factory()->create();

        // Two 30% discounts = 60%, but should be capped at 50%
        Discount::create(['code' => 'P30A', 'name' => '30%', 'type' => 'percentage', 'value' => 30]);
        Discount::create(['code' => 'P30B', 'name' => '30%', 'type' => 'percentage', 'value' => 30]);

        $user->assignDiscount('P30A');
        $user->assignDiscount('P30B');

        $service = new DiscountService();
        $price = $service->apply($user, 100);

        // 100 - 50% = 50
        $this->assertEquals(50, $price, "Should be capped at 50% discount");
    }
}
