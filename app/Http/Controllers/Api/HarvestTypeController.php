<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Harvest_types;

class HarvestTypeController extends Controller
{
   public function index()
    {
        $types = Harvest_types::all();
        return response()->json(['status' => 'success', 'data' => $types]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'note' => 'nullable|string'
        ]);

        $type = Harvest_types::create($validated);

        return response()->json(['status' => 'success', 'message' => 'Created', 'data' => $type], 201);
    }

    public function show($id)
    {
        $type = Harvest_types::find($id);
        if (!$type) return response()->json(['status' => 'error', 'message' => 'Not found'], 404);

        return response()->json(['status' => 'success', 'data' => $type]);
    }

    public function update(Request $request, $id)
    {
        $type = Harvest_types::find($id);
        if (!$type) return response()->json(['status' => 'error', 'message' => 'Not found'], 404);

        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'note' => 'nullable|string'
        ]);

        $type->update($validated);
        return response()->json(['status' => 'success', 'message' => 'Updated', 'data' => $type]);
    }

    public function destroy($id)
    {
        $type = Harvest_types::find($id);
        if (!$type) return response()->json(['status' => 'error', 'message' => 'Not found'], 404);

        $type->delete();
        return response()->json(['status' => 'success', 'message' => 'Deleted']);
    }
}
