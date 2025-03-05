@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Task List</h1>
    <a href="{{ route('tasks.create') }}" class="btn btn-primary mb-3">Create New Task</a>

    <!-- Переключатель категорий -->
    <div class="mb-3">
        <a href="{{ route('tasks.index', ['status' => 'all']) }}" class="btn btn-outline-secondary {{ $status === 'all' ? 'active' : '' }}">All Tasks</a>
        <a href="{{ route('tasks.index', ['status' => 'pending']) }}" class="btn btn-outline-secondary {{ $status === 'pending' ? 'active' : '' }}">Pending</a>
        <a href="{{ route('tasks.index', ['status' => 'completed']) }}" class="btn btn-outline-secondary {{ $status === 'completed' ? 'active' : '' }}">Completed</a>
    </div>

    @if($tasks->isEmpty())
        <p>No tasks found.</p>
    @else
        <div class="list-group">
            @foreach($tasks as $task)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <form action="{{ route('tasks.toggle', $task) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input 
                                type="checkbox" 
                                class="form-check-input me-2" 
                                {{ $task->is_completed ? 'checked' : '' }}
                                onchange="this.form.submit()"
                            >
                        </form>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="{{ $task->is_completed ? 'text-decoration-line-through' : '' }}">{{ $task->title }}</h5>
                        @if($task->description)
                            <p class="mb-0 {{ $task->is_completed ? 'text-decoration-line-through' : '' }}">{{ $task->description }}</p>
                        @endif
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('tasks.destroy', $task) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">
            {{ $tasks->links() }}
        </div>
    @endif
</div>
@endsection