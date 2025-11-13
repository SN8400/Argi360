<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Groups;

class RoleController extends Controller
{
    public function index()
    {
        $groups = Groups::All();
        return response()->json([
            'status' => 'success',
            'data' => $groups
        ]);
    }

    public function store(Request $request)
    {
        // ✅ Validate ข้อมูลที่รับเข้ามา
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $requestData = $request->all();
        $groups = Groups::create($requestData);

        return response()->json([
            'status' => 'success',
            'message' => 'Role created successfully.',
            'data' => $groups
        ], 201);
    }

    public function show($id)
    {
        $groups = Groups::find($id);

        if (!$groups) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $groups]);
    }

    public function update(Request $request, $id)
    {
        $groups = Groups::find($id);

        if (!$groups) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);


        $requestData = $request->all();
        $groups->fill($requestData)->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Updated successfully.',
            'data' => $groups
        ]);
    }

    public function destroy($id)
    {
        $groups = Groups::find($id);

        if (!$groups) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $groups->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Deleted successfully.'
        ]);
    }
}
