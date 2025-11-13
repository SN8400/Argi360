<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Checklists;

class CheckListController extends Controller
{
    public function index()
    {
        $checklists = Checklists::all();
        return response()->json([
            'status' => 'success',
            'data' => $checklists
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'      => 'required|string|max:100',
            'seq'       => 'required|integer',
            'name'      => 'required|string|max:255',
            'name_eng'  => 'nullable|string|max:255',
            'desc'      => 'nullable|string',
            'status'    => 'required|in:active,inactive',
        ]);

        $checklist = Checklists::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Checklist created successfully.',
            'data'    => $checklist
        ], 201);
    }

    public function show($id)
    {
        $checklist = Checklists::find($id);

        if (!$checklist) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Checklist not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $checklist
        ]);
    }

    public function update(Request $request, $id)
    {
        $checklist = Checklists::find($id);

        if (!$checklist) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Checklist not found.'
            ], 404);
        }

        $validated = $request->validate([
            'type'      => 'required|string|max:100',
            'seq'       => 'required|integer',
            'name'      => 'required|string|max:255',
            'name_eng'  => 'nullable|string|max:255',
            'desc'      => 'nullable|string',
            'status'    => 'required|in:active,inactive',
        ]);

        $checklist->fill($validated)->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Checklist updated successfully.',
            'data'    => $checklist
        ]);
    }

    public function destroy($id)
    {
        $checklist = Checklists::find($id);

        if (!$checklist) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Checklist not found.'
            ], 404);
        }

        $checklist->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Checklist deleted successfully.'
        ]);
    }
}
