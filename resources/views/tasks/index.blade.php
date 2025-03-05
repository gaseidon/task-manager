@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-between mb-4">
        <div class="col-md-6">
            <h1>My Tasks</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('tasks.create') }}" class="btn btn-primary">New Task</a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('tasks.index') }}" method="GET">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">All Tasks</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Completed</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Pending</option>
                </select>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
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
                                <h5>{{ $task->title }}</h5>
                                @if($task->description)
                                    <p class="mb-0">{{ $task->description }}</p>
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
    </div>
</div>
@endsection
