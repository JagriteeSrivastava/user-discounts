<?php

namespace App\Http\Controllers;

use App\Models\User;
use SmartDiscounts\UserDiscounts\Models\Discount;
use SmartDiscounts\UserDiscounts\Services\DiscountService;
use Illuminate\Http\Request;

class DemoController extends Controller
{
    protected $discountService;

    public function __construct(DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }

    public function index()
    {
        // Ensure we have a default test user
        $user = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            ['name' => 'Demo User', 'password' => bcrypt('password')]
        );

        // Ensure we have some test discounts
        Discount::firstOrCreate(['code' => 'WELCOME10'], [
            'name' => 'Welcome 10%',
            'type' => 'percentage',
            'value' => 10,
            'active' => true
        ]);

        Discount::firstOrCreate(['code' => 'SAVE5'], [
            'name' => 'Save $5',
            'type' => 'fixed',
            'value' => 5,
            'active' => true
        ]);

        $discounts = Discount::all();
        $userDiscounts = $user->discounts()->with('discount')->get();
        // CALL WITH $logUsage = false to prevent refresh bug
        $finalPrice = $this->discountService->apply($user, 100, false);

        // Fetch recent audits for transparency
        $audits = \SmartDiscounts\UserDiscounts\Models\DiscountAudit::where('user_id', $user->id)
            ->with('discount')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('demo', compact('user', 'discounts', 'userDiscounts', 'finalPrice', 'audits'));
    }

    public function assign(Request $request)
    {
        $user = User::first();
        try {
            // Assign WITHOUT auditing the 'assigned' event separately
            $this->discountService->assign($user, $request->code, false);
            // Trigger the 'applied' audit event immediately upon application from dashboard
            $this->discountService->apply($user, 100, true);

            return back()->with('success', 'Discount ' . $request->code . ' applied successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function revoke(Request $request)
    {
        $user = User::first();
        $this->discountService->revoke($user, $request->code);
        return back()->with('success', 'Discount ' . $request->code . ' revoked!');
    }

    public function audits()
    {
        $audits = \SmartDiscounts\UserDiscounts\Models\DiscountAudit::with('discount')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('audits.index', compact('audits'));
    }
}
