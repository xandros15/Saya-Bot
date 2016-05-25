<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-03
 * Time: 03:09
 */

namespace Saya\Components\Helper;

class StringHelper
{
    public static function fromCamelCase($str)
    {
        $str = lcfirst($str);
        return preg_replace_callback('/([A-Z])/',
            function ($matches) {
                return '_' . strtolower($matches[1]);
            },
            $str);
    }

    public static function toCamelCase($str, $capitaliseFirstChar = false)
    {
        if ($capitaliseFirstChar) {
            $str = ucfirst($str);
        }
        return preg_replace_callback('/_([a-z])/', function ($matches) {
            return strtoupper($matches[1]);
        }, $str);
    }
}