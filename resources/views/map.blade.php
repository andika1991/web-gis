@extends('layouts.app')

@section('title', 'Geographic Data Management')

@section('content')
<body>
 <!-- Data Table -->
 <a href="{{ route('geographic.add') }}" class="btn btn-primary">Add New Geographic Data</a>

 <form action="{{ route('home') }}" method="GET" class="mb-3">
    <div class="input-group">
        <input type="text" name="search" class="form-control" 
               placeholder="Search by name..." value="{{ $search ?? '' }}">
        <button type="submit" class="btn btn-primary">Search</button>
    </div>
</form>

<!-- Table -->
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Tipe</th>
            <th>Coordinates</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($data as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->type }}</td>
                <td>
                    @php
                        $coordinates = json_decode($item->coordinates);
                        // Check if the type is 'Point'
                        if (isset($coordinates->type) && $coordinates->type === 'Point' && isset($coordinates->coordinates)) {
                            $coordinatesArray = $coordinates->coordinates;
                        } else {
                            $coordinatesArray = null; // Don't display coordinates if not a 'Point'
                        }
                    @endphp
                
                    @if($coordinatesArray)
                        {{ implode(',', $coordinatesArray) }}
                    @else
                        <!-- Optionally, you can leave this blank or display something else -->
                        No Coordinates
                    @endif
                </td>
                
                <td>
                    @php
                        $coordinates = json_decode($item->coordinates);
                        // Check if the type is 'Point'
                        $isPoint = isset($coordinates->type) && $coordinates->type === 'Point';
                    @endphp
                
                    @if($isPoint)
                        <!-- Edit Button -->
                        <button class="btn btn-warning btn-sm" onclick="window.location.href='/geographic/edit/{{ $item->id }}'">
                            Edit
                        </button>
                    @endif
                
                    <!-- Delete Button -->
                    <button class="btn btn-danger btn-sm" 
                            data-id="{{ $item->id }}"
                            data-name="{{ $item->name }}"
                            data-bs-toggle="modal" 
                            data-bs-target="#deleteModal">
                        Delete
                    </button>
                </td>
                
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center">No data found.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Pagination Controls -->
<div class="d-flex justify-content-center">
    {{ $data->appends(['search' => $search])->links('vendor.pagination.bootstrap-5') }}
</div>
    <div class="container mt-4">
        <h3 style="font-weight:bold;">Map Interaktif</h3>
        <div style="width: 100%; height: 500px;" id="map"></div>
    </div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Object</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="editName" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editPhoto" class="form-label">Upload Photo</label>
                        <input type="file" class="form-control" id="editPhoto" accept="image/*">
                    </div>
                    <input type="hidden" id="editObjectId">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Modal Konfirmasi Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this object: <span id="deleteItemName"></span>?</p>
                <button id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>


<script>
         var map = L.map('map').setView([-5.368, 105.267], 12); // Center and zoom level

// 2. Add OpenStreetMap tile layer
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 19
}).addTo(map);

// 3. Add GeoJSON Data
// URL harus menunjuk ke file GeoJSON valid
var geojsonURL = 'https://raw.githubusercontent.com/andika1991/web-gis/geojson-page/public/geojson/Labuhan%20Ratu.geojson';

// Fetch GeoJSON
fetch(geojsonURL)
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('GeoJSON data loaded:', data);
        L.geoJSON(data, {
            style: function (feature) {
                return {       color: "blue",  // Warna polygon
                            weight: 2,      // Ketebalan garis polygon
                            opacity: 0.7  }; // Style for GeoJSON polygons
            },
            onEachFeature: function (feature, layer) {
                // Add popup for each feature
                if (feature.properties && feature.properties.name) {
                    layer.bindPopup('<b>' + feature.properties.name + '</b><br>' + feature.properties.description);
                }
            }
        }).addTo(map);
    })
    .catch(error => {
        console.error('Error loading GeoJSON:', error);
        alert('Failed to load GeoJSON data. Check the console for more details.');
    });

// 4. Add Draw Control
var drawnItems = new L.FeatureGroup();
map.addLayer(drawnItems);

var drawControl = new L.Control.Draw({
    edit: {
        featureGroup: drawnItems
    },
    draw: {
        polygon: true,
        polyline: true,
        rectangle: true,
        circle: false,
        marker: true
    }
});
map.addControl(drawControl);

// 5. Handle created layers
map.on('draw:created', function (e) {
    var layer = e.layer;
    drawnItems.addLayer(layer);
});

    // Handle Delete Button Click
    function handleDelete(id) {
        fetch(`/geographic/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                alert('Data deleted successfully!');
                location.reload();  // Reload the page to reflect changes
            } else {
                alert('Failed to delete data.');
            }
        });
    }

    // Handle modal delete action
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        var objectId = document.getElementById('deleteObjectId').value;
        handleDelete(objectId);
    });

    function loadMapData() {
        fetch('/geographic/data')
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    result.data.forEach(item => {
                        var geoJsonLayer = L.geoJSON(JSON.parse(item.coordinates));

                        geoJsonLayer.addTo(map);

                        geoJsonLayer.on('click', function() {
                            var popupContent = ` 
                                <b>${item.name}</b><br>
                                ${item.description || ''}<br>
                                <img src="storage/${item.photo}" alt="Photo" style="width: 100px; height: 100px;"><br>
                                <button class="edit-btn btn btn-warning btn-sm">Edit</button>
                                <button class="delete-btn btn btn-danger btn-sm">Delete</button>
                            `;

                            var customPopup = L.popup()
                                .setLatLng(geoJsonLayer.getBounds().getCenter())
                                .setContent(popupContent)
                                .openOn(map);

                            customPopup.getElement().querySelector('.edit-btn').addEventListener('click', function () {
                                document.getElementById('editName').value = item.name;
                                document.getElementById('editDescription').value = item.description || '';
                                document.getElementById('editObjectId').value = item.id;
                                var editModal = new bootstrap.Modal(document.getElementById('editModal'));
                                editModal.show();
                            });

                            customPopup.getElement().querySelector('.delete-btn').addEventListener('click', function () {
                                document.getElementById('deleteObjectId').value = item.id;
                                var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                                deleteModal.show();
                            });
                        });
                    });
                }
            });
    }

    loadMapData();

// 6. Add Geocoder (Search Bar)
L.Control.geocoder({
    defaultMarkGeocode: false
})
    .on('markgeocode', function (e) {
        var bbox = e.geocode.bbox;
        var poly = L.polygon([
            [bbox.getSouthEast().lat, bbox.getSouthEast().lng],
            [bbox.getNorthEast().lat, bbox.getNorthEast().lng],
            [bbox.getNorthWest().lat, bbox.getNorthWest().lng],
            [bbox.getSouthWest().lat, bbox.getSouthWest().lng]
        ]).addTo(map);
        map.fitBounds(poly.getBounds());
    })
    .addTo(map);


    var drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);


  
    function loadMapData() {
        fetch('/geographic/data')
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    result.data.forEach(item => {
                        var geoJsonLayer = L.geoJSON(JSON.parse(item.coordinates));

                        geoJsonLayer.addTo(map);

                        geoJsonLayer.on('click', function() {
                            // Create custom content for the popup with buttons
                            var popupContent = ` 
                                <b>${item.name}</b><br>
                                ${item.description || ''}<br>
                                <img src="storage/${item.photo}" alt="Photo" style="width: 100px; height: 100px;"><br>
                             <a href="/geographic/edit/${item.id}" class="edit-btn btn btn-warning btn-sm">Edit</a>

                                <button class="delete-btn btn btn-danger btn-sm">Delete</button>
                            `;

                            // Create a custom popup and set its content
                            var customPopup = L.popup()
                                .setLatLng(geoJsonLayer.getBounds().getCenter())  // Position the popup at the center of the layer
                                .setContent(popupContent)
                                .openOn(map);

                            // Handle Edit Button Click
                            customPopup.getElement().querySelector('.edit-btn').addEventListener('click', function () {
                                // Open the modal and pre-fill form fields
                                document.getElementById('editName').value = item.name;
                                document.getElementById('editDescription').value = item.description || '';
                                document.getElementById('editObjectId').value = item.id;

                                // Open the modal
                                var editModal = new bootstrap.Modal(document.getElementById('editModal'));
                                editModal.show();
                            });

                            // Handle Delete Button Click
                            customPopup.getElement().querySelector('.delete-btn').addEventListener('click', function () {
                                if (confirm('Are you sure you want to delete this object?')) {
                                    fetch(`/geographic/${item.id}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(result => {
                                        if (result.status === 'success') {
                                            alert('Data deleted successfully!');
                                            window.location.href = "{{ route('home') }}";
                                        } else {
                                            alert('Failed to delete data.');
                                        }
                                    });
                                }
                            });
                        });
                    });
                }
            });
    }

   

   
    loadMapData();

    // Handle new feature creation
    map.on('draw:created', function (e) {
        var layer = e.layer;
        var geoJSONData = layer.toGeoJSON();


        // Show modal for photo upload
        var modalHtml = `
            <div class="modal fade" id="newFeatureModal" tabindex="-1" aria-labelledby="newFeatureModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="newFeatureModalLabel">Add New Object</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="newFeatureForm">
                                <div class="mb-3">
                                    <label for="newFeatureName" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="newFeatureName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="newFeatureDescription" class="form-label">Description</label>
                                    <textarea class="form-control" id="newFeatureDescription" rows="3"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="newFeaturePhoto" class="form-label">Upload Photo</label>
                                    <input type="file" class="form-control" id="newFeaturePhoto" accept="image/*">
                                </div>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        var modal = new bootstrap.Modal(document.getElementById('newFeatureModal'));
        modal.show();

        document.getElementById('newFeatureForm').addEventListener('submit', function(event) {
            event.preventDefault();

            var formData = new FormData();
            formData.append('name', document.getElementById('newFeatureName').value);
            formData.append('description', document.getElementById('newFeatureDescription').value);
            formData.append('coordinates', JSON.stringify(geoJSONData.geometry));
            formData.append('type', geoJSONData.geometry.type);

            var photoInput = document.getElementById('newFeaturePhoto');
            if (photoInput.files[0]) {
                formData.append('photo', photoInput.files[0]);
            }

            fetch('/geographic', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    alert('Data added successfully!');
                    window.location.href = '/';
                    loadMapData(); // Reload map data after adding new data
                    modal.hide();
                }
            });
        });

        drawnItems.addLayer(layer); // Add layer to map
    });


    // Menangani klik tombol delete di modal
document.querySelectorAll('.btn-danger').forEach(function(button) {
    button.addEventListener('click', function () {
        const itemId = this.getAttribute('data-id'); // Ambil ID item yang akan dihapus
        const itemName = this.getAttribute('data-name'); // Ambil nama item

        // Tampilkan nama item yang akan dihapus di modal
        document.getElementById('deleteItemName').textContent = itemName;

        // Ketika tombol "Delete" di modal diklik
        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
            // Kirim permintaan DELETE ke server menggunakan fetch API
            fetch(`/geographic/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', // CSRF token untuk Laravel
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    alert('Data deleted successfully!');
                    window.location.reload(); // Reload halaman setelah penghapusan
                } else {
                    alert('Failed to delete data.');
                }
            })
            .catch(error => {
                alert('An error occurred while deleting the data.');
                console.error('Error:', error);
            });
        });
    });
});

</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

@endsection