<?php

namespace Bims;

use Bims\Filename;

class Instance
{
    private static $prefix;

    /**
     * Init WPeopleAPI with set filename and create access_token json file
     * 
     * @return Bims\Instance $prefix
     */
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

    /**
     * Get access token json file name
     */
    public static function getAccessToken()
    {
        return 'access_token_' .  Filename::filename() . '.json';
    }

    /**
     * Get client secret json file name
     */
    public static function getClientSecret()
    {
        return 'client_secret_' . Filename::filename() . '.json';
    }
}
