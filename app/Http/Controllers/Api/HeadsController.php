<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Heads;

class HeadsController extends Controller
{
    public function index()
    {
        $heads = Heads::all();
        return response()->json([
            'status' => 'success',
            'data' => $heads
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'init' => 'nullable|string|max:10',
            'fname' => 'required|string|max:100',
            'lname' => 'required|string|max:100',
            'citizenid' => 'nullable|string|max:20',
            'address1' => 'nullable|string|max:255',
            'address2' => 'nullable|string|max:255',
            'address3' => 'nullable|string|max:255',
            'sub_cities' => 'nullable|string|max:100',
            'city_id' => 'nullable|integer',
            'province_id' => 'nullable|integer',
            'createdBy' => 'nullable|integer',
            'modifiedBy' => 'nullable|integer',
        ]);

        $head = Heads::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Head created successfully.',
            'data' => $head
        ], 201);
    }

    public function show($id)
    {
        $head = Heads::find($id);

        if (!$head) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $head]);
    }

    public function update(Request $request, $id)
    {
        $head = Heads::find($id);

        if (!$head) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'init' => 'nullable|string|max:10',
            'fname' => 'required|string|max:100',
            'lname' => 'required|string|max:100',
            'citizenid' => 'nullable|string|max:20',
            'address1' => 'nullable|string|max:255',
            'address2' => 'nullable|string|max:255',
            'address3' => 'nullable|string|max:255',
            'sub_cities' => 'nullable|string|max:100',
            'city_id' => 'nullable|integer',
            'province_id' => 'nullable|integer',
            'createdBy' => 'nullable|integer',
            'modifiedBy' => 'nullable|integer',
        ]);

        $head->fill($validated)->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Head updated successfully.',
            'data' => $head
        ]);
    }

    public function destroy($id)
    {
        $head = Heads::find($id);

        if (!$head) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $head->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Head deleted successfully.'
        ]);
    }
}
