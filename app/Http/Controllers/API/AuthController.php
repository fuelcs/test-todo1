<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Resources\User\UserResource;
use App\Services\UserService;
use App\DTO\UserDTO;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="API Endpoints for User Authentication"
 * )
 *
 * Class AuthController
 *
 * Controller for handling user authentication actions.
 */
class AuthController extends Controller
{
    private UserService $userService;

    /**
     * AuthController constructor.
     *
     * @param UserService $userService User service for business logic.
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Register a new user.
     *
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UserDTO")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User successfully registered",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object", ref="#/components/schemas/UserDTO"),
     *             @OA\Property(property="token", type="string", example="jwt_token_here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $userDTO = new UserDTO($request->validated());
        $result = $this->userService->register($userDTO);

        return response()->json([
            'user'  => new UserResource($result['user']),
            'token' => $result['token']
        ], 201);
    }

    /**
     * Authenticate the user and return a token.
     *
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="User login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successfully authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="jwt_token_here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data  = $request->validated();
        $token = $this->userService->login($data['email'], $data['password']);

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json(['token' => $token], 200);
    }

    /**
     * Log out the user by invalidating the token.
     *
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="User logout",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User successfully logged out",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Successfully logged out"))
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to logout"
     *     )
     * )
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            $this->userService->logout();
            return response()->json(['message' => 'Successfully logged out'], 200);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to logout, please try again'], 500);
        }
    }

    /**
     * Refresh the JWT token.
     *
     * @OA\Post(
     *     path="/api/auth/refresh",
     *     summary="Refresh JWT token",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully",
     *         @OA\JsonContent(@OA\Property(property="token", type="string", example="new_jwt_token_here"))
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to refresh token"
     *     )
     * )
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        try {
            $newToken = $this->userService->refresh();
            return response()->json(['token' => $newToken], 200);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to refresh token'], 500);
        }
    }

    /**
     * Get the authenticated user's details.
     *
     * @OA\Get(
     *     path="/api/auth/me",
     *     summary="Get current user details",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Authenticated user data",
     *         @OA\JsonContent(ref="#/components/schemas/UserDTO")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        $user = $this->userService->me();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json(new UserResource($user), 200);
    }
}
