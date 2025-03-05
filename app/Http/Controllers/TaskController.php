<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function index()
    {
        // Получаем статус из запроса (по умолчанию 'all')
        $status = request('status', 'all');

        // Фильтрация задач
        $tasks = auth()->user()->tasks()
            ->when($status === 'pending', function ($query) {
                return $query->where('is_completed', false); // Не выполненные задачи
            })
            ->when($status === 'completed', function ($query) {
                return $query->where('is_completed', true); // Выполненные задачи
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('tasks.index', compact('tasks', 'status'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|max:255',
            'description' => 'nullable|string',
        ]);

        auth()->user()->tasks()->create($request->all());

        return redirect()->route('tasks.index');
    }

    public function edit(Task $task)
    {
        $status = request('status', 'all');
        $page = request('page', 1);

        return view('tasks.edit', compact('task', 'status', 'page'));
    }

    public function update(Request $request, Task $task)
    {
        Gate::authorize('update', $task);

        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|string',
        ]);

        $task->update($request->all());

        return redirect()->route('tasks.index', [
            'status' => $request->status,
            'page' => $request->page,
        ])->with('success', 'Task updated successfully!');
    }

    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);
        $task->delete();
        return redirect()->route('tasks.index', [
            'status' => request('status'),
            'page' => request('page'),
        ])->with('success', 'Task deleted successfully!');
    }


    public function toggle(Task $task)
    {
        // Проверка прав доступа
        Gate::authorize('update', $task);

        // Изменение статуса задачи
        $task->update(['is_completed' => !$task->is_completed]);

        // Возвращаем JSON-ответ
        return response()->json([
            'success' => true,
            'is_completed' => $task->is_completed,
        ]);
    }
}
