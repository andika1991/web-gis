<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GeoJsonController extends Controller
{
    public function index()
    {
        // Mengambil file GeoJSON dari storage
        $geoJson = Storage::get('geojson/Labuhan Ratu.geojson');
        
        // Menampilkan file GeoJSON di view
        return view('geojson.index', compact('geoJsonUrl'));
    }
}
