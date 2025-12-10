<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Helper\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use OpenApi\Attributes as OA;
use Exception;

#[OA\Tag(
    name: "Authentication",
    description: "Authentication endpoints"
)]
class AuthController extends Controller
{
    #[OA\Post(
        path: "/auth/login",
        summary: "User login",
        description: "Authenticate user and return access token",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "data", type: "object"),
                        new OA\Property(property: "message", type: "string", example: "Login successful")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "The given data was invalid"),
                        new OA\Property(property: "error", type: "object")
                    ]
                )
            )
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:6'
            ]);

            if (!Auth::attempt($request->only('email', 'password'))) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $user = User::where('email', $request->email)->firstOrFail();
            $token = $user->createToken('auth-token')->plainTextToken;

            return ApiResponse::success([
                'user' => new UserResource($user),
                'token' => $token
            ], 'Login successful');
        } catch (Exception $e) {
            return ApiResponse::error('Request failed', ['message' => $e->getMessage()], 400);
        }
    }


    #[OA\Post(
        path: "/auth/register",
        summary: "User registration",
        description: "Register a new user",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "John Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "password")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Registration successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "data", type: "object"),
                        new OA\Property(property: "message", type: "string", example: "Registration successful")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "The given data was invalid"),
                        new OA\Property(property: "error", type: "object")
                    ]
                )
            )
        ]
    )]
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:admin,student,instructor',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role'=> $request->role,
            ]);

            $token = $user->createToken('auth-token')->plainTextToken;

            return ApiResponse::success([
                'user' => new UserResource($user),
                'token' => $token
            ], 'Registration successful', 201);
        } catch (Exception $e) {
            return ApiResponse::error('Request failed', ['message' => $e->getMessage()], 400);
        }
    }


    #[OA\Post(
        path: "/auth/logout",
        summary: "User logout",
        description: "Logout user and revoke access token",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Logout successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Logout successful")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized"
            )
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return ApiResponse::success(null, 'Logout successful');
        } catch (Exception $e) {
            return ApiResponse::error('Request failed', ['message' => $e->getMessage()], 400);
        }
    }


    // #[OA\Get(
    //     path: "/auth/user",
    //     summary: "Get authenticated user",
    //     description: "Get current authenticated user information",
    //     tags: ["Authentication"],
    //     security: [["bearerAuth" => []]],
    //     responses: [
    //         new OA\Response(response: 200, description: "User information retrieved"),
    //         new OA\Response(response: 401, description: "Unauthorized")
    //     ]
    // )]
    // public function user(Request $request): JsonResponse
    // {
    //     return ApiResponse::success(
    //         new UserResource($request->user()),
    //         'User information retrieved successfully'
    //     );
    // }


    // #[OA\Post(
    //     path: "/auth/refresh",
    //     summary: "Refresh access token",
    //     description: "Refresh the current access token",
    //     tags: ["Authentication"],
    //     security: [["bearerAuth" => []]],
    //     responses: [
    //         new OA\Response(response: 200, description: "Token refreshed successfully"),
    //         new OA\Response(response: 401, description: "Unauthorized")
    //     ]
    // )]
    // public function refresh(Request $request): JsonResponse
    // {
    //     $user = $request->user();
    //     $user->currentAccessToken()->delete();
    //     $token = $user->createToken('auth-token')->plainTextToken;

    //     return ApiResponse::success(['token' => $token], 'Token refreshed successfully');
    // }
}
