<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\DTOs\LoginDTO;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite; 

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $dto = new LoginDTO(
            $request->email,
            $request->password
        );

        $result = $this->authService->login($dto);

        return response()->json([
            'message' => 'Login berhasil',
            'data' => $result
        ]);
    }


    public function redirectToGoogle()
    {
        return response()->json([
            'url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl()
        ]);
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            $result = $this->authService->loginWithGoogle($googleUser);

            return response()->json([
                'message' => 'Login via Google berhasil',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Autentikasi Google gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}