<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cities;

class CityController extends Controller
{
   public function index()
    {
        $cities = Cities::with('province')->get();
        return response()->json([
            'status' => 'success',
            'data' => $cities
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'province_id' => 'required|exists:provinces,id',
            'th_name' => 'required|string|max:255',
            'en_name' => 'nullable|string|max:255',
        ]);

        $city = Cities::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'City created successfully.',
            'data' => $city
        ], 201);
    }

    public function getByProvince($id)
    {
        $cities = Cities::with('province')->where('province_id', $id)->get();

        if (!$cities) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $cities]);
    }

    public function show($id)
    {
        $city = Cities::with('province')->find($id);

        if (!$city) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $city]);
    }

    public function update(Request $request, $id)
    {
        $city = Cities::find($id);

        if (!$city) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'province_id' => 'required|exists:provinces,id',
            'th_name' => 'required|string|max:255',
            'en_name' => 'nullable|string|max:255',
        ]);

        $city->fill($validated)->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Updated successfully.',
            'data' => $city
        ]);
    }

    public function destroy($id)
    {
        $city = Cities::find($id);

        if (!$city) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $city->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Deleted successfully.'
        ]);
    }
}
