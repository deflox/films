<?php

/**
 * Controller for handling all requests regarding
 * films.
 *
 * @author Leo Rudin
 */

namespace App\Controllers;

use App\Accessor;
use App\Models\Movie;
use App\Models\Actor;
use App\Models\Genre;

class FilmController extends Accessor
{
    /**
     * Get request for displaying the index page.
     *
     * @param  $req
     * @param  $res
     * @return mixed
     */
    public function getIndex($req, $res)
    {
        return $this->view->render($res, 'index.twig');
    }

    /**
     * Post request for storing a new item.
     *
     * @param  $req
     * @param  $res
     * @return string
     */
    public function addItem($req, $res)
    {
        $json = [
            'error' => false,
            'error_messages' => []
        ];

        // Check if item already exists
        $dbMovie = Movie::where('imdb_id', $req->getParam('imdb_id'))
            ->get()
            ->first();

        if ($dbMovie !== null) {
            $json['error'] = true;
            $json['error_messages']['global'] = [
                'There exists already an entry with that IMDb-Id.'
            ];
            return json_encode($json);
        }

        // Validate input
        $validator = $this->validator->validate($req, [
            'imdb_id|IMDb Id' => ['required'],
            'title|Title' => ['required'],
            'year|Year' => ['required', 'integer', ['length', 4]],
            'runtime|Runtime' => ['required', 'integer'],
            'genres|Genres' => ['required'],
            'actors|Actors' => ['required'],
            'imdb_rating|IMDb rating' => ['required'],
            'plot|Plot' => ['required']
        ]);

        if ($validator->failed()) {
            $json['error'] = true;
            $json['error_messages'] = $validator->errors();
            return json_encode($json);
        }

        // Movie
        $movie = new Movie();
        $movie->imdb_id = $req->getParam('imdb_id');
        $movie->title = $req->getParam('title');
        $movie->title_german = $req->getParam('title_german');
        $movie->imdb_rating = $req->getParam('imdb_rating');
        $movie->personal_rating = $req->getParam('title');
        $movie->runtime = $req->getParam('runtime');
        $movie->plot = $req->getParam('plot');
        $movie->save();

        // Genres
        $allGenres = explode(',', $req->getParam('genres'));

        foreach ($allGenres as $genre) {

            $dbGenre = Genre::where('name', trim($genre))
                ->get()
                ->first();

            if (!isset($dbGenre)) {

                // Create a new genre
                $newGenre = new Genre();
                $newGenre->name = $genre;
                $newGenre->save();

                // Create relation
                $movie->genres()->save($newGenre);

            } else {

                // Create relation
                $movie->genres()->save($dbGenre);

            }

        }

        // Actors
        $allActors = explode(',', $req->getParam('actors'));

        foreach ($allActors as $actorName) {

            $dbActor = Actor::where('name', trim($actorName))
                ->get()
                ->first();

            if (!isset($dbActor)) {

                // Create a new actor
                $newActor = new Actor();
                $newActor->name = $actorName;
                $newActor->save();

                // Create relation
                $movie->actors()->save($newActor);

            } else {

                // Crate relation
                $movie->actors()->save($dbActor);

            }

        }

        return json_encode($json);

    }

    /**
     * Post request for deleting a film.
     *
     * @param $req
     * @param $res
     */
    public function deleteItem($req, $res)
    {

    }
}