<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\DTOs\LoginDTO;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function login(LoginDTO $data)
    {
        $user = $this->userRepository->findByEmail($data->email);

        
        if (!$user || !Hash::check($data->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial yang Anda berikan salah.'],
            ]);
        }

        return [
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken
        ];
    }
}