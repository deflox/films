<?php

/**
 * Controller for handling all requests regarding
 * movie views.
 *
 * @author Leo Rudin
 */

namespace App\Controllers;

use App\Accessor;
use App\Models\View as View;
use DateTime;

class ViewController extends Accessor
{
    /**
     * Get request for getting all views for a
     * movie.
     *
     * @param  $req
     * @param  $res
     * @param  $args
     * @return string
     */
    public function getMovieViews($req, $res, $args)
    {
        return json_encode(
            View::where('movie_id', $args['id'])
                ->get()
        );
    }

    /**
     * Post request for adding a new movie view. 
     *
     * @param  $req
     * @param  $res
     * @return string
     */
    public function addMovieView($req, $res)
    {
        $json = [
            'error' => false,
            'error_messages' => []
        ];

        $validation = $this->validator->validate($req, [
            'movie_id|movie id' => ['required', 'integer'],
            'viewDate|movie view date' => ['required', ['dateFormat', 'd/m/Y']]
        ]);

        if ($validation->failed()) {
            $json['error'] = true;
            $json['error_messages'] = $validation->errors();
            return json_encode($json);
        }

        View::create([
            'view_date' => date('Y-m-d H:i:s', DateTime::createFromFormat('d/m/Y H:i:s', $req->getParam('viewDate'). '00:00:00')->getTimestamp()),
            'movie_id' => $req->getParam('movie_id')
        ]);

        return json_encode($json);
    }
}