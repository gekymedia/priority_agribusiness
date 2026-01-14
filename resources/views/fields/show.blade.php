@extends('layouts.app')

@section('title', 'View Field')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h2>Field: {{ $field->name }}</h2>
    <div>
        <a href="{{ route('fields.edit', $field) }}" class="btn btn-warning">Edit</a>
        <form action="{{ route('fields.destroy', $field) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this field?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>
<div class="card mb-3">
    <div class="card-body">
        <p><strong>Farm:</strong> {{ $field->farm->name ?? '' }}</p>
        <p><strong>Size:</strong> {{ $field->size }}</p>
        <p><strong>Soil Type:</strong> {{ $field->soil_type }}</p>
        <p><strong>Description:</strong><br>{{ $field->description }}</p>
    </div>
</div>
<a href="{{ route('fields.index') }}" class="btn btn-secondary">Back to list</a>
@endsection