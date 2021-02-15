<?php

namespace Bims\Helpers;

class Encryptor
{

    public static function EncryptorCipher($ch, $key)
    {
        if (!ctype_alpha($ch))
            return $ch;

        $offset = ord(ctype_upper($ch) ? 'A' : 'a');
        return chr(fmod(((ord($ch) + $key) - $offset), 26) + $offset);
    }

    public static function EncryptorEncipher($input, $key)
    {
        $output = "";

        $inputArr = str_split($input);
        foreach ($inputArr as $ch)
            $output .= self::EncryptorCipher($ch, $key);

        return $output;
    }

    public static function EncryptorDecipher($input, $key)
    {
        return self::EncryptorEncipher($input, 26 - $key);
    }

    public static function EncryptorAbc($data)
    {
        $md5    = md5("$data" . 'Encryptor');
        $az     = implode(range('a', 'z'));
        $base64 = strtolower(base64_encode($md5)) . "$az";
        $final  = preg_replace('([0-9|\=])', '', "$base64");
        $arr    = array_unique(str_split($final));
        return implode($arr);
    }

    public static function EncryptorABCD($data)
    {
        $md5    = md5("$data" . '_Encryptor');
        $az     = implode(range('A', 'Z'));
        $base64 = strtoupper(base64_encode($md5)) . "$az";
        $final  = preg_replace('([0-9|\=])', '', "$base64");
        $arr    = array_unique(str_split($final));
        return implode($arr);
    }

    public static function encrypt($data, $key)
    {
        $keyLen = strlen($key);
        $az     = implode(range('a', 'z'));
        $AZ     = implode(range('A', 'Z'));
        $rand   = self::EncryptorAbc($key);
        $rand2  = self::EncryptorABCD($key);

        $one    = base64_encode($data);
        $two    = self::EncryptorEncipher($one, $keyLen);
        $three  = strtr($two, $az, $rand);

        $four   = strtr($three, $AZ, $rand2);
        return $four;
    }
}
