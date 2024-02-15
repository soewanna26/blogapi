<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|max:10',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        $token = $user->createToken('Blog')->accessToken;
        return ResponseHelper::success([
            'accept_token' => $token
        ]);
    }
    public function login(Request $request)
    {
        $credential = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::attempt($credential)) {
            $user = auth()->user();
            $token = $user->createToken('Blog')->accessToken;
            return ResponseHelper::success([
                'accept_token' => $token,
            ]);
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user()->token();
        $user->revoke();
        return ResponseHelper::success([],'Successfully logged out');

    }
}
