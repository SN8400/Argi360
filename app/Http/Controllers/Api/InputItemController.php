<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Input_item;
use Illuminate\Support\Facades\DB;

class InputItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Input_item::with('unit')->get();
        return response()->json([
            'status' => 'success',
            'data' => $items
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'tradename' => 'nullable|string|max:255',
            'common_name' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:100',
            'unit_id' => 'nullable|integer',
            'pur_of_use' => 'nullable|string|max:255',
            'RM_Group' => 'nullable|string|max:255',
        ]);

        $item = Input_item::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Input item created successfully.',
            'data' => $item
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
            $item = Input_item::find($id);

        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $item]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = Input_item::find($id);

        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'tradename' => 'nullable|string|max:255',
            'common_name' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:100',
            'unit_id' => 'nullable|integer',
            'pur_of_use' => 'nullable|string|max:255',
            'RM_Group' => 'nullable|string|max:255',
        ]);

        $item->fill($validated)->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Updated successfully.',
            'data' => $item
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
          $item = Input_item::find($id);

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
