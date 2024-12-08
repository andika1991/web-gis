@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Geographic Object</h3>

    <form action="{{ route('geographic.update', $item->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT') <!-- This is for PUT request to simulate updating -->

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $item->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $item->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="photo" class="form-label">Upload Photo</label>
            <input type="file" class="form-control" id="photo" name="photo" accept="image/*" onchange="previewImage(event)">
        </div>

        <div class="mb-3">
            <!-- Combined Image Preview -->
            <img id="image-preview" src="{{ old('photo', asset('storage/' . $item->photo)) }}" alt="Image Preview" style="width: 100px; height: 100px; display: block;">
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="/" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('image-preview');
            output.src = reader.result;
            output.style.display = 'block'; // Show the preview image
        };
        reader.readAsDataURL(event.target.files[0]); // Read the selected file
    }
</script>
@endsection
