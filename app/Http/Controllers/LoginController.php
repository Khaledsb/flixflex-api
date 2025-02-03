<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginFormRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginFormRequest $request)
    {
        try {
            $credentials = $request->validated();

            if (!Auth::attempt($credentials)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
    
            $user = Auth::user();

            //revoke all tokens
            $user->tokens()->delete();
    
            return response()->json(['token' => $user->createToken('flixflex_api')->plainTextToken]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
        
    }
}
