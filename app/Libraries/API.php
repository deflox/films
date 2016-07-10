<?php

/**
 * This library provides functions for
 * API json responses.
 *
 * @author Leo Rudin
 */

namespace App\Libraries;

class API
{
    /**
     * Creates an error response for json.
     *
     * @param  $res
     * @param  $message
     * @param  $messages
     * @return mixed
     */
    public static function createError($res, $message, $messages = [])
    {
        $json = [
            'errors' => true,
            'message' => $message
        ];

        if (!empty($messages))
            $json['messages'] = $messages;

        return $res->withJson($json);
    }

    /**
     * Creates a general json response. 
     *
     * @param  $res
     * @param  $json
     * @return mixed
     */
    public static function createResponse($res, $json)
    {
        return $res->withJson($json);
    }
}