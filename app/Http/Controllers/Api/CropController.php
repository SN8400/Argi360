<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Crops;
use Illuminate\Support\Facades\Auth;


class CropController extends Controller
{
    public function index()
    {
        $crops = Crops::orderBy('id', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $crops
        ]);
    }

    public function store(Request $request)
    {
        // ✅ Validate ข้อมูลที่รับเข้ามา
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sap_code' => 'nullable|string|max:100',
            'linkurl' => 'nullable|string|max:255',
            'startdate' => 'required|date',
            'enddate' => 'required|date|after_or_equal:startdate',
            'max_per_day' => 'nullable|numeric|min:1',
            'details' => 'nullable|string|max:1000',
        ]);

        $requestData = $request->all();
        $requestData['createdBy'] = Auth::id();
        $requestData['modifiedBy'] = Auth::id();

        $crop = Crops::create($requestData);

        return response()->json([
            'status' => 'success',
            'message' => 'Crops created successfully.',
            'data' => $crop
        ], 201);
    }
    
    public function show($id)
    {
        $crop = Crops::find($id);

        if (!$crop) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $crop]);
    }

    public function update(Request $request, $id)
    {
        $crop = Crops::find($id);

        if (!$crop) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sap_code' => 'nullable|string|max:100',
            'linkurl' => 'nullable|string|max:255',
            'startdate' => 'required|date',
            'enddate' => 'required|date|after_or_equal:startdate',
            'max_per_day' => 'nullable|numeric|min:1',
            'details' => 'nullable|string|max:1000',
        ]);


        $requestData = $request->all();
        $requestData['modifiedBy'] = Auth::id();

        $crop->fill($requestData)->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Updated successfully.',
            'data' => $crop
        ]);
    }

    public function destroy($id)
    {
        $crop = Crops::find($id);

        if (!$crop) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $crop->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Deleted successfully.'
        ]);
    }
}