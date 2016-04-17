<?php

namespace JoeBengalen\RamlApiTester;

class Util
{
    public static function pascalCase($str)
    {
        $str = preg_replace('/[^a-z0-9]+/i', ' ', $str);
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);

        return $str;
    }

    public static function camelCase($str)
    {
        return lcfirst(self::pascalCase($str));
    }
}
