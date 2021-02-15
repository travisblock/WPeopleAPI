<?php

namespace Bims\Http;

class Response{

    public static function set($message, $prefix = null)
    {
        if (is_null($prefix)) {
            $prefix = 'message';
        }

        return json_decode(json_encode([$prefix => $message]));
    }
}