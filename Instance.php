<?php

namespace Bims;

use Bims\Filename;

class Instance
{
    private static $prefix;

    public static function init()
    {
        self::$prefix = Filename::filename();
        if (self::$prefix) {
            return self::$prefix;
        }

        self::$prefix = bin2hex(random_bytes(32));
        $file = file_get_contents(__DIR__ . '\Filename.php');
        $file = str_replace('NULL', '"' . self::$prefix . '"', $file);
        file_put_contents(dirname(__FILE__) . '/access_token_' . self::$prefix . '.json', '{}');
        file_put_contents(dirname(__FILE__) . '/Filename.php', $file);
        return self::$prefix;
    }

    public static function getAccessToken()
    {
        return 'access_token_' .  Filename::filename() . '.json';
    }

    public static function getClientSecret()
    {
        return 'client_secret_' . Filename::filename() . '.json';
    }
}
