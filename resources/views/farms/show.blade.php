@extends('layouts.app')

@section('title', 'View Farm')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h2>Farm: {{ $farm->name }}</h2>
    <div>
        <a href="{{ route('farms.edit', $farm) }}" class="btn btn-warning">Edit</a>
        <form action="{{ route('farms.destroy', $farm) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this farm?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>
<div class="card mb-3">
    <div class="card-body">
        <p><strong>Location:</strong> {{ $farm->location }}</p>
        <p><strong>Type:</strong> {{ ucfirst($farm->farm_type) }}</p>
        <p><strong>Description:</strong><br>{{ $farm->description }}</p>
    </div>
</div>
<a href="{{ route('farms.index') }}" class="btn btn-secondary">Back to list</a>
@endsection