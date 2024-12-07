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
</head>
<body>

<div style="width: 100%; height: 500px;" id="map"></div>

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
                        L.geoJSON(JSON.parse(item.coordinates), {
                            onEachFeature: function (feature, layer) {
                                if (item.name) {
                                    layer.bindPopup('<b>' + item.name + '</b><br>' + item.description);
                                }

                                // Add delete button in popup
                                layer.on('popupopen', function () {
                                    let deleteButton = document.createElement('button');
                                    deleteButton.textContent = 'Delete';
                                    deleteButton.style = 'margin-top: 5px;';

                                    deleteButton.onclick = function () {
                                        fetch(`/geographic/${item.id}`, {
                                            method: 'DELETE',
                                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                        })
                                            .then(response => response.json())
                                            .then(result => {
                                                if (result.status === 'success') {
                                                    map.removeLayer(layer); // Remove layer from map
                                                    alert('Data deleted successfully');
                                                }
                                            });
                                    };

                                    layer.getPopup().setContent(
                                        `<b>${item.name}</b><br>${item.description || ''}<br>`
                                    ).getElement().appendChild(deleteButton);
                                });
                            }
                        }).addTo(map);
                    });
                }
            });
    }

    loadMapData();

    // Handle new objects drawn
    map.on('draw:created', function (e) {
        var layer = e.layer;
        var geoJSONData = layer.toGeoJSON();

        var name = prompt("Enter name for the object:");
        var description = prompt("Enter description for the object:");

        if (!name) {
            alert('Name is required!');
            return;
        }

        // Save data to server
        fetch('/geographic', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                name: name,
                description: description,
                type: geoJSONData.geometry.type,
                coordinates: JSON.stringify(geoJSONData.geometry)
            })
        })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    alert('Data added successfully!');
                    loadMapData(); // Reload map data
                }
            });

        drawnItems.addLayer(layer);
    });
</script>

</body>
</html>
