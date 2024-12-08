<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive GeoJSON Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-draw/dist/leaflet.draw.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw/dist/leaflet.draw.css" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h3>Interactive Map</h3>
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

<script>
    var map = L.map('map').setView([-7.250445, 112.768845], 13);

    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    // Draw control
    var drawControl = new L.Control.Draw({
        edit: { featureGroup: drawnItems },
        draw: { polygon: true, polyline: true, marker: true, rectangle: true }
    });
    map.addControl(drawControl);

    // Fetch and load existing data from the server
    function loadMapData() {
        fetch('/geographic/data')
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    result.data.forEach(item => {
                        var geoJsonLayer = L.geoJSON(JSON.parse(item.coordinates));

                        geoJsonLayer.addTo(map);

                        geoJsonLayer.on('click', function() {
                            // Zoom to the clicked feature
                            map.fitBounds(geoJsonLayer.getBounds());

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
                    loadMapData(); // Reload map data after adding new data
                    modal.hide();
                }
            });
        });

        drawnItems.addLayer(layer); // Add layer to map
    });
</script>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
