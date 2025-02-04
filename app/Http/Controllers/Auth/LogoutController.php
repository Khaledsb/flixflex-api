<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LogoutFormRequest;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function __invoke(LogoutFormRequest $request) {
        $user = Auth::user();

        if ($user) {
            $user->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Successfully logged out'
            ], 200);
        }

        return response()->json([
            'message' => 'No authenticated user found'
        ], 401);
    }
}
