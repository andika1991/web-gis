@extends('layouts.app')

@section('content')
    <h1>Coordinates</h1>
    <a href="{{ route('coordinates.create') }}" class="btn btn-primary mb-3">Add Coordinate</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($coordinates as $coordinate)
                <tr>
                    <td>{{ $coordinate->name }}</td>
                    <td>{{ $coordinate->latitude }}</td>
                    <td>{{ $coordinate->longitude }}</td>
                    <td>
                        <a href="{{ route('coordinates.edit', $coordinate) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('coordinates.destroy', $coordinate) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div id="map" style="height: 500px;"></div>

    <script>
        // Membuat peta dengan koordinat awal
        var map = L.map('map').setView([-5.4273, 105.2558], 10); // Koordinat Provinsi Lampung

        // Tile layer dari OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        // Menambahkan Marker untuk Lokasi tertentu dari database
        @foreach ($coordinates as $coordinate)
            L.marker([{{ $coordinate->latitude }}, {{ $coordinate->longitude }}])
                .addTo(map)
                .bindPopup("{{ $coordinate->name }}");
        @endforeach

        // Memuat dan menambahkan file GeoJSON ke peta
        var geojsonUrl = '{{ asset("geojson/Labuhan%20Ratu.geojson") }}'; // URL ke file GeoJSON
        fetch(geojsonUrl)
            .then(response => response.json())
            .then(data => {
                // Menambahkan data GeoJSON ke peta
                L.geoJSON(data).addTo(map);
            })
            .catch(error => {
                console.error("Error loading GeoJSON:", error);
            });
        polygon.bindPopup("Lampung Area");

        // Menyesuaikan peta dengan batas polygon
        map.fitBounds(polygon.getBounds());
    </script>
@endsection
