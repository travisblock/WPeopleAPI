<?php

namespace Bims\Core;

class Filename
{
    private static $prefix = "43cede15cf15c978251d0e8b76aa395ed8bb3da117b99c920f38bebb2ee765ba";
    public static function filename()
    {
        return self::$prefix;
    }
}
