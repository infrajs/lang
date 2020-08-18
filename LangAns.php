<?php

namespace infrajs\lang;

use infrajs\ans\Ans;

trait LangAns
{
    //public static $name = '...';
    public static function lang($lang, $str)
    {
        return Lang::lang($lang, static::$name, $str);
    }
    public static function ln($code, $lang)
    {
        $r = explode('.', $code);
        $msg = static::lang($lang, $r[0]);
        return $msg;
    }
    //Без кода ошибки в сообщении
    public static function err($ans, $lang = null, $code = null)
    {
        if (is_null($code)) return Ans::err($ans);
        $r = explode('.', $code);
        $msg = static::lang($lang, $r[0]);
        $ans['code'] = $code;
        return Ans::err($ans, $msg);
    }
    //С кодом ошибки в сообщении
    public static function fail($ans, $lang = null, $code = null)
    {
        if (is_null($code)) return Ans::err($ans);
        $r = explode('.', $code);
        $msg = static::lang($lang, $r[0]);
        if ($msg[strlen($msg) - 1] != '.') $msg .= '.';
        $msg .= ' '.static::lang($lang, 'Code') . ' ' . $code . '';
        $ans['code'] = $code;

        return Ans::err($ans, $msg);
    }
    //Без кода ошибки в сообщении
    public static function ret($ans, $lang = null, $code = null)
    {
        if (is_null($code)) return Ans::ret($ans);
        $r = explode('.', $code);
        $msg = static::lang($lang, $r[0]);
        if ($code) $ans['code'] = $code;
        return Ans::ret($ans, $msg);
    }
}
