<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\DTOs\LoginDTO;
use Illuminate\Http\Request;

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
}