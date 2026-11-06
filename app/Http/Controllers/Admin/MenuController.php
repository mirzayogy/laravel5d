<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $categoryId = $request->query('category');
        $availability = $request->query('availability');

        $menus = Menu::with(['category', 'tags'])
            ->when($search !== '', fn ($query) => $query->where(
                fn ($inner) => $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
            ))
            ->when($categoryId, fn ($query) => $query->where('category_id', (int) $categoryId))
            ->when($availability === 'available', fn ($query) => $query->where('is_available', true))
            ->when($availability === 'sold_out', fn ($query) => $query->where(
                fn ($inner) => $inner->where('is_available', false)->orWhere('stock', 0)
            ))
            ->when($availability === 'low_stock', fn ($query) => $query->whereBetween('stock', [1, 5]))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('admin.menus.index', compact('menus', 'categories', 'search', 'categoryId', 'availability'));
    }

    public function create(): View
    {
        return view('admin.menus.form', $this->formData(null));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $attributes = $this->attributes($request, $data);

        if ($request->hasFile('image')) {
            $attributes['image'] = $request->file('image')->store('menus', 'public');
        }

        $menu = Menu::create($attributes);
        $menu->tags()->sync($data['tag_ids'] ?? []);

        return redirect()->route('admin.menus.index')->with('success', __('Menu created.'));
    }

    public function edit(Menu $menu): View
    {
        $menu->load('tags');

        return view('admin.menus.form', $this->formData($menu));
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $data = $this->validated($request);

        $attributes = $this->attributes($request, $data);

        if ($request->hasFile('image')) {
            $this->deleteStoredImage($menu);
            $attributes['image'] = $request->file('image')->store('menus', 'public');
        } elseif ($request->boolean('remove_image')) {
            $this->deleteStoredImage($menu);
            $attributes['image'] = null;
        }

        $menu->update($attributes);
        $menu->tags()->sync($data['tag_ids'] ?? []);

        return redirect()->route('admin.menus.index')->with('success', __('Menu updated.'));
    }

    public function toggle(Menu $menu): RedirectResponse
    {
        $menu->update(['is_available' => ! $menu->is_available]);

        return back()->with('success', __(':name is now :state.', ['name' => $menu->name, 'state' => $menu->is_available ? __('available') : __('sold out')]));
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        if ($menu->orderItems()->exists()) {
            return back()->with('error', __('This menu is used in orders. Mark it as unavailable instead of deleting it.'));
        }

        $this->deleteStoredImage($menu);
        $menu->delete();

        return redirect()->route('admin.menus.index')->with('success', __('Menu deleted.'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0', 'max:1000000'],
            'stock' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'is_available' => ['nullable', 'boolean'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function attributes(Request $request, array $data): array
    {
        return [
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'stock' => $data['stock'] ?? null,
            'is_available' => $request->boolean('is_available'),
        ];
    }

    /**
     * Only files uploaded through the form are deleted; photos shipped in public/images stay.
     */
    private function deleteStoredImage(Menu $menu): void
    {
        if ($menu->image && ! str_starts_with($menu->image, 'images/') && ! str_starts_with($menu->image, 'http')) {
            Storage::disk('public')->delete($menu->image);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(?Menu $menu): array
    {
        return [
            'menu' => $menu,
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ];
    }
}
