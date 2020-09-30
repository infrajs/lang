<?php

namespace infrajs\lang;

use infrajs\env\Env;
use infrajs\config\Config;
use infrajs\load\Load;
use infrajs\ans\Ans;
use infrajs\template\Template;
use infrajs\cache\CacheOnce;

class Lang
{
	use CacheOnce;
	public static function detectExt()
	{
		$plug = $_SERVER['REQUEST_URI'];
		return static::once('detectExt', $plug, function ($plug) {
			$r = preg_split("/[\/\?]+/", $plug);
			if (!isset($r[1])) {
				$plug = 'index';
			} else {
				$plug = preg_replace("/^-/", "", $r[1]);
			}
			return $plug;
		});
	}
	public static function detect()
	{
		$conf = Config::get('lang');

		if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) $accept = 'ru';
		else  $accept = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

		preg_match('/^\w{2}/', $accept, $matches);
		if (isset($matches[0])) $accept = $matches[0];
		else $accept = 'ru';

		switch (strtolower($accept)) {
			case "ru":
				$lang = "ru";
				break; // если русский
			case "de":
				$lang = "de";
				break; // если немецкий
			case "fr":
				$lang = "fr";
				break; // если французкий
			case "en":
			case "uk":
			case "us":
				$lang = "en";
				break; // если английский
			case "ua":
				$lang = "ua";
				break; // если украинский
			default:
				$lang = $conf['lang']['def'];
				break;
		}
		if (!in_array($lang, $conf['lang']['list'])) $lang = $conf['lang']['def'];
		return $lang;
	}
	public static function code($lang, &$code, $ans = false)
	{
		$r = explode('.', $code);
		if (sizeof($r) == 4) {
			$ext = $r[0];
			$name = $r[1];
			$kod = $r[2];
		} else if (sizeof($r) == 3) {
			$name = $r[0];
			if (isset(static::$name)) {
				$ext = $name;
			} else {
				$ext = Lang::detectExt();
				if ($name != $ext) $code = $ext . '.' . $code;
			}
			$kod = $r[1];
		} else if (sizeof($r) == 2) {
			$name = $r[0];

			if (isset(static::$name)) {
				$ext = $name;
			} else {
				$ext = Lang::detectExt();
				if ($name != $ext) $code = $ext . '.' . $code;
			}
			$kod = $r[1];
		}
		return Lang::lang($lang, $name, $kod, $ans);
	}
	//Без кода ошибки в сообщении
	public static function err($ans, $lang = null, $code = null)
	{
		if (is_null($code)) return Ans::err($ans);
		$ans['code'] = $code;
		$msg = static::code($lang, $code);
		return Ans::err($ans, $msg);
	}
	public static function errtpl($ans, $lang = null, $code = null)
	{
		if (is_null($code)) return Ans::err($ans);
		$ans['code'] = $code;
		$msg = static::code($lang, $code, $ans);
		return Ans::err($ans, $msg);
	}
	//С кодом ошибки в сообщении
	public static function fail($ans, $lang = null, $code = null)
	{
		if (is_null($code)) return Ans::err($ans);
		$ans['code'] = $code;
		$msg = static::code($lang, $code);
		if (!in_array($msg[strlen($msg) - 1], ['.', '!', '?'])) $msg .= '.';
		$msg .= ' <code>' . $code . '</code>';
		return Ans::err($ans, $msg);
	}
	public static function failtpl($ans, $lang = null, $code = null)
	{
		if (is_null($code)) return Ans::err($ans);
		$ans['code'] = $code;
		$msg = static::code($lang, $code, $ans);
		if (!in_array($msg[strlen($msg) - 1], ['.', '!', '?'])) $msg .= '.';
		$msg .= ' <code>' . $code . '</code>';
		return Ans::err($ans, $msg);
	}
	//Без кода ошибки в сообщении
	public static function ret($ans, $lang = null, $code = null)
	{
		if (is_null($code)) return Ans::ret($ans);
		$ans['code'] = $code;
		$msg = static::code($lang, $code);
		return Ans::ret($ans, $msg);
	}
	public static function rettpl($ans, $lang = null, $code = null)
	{
		if (is_null($code)) return Ans::ret($ans);
		$ans['code'] = $code;
		$msg = static::code($lang, $code, $ans);
		return Ans::ret($ans, $msg);
	}



	public static function lang($lang, $name, $str, $data = false)
	{
		$src = '-' . $name . '/i18n/';

		$langs = Load::loadJSON($src . $lang . '.json');
		if (!empty($langs[$str])) {
			return $data ? Template::parse([$langs[$str]], $data) : $langs[$str];
		}

		$langs = Load::loadJSON($src . $lang . '.server.json');
		if (!empty($langs[$str])) {
			return $data ? Template::parse([$langs[$str]], $data) : $langs[$str];
		}

		return $str;
	}
	public static function str($name, $str)
	{
		//$str указывать обязатель, что бы было сообщение если забыто про $name
		if (!$name) $name = 'lang';

		$lang = Lang::name($name);
		if (is_null($str)) return $lang;

		return Lang::lang($lang, $name, $str);
	}
	public static function name($name = false)
	{
		//Определяем текущий язык сайта или расширения ext (ext.def ext.list)
		$sel = Env::get('lang');
		$lang = Config::get('lang')['lang'];
		if ($lang['list'] && !in_array($sel, $lang['list'])) $sel = $lang['def'];

		$ext = Config::get($name);
		if (empty($ext['lang'])) return $sel;
		$ext = $ext['lang'];
		if (!$ext || empty($ext['list'])) return $sel;

		if (!in_array($sel, $ext['list'])) { //У расширения нет поддержки текущего языка сайта
			if (!in_array($lang['def'], $ext['list'])) return $lang['def']; //Переходим на язык по умолчанию для сайта, если возможно.
			return $ext['def']; //Переходим на язык по умолчанию для расширения.
		}
		return $sel;
	}
}
