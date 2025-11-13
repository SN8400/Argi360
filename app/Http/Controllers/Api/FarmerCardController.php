<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Farmer_cards;

class FarmerCardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cards = Farmer_cards::all();
        return response()->json([
            'status' => 'success',
            'data' => $cards
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {        
        // ✅ Validate ข้อมูลที่รับเข้ามา
        $validated = $request->validate([
            'farmer_id' => 'required|integer',
            'attach_dir' => 'nullable|string|max:255',
            'attach' => 'nullable|string|max:255'
        ]);

        $requestData = $request->all();
        $card = Farmer_cards::create($requestData);

        return response()->json([
            'status' => 'success',
            'message' => 'Farmer card created successfully.',
            'data' => $card
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $card = Farmer_cards::find($id);

        if (!$card) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $card]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $card = Farmer_cards::find($id);

        if (!$card) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'farmer_id' => 'required|integer',
            'attach_dir' => 'nullable|string|max:255',
            'attach' => 'nullable|string|max:255'
        ]);

        $card->fill($request->all())->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Farmer card updated successfully.',
            'data' => $card
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $card = Farmer_cards::find($id);

        if (!$card) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $card->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Farmer card deleted successfully.'
        ]);
    }
}
