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
use App\Models\View;
use DateTime;

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
        return $this->view->render($res, 'index.twig', [
            'movies' => Movie::orderBy('created_at', 'desc')
                ->get()
        ]);
    }

    /**
     * Post request for getting a single item.
     *
     * @param  $req
     * @param  $res
     * @return \App\Models\Movie
     */
    public function getSingleItem($req, $res)
    {
        $movie = Movie::find($req->getParam('id'));

        return json_encode([
            'movie' => $movie,
            'actors' => $movie->actors()->get(),
            'genres' => $movie->genres()->get()
        ]);
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
            'title|Title' => ['required', ['lengthMax', 255]],
            'title_foreign_language|Title in foreign language' => [['lengthMax', 255]],
            'year|Year' => ['required', 'integer', ['length', 4]],
            'runtime|Runtime' => ['required', 'integer'],
            'movieViewDate|Movie view date' => [['dateFormat', 'd/m/Y']],
            'genres|Genres' => ['required'],
            'actors|Actors' => ['required'],
            'imdb_rating|IMDb rating' => ['required', 'numeric'],
            'personal_rating|Personal rating' => ['numeric'],
            'plot|Plot' => ['required', ['lengthMax', 1000]]
        ]);

        if ($validator->failed()) {
            $json['error'] = true;
            $json['error_messages'] = $validator->errors();
            return json_encode($json);
        }

        // Save image
        $url = $this->saveImage($req->getParam('imdb_id'), $req->getParam('image_url'));

        // Movie
        $movie = new Movie();
        $movie->imdb_id = $req->getParam('imdb_id');
        $movie->title = $req->getParam('title');
        $movie->title_foreign_language = $req->getParam('title_foreign_language');
        $movie->imdb_rating = $req->getParam('imdb_rating');
        $movie->personal_rating = $req->getParam('personal_rating');
        $movie->image_url = $url;
        $movie->runtime = $req->getParam('runtime');
        $movie->year = $req->getParam('year');
        $movie->plot = $req->getParam('plot');
        $movie->save();

        // Genres
        $allGenres = explode(',', $req->getParam('genres'));

        foreach ($allGenres as $genreName) {

            $dbGenre = Genre::where('name', trim($genreName))
                ->get()
                ->first();

            if (!isset($dbGenre)) {

                // Create a new genre
                $newGenre = new Genre();
                $newGenre->name = trim($genreName);
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
                $newActor->name = trim($actorName);
                $newActor->save();

                // Create relation
                $movie->actors()->save($newActor);

            } else {

                // Crate relation
                $movie->actors()->save($dbActor);

            }

        }

        // Create movie view date if necessary
        if (!empty($req->getParam('movieViewDate'))) {
            View::create([
                'view_date' => date('Y-m-d H:i:s', DateTime::createFromFormat('d/m/Y H:i:s', $req->getParam('movieViewDate'). '00:00:00')->getTimestamp()),
                'movie_id' => $movie->id
            ]);
        }

        return json_encode($json);

    }

    /**
     * Post request for deleting a film.
     *
     * @param  $req
     * @param  $res
     * @return string
     */
    public function deleteItem($req, $res)
    {
        $json = [
            'error' => false,
            'error_messages' => []
        ];

        if (Movie::destroy($req->getParam('id')) !== 1) {
            $json['error'] = true;
            $json['error_description']['global'] = [
                'We could not delete the desired item. Please try again later.'
            ];
        }

        return json_encode($json);
    }

    /**
     * Saves an image to the local file system.
     *
     * @param  $id
     * @param  $url
     * @return string
     */
    public function saveImage($id, $url)
    {
        if (ini_get('allow_url_fopen')) {

            file_put_contents(__DIR__.'/../../public/assets/images/uploads/'.$id.'.jpg', file_get_contents($url));

            return '';

        } else if (function_exists('curl_version')) {

            $ch = curl_init($url);
            $fp = fopen(__DIR__.'/../../public/assets/images/uploads/'.$id.'.jpg', 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);

            return '';

        } else {

            return $url;

        }
    }
}