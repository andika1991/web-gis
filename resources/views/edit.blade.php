@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Geographic Object</h3>

    <form action="{{ route('geographic.update', $item->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Name Field -->
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $item->name) }}" required>
        </div>

        <!-- Description Field -->
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $item->description) }}</textarea>
        </div>

        <!-- Coordinates Field -->
        <div class="mb-3">
            <label for="coordinates" class="form-label">Coordinates (Longitude, Latitude)</label>
            @php
                // Extract the coordinates in [longitude, latitude] format
                $coordinates = json_decode($item->coordinates)->coordinates ?? [0, 0];
            @endphp
            <input type="text" class="form-control" id="coordinates" name="coordinates"
                   value="{{ old('coordinates', implode(',', $coordinates)) }}" placeholder="105.24147,-5.37222" required>
            <small class="text-muted">Input format: longitude,latitude</small>
        </div>

        <!-- Photo Upload Field -->
        <div class="mb-3">
            <label for="photo" class="form-label">Upload Photo</label>
            <input type="file" class="form-control" id="photo" name="photo" accept="image/*" onchange="previewImage(event)">
        </div>

        <!-- Image Preview -->
        <div class="mb-3">
            <img id="image-preview" src="{{ old('photo', asset('storage/' . $item->photo)) }}" alt="Image Preview"
                 style="width: 100px; height: 100px; display: block;">
        </div>

        <!-- Action Buttons -->
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="/" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
    // Function to preview uploaded image
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function () {
            var output = document.getElementById('image-preview');
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection
