<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DiscountPkgTest extends TestCase
{
    use RefreshDatabase;

    public function test_assign_and_check_eligibility()
    {
        $user = \App\Models\User::factory()->create();
        $discount = \SmartDiscounts\UserDiscounts\Models\Discount::create([
            'code' => 'WELCOME10',
            'name' => 'Welcome',
            'type' => 'percentage',
            'value' => 10,
        ]);

        $user->assignDiscount('WELCOME10');

        $this->assertTrue($user->discounts()->where('discount_id', $discount->id)->exists());
        $this->assertDatabaseHas('discount_audits', ['event' => 'assigned', 'user_id' => $user->id]);

        $service = new \SmartDiscounts\UserDiscounts\Services\DiscountService();
        $this->assertTrue($service->eligibleFor($user, 'WELCOME10'));
    }

    public function test_expired_discount_is_not_eligible()
    {
        $user = \App\Models\User::factory()->create();
        $discount = \SmartDiscounts\UserDiscounts\Models\Discount::create([
            'code' => 'EXPIRED',
            'name' => 'Expired',
            'type' => 'fixed',
            'value' => 5,
            'expires_at' => now()->subDays(1)
        ]);

        try {
            $user->assignDiscount('EXPIRED');
            $this->fail('Should throw exception');
        } catch (\Exception $e) {
            $this->assertEquals("Discount is inactive or expired.", $e->getMessage());
        }
    }

    public function test_apply_stacking_and_caps()
    {
        \Illuminate\Support\Facades\Config::set('discounts.stacking_order', ['percentage', 'fixed']);
        \Illuminate\Support\Facades\Config::set('discounts.max_percentage_cap', 50);
        \Illuminate\Support\Facades\Config::set('discounts.rounding', 'down');

        $user = \App\Models\User::factory()->create();

        $d1 = \SmartDiscounts\UserDiscounts\Models\Discount::create([
            'code' => 'P10',
            'name' => '10 Percent',
            'type' => 'percentage',
            'value' => 10
        ]);
        $d2 = \SmartDiscounts\UserDiscounts\Models\Discount::create([
            'code' => 'F5',
            'name' => '5 Fixed',
            'type' => 'fixed',
            'value' => 5
        ]);

        $user->assignDiscount('P10');
        $user->assignDiscount('F5');

        $finalPrice = $user->applyDiscounts(100);
        $this->assertEquals(85, $finalPrice); // 100 - 10% = 90; 90 - 5 = 85.

        $this->assertDatabaseHas('discount_audits', ['event' => 'applied', 'discount_id' => $d1->id]);
    }

    public function test_usage_limit()
    {
        $user = \App\Models\User::factory()->create();
        $discount = \SmartDiscounts\UserDiscounts\Models\Discount::create([
            'code' => 'ONCE',
            'name' => 'Once Only',
            'type' => 'fixed',
            'value' => 10,
            'usage_limit_per_user' => 1
        ]);

        $user->assignDiscount('ONCE');

        $service = new \SmartDiscounts\UserDiscounts\Services\DiscountService();
        $this->assertTrue($service->eligibleFor($user, 'ONCE'));
        $user->applyDiscounts(100);

        $this->assertEquals(1, $user->discounts()->first()->usage_count);

        $this->assertFalse($service->eligibleFor($user, 'ONCE'));

        $finalPrice = $user->applyDiscounts(100);
        $this->assertEquals(100, $finalPrice);
    }

    public function test_revoked_discount()
    {
        $user = \App\Models\User::factory()->create();
        $discount = \SmartDiscounts\UserDiscounts\Models\Discount::create([
            'code' => 'REVOKE',
            'name' => 'Revoke Me',
            'type' => 'fixed',
            'value' => 10
        ]);

        $user->assignDiscount('REVOKE');
        $user->revokeDiscount('REVOKE');

        $service = new \SmartDiscounts\UserDiscounts\Services\DiscountService();
        $this->assertFalse($service->eligibleFor($user, 'REVOKE'));

        $finalPrice = $user->applyDiscounts(100);
        $this->assertEquals(100, $finalPrice);
    }
}
