<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Units;

class UnitsController extends Controller
{
     public function index()
    {
        $units = Units::all();
        return response()->json([
            'status' => 'success',
            'data' => $units
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'detail' => 'nullable|string'
        ]);

        $unit = Units::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Unit created successfully.',
            'data' => $unit
        ], 201);
    }

    public function show($id)
    {
        $unit = Units::find($id);

        if (!$unit) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $unit]);
    }

    public function update(Request $request, $id)
    {
        $unit = Units::find($id);

        if (!$unit) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'detail' => 'nullable|string'
        ]);

        $unit->fill($validated)->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Updated successfully.',
            'data' => $unit
        ]);
    }

    public function destroy($id)
    {
        $unit = Units::find($id);

        if (!$unit) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $unit->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Deleted successfully.'
        ]);
    }
}
