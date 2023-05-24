<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('category.index', compact('categories'));
    }

    public function create()
    {
        return view('category.create');
    }

    public function edit(Category $category)
    {
        // Code lebih pendek dan tidak memerlukan else
        if (auth()->user()->id == $category->user_id) {
            return view('category.edit', compact('category'));
        }

        // Bagian Else
        return redirect()->route('category.index')->with('danger', 'You are not authorized to edit this todo!');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'title' => 'required|max:255',
        ]);
        $category->update([
            'title' => ucfirst($request->title),
        ]);
        return redirect()->route('category.index')->with('success', 'Category updated successfully!');
    }

    public function store(Request $request, Category $categories)
    {
        $request->validate([
            'title' => 'required|max:255',
        ]);

        // Practical
        // $todo = new Todo;
        // $todo->title = $request->title;
        // $todo->user_id = auth()->user()->id;
        // $todo->save();

        // Query Builder way
        // DB::table('todos)->insert([
        // 'title' => $request->title,
        // 'user_id' => auth()->user()->id,
        // 'created_at' => now(),
        // 'updated_at' => now(),
        // ])

        // Eloquent Way - Readable

        $categories = Category::create([
            'title' => ucfirst($request->title),
            'user_id' => auth()->user()->id,
        ]);

        return redirect()->route('category.index')->with('success', $categories->title . ' Category created successfully!');
    }

    public function destroy(Category $category)
    {
        if (auth()->user()->id == $category->user_id) {
            $category->delete();
            return redirect()->route('category.index')->with('success', $category->title . ' Category deleted successfully!');
        }
        return redirect()->route('category.index')->with('danger', 'You are not authorized to delete this todo!');
    }
}
