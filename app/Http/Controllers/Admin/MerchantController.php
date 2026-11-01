<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    public function index()
    {
        $merchants = Merchant::with('owner')->withCount('menus')->get();
        return view('admin.merchants.index', compact('merchants'));
    }

    public function create()
    {
        $users = User::all();
        return view('admin.merchants.form', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'store_name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:merchants',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'phone_number' => 'required|string|max:20',
            'is_open' => 'boolean',
            'rating' => 'nullable|numeric|min:0|max:5',
        ]);

        $validated['is_open'] = $request->has('is_open');
        Merchant::create($validated);
        return redirect()->route('admin.merchants.index')->with('success', 'Merchant created successfully!');
    }

    public function edit(Merchant $merchant)
    {
        $users = User::all();
        return view('admin.merchants.form', compact('merchant', 'users'));
    }

    public function update(Request $request, Merchant $merchant)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'store_name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:merchants,slug,' . $merchant->id,
            'description' => 'nullable|string',
            'address' => 'required|string',
            'phone_number' => 'required|string|max:20',
            'is_open' => 'boolean',
            'rating' => 'nullable|numeric|min:0|max:5',
        ]);

        $validated['is_open'] = $request->has('is_open');
        $merchant->update($validated);
        return redirect()->route('admin.merchants.index')->with('success', 'Merchant updated successfully!');
    }

    public function destroy(Merchant $merchant)
    {
        $merchant->delete();
        return redirect()->route('admin.merchants.index')->with('success', 'Merchant deleted successfully!');
    }
}

