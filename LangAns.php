<?php

namespace infrajs\lang;

use infrajs\ans\Ans;

trait LangAns
{
    //public static $name = '...';
    public static function code($lang, $code)
    {
        $code = static::$name.'.'.$code;
        return Lang::code($lang, $code);
    }
    public static function lang($lang, $str)
    {
        return Lang::lang($lang, static::$name, $str);
    }
    //Без кода ошибки в сообщении
    public static function err($ans, $lang = null, $code = null)
    {
        if (is_null($code)) return Ans::err($ans);
        $code = static::$name.'.'.$code;
        return Lang::err($ans, $lang, $code);
    }
    //С кодом ошибки в сообщении
    public static function fail($ans, $lang = null, $code = null)
    {
        if (is_null($code)) return Ans::err($ans);
        
        $code = static::$name.'.'.$code;
        return Lang::fail($ans, $lang, $code);
    }
    //Без кода ошибки в сообщении
    public static function ret($ans, $lang = null, $code = null)
    {
        if (is_null($code)) return Ans::ret($ans);
        $code = static::$name.'.'.$code;
        return Lang::ret($ans, $lang, $code);
    }
}
