<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeographicData;

class GeographicDataController extends Controller
{
    public function index()
    {
        $data = GeographicData::all();
        return view('geographic.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required|in:Point,LineString,Polygon',
            'coordinates' => 'required|json',
        ]);

        $data = GeographicData::create([
            'name' => $request->name,
            'type' => $request->type,
            'coordinates' => $request->coordinates,
            'description' => $request->description,
        ]);

        return response()->json(['status' => 'success', 'data' => $data], 201);
    }

    public function destroy($id)
    {
        $data = GeographicData::findOrFail($id);
        $data->delete();

        return response()->json(['status' => 'success', 'message' => 'Data deleted successfully']);
    }

    public function getAllData()
    {
        $data = GeographicData::all();
        return response()->json(['status' => 'success', 'data' => $data]);
    }
}
