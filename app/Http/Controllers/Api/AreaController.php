<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Areas;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $areas = Areas::all();

        return response()->json([
            'status' => 'success',
            'data' => $areas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $area = Areas::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Area created successfully.',
            'data' => $area
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $area = Areas::find($id);

        if (!$area) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $area
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $area = Areas::find($id);

        if (!$area) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $area->fill($validated)->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Updated successfully.',
            'data' => $area
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $area = Areas::find($id);

        if (!$area) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $area->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Deleted successfully.'
        ]);
    }
}
