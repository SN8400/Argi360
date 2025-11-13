<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('group')->get();
        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'group_id' => 'nullable|exists:groups,id',
            'citizenid' => 'nullable|string|max:20',
            'init' => 'nullable|string|max:10',
            'fname' => 'nullable|string|max:100',
            'lname' => 'nullable|string|max:100',
            'canEdit' => 'nullable|in:Y,N',
            'reviewTeam' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['password'] = bcrypt($data['password']);
        $data['status'] = 1;

        $user = User::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully.',
            'data' => $user
        ], 201);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.'
            ], 404);
        }

        // Validate with conditional password
        $rules = [
            'email' => 'required|email|unique:users,email,' . $id,
            'group_id' => 'nullable|exists:groups,id',
            'citizenid' => 'nullable|string|max:20',
            'init' => 'nullable|string|max:10',
            'fname' => 'nullable|string|max:100',
            'lname' => 'nullable|string|max:100',
            'canEdit' => 'nullable|in:Y,N',
            'reviewTeam' => 'nullable|string|max:255',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|confirmed|min:6';
        }

        $validated = $request->validate($rules);

        $data = $request->all();

        if ($request->filled('password')) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->fill($data)->save();

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully.',
            'data' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully.'
        ]);
    }
}
