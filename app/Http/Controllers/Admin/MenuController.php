<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Merchant;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with(['merchant', 'category', 'tags'])->get();
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        $merchants = Merchant::all();
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.menus.form', compact('merchants', 'categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:menus',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_available' => 'boolean',
            'preparation_time_minutes' => 'nullable|integer|min:1',
            'tags' => 'nullable|array',
        ]);

        $validated['is_available'] = $request->has('is_available');
        $menu = Menu::create($validated);
        if ($request->has('tags')) {
            $menu->tags()->sync($request->tags);
        }
        return redirect()->route('admin.menus.index')->with('success', 'Menu created successfully!');
    }

    public function edit(Menu $menu)
    {
        $merchants = Merchant::all();
        $categories = Category::all();
        $tags = Tag::all();
        $menu->load('tags');
        return view('admin.menus.form', compact('menu', 'merchants', 'categories', 'tags'));
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:menus,slug,' . $menu->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_available' => 'boolean',
            'preparation_time_minutes' => 'nullable|integer|min:1',
            'tags' => 'nullable|array',
        ]);

        $validated['is_available'] = $request->has('is_available');
        $menu->update($validated);
        $menu->tags()->sync($request->tags ?? []);
        return redirect()->route('admin.menus.index')->with('success', 'Menu updated successfully!');
    }

    public function destroy(Menu $menu)
    {
        $menu->tags()->detach();
        $menu->delete();
        return redirect()->route('admin.menus.index')->with('success', 'Menu deleted successfully!');
    }
}

