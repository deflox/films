<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'movies';

    /**
     * The actors that belong to the movie.
     */
    public function actors()
    {
        return $this->belongsToMany('App\Models\Actor', 'actor_movie', 'movie_id', 'actor_id');
    }

    /**
     * The genres that belong to the movie.
     */
    public function genres()
    {
        return $this->belongsToMany('App\Models\Genre', 'genre_movie', 'movie_id', 'genre_id');
    }
}