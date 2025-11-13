<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Farmers;

class FarmerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $farmers = Farmers::with(['province','city','farmer_card'])->get();

        return response()->json([
            'status' => 'success',
            'data' => $farmers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'init' => 'nullable|string|max:10',
            'fname' => 'required|string|max:100',
            'lname' => 'required|string|max:100',
            'citizenid' => 'nullable|string|max:20',
            'address1' => 'nullable|string|max:255',
            'address2' => 'nullable|string|max:255',
            'address3' => 'nullable|string|max:255',
            'sub_cities' => 'nullable|string|max:100',
            'city_id' => 'nullable|exists:cities,id',
            'province_id' => 'nullable|exists:provinces,id',
            'createdBy' => 'nullable|integer',
            'modifiedBy' => 'nullable|integer',
        ]);

        $farmer = Farmers::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Farmer created successfully.',
            'data' => $farmer
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $farmer = Farmers::with(['province', 'city', 'farmer_card'])->find($id);

        if (!$farmer) {
            return response()->json(['status' => 'error', 'message' => 'Farmer not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $farmer]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $farmer = Farmers::find($id);

        if (!$farmer) {
            return response()->json(['status' => 'error', 'message' => 'Farmer not found'], 404);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'init' => 'nullable|string|max:10',
            'fname' => 'required|string|max:100',
            'lname' => 'required|string|max:100',
            'citizenid' => 'nullable|string|max:20',
            'address1' => 'nullable|string|max:255',
            'address2' => 'nullable|string|max:255',
            'address3' => 'nullable|string|max:255',
            'sub_cities' => 'nullable|string|max:100',
            'city_id' => 'nullable|exists:cities,id',
            'province_id' => 'nullable|exists:provinces,id',
            'createdBy' => 'nullable|integer',
            'modifiedBy' => 'nullable|integer',
        ]);

        $farmer->fill($validated)->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Farmer updated successfully.',
            'data' => $farmer
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $farmer = Farmers::find($id);

        if (!$farmer) {
            return response()->json(['status' => 'error', 'message' => 'Farmer not found'], 404);
        }

        $farmer->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Farmer deleted successfully.'
        ]);
    }
}
