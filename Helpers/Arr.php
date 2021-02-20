<?php

namespace Bims\Helpers;

class Arr
{

    public static $result = null;

    public static function filter($array, $match)
    {
        $filter = array();
        foreach($array as $data) {
            if (is_array($data) || is_object($data)) {
                foreach($data as $key) {
                    if ($key['name'] == $match){
                        $filter = $key;
                        break;
                    }
                }	
            }
        }
        
        self::$result = $filter;

        return __CLASS__;
    }

    /**
     * format date to array
     * example : yyyy-mm-dd to array[['year'],['month'], ['day]]
     * 
     * @param $date string
     * @param $delimiter string
     * 
     * @return __CLASS__
     */
    public static function dateToArray(string $date, string $delimiter = '-')
    {
        // intl format = yyyy-mm-dd
        $parse  = str_replace('/', '-', $date);
        $parse  = date_create($parse);
        $parse  = date_format($parse, 'Y-m-d');
        $format = explode($delimiter, $parse);
        $date   = [];
        if (is_array($format) && count($format) >= 3) {
            $date['year']   = $format[0];
            $date['month']  = $format[1];
            $date['day']    = $format[2];
        }

        self::$result = $date;

        return __CLASS__;
    }

    public static function pipeToArray(string $string, $key = 'key,value')
    {   
        $format = explode('|', $string);
        $key    = explode(',', $key);

        $array  = [];
        if (is_array($format) && count($format) >= 2) {
            $array[$key[0]]     = $format[0];
            $array[$key[1]]     = $format[1];
        }

        self::$result = $array;

        return __CLASS__;
    }

    public static function arrToPipeArray(array $array, string $keyName = null)
    {
        $data = [];
        foreach($array as $val) {
            $data[] = self::pipeToArray($val, $keyName)::result();
        }

        self::$result = $data;
        return __CLASS__;
    }

    public static function toObject()
    {
        self::$result = json_decode(json_encode(self::$result));
        return __CLASS__;
    }

    public static function result()
    {
        return self::$result;
    }
}