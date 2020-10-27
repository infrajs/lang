<?php

namespace infrajs\lang;

use infrajs\ans\Ans;

trait LangAns
{
    //public static $name = '...';
    public static function code($lang, $code, $data = false)
    {
        return Lang::code($lang, static::$name.'.'.$code, $data);
        // if (empty(static::$name)) throw new Exception('Требуется static:$name');

        // //dic.key#more
        // //key#more
        
        // $from = static::$name;
        // $r = explode('.', $code, 2);

        // if (sizeof($r) == 2) {
        //     $dic = $r[0];
        //     $key = $r[1];
            
        // } else {
        //     $dic = $from;
        //     $key = $code;
        // }
        // $s = explode('#', $key);
        // $key = $s[0];
        // $more = $s[1] ?? false;
        // if (!$more && preg_match("/\d/", $key)) { //depricated
        //     $more = $key;
        //     $key = $dic;
        //     $dic = $from;
        // }

        
        
        // return Lang::lang($lang, $dic, $key, $data);
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
    public static function errtpl($ans, $lang = null, $code = null)
    {
        if (is_null($code)) return Ans::err($ans);
        $code = static::$name.'.'.$code;
        return Lang::errtpl($ans, $lang, $code);
    }
    //С кодом ошибки в сообщении
    public static function fail($ans, $lang = null, $code = null)
    {
        if (is_null($code)) return Ans::err($ans);
        $code = static::$name.'.'.$code;
        return Lang::fail($ans, $lang, $code);
    }
    public static function failtpl($ans, $lang = null, $code = null)
    {
        if (is_null($code)) return Ans::err($ans);
        $code = static::$name.'.'.$code;
        return Lang::failtpl($ans, $lang, $code);
    }
    //Без кода ошибки в сообщении
    public static function ret($ans, $lang = null, $code = null)
    {
        if (is_null($code)) return Ans::ret($ans);
        $code = static::$name.'.'.$code;
        return Lang::ret($ans, $lang, $code);
    }
    public static function rettpl($ans, $lang = null, $code = null)
    {
        if (is_null($code)) return Ans::ret($ans);
        $code = static::$name.'.'.$code;
        return Lang::rettpl($ans, $lang, $code);
    }
}
