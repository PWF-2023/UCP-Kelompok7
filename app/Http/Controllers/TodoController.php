<?php

namespace App\Http\Controllers;

use App\Models\Todo;

use Illuminate\Http\Request;

use function Pest\Laravel\delete;
use function Pest\Laravel\get;

class TodoController extends Controller
{
    public function index()
    {
        // $todos = Todo::where('user_id',auth()->user()->id)
        // ->orderBy('is_complete','asc')
        // ->orderBy('created_at','desc')
        // ->get();

        // $todosCompleted = Todo::where('user_id', auth()->user()->id)
        // ->where('is_complete', true)
        // ->count();

        // return view('todo.index',compact('todos', 'todosCompleted'));

        return view('category.index');
    }

    public function store(Request $request, Todo $todo)
    {
        $request->validate([
            'title' => 'required|max:255',
        ]);

        // // Practical
        // $todo = new Todo;
        // $todo->title = $request->title;
        // $todo->user_id = auth()->user()->id;
        // $todo->save();

        // // Query Builder way
        // DB::table('todos')->insert([
        //     'title' => $request->title,
        //     'user_id' => auth()->user()->id,
        //     'created_at' => now(),
        //     'update_at' => now(),
        // ]);

        // Eloquent Way - Readable

        // $todo = Todo::create([
        //     'title' => ucfirst($request->title),
        //     'user_id' => auth()->user()->id,
        // ]);

        // Eloqvent way - Shortest
        $request->user()->todos()->create($request->all());
        // $request->user()->todos()->create([
        //     'title' => ucfirst($request->title),
        // ]);

        return redirect()->route('todo.index')->with('success', 'Todo create successfully');


    }

    public function create()
    {
        return view('todo.create');
    }

    public function edit(Todo $todo)
    {
        // if(auth()->user()->id == $todo->user_id)
        // {
        //     // dd($todo);
        //     return view('todo.edit', compact('todo'));
        // }else{
        //     // abort(403);
        //     // abort(403, 'Not authorized')
        //     return redirect()->route('todo.index')->with('danger','You are not authorized to edit this todo!');
        // }

        if (auth()->user()->id = $todo->user_id){
            return view('todo.edit', compact('todo'));
        }
        return redirect()->route('todo.index')->with('danger','You are not authorized to edit this todo!');
    }

    public function update(Request $request, Todo $todo)
    {
        $request->validate([
            'title' => 'required|max:255',
        ]);

        // Practical
        // $todo->title = $request->title;
        // $todo->save();

        // Elequent Way - Readble
        $todo->update([
            'title' => ucfirst($request->title),
        ]);
        return redirect()->route('todo.index')->with('success', 'Todo update successfully');

    }

    public function complete(Todo $todo)
    {
        if(auth()->user()->id == $todo->user_id){
            $todo->update([
                'is_complete' => true,
            ]);
            return redirect()->route('todo.index')->with('success','Todo comleted successfully!');
        }else{
            return redirect()->route('todo.index')->with('danger','You are not authorized to complete this todo!');

        }
    }

    public function uncomplete(Todo $todo)
    {
        if(auth()->user()->id == $todo->user_id){
            $todo->update([
                'is_complete' => false,
            ]);
            return redirect()->route('todo.index')->with('success','Todo uncomleted successfully!');
        }else{
            return redirect()->route('todo.index')->with('danger','You are not authorized to complete this todo!');

        }
    }

    public function destroy(Todo $todo)
    {
        if(auth()->user()->id == $todo->user_id)
        {
            $todo->delete();
            return redirect()->route('todo.index')->with('success','Todo deleted successfully!');
        }else{
            return redirect()->route('todo.index')->with('danger','You are not authorized to delete this todo!');
        }
    }

    public function destroyCompleted()
    {
        // get all todos for current user where is_complete is true
        $todosCompleted = Todo::where('user_id', auth()->user()->id)
            ->where('is_complete', true)
            ->get();
        foreach ($todosCompleted as $todo)
        {
            $todo->delete();
        }
        return redirect()->route('todo.index')->with('success','All completed todos deleted successfully!');
    }
}
