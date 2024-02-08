<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{

    /**
     * Handle an incoming authentication request.
     */
    public function adminStore(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        if (Auth::user()->roles()->first()->name != 'superadmin') {

            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $token = Auth::user()->createToken('accessToken')->accessToken;

        return response()->json([
            'token' => $token,
            'type' => 'bearer',
            'user' => [
                'avatar' => "assets/images/avatars/brian-hughes.jpg",
                'email' => Auth::user()->email,
                'id' => Auth::user()->uuid,
                'name' => Auth::user()->name . ' ' . Auth::user()->last_name . ' ' . Auth::user()->second_last_name,
                'status' => 'online ',
                'rol' => Auth::user()->roles()->first()->name
            ]
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $token = Auth::user()->createToken('accessToken')->accessToken;

        return response()->json([
            'token' => $token,
            'type' => 'bearer',
            'user' => [
                'avatar' => "assets/images/avatars/brian-hughes.jpg",
                'email' => Auth::user()->email,
                'id' => Auth::user()->uuid,
                'name' => Auth::user()->name . ' ' . Auth::user()->last_name . ' ' . Auth::user()->second_last_name,
                'status' => 'online ',
                'rol' => Auth::user()->roles()->first()->name
            ]
        ]);
    }

    public function validateToken() : JsonResponse {
        return response()->json([
            'user' => [
                'avatar' => "assets/images/avatars/brian-hughes.jpg",
                'email' => Auth::user()->email,
                'id' => Auth::user()->uuid,
                'name' => Auth::user()->name . ' ' . Auth::user()->last_name . ' ' . Auth::user()->second_last_name,
                'status' => 'online ',
                'rol' => Auth::user()->roles()->first()->name
            ]
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
