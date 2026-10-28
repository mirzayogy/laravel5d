<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $tag = $request->query('tag');

        $categories = Category::with(['menus' => function ($query) use ($tag) {
            $query->with('tags')->orderBy('name');

            if ($tag) {
                $query->whereHas('tags', fn ($tags) => $tags->where('name', $tag));
            }
        }])->orderBy('name')->get();

        $tags = Tag::orderBy('name')->get();

        return view('menu.index', compact('categories', 'tags', 'tag'));
    }
}
