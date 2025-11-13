<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Broker_head;

class BrokerHeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brokerAreas = Broker_head::with(['crop','broker','head'])->get();
        return response()->json([
            'status' => 'success',
            'data' => $brokerAreas
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function getListByCrop($cropId, $brokerId)
    {
        $brokerAreas = Broker_head::with(['crop','broker','head'])->where('crop_id', $cropId)->where('broker_id', $brokerId)->get();
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
            'head_id' => 'required|exists:heads,id'
        ]);

        $brokerHead = Broker_head::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Broker Head created successfully.',
            'data' => $brokerHead
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // $brokerHead = Broker_head::find($id);
        
        $brokerHeads = Broker_head::with(['crop','broker','head'])->where('crop_id', $id)->get();

        if (!$brokerHeads) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $brokerHeads]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $brokerHead = Broker_head::find($id);

        if (!$brokerHead) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'crop_id' => 'required|exists:crops,id',
            'broker_id' => 'required|exists:brokers,id',
            'head_id' => 'required|exists:heads,id',
        ]);

        $brokerHead->fill($validated)->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Updated successfully.',
            'data' => $brokerHead
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $brokerHead = Broker_head::find($id);

        if (!$brokerHead) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $brokerHead->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Deleted successfully.'
        ]);
    }
}
