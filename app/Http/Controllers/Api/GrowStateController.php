<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grow_states;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GrowStateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $growStates = Grow_states::with(['crop', 'input_item'])->get();
        return response()->json([
            'status' => 'success',
            'data' => $growStates
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
            'code' => 'required|string',
            'name' => 'required|string',
            'age' => 'required|integer',
        ]);

        $growState = Grow_states::create([
            'crop_id' => $request->crop_id,
            'input_item_id' => $request->input_item_id,
            'code' => $request->code,
            'name' => $request->name,
            'age' => $request->age
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $growState
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function edit(string $id)
    {
        $growState = Grow_states::with(['crop', 'input_item'])->find($id);
        if (!$growState) {
            return response()->json([
                'status' => 'error',
                'message' => 'Grow state not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $growState
        ]);
    }

    public function show(string $id)
    {
        // $growState = Grow_states::with(['crop', 'input_item'])->find($id);
        $growState = Grow_states::where('crop_id', $id)->with('crop', 'input_item')->get();
        if (!$growState) {
            return response()->json([
                'status' => 'error',
                'message' => 'Grow state not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $growState
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'crop_id' => 'required|integer',
            'input_item_id' => 'required|integer',
            'code' => 'required|string',
            'name' => 'required|string',
            'age' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $growState = Grow_states::find($request->id);

        if (!$growState) {
            return response()->json([
                'status' => 'error',
                'message' => 'Grow state not found.'
            ], 404);
        }

        $growState->update([
            'crop_id' => $request->crop_id,
            'input_item_id' => $request->input_item_id,
            'code' => $request->code,
            'name' => $request->name,
            'age' => $request->age,
            'modified' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $growState
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $growState = Grow_states::find($id);

        if (!$growState) {
            return response()->json([
                'status' => 'error',
                'message' => 'Grow state not found.'
            ], 404);
        }

        $growState->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Grow state deleted successfully.'
        ]);
    }

        // Clone grow states
    public function clone(Request $request)
    {
        $request->validate([
            'from_crop_id' => 'required|integer',
            'from_item_id' => 'required|integer',
            'to_crop_id' => 'required|integer',
            'to_item_id' => 'required|integer',
        ]);

        $result = (new Grow_states)->copynewcrop(
            $request->from_crop_id,
            $request->from_item_id,
            $request->to_crop_id,
            $request->to_item_id,
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
