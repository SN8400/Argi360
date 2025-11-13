<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PlanSchedule;
use App\Models\PlanScheduleDetail;

class PlanScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $cropId)
    {
        $planSchedules = PlanSchedule::with(['crop','broker','input_item','area'])
        ->where('crop_id', $cropId)
        ->orderBy('input_item_id')
        ->orderBy('day')
        ->get();
        return response()->json([
            'status' => 'success',
            'data' => $planSchedules
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
        $groupNo = 1;
        $schedule_id = $request->input('plan_schedule_id');
        $planScheduleDetail = PlanScheduleDetail::where('plan_schedule_id', $schedule_id)
        ->orderByDesc('set_group')
        ->first();
        if($planScheduleDetail){
            $groupNo = $planScheduleDetail["set_group"] + 1;
        }


        


        $planSchedules = PlanScheduleDetail::create([
            'plan_schedule_id' => $request->plan_schedule_id,
            'chemical_id' => $request->chemical_id,
            'value' => $request->value,
            'unit_id' => $request->unit_id,
            'name' => $request->name,
            'rate' => $request->rate,
            'ctype' => $request->ctype,
            'set_group' => $groupNo,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'PlanScheduleDetail created successfully.',
            'data' => $planSchedules
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, string $schedule_id)
    {
        $planScheduleDetail = PlanScheduleDetail::with(['planSchedule','chemical','unit','pUnit'])  
        ->where('plan_schedule_id', $schedule_id)
        ->orderBy('set_group')
        ->get();

        $planSchedules = PlanSchedule::with(['crop','broker','input_item','area'])
        ->where('crop_id', $id)
        ->where('id', $schedule_id)
        ->orderBy('input_item_id')
        ->orderBy('day')
        ->first();
    
        return response()->json([
            'status' => 'success',
            'data' => $planScheduleDetail,
            'crop' => $planSchedules
        ]);
    }

    public function getView(string $type_id, string $crop_id, string $broker_id, string $input_item_id)
    {
        $planSchedules = PlanSchedule::with(['crop','broker','input_item','area', 'details' => function ($q) { $q->orderBy('set_group'); }, 'details.chemical', 'details.unit', 'details.pUnit'])
        ->where('crop_id', $crop_id)
        ->where('broker_id', $broker_id)
        ->where('input_item_id', $input_item_id)
        ->orderBy('day')
        ->get();
    
        return response()->json([
            'status' => 'success',
            'data' => $planSchedules
        ]);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $PlanScheduleDetail = PlanScheduleDetail::find($id);
        if (!$PlanScheduleDetail) {
            return response()->json([
                'status' => 'error',
                'message' => 'Map matcode not found.'
            ], 404);
        }

        $PlanScheduleDetail->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'PlanScheduleDetail deleted successfully.'
        ]);
    }
}
