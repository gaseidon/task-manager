<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = auth()->user()->tasks()
            ->when(request('status'), function ($query) {
                return $query->where('is_completed', request('status'));
            })
            ->latest()
            ->paginate(10);

        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|string',
        ]);

        auth()->user()->tasks()->create($request->all());

        return redirect()->route('tasks.index');
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|string',
        ]);

        $task->update($request->all());

        return redirect()->route('tasks.index');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();
        return redirect()->route('tasks.index');
    }

    public function toggle(Task $task)
    {
        $this->authorize('update', $task);
        $task->update(['is_completed' => !$task->is_completed]);
        return back();
    }
}