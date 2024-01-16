<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(AuthRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (User::where('name', $data['name'])->count() == 1) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'name already registered',
                    ],
                ],
            ], 400));
        }

        $user = new User($data);
        $user->role =  'customer';
        $user->password = Hash::make($data['password']);
        $user->save();

        // $token = $user->createToken('auth_token')->plainTextToken;

        $response = [
            'user' => $user,
            // 'token' => $token,
        ];
        return response()->json($response, 201);
    }

    public function login(AuthLoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'email or password invalid',
                    ],
                ],
            ], 400));
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];
        return response()->json($response)->setStatusCode(200);
    }

    public function logout(Request $request):JsonResponse{
        $request->user()->tokens()->delete();
        return response()->json([
         'message' => 'logged out'
        ], 200);
    }
}
