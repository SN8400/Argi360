<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Map_matcode;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class MapMatcodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Map_matcode::with(['crop', 'inputitem', 'broker']);

        if ($request->filled('crop_id')) {
            $query->where('crop_id', $request->crop_id);
        }

        $mapMatcodes = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $mapMatcodes
        ]);
    }
    
    public function getbycrop(Request $request)
    {
        $query = Map_matcode::with(['crop', 'inputitem', 'broker']);

        if ($request->filled('crop_id')) {
            $query->where('crop_id', $request->crop_id);
        }

        $mapMatcodes = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $mapMatcodes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'crop_id' => 'required|integer',
            'input_item_id' => 'required|integer',
            'broker_id' => 'nullable|integer',
            'harvest_by' => 'nullable|string',
            'harvest_to' => 'nullable|string',
            'matcode' => 'required|string',
            'desc' => 'nullable|string',
        ]);

        $mapMatcode = Map_matcode::create([
            'crop_id' => $request->crop_id,
            'input_item_id' => $request->input_item_id,
            'broker_id' => $request->broker_id,
            'harvest_by' => $request->harvest_by,
            'harvest_to' => $request->harvest_to,
            'matcode' => $request->matcode,
            'desc' => $request->desc,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $mapMatcode
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mapMatcodes = Map_matcode::where('crop_id', $id)->with(['crop', 'inputitem', 'broker'])->get();
        if ($mapMatcodes->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No map matcodes found for this crop.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $mapMatcodes
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $mapMatcode = Map_matcode::with(['crop', 'inputitem', 'broker'])->find($id);
        if (!$mapMatcode) {
            return response()->json([
                'status' => 'error',
                'message' => 'Map matcode not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $mapMatcode
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'crop_id' => 'required|integer',
            'input_item_id' => 'required|integer',
            'broker_id' => 'nullable|integer',
            'harvest_by' => 'nullable|string',
            'harvest_to' => 'nullable|string',
            'matcode' => 'required|string',
            'desc' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $mapMatcode = Map_matcode::find($request->id);
        if (!$mapMatcode) {
            return response()->json([
                'status' => 'error',
                'message' => 'Map matcode not found.'
            ], 404);
        }

        $mapMatcode->update([
            'crop_id' => $request->crop_id,
            'input_item_id' => $request->input_item_id,
            'broker_id' => $request->broker_id,
            'harvest_by' => $request->harvest_by,
            'harvest_to' => $request->harvest_to,
            'matcode' => $request->matcode,
            'desc' => $request->desc,
            'modified' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $mapMatcode
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $mapMatcode = Map_matcode::find($id);
        if (!$mapMatcode) {
            return response()->json([
                'status' => 'error',
                'message' => 'Map matcode not found.'
            ], 404);
        }

        $mapMatcode->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Map matcode deleted successfully.'
        ]);
    }

    public function clone(Request $request)
    {
        $request->validate([
            'from_crop_id' => 'required|integer',
            'from_broker_id' => 'required|integer',
            'to_crop_id' => 'required|integer',
            'to_broker_id' => 'required|integer',
        ]);

        $result = (new Map_matcode)->copynewcrop(
            $request->from_crop_id,
            $request->from_broker_id,
            $request->to_crop_id,
            $request->to_broker_id,
            Carbon::now(),
            Carbon::now()
        );

        if ($result == 'complete') {
            return response()->json([
                'status' => 'success',
                'message' => 'Cloned successfully.'
            ]);
        } elseif ($result == 'duplicate') {
            return response()->json([
                'status' => 'error',
                'message' => 'Target already has data.'
            ], 409);
        } elseif ($result == 'nodata') {
            return response()->json([
                'status' => 'error',
                'message' => 'No source data found.'
            ], 404);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Unknown error.'
        ], 500);
    }
}
