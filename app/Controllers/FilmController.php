<?php


namespace App\Controllers;


use App\Accessor;
use App\Models\Movie;
use App\Models\Actor;
use App\Models\Genre;

class FilmController extends Accessor
{
    public function getIndex($req, $res)
    {
        return $this->view->render($res, 'index.twig');
    }

    public function storeItem($req, $res)
    {
        $json = [
            'error' => false,
            'error_messages' => []
        ];

        if (empty($req->getParam('imdb_id')) ||
            empty($req->getParam('title')) ||
            empty($req->getParam('year')) ||
            empty($req->getParam('runtime')) ||
            empty($req->getParam('genres')) ||
            empty($req->getParam('actors')) ||
            empty($req->getParam('imdb_rating')) ||
            empty($req->getParam('plot'))) {
            $json['error'] = true;
            array_push($json['error_messages'], "Please fill out all required fields.");
        }

        // Movie
        $movie = new Movie();
        $movie->imdb_id = $req->getParam('imdb_id');
        $movie->title = $req->getParam('title');
        $movie->title_german = $req->getParam('title_german');
        $movie->imdb_rating = $req->getParam('imdb_rating');
        $movie->personal_rating = $req->getParam('title');
        $movie->runtime = trim(substr($req->getParam('runtime'), -3));
        $movie->plot = $req->getParam('plot');
        $movie->save();

        // Genres
        $allGenres = explode(',', $req->getParam('genres'));

        foreach ($allGenres as $genre) {

            $dbGenre = Genre::where('name', trim($genre))
                ->get()
                ->first();

            if (isset($dbGenre)) {

                

            }

        }

        // Actors
        $allActors = explode(',', $req->getParam('actors'));

    }
}