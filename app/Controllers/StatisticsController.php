<?php

/**
 * Controller for handling all requests regarding
 * statistics.
 *
 * @author Leo Rudin
 */

namespace App\Controllers;

use App\Accessor;
use App\Models\Movie;

class StatisticsController extends Accessor
{
    /**
     * Get request for getting the total time
     * to watch all added movies.
     *
     * @param  $req
     * @param  $res
     * @return string
     */
    public function getTotalTimeString($req, $res)
    {
        $json = [
            'errors' => false,
            'messages' => []
        ];

        $movies = Movie::all()
            ->count();

        if ($movies === 0) {
            $json['error'] = true;
            $json['messages']['global'] = 'There is not enough data to calculate statistics';
            return json_encode($json);
        }

        $total = Movie::all()->sum('runtime');

        return json_encode($this->secondsToTime($total * 60));
    }

    /**
     * Converts seconds to readable format.
     *
     * @param  $seconds
     * @return string
     */
    function secondsToTime($seconds)
    {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
    }
}