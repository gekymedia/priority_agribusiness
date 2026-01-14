@extends('layouts.app')

@section('title', 'Add House')

@section('content')
<h2 class="mb-4">Add House/Pen</h2>
<form method="POST" action="{{ route('houses.store') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Farm</label>
        <select name="farm_id" class="form-select" required>
            @foreach($farms as $farm)
                <option value="{{ $farm->id }}">{{ $farm->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
        @error('name')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label class="form-label">Capacity</label>
        <input type="number" name="capacity" value="{{ old('capacity') }}" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label">Type</label>
        <input type="text" name="type" value="{{ old('type') }}" class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="{{ route('houses.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection