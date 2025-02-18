<?php

namespace App\Services;

use App\DTO\UserDTO;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Class UserService
 *
 * Contains business logic for user registration, authentication, token management, etc.
 */
class UserService
{
    /**
     * Register a new user.
     *
     * @param UserDTO $userDTO
     * @return array{user: User, token: string}
     */
    public function register(UserDTO $userDTO): array
    {
        $user = User::create([
            'name'     => $userDTO->name,
            'email'    => $userDTO->email,
            'password' => $userDTO->password,
        ]);

        $token = JWTAuth::fromUser($user);

        return ['user' => $user, 'token' => $token];
    }

    /**
     * Log in the user.
     *
     * @param string $email
     * @param string $password
     * @return string|null
     */
    public function login(string $email, string $password): ?string
    {
        if (!$token = JWTAuth::attempt(['email' => $email, 'password' => $password])) {
            return null;
        }
        return $token;
    }

    /**
     * Log out the user by invalidating the token.
     *
     * @return void
     * @throws JWTException
     */
    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    /**
     * Refresh the JWT token.
     *
     * @return string
     * @throws JWTException
     */
    public function refresh(): string
    {
        return JWTAuth::refresh(JWTAuth::getToken());
    }

    /**
     * Get the authenticated user.
     *
     * @return User|null
     */
    public function me(): ?User
    {
        return auth()->user();
    }
}
