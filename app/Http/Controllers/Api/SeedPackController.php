<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SeedPack;
use App\Models\Seed_codes;

class SeedPackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $id)
    {
        $seedPacks = SeedPack::with(['seedcode'])
        ->whereHas('seedcode', function ($query) use ($id) {
            $query->where('crop_id', $id);
        })
        ->get();

        return response()->json([
            'status' => 'success',
            'data' => $seedPacks
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'seed_code_id' => 'required|exists:seed_codes,id',
            'name' => 'required|string|max:255',
            'details' => 'nullable|string',
            'pack_date' => 'required|date',
            'status' => 'required|int'
        ]);

        // Create new seed pack
        $seedPack = SeedPack::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Seed pack created successfully.',
            'data' => $seedPack
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $seedCodeListRaw = Seed_codes::with('input_item')
            ->where('crop_id', $id)
            ->get();

        $seedCodeList = [];

        foreach ($seedCodeListRaw as $seedCode) {
            $seedCodeList[$seedCode->id] = $seedCode->input_item->tradename . ' - ' . $seedCode->code;
        }


        return response()->json([
            'status' => 'success',
            'data' => $seedCodeList
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
       $request->validate([
            'seed_code_id' => 'required|exists:seed_codes,id',
            'name' => 'required|string|max:255',
            'details' => 'nullable|string',
            'pack_date' => 'required|date',
            'status' => 'required|int'
        ]);

        $seedPack = SeedPack::findOrFail($id);

        $seedPack->update([
            'seed_code_id' => $request->seed_code_id,
            'name' => $request->name,
            'details' => $request->details,
            'pack_date' => $request->pack_date,
            'status' => $request->status,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Seed pack updated successfully.',
            'data' => $seedPack
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $seedPacks = SeedPack::find($id);

        if (!$seedPacks) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $seedPacks->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Seed Pack deleted successfully.'
        ]);
    }
}
