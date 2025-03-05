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
        <div id="task-{{ $task->id }}" class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <input
                    type="checkbox"
                    class="form-check-input me-2 toggle-status"
                    data-task-id="{{ $task->id }}"
                    {{ $task->is_completed ? 'checked' : '' }}>
            </div>
            <div class="flex-grow-1 ms-3">
                <h5 id="task-title-{{ $task->id }}" class="{{ $task->is_completed ? 'text-decoration-line-through' : '' }}">
                    {{ $task->title }}
                </h5>
                @if($task->description)
                <p id="task-description-{{ $task->id }}" class="mb-0 {{ $task->is_completed ? 'text-decoration-line-through' : '' }}">
                    {{ $task->description }}
                </p>
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
        {{ $tasks->appends(['status' => $status])->links() }}
    </div>
    @endif
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Находим все чекбоксы с классом toggle-status
        document.querySelectorAll('.toggle-status').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const taskId = this.dataset.taskId; // Получаем ID задачи
                const isCompleted = this.checked; // Получаем новое состояние чекбокса

                // Получаем CSRF-токен
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                // Отправляем AJAX-запрос
                fetch(`/tasks/${taskId}/toggle`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            is_completed: isCompleted
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Обновляем интерфейс
                            const taskTitle = document.getElementById(`task-title-${taskId}`);
                            const taskDescription = document.getElementById(`task-description-${taskId}`);

                            // Добавляем или убираем класс text-decoration-line-through
                            if (data.is_completed) {
                                taskTitle.classList.add('text-decoration-line-through');
                                if (taskDescription) {
                                    taskDescription.classList.add('text-decoration-line-through');
                                }
                            } else {
                                taskTitle.classList.remove('text-decoration-line-through');
                                if (taskDescription) {
                                    taskDescription.classList.remove('text-decoration-line-through');
                                }
                            }

                            // Удаляем задачу из списка, если она больше не соответствует текущему фильтру
                            const currentStatus = new URLSearchParams(window.location.search).get('status');
                            if (
                                (currentStatus === 'pending' && data.is_completed) ||
                                (currentStatus === 'completed' && !data.is_completed)
                            ) {
                                const taskElement = document.getElementById(`task-${taskId}`);
                                if (taskElement) {
                                    taskElement.remove(); // Удаляем задачу из DOM
                                }
                            }
                        } else {
                            // Возвращаем чекбокс в исходное состояние, если что-то пошло не так
                            this.checked = !isCompleted;
                            alert('Failed to update task status.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.checked = !isCompleted; // Возвращаем чекбокс в исходное состояние
                        alert('An error occurred while updating the task status.');
                    });
            });
        });
    });
</script>
@endsection
