<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

       /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'movie_id',
        'type',
    ];

    /**
     * Relation avec le modèle User.
     * Un favori appartient à un utilisateur.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pour récupérer uniquement les favoris de type movie.
     */
    public function scopeMovies($query)
    {
        return $query->where('type', 'movie');
    }

    /**
     * Scope pour récupérer uniquement les favoris de type series.
     */
    public function scopeSeries($query)
    {
        return $query->where('type', 'series');
    }
}
