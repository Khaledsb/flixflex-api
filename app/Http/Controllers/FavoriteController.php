<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFavoriteRequest;
use App\Models\Favorite;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class FavoriteController extends Controller
{

    /**
     * Returns a list of the current user's favorite movies from The Movie Database (TMDB)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        $user = Auth::user();
        $favorites = $user->favorites()->pluck('movie_id'); // Get only movie IDs
    
        $movies = collect($favorites)->map(function ($movieId) {
            $response = Http::get("https://api.themoviedb.org/3/movie/{$movieId}", [
                'api_key' => env('TMDB_API_KEY'),
                'language' => 'en-US', // Optional: Specify language
            ]);
    
            return $response->successful() ? $response->json() : null;
        })->filter(); // Remove null values if API request fails
    
        return response()->json($movies);
    }

    /**
     * Store a newly created favorite in storage.
     *
     * @param  \App\Http\Requests\StoreFavoriteRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFavoriteRequest $request) {
        try {
            $data = $request->validated();

            $user = Auth::user();

            if($user->favorites()->where('movie_id', $data['movie_id'])->exists()) {
                throw new Exception('This movie is already in your favorites');
            }

            $user->favorites()->create([
                'movie_id' => $data['movie_id'],
                'type' => $data['type'],
            ]);

            return response()->json(['message' => 'Added to favorites']);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id) {
        
        try {
            $user = Auth::user();

            $favorite = $user->favorites()->where('movie_id', $id)->firstOrFail();

            if (! $favorite) {
                throw new ModelNotFoundException('This movie is not in your favorites');
            }

            $favorite->delete();

            return response()->json(['message' => 'Removed from favorites']);
        }  catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => "This movie is not in your favorites",
            ], JsonResponse::HTTP_NOT_FOUND);
        }   catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
