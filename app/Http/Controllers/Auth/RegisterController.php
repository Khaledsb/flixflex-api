<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['password'] = bcrypt($data['password']);
            $user = User::create($data);

            return response()->json(['token' => $user->createToken('flixflex_api')->plainTextToken]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }
}
