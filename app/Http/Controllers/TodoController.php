<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Todo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use function Pest\Laravel\delete;
use function Pest\Laravel\get;

class TodoController extends Controller
{
    public function index()
    {
        $todos = Todo::where('user_id', auth()->user()->id)
            ->orderBy('is_complete', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        $categories = Category::where('user_id', auth()->user()->id)
            ->get();

        // dd($todos);
        $todosCompleted = Todo::where('user_id', auth()->user()->id)
            ->where('is_complete', true)
            ->count();
        return view('todo.index', compact('todos', 'todosCompleted', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('todo.create', compact('categories'));
    }

    public function edit(Todo $todo)
    {
        // Code lebih pendek dan tidak memerlukan else
        if (auth()->user()->id == $todo->user_id) {
            $categories = Category::where('user_id', auth()->user()->id)
                ->get();
            return view('todo.edit', compact('todo', 'categories'));
        }

        // Bagian Else
        return redirect()->route('todo.index')->with('danger', 'You are not authorized to edit this todo!');
    }

    public function update(Request $request, Todo $todo)
    {
        $request->validate([
            'title' => 'required|max:255',
        ]);
        $todo->update([
            'title' => ucfirst($request->title),
            'category_id' => $request->category_id,
        ]);
        return redirect()->route('todo.index')->with('success', 'Todo updated successfully!');
    }

    public function complete(Todo $todo)
    {
        if (auth()->user()->id == $todo->user_id) {
            $todo->update([
                'is_complete' => true,
            ]);
            return redirect()->route('todo.index')->with('success', 'Todo completed successfully!');
        }
        return redirect()->route('todo.index')->with('danger', 'You are not authorized to complete this todo!');
    }

    public function uncomplete(Todo $todo)
    {
        if (auth()->user()->id == $todo->user_id) {
            $todo->update([
                'is_complete' => false,
            ]);
            return redirect()->route('todo.index')->with('success', 'Todo uncompleted successfully!');
        }
        return redirect()->route('todo.index')->with('danger', 'You are not authorized to uncomplete this todo!');
    }

    public function store(Request $request, Todo $todo)
    {
        $request->validate([
            'title' => 'required|max:255',
            'category_id'  => 'required|max:255'
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

        $todo = Todo::create([
            'title' => ucfirst($request->title),
            'user_id' => auth()->user()->id,
            'category_id' => $request->category_id
        ]);

        return redirect()->route('todo.index')->with('success', 'Todo created successfully!');
    }

    public function destroy(Todo $todo)
    {
        if (auth()->user()->id == $todo->user_id) {
            $todo->delete();
            return redirect()->route('todo.index')->with('success', 'Todo deleted successfully!');
        }
        return redirect()->route('todo.index')->with('danger', 'You are not authorized to delete this todo!');
    }

    public function destroyCompleted()
    {
        $todoCompleted = Todo::where('user_id', auth()->user()->id)
            ->where('is_complete', true)
            ->get();
        foreach ($todoCompleted as $todo) {
            $todo->delete();
        }
        return redirect()->route('todo.index')->with('success', 'All completed todos deleted successfully!');
    }
}
