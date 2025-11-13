<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\YieldRecord;
use App\Models\Planning;
use App\Models\PlanningDetail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class YeildController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $yields = YieldRecord::all();
        
        $yields = YieldRecord::with(['crop', 'area', 'broker', 'inputitem', 'harvesttype'])->get();
        return response()->json(['status' => 'success', 'data' => $yields]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'crop_id' => 'required|integer',
            'area_id' => 'required|integer',
            'broker_id' => 'required|integer',
            'input_item_id' => 'required|integer',
            'harvest_type_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'rate' => 'required|numeric',
            'status' => 'nullable|string|max:255',
            'kg_per_area' => 'nullable|numeric'
        ]);

        $yield = YieldRecord::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Yield created successfully.',
            'data' => $yield
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $yield = YieldRecord::find($id);

        if (!$yield) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $planDetails = PlanningDetail::with(['planning', 'broker', 'area', 'planningdetaildates', 'yeilds'])
            ->where('planning_id', $request->plan_id)
            ->where('area_id', $request->area_id)
            ->where('broker_id', $request->broker_id)
            ->get()
            ->first();

        $query = Planning::with(['crop', 'inputitem', 'harvesttype', 'planningdetails']);
        if ($request->filled('plan_id')) {
            $query->where('id', $request->plan_id);
        }
        $plannings = $query->get()->first();

        return response()->json([
            'status' => 'success',
            'message' => 'Yield created successfully.',
            'plannings' => $plannings,
            'planDetails' => $planDetails,
            'yield' => $yield
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $yield = YieldRecord::find($id);

        if (!$yield) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'crop_id' => 'required|integer',
            'area_id' => 'required|integer',
            'broker_id' => 'required|integer',
            'input_item_id' => 'required|integer',
            'harvest_type_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'rate' => 'required|numeric',
            'status' => 'nullable|string|max:255',
            'kg_per_area' => 'nullable|numeric'
        ]);
        // $validated['rate'] = $validated['rate'] * 1000;
        $yield->fill($validated)->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Updated successfully.',
            'data' => $yield
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $yield = YieldRecord::find($id);

        if (!$yield) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $yield->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Deleted successfully.'
        ]);
    }

    public function editbyplanning(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Yield created successfully.',
            'data' => $request
        ], 201);
    }

    public function getbyplanning(Request $request)
    {
        $validated = $request->validate([
            'crop_id' => 'required|integer',
            'area_id' => 'required|integer',
            'broker_id' => 'required|integer',
            'input_item_id' => 'required|integer',
            'harvest_type_id' => 'required|integer',
        ]);

        $planDetails = PlanningDetail::with(['planning', 'broker', 'area', 'planningdetaildates', 'yeilds'])
            ->where('planning_id', $request->plan_id)
            ->where('area_id', $request->area_id)
            ->where('broker_id', $request->broker_id)
            ->get()->first();

        $query = Planning::with(['crop', 'inputitem', 'harvesttype', 'planningdetails']);
        if ($request->filled('plan_id')) {
            $query->where('id', $request->plan_id);
        }
        $plannings = $query->get()->first();

        return response()->json([
            'status' => 'success',
            'message' => 'Yield created successfully.',
            'plannings' => $plannings,
            'planDetails' => $planDetails
        ], 200);
    }


}
