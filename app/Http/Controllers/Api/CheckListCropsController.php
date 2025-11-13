<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Checklist_crops;
use Illuminate\Support\Carbon;

class CheckListCropsController extends Controller
{
    public function index()
    {
        $Checklists = Checklist_crops::with(['crop', 'checklist'])->get();

        return response()->json([
            'status' => 'success',
            'data' => $Checklists
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'crop_id'           => 'required|integer|exists:crops,id',
            'checklist_id'      => 'required|integer|exists:checklists,id',
            'conds'             => 'nullable|string',
            'unit'              => 'nullable|string|max:100',
            'field_map_result'  => 'nullable|string',
            'field_map_val'     => 'nullable|string',
            'desc'              => 'nullable|string',
        ]);

        $validated['created'] = Carbon::now();
        $validated['modified'] = Carbon::now();

        $checklist = Checklist_crops::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Checklist crop created successfully.',
            'data' => $checklist
        ], 201);
    }

    public function show($id)
    {
        $checklist = Checklist_crops::with(['crop', 'checklist'])->find($id);

        if (!$checklist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Checklist crop not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $checklist
        ]);
    }

    public function update(Request $request, $id)
    {
        $checklist = Checklist_crops::find($id);

        if (!$checklist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Checklist crop not found.'
            ], 404);
        }

        $validated = $request->validate([
            'crop_id'           => 'required|integer|exists:crops,id',
            'checklist_id'      => 'required|integer|exists:checklists,id',
            'conds'             => 'nullable|string',
            'unit'              => 'nullable|string|max:100',
            'field_map_result'  => 'nullable|string',
            'field_map_val'     => 'nullable|string',
            'desc'              => 'nullable|string',
        ]);

        $validated['modified'] = Carbon::now();

        $checklist->fill($validated)->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Checklist crop updated successfully.',
            'data' => $checklist
        ]);
    }

    public function destroy($id)
    {
        $checklist = Checklist_crops::find($id);

        if (!$checklist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Checklist crop not found.'
            ], 404);
        }

        $checklist->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Checklist crop deleted successfully.'
        ]);
    }

    // เมธอด copy checklist ไป crop ใหม่
    public function clone(Request $request)
    {
        $validated = $request->validate([
            'from_crop_id' => 'required|integer|exists:crops,id',
            'to_crop_id'   => 'required|integer|exists:crops,id',
        ]);

        $result = (new Checklist_crops)->copynewcrop(
            $validated['from_crop_id'],
            $validated['to_crop_id'],
            Carbon::now(),
            Carbon::now()
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Checklist crop copy: ' . $result
        ]);
    }
}
