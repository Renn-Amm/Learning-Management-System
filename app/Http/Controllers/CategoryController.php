<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of categories with caching.
     * Cache is cleared when categories are created, updated, or deleted.
     */
    public function index()
    {
        // Authorization: Only teachers can view categories
        $this->authorize('viewAny', Category::class);

        // Cache categories list for 60 minutes to reduce database queries
        $categories = Cache::remember('categories.index', 3600, function () {
            return Category::with('user')->withCount('courses')->get();
        });

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        // Authorization: Only teachers can create categories
        $this->authorize('create', Category::class);

        return view('categories.create');
    }

    public function store(Request $request)
    {
        // Authorization: Only teachers can create categories
        $this->authorize('create', Category::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['color_code'] = $this->generateUniqueColor();

        Category::create($validated);

        // Clear categories cache after creating new category
        Cache::forget('categories.index');

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    private function generateUniqueColor()
    {
        $usedColors = Category::pluck('color_code')->toArray();
        
        $availableColors = [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8',
            '#F7DC6F', '#BB8FCE', '#85C1E2', '#F8B739', '#52B788',
            '#E63946', '#F77F00', '#06AED5', '#118AB2', '#073B4C',
            '#8338EC', '#FB5607', '#FFBE0B', '#38B000', '#FF1744',
            '#00E676', '#FFEA00', '#FF9100', '#651FFF', '#00B0FF',
        ];
        
        foreach ($availableColors as $color) {
            if (!in_array($color, $usedColors)) {
                return $color;
            }
        }
        
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    public function show(Category $category)
    {
        $courses = $category->courses()->with('teacher')->withCount('lessons', 'enrollments')->paginate(12);
        return view('categories.show', compact('category', 'courses'));
    }

    public function edit(Category $category)
    {
        // Authorization: Only category owner can edit
        $this->authorize('update', $category);

        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        // Authorization: Only category owner can update
        $this->authorize('update', $category);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update($validated);

        // Clear categories cache after updating
        Cache::forget('categories.index');

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        // Authorization: Only category owner can delete, and only if no courses
        $this->authorize('delete', $category);

        if ($category->courses()->count() > 0) {
            return redirect()->route('categories.index')->with('error', 'Cannot delete category with existing courses.');
        }

        $category->delete();

        // Clear categories cache after deleting
        Cache::forget('categories.index');

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
