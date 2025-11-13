<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Broker_areas;

class BrokerAreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brokerAreas = Broker_areas::with(['crop','broker','area'])->get();
        return response()->json([
            'status' => 'success',
            'data' => $brokerAreas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'crop_id' => 'required|exists:crops,id',
            'broker_id' => 'required|exists:brokers,id',
            'area_id' => 'required|exists:areas,id'
        ]);

        $brokerArea = Broker_areas::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Broker Area created successfully.',
            'data' => $brokerArea
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // $brokerArea = Broker_areas::find($id);
        $brokerAreas = Broker_areas::with(['crop','broker','area'])->where('crop_id', $id)->get();

        if (!$brokerAreas) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $brokerAreas
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $brokerArea = Broker_areas::find($id);

        if (!$brokerArea) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'crop_id' => 'required|exists:crops,id',
            'broker_id' => 'required|exists:brokers,id',
            'area_id' => 'required|exists:areas,id'
        ]);

        $brokerArea->fill($validated)->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Updated successfully.',
            'data' => $brokerArea
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $brokerArea = Broker_areas::find($id);

        if (!$brokerArea) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $brokerArea->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Deleted successfully.'
        ]);
    }
}
