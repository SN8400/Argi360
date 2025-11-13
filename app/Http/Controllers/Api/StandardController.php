<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Standard_code;

class StandardController extends Controller
{
    public function index()
    {
        $standards = Standard_code::all();
        return response()->json([
            'status' => 'success',
            'data' => $standards
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'standard_name' => 'required|string|max:255',
            'chemical_type' => 'nullable|string|max:255',
            'MRLs' => 'nullable|string|max:255',
            'major_type' => 'nullable|string|max:255',
            'type_code' => 'nullable|string|max:255',
            'rate' => 'nullable|numeric',
            'details' => 'nullable|string'
        ]);

        $standard = Standard_code::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Standard created successfully.',
            'data' => $standard
        ], 201);
    }

    public function show($id)
    {
        $standard = Standard_code::find($id);

        if (!$standard) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $standard]);
    }

    public function update(Request $request, $id)
    {
        $standard = Standard_code::find($id);

        if (!$standard) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'standard_name' => 'required|string|max:255',
            'chemical_type' => 'nullable|string|max:255',
            'MRLs' => 'nullable|string|max:255',
            'major_type' => 'nullable|string|max:255',
            'type_code' => 'nullable|string|max:255',
            'rate' => 'nullable|numeric',
            'details' => 'nullable|string'
        ]);

        $standard->fill($validated)->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Standard updated successfully.',
            'data' => $standard
        ]);
    }

    public function destroy($id)
    {
        $standard = Standard_code::find($id);

        if (!$standard) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $standard->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Standard deleted successfully.'
        ]);
    }
}
