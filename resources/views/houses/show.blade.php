@extends('layouts.app')

@section('title', 'View House')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h2>House: {{ $house->name }}</h2>
    <div>
        <a href="{{ route('houses.edit', $house) }}" class="btn btn-warning">Edit</a>
        <form action="{{ route('houses.destroy', $house) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this house?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>
<div class="card mb-3">
    <div class="card-body">
        <p><strong>Farm:</strong> {{ $house->farm->name ?? '' }}</p>
        <p><strong>Capacity:</strong> {{ $house->capacity }}</p>
        <p><strong>Type:</strong> {{ $house->type }}</p>
    </div>
</div>
<a href="{{ route('houses.index') }}" class="btn btn-secondary">Back to list</a>
@endsection