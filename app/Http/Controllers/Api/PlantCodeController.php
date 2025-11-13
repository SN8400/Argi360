<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plant_code;

class PlantCodeController extends Controller
{
    public function index()
    {
        $plants = Plant_code::all();
        return response()->json([
            'status' => 'success',
            'data' => $plants
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'details' => 'nullable|string|max:1000',
        ]);

        $plant = Plant_code::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Plant template created successfully.',
            'data' => $plant
        ], 201);
    }

    public function show($id)
    {
        $plant = Plant_code::find($id);

        if (!$plant) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $plant]);
    }

    public function update(Request $request, $id)
    {
        $plant = Plant_code::find($id);

        if (!$plant) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'details' => 'nullable|string|max:1000',
        ]);

        $plant->fill($validated)->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Plant template updated successfully.',
            'data' => $plant
        ]);
    }

    public function destroy($id)
    {
        $plant = Plant_code::find($id);

        if (!$plant) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $plant->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Plant template deleted successfully.'
        ]);
    }
}
