<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Provinces;

class ProvinceController extends Controller
{
    public function index()
    {
        $provinces = Provinces::all();
        return response()->json([
            'status' => 'success',
            'data' => $provinces
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'th_name' => 'required|string|max:255',
            'en_name' => 'nullable|string|max:255',
        ]);

        $province = Provinces::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Province created successfully.',
            'data' => $province
        ], 201);
    }

    public function show($id)
    {
        $province = Provinces::find($id);

        if (!$province) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $province]);
    }

    public function update(Request $request, $id)
    {
        $province = Provinces::find($id);

        if (!$province) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'th_name' => 'required|string|max:255',
            'en_name' => 'nullable|string|max:255',
        ]);

        $province->fill($validated)->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Updated successfully.',
            'data' => $province
        ]);
    }

    public function destroy($id)
    {
        $province = Provinces::find($id);

        if (!$province) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $province->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Deleted successfully.'
        ]);
    }
}
