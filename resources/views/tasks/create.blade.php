@extends('layouts.app')

@section('title', 'Add Task')

@section('content')
<div class="page-header">
    <h1 class="page-title">Add Task / Reminder</h1>
    <p class="page-subtitle">Create a new task or reminder for your agricultural activities</p>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <form method="POST" action="{{ route('tasks.store') }}">
            @csrf

            <div class="row g-3">
                <div class="col-12">
                    <label for="title" class="form-label">
                        <i class="fas fa-heading me-2"></i>Title
                    </label>
                    <input type="text" 
                           class="form-control @error('title') is-invalid @enderror" 
                           id="title" 
                           name="title" 
                           value="{{ old('title') }}" 
                           required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="description" class="form-label">
                        <i class="fas fa-align-left me-2"></i>Description
                    </label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" 
                              name="description" 
                              rows="4">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="due_date" class="form-label">
                        <i class="fas fa-calendar me-2"></i>Due Date
                    </label>
                    <input type="date" 
                           class="form-control @error('due_date') is-invalid @enderror" 
                           id="due_date" 
                           name="due_date" 
                           value="{{ old('due_date') }}">
                    @error('due_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="priority" class="form-label">
                        <i class="fas fa-exclamation-circle me-2"></i>Priority
                    </label>
                    <select class="form-select @error('priority') is-invalid @enderror" 
                            id="priority" 
                            name="priority" 
                            required>
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                    @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="assigned_to" class="form-label">
                        <i class="fas fa-user me-2"></i>Assign to Employee
                    </label>
                    <select class="form-select @error('assigned_to') is-invalid @enderror" 
                            id="assigned_to" 
                            name="assigned_to">
                        <option value="">Unassigned</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('assigned_to') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->full_name }} ({{ ucfirst($employee->access_level) }})
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Select an employee to assign this task to</small>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-agri">
                    <i class="fas fa-save me-2"></i>Create Task
                </button>
                <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection