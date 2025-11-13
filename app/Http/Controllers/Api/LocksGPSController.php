<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LockDate;
use App\Models\LockGPX;

class LocksGPSController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function lock_gps(Request $request)
    { 
        $lockGPX = LockGPX::create([
            'crop_id' => $request->crop_id,
            'dt_lock_gpx' => $request->selected_date
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'lockGPX created successfully.',
            'data' => $lockGPX
        ], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function lock_sowing(Request $request)
    {
        $lockDate = LockDate::create([
            'crop_id' => $request->crop_id,
            'type' => 'SOWING',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'lockDate created successfully.',
            'data' => $lockDate
        ], 201);
    }
}
