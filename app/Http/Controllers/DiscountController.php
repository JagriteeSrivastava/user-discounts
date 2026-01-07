<?php

namespace App\Http\Controllers;

use SmartDiscounts\UserDiscounts\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::all();
        return view('discounts.index', compact('discounts'));
    }

    public function create()
    {
        return view('discounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:discounts,code',
            'name' => 'required',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'usage_limit_per_user' => 'required|integer|min:1',
            'active' => 'boolean',
            'expires_at' => 'nullable|date'
        ]);

        Discount::create($request->all());

        return redirect()->route('discounts.index')->with('success', 'Discount created successfully!');
    }

    public function edit(Discount $discount)
    {
        return view('discounts.edit', compact('discount'));
    }

    public function update(Request $request, Discount $discount)
    {
        $request->validate([
            'code' => 'required|unique:discounts,code,' . $discount->id,
            'name' => 'required',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'usage_limit_per_user' => 'required|integer|min:1',
            'active' => 'boolean',
            'expires_at' => 'nullable|date'
        ]);

        // Handle the 'active' checkbox (if unchecked, it won't be in the request)
        $data = $request->all();
        $data['active'] = $request->has('active');

        $discount->update($data);

        return redirect()->route('discounts.index')->with('success', 'Discount updated successfully!');
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();
        return redirect()->route('discounts.index')->with('success', 'Discount deleted successfully!');
    }
}
