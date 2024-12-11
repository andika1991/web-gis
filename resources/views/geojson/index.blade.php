@extends('layouts.app')

@section('content')
    <h1>WebGIS - GeoJSON Example</h1>
    <div id="map" style="height: 500px;"></div>

    <script>
        // Membuat peta dengan koordinat awal
        var map = L.map('map').setView([-5.4273, 105.2558], 10); // Provinsi Lampung

        // Tile layer dari OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        // URL GeoJSON yang diteruskan dari controller
        var geojsonUrl = '{{ $geoJsonUrl }}'; // Mengambil URL file GeoJSON dari controller

        // Memuat GeoJSON menggunakan Leaflet
        fetch(geojsonUrl)
            .then(response => response.json())
            .then(data => {
                // Menampilkan data GeoJSON di console untuk pengecekan
                console.log("GeoJSON Data:", data);

                // Menambahkan data GeoJSON ke peta dengan style untuk memperjelas polygon
                L.geoJSON(data, {
                    style: function (feature) {
                        return {
                            color: "blue",  // Warna polygon
                            weight: 2,      // Ketebalan garis polygon
                            opacity: 0.7    // Opasitas polygon
                        };
                    }
                }).addTo(map);
            })
            .catch(error => {
                console.error("Error loading GeoJSON:", error);
            });
    </script>
@endsection
