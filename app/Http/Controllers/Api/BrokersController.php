<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brokers;

class BrokersController extends Controller
{
      public function index()
    {
        $brokers = Brokers::all();
        return response()->json([
            'status' => 'success',
            'data' => $brokers
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:brokers,code',
            'init' => 'nullable|string|max:10',
            'fname' => 'nullable|string|max:100',
            'lname' => 'nullable|string|max:100',
            'citizenid' => 'nullable|string|max:20',
            'address1' => 'nullable|string|max:255',
            'address2' => 'nullable|string|max:255',
            'address3' => 'nullable|string|max:255',
            'sub_cities' => 'nullable|string|max:100',
            'city_id' => 'nullable|integer',
            'province_id' => 'nullable|integer',
            'loc' => 'nullable|string|max:255',
            'broker_color' => 'nullable|string|max:50',
            'createdBy' => 'nullable|integer',
            'modifiedBy' => 'nullable|integer',
        ]);

        $broker = Brokers::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Broker created successfully.',
            'data' => $broker
        ], 201);
    }

    public function show($id)
    {
        $broker = Brokers::find($id);

        if (!$broker) {
            return response()->json([
                'status' => 'error',
                'message' => 'Broker not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $broker
        ]);
    }

    public function update(Request $request, $id)
    {
        $broker = Brokers::find($id);

        if (!$broker) {
            return response()->json([
                'status' => 'error',
                'message' => 'Broker not found.'
            ], 404);
        }

        $validated = $request->validate([
            'code' => 'required|string|unique:brokers,code,' . $id,
            'init' => 'nullable|string|max:10',
            'fname' => 'nullable|string|max:100',
            'lname' => 'nullable|string|max:100',
            'citizenid' => 'nullable|string|max:20',
            'address1' => 'nullable|string|max:255',
            'address2' => 'nullable|string|max:255',
            'address3' => 'nullable|string|max:255',
            'sub_cities' => 'nullable|string|max:100',
            'city_id' => 'nullable|integer',
            'province_id' => 'nullable|integer',
            'loc' => 'nullable|string|max:255',
            'broker_color' => 'nullable|string|max:50',
            'createdBy' => 'nullable|integer',
            'modifiedBy' => 'nullable|integer',
        ]);

        $broker->fill($validated)->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Broker updated successfully.',
            'data' => $broker
        ]);
    }

    public function destroy($id)
    {
        $broker = Brokers::find($id);

        if (!$broker) {
            return response()->json([
                'status' => 'error',
                'message' => 'Broker not found.'
            ], 404);
        }

        $broker->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Broker deleted successfully.'
        ]);
    }
}
