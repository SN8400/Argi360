<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;


class LoginController extends Controller
{
    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('Auth.login');
    }
    
    public function login(Request $request)
    {
        $input = $request->only('username', 'password');

        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = DB::table('users')
            ->join('groups', 'users.group_id', '=', 'groups.id')
            ->select(
                'users.*',
                'groups.name as groupName'
            )
            ->where('users.username', $input['username'])
            ->first();

        if ($user) {
            $hashedPassword = $user->password;
            if (substr($hashedPassword, 0, 4) === '$2a$') {
                $hashedPassword = '$2y$' . substr($hashedPassword, 4);
            }

            if (Hash::check($input['password'], $hashedPassword)) {
                // จำเป็นต้องใช้ Eloquent Model เพื่อ login ผ่าน Auth::login()
                $eloquentUser = \App\Models\User::find($user->id);
                Auth::login($eloquentUser);

                // เก็บ groupName ใน session หรือ Auth::user()->groupName ด้วย session
                session(['fullName' => $user->fname." ".$user->lname]);
                session(['groupName' => $user->groupName]);

                return redirect()->intended('/index');
            } else {
                return back()->withInput()->withErrors([
                    'password' => 'รหัสผ่านไม่ถูกต้อง',
                ]);
            }
        } else {
            return back()->withInput()->withErrors([
                'username' => 'ไม่พบชื่อผู้ใช้นี้ในระบบ',
            ]);
        }
    }

    public function forgotPassword()
    {
        return view('Auth.forgot_password');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
