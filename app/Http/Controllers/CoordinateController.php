<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coordinate;

class CoordinateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coordinates = Coordinate::all();
        return view('coordinates.index', compact('coordinates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('coordinates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        Coordinate::create($request->all());
        return redirect()->route('coordinates.index')->with('success', 'Coordinate added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coordinate $coordinate)
    {
        return view('coordinates.edit', compact('coordinate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coordinate $coordinate)
    {
        $request->validate([
            'name' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $coordinate->update($request->all());
        return redirect()->route('coordinates.index')->with('success', 'Coordinate updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coordinate $coordinate)
    {
        $coordinate->delete();
        return redirect()->route('coordinates.index')->with('success', 'Coordinate deleted successfully');
    }
}
