<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Planning;
use App\Models\Broker_areas;
use App\Models\PlanningDetail;
use App\Models\YieldRecord;
use App\Models\User_farmer;
use App\Models\Sowing;
use App\Models\Brokers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class PlanningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Planning::with(['crop', 'inputitem', 'harvesttype', 'planningdetails']);

        if ($request->filled('crop_id')) {
            $query->where('crop_id', $request->crop_id);
        }

        $plannings = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $plannings
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $crop_id = $request->input('crop_id');
        $broker_id = $request->input('broker_id');
        $input_item_id = $request->input('input_item_id');
        $harvest_type_id = $request->input('harvest_type_id');
        $yeildRate = $request->input('yeildRate');
        $masterdetailid = $request->input('masterdetailid');

        // ดึง User_farmer ID ที่ตรงตามเงื่อนไข
        $userFarmerIds = User_farmer::where('crop_id', $crop_id)
            ->where('broker_id', $broker_id)
            ->pluck('id');

        // ดึง sowings ที่ตรงตามเงื่อนไข
        $sowings = Sowing::whereIn('user_farmer_id', $userFarmerIds)
            ->where('crop_id', $crop_id)
            ->where('input_item_id', $input_item_id)
            ->where('harvest_type_id', $harvest_type_id)
            ->get();

        foreach ($sowings as $sowing) {
            // อัปเดต yield_rate และ yield_rate7
            $sowing->yield_rate = $yeildRate;
            if (!empty($sowing->yield_rate7)) {
                $sowing->yield_rate7 = $yeildRate;
            }
            $sowing->save();

            // อัปเดต value_est ใน harvest plans ที่เกี่ยวข้อง
            foreach ($sowing->harvestPlan()->get() as $harvest) {
                $harvest->value_est = $yeildRate * $sowing->current_land;
                $harvest->save();
            }
        }

        $broker = Brokers::find($broker_id);
        return response()->json([
            'status' => 'success',
            'message' => 'Upload Yield ' . $broker->code . ' Complete',
            'data' => $masterdetailid
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $cropId, string $planId)
    {
        $plan = Planning::with(['crop', 'inputitem', 'harvesttype'])->findOrFail($planId);
        $areas = Broker_areas::where('crop_id', $cropId)->get();
        $planDetails = PlanningDetail::with(['planning', 'broker', 'area', 'planningdetaildates', 'yeilds'])->where('planning_id', $planId)->get();
        $resultAll = [];
        $planDetails->transform(function ($detail) {
            $detail->filtered_yeilds = $detail->yeilds->filter(function ($yeild) use ($detail) {
                return $yeild->area_id == $detail->area_id && $yeild->broker_id == $detail->broker_id;
            })->values(); // values() เพื่อ reset key เป็น 0-based
            return $detail;
        });

        return response()->json([
            'status' => 'success',
            'data' => $planDetails
        ]);
    }

    private function generatePlanningAndYeild($plan, $areas)
    {
        foreach ($areas as $area) {
            PlanningDetail::create([
                'planning_id' => $plan->id,
                'broker_id' => $area->broker_id,
                'area_id' => $area->area_id,
                // เพิ่มค่า default อื่น ๆ ตามต้องการ
            ]);

            YieldRecord::create([
                'crop_id' => $plan->crop_id,
                'input_item_id' => $plan->item_input_id,
                'harvest_type_id' => $plan->harvest_type_id,
                'broker_id' => $area->broker_id,
                'area_id' => $area->area_id,
                'value' => 1500 // ค่า default
            ]);
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
          $validator = Validator::make($request->all(), [
            'plan_kg_area' => 'nullable|numeric',
            'plan_value' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $plannings = Planning::find($id);
        if (!$plannings) {
            return response()->json([
                'status' => 'error',
                'message' => 'Map matcode not found.'
            ], 404);
        }

        $plannings->update([
            'plan_kg_area' => $request->plan_kg_area,
            'plan_value' => $request->plan_value,
            'modified' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $plannings
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
