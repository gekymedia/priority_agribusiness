@extends('layouts.app')

@section('title', 'Edit House')

@section('content')
<h2 class="mb-4">Edit House/Pen</h2>
<form method="POST" action="{{ route('houses.update', $house) }}">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="form-label">Farm</label>
        <select name="farm_id" class="form-select" required>
            @foreach($farms as $farm)
                <option value="{{ $farm->id }}" {{ ($house->farm_id == $farm->id) ? 'selected' : '' }}>{{ $farm->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" value="{{ old('name', $house->name) }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Capacity</label>
        <input type="number" name="capacity" value="{{ old('capacity', $house->capacity) }}" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label">Type</label>
        <input type="text" name="type" value="{{ old('type', $house->type) }}" class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="{{ route('houses.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection