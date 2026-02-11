<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseApiController
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, remember: true)) {
            throw ValidationException::withMessages([
                'email' => ['بيانات الدخول غير صحيحة.'],
            ]);
        }

        $user = $request->user();
        $token = null;

        // SPA: session-based (Sanctum stateful requests). Mobile/CLI: token-based.
        if ($request->hasSession()) {
            $request->session()->regenerate();
        } else {
            $token = $user->createToken('api')->plainTextToken;
        }

        return $this->ok([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => method_exists($user, 'getRoleNames') ? $user->getRoleNames()->values() : [],
            ],
            'token' => $token,
        ], 'تم تسجيل الدخول بنجاح');
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return $this->ok([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => method_exists($user, 'getRoleNames') ? $user->getRoleNames()->values() : [],
            ],
        ]);
    }

    public function logout(Request $request)
    {
        // Token-based logout (mobile/CLI)
        if ($request->user()?->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        // Session-based logout (SPA)
        if ($request->hasSession()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return $this->ok(null, 'تم تسجيل الخروج بنجاح');
    }

    /**
     * Refreshes the session for SPA clients.
     * (For mobile later, we can add token refresh / PAT rotation separately.)
     */
    public function refresh(Request $request)
    {
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return $this->me($request);
    }
}

