<?php

namespace Bims\Core;

class Filename
{
    private static $prefix = NULL;
    public static function filename()
    {
        return self::$prefix;
    }
}
