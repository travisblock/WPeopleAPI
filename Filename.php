<?php

namespace Bims;

class Filename
{
    private static $prefix = NULL;
    public static function filename()
    {
        return self::$prefix;
    }
}
