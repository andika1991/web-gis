@extends('layouts.app')

@section('content')
    <h1>{{ isset($coordinate) ? 'Edit' : 'Add' }} Coordinate</h1>
    <form action="{{ isset($coordinate) ? route('coordinates.update', $coordinate) : route('coordinates.store') }}" method="POST">
        @csrf
        @if(isset($coordinate))
            @method('PUT')
        @endif
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $coordinate->name ?? '' }}" required>
        </div>
        <div class="mb-3">
            <label for="latitude" class="form-label">Latitude</label>
            <input type="text" class="form-control" id="latitude" name="latitude" value="{{ $coordinate->latitude ?? '' }}" required>
        </div>
        <div class="mb-3">
            <label for="longitude" class="form-label">Longitude</label>
            <input type="text" class="form-control" id="longitude" name="longitude" value="{{ $coordinate->longitude ?? '' }}" required>
        </div>
        <button type="submit" class="btn btn-success">Save</button>
    </form>
@endsection
