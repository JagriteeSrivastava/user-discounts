<?php

namespace SmartDiscounts\UserDiscounts\Services;

use SmartDiscounts\UserDiscounts\Models\Discount;
use SmartDiscounts\UserDiscounts\Models\UserDiscount;
use SmartDiscounts\UserDiscounts\Models\DiscountAudit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use SmartDiscounts\UserDiscounts\Events\DiscountAssigned;
use SmartDiscounts\UserDiscounts\Events\DiscountRevoked;
use SmartDiscounts\UserDiscounts\Events\DiscountApplied;

class DiscountService
{
    public function assign($user, string $code, bool $shouldAudit = true)
    {
        $discount = Discount::where('code', $code)->firstOrFail();

        if (!$discount->active || ($discount->expires_at && $discount->expires_at->isPast())) {
            throw new \Exception("Discount is inactive or expired.");
        }

        return DB::transaction(function () use ($user, $discount, $shouldAudit) {
            $userDiscount = UserDiscount::updateOrCreate(
                ['user_id' => $user->id, 'discount_id' => $discount->id],
                ['is_revoked' => false]
            );

            if ($shouldAudit) {
                DiscountAudit::create([
                    'user_id' => $user->id,
                    'discount_id' => $discount->id,
                    'event' => 'assigned',
                ]);
            }

            event(new DiscountAssigned($user, $discount));

            return $userDiscount;
        });
    }

    public function revoke($user, string $code)
    {
        $discount = Discount::where('code', $code)->firstOrFail();

        $userDiscount = UserDiscount::where('user_id', $user->id)
            ->where('discount_id', $discount->id)
            ->first();

        if ($userDiscount) {
            $userDiscount->update(['is_revoked' => true]);

            DiscountAudit::create([
                'user_id' => $user->id,
                'discount_id' => $discount->id,
                'event' => 'revoked',
            ]);

            event(new DiscountRevoked($user, $discount));
        }
    }

    public function eligibleFor($user, $discount)
    {
        if (is_string($discount)) {
            $discount = Discount::where('code', $discount)->first();
        }

        if (!$discount || !$discount->active) return false;
        if ($discount->expires_at && $discount->expires_at->isPast()) return false;

        $userDiscount = UserDiscount::where('user_id', $user->id)
            ->where('discount_id', $discount->id)
            ->first();

        if (!$userDiscount || $userDiscount->is_revoked) return false;

        if ($userDiscount->usage_count >= $discount->usage_limit_per_user) return false;

        return true;
    }

    public function apply($user, float $price, bool $logUsage = true)
    {
        return DB::transaction(function () use ($user, $price, $logUsage) {
            // Get all candidate discounts and lock them for update to prevent race conditions on usage_count
            $query = UserDiscount::where('user_id', $user->id)
                ->where('is_revoked', false)
                ->with('discount');

            if ($logUsage) {
                $query->lockForUpdate();
            }

            $userDiscounts = $query->get();

            $eligibleDiscounts = $userDiscounts->filter(function ($ud) use ($user) {
                return $this->eligibleFor($user, $ud->discount);
            });

            if ($eligibleDiscounts->isEmpty()) {
                return $price;
            }

            $totalPercentage = 0;
            $totalFixed = 0;
            $appliedDiscounts = [];

            foreach ($eligibleDiscounts as $ud) {
                $discount = $ud->discount;
                if ($discount->type === 'percentage') {
                    $totalPercentage += $discount->value;
                } elseif ($discount->type === 'fixed') {
                    $totalFixed += $discount->value;
                }
                $appliedDiscounts[] = $ud;
            }

            $maxCap = Config::get('discounts.max_percentage_cap', 100);
            if ($totalPercentage > $maxCap) {
                $totalPercentage = $maxCap;
            }

            $stackingOrder = Config::get('discounts.stacking_order', ['percentage', 'fixed']);
            $currentPrice = $price;
            $originalPrice = $price;

            foreach ($stackingOrder as $type) {
                if ($type === 'percentage' && $totalPercentage > 0) {
                    $currentPrice -= ($currentPrice * ($totalPercentage / 100));
                } elseif ($type === 'fixed' && $totalFixed > 0) {
                    $currentPrice -= $totalFixed;
                }
            }

            if ($currentPrice < 0) $currentPrice = 0;

            $rounding = Config::get('discounts.rounding', 'down');
            if ($rounding === 'down') {
                $currentPrice = floor($currentPrice * 100) / 100;
            } elseif ($rounding === 'up') {
                $currentPrice = ceil($currentPrice * 100) / 100;
            } else {
                $currentPrice = round($currentPrice, 2);
            }

            if ($logUsage) {
                foreach ($appliedDiscounts as $ud) {
                    $ud->increment('usage_count');

                    DiscountAudit::create([
                        'user_id' => $user->id,
                        'discount_id' => $ud->discount_id,
                        'event' => 'applied',
                        'metadata' => ['price_before' => $originalPrice, 'price_after' => $currentPrice]
                    ]);

                    event(new DiscountApplied($user, $ud->discount, $originalPrice, $currentPrice));
                }
            }

            return $currentPrice;
        });
    }
}
