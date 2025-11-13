<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chemicals;
use App\Models\Standard_code;
use App\Models\Tmp_schedule_plan_details;
use Illuminate\Support\Facades\DB;

class ChemicalsController extends Controller
{
    public function index()
    {
        
        $items = Chemicals::with(['unit', 'standardcode', 'tmp_schedule_plan_details','bigunit'])->get();
        return response()->json([
            'status' => 'success',
            'data' => $items
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:100',
            'name' => 'required|string|max:255',
            'details' => 'nullable|string',
            'formula_code' => 'nullable|string|max:100',
            'standard_code_id' => 'nullable|integer|exists:standard_code,id',
            'unit_id' => 'nullable|integer|exists:units,id',
            'rate_per_land' => 'nullable|numeric',
            'bigunit_id' => 'nullable|integer',
            'package_per_bigunit' => 'nullable|numeric',
            'ctype' => 'nullable|string|max:50',
        ]);

        $item = Chemicals::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Chemical created successfully.',
            'data' => $item
        ], 201);
    }

    public function show(string $id)
    {
        $item = Chemicals::find($id);

        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $item]);
    }

    public function update(Request $request, string $id)
    {
        $item = Chemicals::find($id);

        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'code' => 'nullable|string|max:100',
            'name' => 'required|string|max:255',
            'details' => 'nullable|string',
            'formula_code' => 'nullable|string|max:100',
            'standard_code_id' => 'nullable|integer|exists:standard_code,id',
            'unit_id' => 'nullable|integer|exists:units,id',
            'rate_per_land' => 'nullable|numeric',
            'bigunit_id' => 'nullable|integer',
            'package_per_bigunit' => 'nullable|numeric',
            'ctype' => 'nullable|string|max:50',
        ]);

        $item->fill($validated)->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Updated successfully.',
            'data' => $item
        ]);
    }

    public function destroy(string $id)
    {
        $item = Chemicals::find($id);

        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $item->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Deleted successfully.'
        ]);
    }
}
