<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeographicData;
use Illuminate\Support\Facades\Storage;

class GeographicDataController extends Controller
{
    public function index(Request $request)
    {   $search = $request->input('search');

        $data = GeographicData::when($search, function ($query, $search) {
            $query->where('name', 'LIKE', "%{$search}%");
        })->paginate(5);
        return view('map', compact('data','search'));
    }

    public function showAdd()
    {
        return view('add');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required|in:Point,LineString,Polygon',
            'coordinates' => 'required|json',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',  // Add validation for the photo
        ]);

        // Handle the photo upload if present
        $photoPath = null;
        if ($request->hasFile('photo')) {
            // Store the photo in the public directory
            $photoPath = $request->file('photo')->store('photos', 'public');
        }

        // Create a new geographic data record
        $data = GeographicData::create([
            'name' => $request->name,
            'type' => $request->type,
            'coordinates' => $request->coordinates,
            'description' => $request->description,
            'photo' => $photoPath,  // Store the photo path in the database
        ]);

        return response()->json(['status' => 'success', 'data' => $data], 201);
    }
 
    
    public function destroy($id)
    {
        $data = GeographicData::find($id);
        if ($data) {
            // Delete the photo file if it exists
            if ($data->photo && Storage::exists('public/' . $data->photo)) {
                Storage::delete('public/' . $data->photo);
            }
            $data->delete();
            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'error', 'message' => 'Data not found'], 404);
    }
    public function getAllData()
    {
        $data = GeographicData::all();
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function edit($id)
    {
        // Fetch the item by ID and return the edit view
        $item = GeographicData::findOrFail($id);
        return view('edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'coordinates' => 'required|regex:/^[-\d.]+,[-\d.]+$/', // Validate longitude,latitude format
        ]);
    
        // Find the item to update
        $item = GeographicData::findOrFail($id);
    
        // Update name and description
        $item->name = $request->input('name');
        $item->description = $request->input('description');
    
        // Update coordinates in proper format
        $coordinatesInput = $request->input('coordinates'); // Example: "105.24147,-5.37222"
        [$longitude, $latitude] = explode(',', $coordinatesInput); // Split by comma
        $item->coordinates = json_encode([
            'type' => 'Point',
            'coordinates' => [(float)$longitude, (float)$latitude],
        ]);
    
        // If a new photo is uploaded, handle file upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('geographic_photos', 'public');
            $item->photo = $photoPath;
        }
    
        // Save the updated item
        $item->save();
    
        // Redirect back with success message
        return redirect()->route('home')->with('success', 'Data updated successfully!');
    }

    public function save(Request $request)
{
    $request->validate([
        'name' => 'required',
        'type' => 'required|in:Point,LineString,Polygon',
        'coordinates' => 'required|string',
        'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    // Handle the photo upload if present
    $photoPath = null;
    if ($request->hasFile('photo')) {
        // Store the photo in the public directory
        $photoPath = $request->file('photo')->store('photos', 'public');
    }

    // Process the coordinates input to convert it to the correct format
    $coordinates = explode(',', $request->coordinates);
    if (count($coordinates) == 2) {
        // Assuming the input is in "longitude,latitude" format
        $coordinatesJson = json_encode([
            'type' => 'Point',
            'coordinates' => [floatval($coordinates[0]), floatval($coordinates[1])],
        ]);
    } else {
        // If the format is incorrect, you could either return an error or handle it differently.
        return redirect()->back()->with('error', 'Invalid coordinates format.');
    }

    // Create a new geographic data record
    $data = GeographicData::create([
        'name' => $request->name,
        'type' => $request->type,
        'coordinates' => $coordinatesJson,
        'description' => $request->description,
        'photo' => $photoPath,  // Store the photo path in the database
    ]);

    return redirect()->route('home')->with('success', 'Geographic data added successfully.');
}

    
    
}
