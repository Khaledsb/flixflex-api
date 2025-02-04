<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovieListRequest;
use App\Http\Requests\MovieSearchRequest;
use App\Http\Requests\MovieShowRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;

class MovieController extends Controller
{
    /**
     * Returns a list of popular movies from The Movie Database (TMDB)
     *
     * @param MovieListRequest $request
     * @return JsonResponse
     */
    public function index(MovieListRequest $request) {

        try {
            $data = $request->validated();

            // $movies = Http::get("https://api.themoviedb.org/3/movie/popular", [
            //     'api_key' => env('TMDB_API_KEY'),
            //     'page' => $data['page'],
            // ])->json();
        
            // if (isset($movies['success']) && !$movies['success']) {
            //     throw new Exception("Error: " . $movies['status_message']);
            // }

            // return response()->json($movies['results']);
            $page = max(1, (int) $data['page']); // Ensure page is at least 1
            $perPage = 10; // Number of movies per page
            $apiKey = env('TMDB_API_KEY'); // TMDb API Key
    
            $movies = [];  
            $tmdbPage = 1;
    
            // Fetch enough pages from TMDb to fill the requested pagination
            while (count($movies) < $page * $perPage) {
                $response = Http::get("https://api.themoviedb.org/3/movie/popular", [
                    'api_key' => $apiKey,
                    'page' => $tmdbPage
                ]);
    
                if ($response->failed()) {
                    return response()->json(['error' => 'Failed to fetch movies'], 500);
                }
    
                $data = $response->json();
                $movies = array_merge($movies, $data['results']);
    
                if ($tmdbPage >= $data['total_pages']) {
                    break; // Stop if no more pages exist in TMDb
                }
    
                $tmdbPage++; // Go to the next TMDb page
            }
    
            // Paginate the movies manually
            $totalMovies = count($movies);
            $totalPages = ceil($totalMovies / $perPage);
            $moviesPaginated = array_slice($movies, ($page - 1) * $perPage, $perPage);
    
            return response()->json([
                'data' => $moviesPaginated,
                'current_page' => $page,
                'last_page' => $totalPages,
                'total_movies' => $totalMovies,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle the incoming request.
     *
     * @param  \App\Http\Requests\MovieShowRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function show(MovieShowRequest $request) {
        try {
            $data = $request->validated();

            $movie = Http::get("https://api.themoviedb.org/3/movie/{$data['movie']}", [
                'api_key' => env('TMDB_API_KEY')
            ])->json();
        
            if (isset($movie['success']) && !$movie['success']) {
                throw new Exception($movie['status_message']);
            }

            return response()->json($movie);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Searches for movies, TV shows, and people based on a query string.
     *
     * @param MovieSearchRequest $request
     * @return JsonResponse
     */

    public function search(MovieSearchRequest $request) {
        // $data = $request->validated();

        // $results = Http::get("https://api.themoviedb.org/3/search/movie", [
        //     'api_key' => env('TMDB_API_KEY'),
        //     //'query' => $query,
        // ])->json();

        // return response()->json($results['results']);
    }
    
}
