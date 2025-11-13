<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seed_codes;

class SeedCodeController extends Controller
{
    public function index()
    {
        $seedCodes = Seed_codes::with(['crop', 'input_item', 'seed_packs'])->get();
        return response()->json([
            'status' => 'success',
            'data' => $seedCodes
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'crop_id' => 'required|integer|exists:crops,id',
            'input_item_id' => 'required|integer|exists:input_items,id',
            'code' => 'required|string|max:50',
            'details' => 'nullable|string|max:1000',
            'val_per_area' => 'nullable|numeric',
            'seed_per_kg' => 'nullable|numeric',
            'pack_date' => 'nullable|date',
        ]);

        $plant = Seed_codes::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Plant template created successfully.',
            'data' => $plant
        ], 201);
    }

    public function show($id)
    {
        // $plant = Seed_codes::find($id);

        $seedCodes = Seed_codes::with(['crop', 'input_item', 'seed_packs'])
                    ->where('crop_id', $id)
                    ->get();

        if (!$seedCodes) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $seedCodes]);
    }

    public function update(Request $request, $id)
    {
        $plant = Seed_codes::find($id);

        if (!$plant) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'crop_id' => 'required|integer|exists:crops,id',
            'input_item_id' => 'required|integer|exists:input_items,id',
            'code' => 'required|string|max:50',
            'details' => 'nullable|string|max:1000',
            'val_per_area' => 'nullable|numeric',
            'seed_per_kg' => 'nullable|numeric',
            'pack_date' => 'nullable|date',
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
        $plant = Seed_codes::find($id);

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
