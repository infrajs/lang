<?php

namespace infrajs\lang;

use infrajs\env\Env;
use infrajs\config\Config;
use infrajs\load\Load;
use infrajs\path\Path;
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
		return static::once('detect', [], function () {
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
		});
	}
	public static function code($lang, $code, $data = false)
	{
		//from.dic.key#more
		//dic.key#more exception
		//key#more
		
		$r = explode('.', $code, 3);
		if (sizeof($r) == 3) {
			//from.dic.key#more
			//from.dic.key.more - depricated
			//dic.key.more - depricated
			$dic = $r[1];
			$from = $r[0];
			$key = $r[2];
			
		} else if (sizeof($r) == 2) {
			//key.more
			//dic.key
			$dic = $r[0];
			$from = $r[0];
			$key = $r[1];
		} else {
			echo '<pre>';
			throw new \Exception('Ошибка '.$code);
		}
		
		$s = explode('#', $key);
		$key = $s[0];
		$more = $s[1] ?? false;
		if (sizeof($r) > 2) { //depricated
			if (!$more && preg_match("/\d/", $key)) {
				$more = $key;
				$key = $dic;
				$dic = $from;
			}
		}
		return Lang::lang($lang, $dic, $key, $data);
	}
	public static function make($opt = []) {
		//from.dic.key#more
		//from.dic.key#more

		//dic.key#more
		//key#more
		extract(array_merge([
			'dic'=> Lang::detectExt(),
			'key' => 'undefined',
			'payload' => false, //Данные для шаблона сообщения
			'lang' => Lang::detect()
		], $opt));

		$str = Lang::lang($lang, $dic, $key, $payload);
		if ($more) $str = $str ? $str.' '.$more : $more;
	}


	
	//Без кода ошибки в сообщении
	public static function err($ans, $lang = null, $code = null)
	{
		if (is_null($code)) return Ans::err($ans);
		$ans['code'] = $code;
		$msg = Lang::code($lang, $code);
		return Ans::err($ans, $msg);
	}
	public static function errtpl($ans, $lang = null, $code = null)
	{
		if (is_null($code)) return Ans::err($ans);
		$ans['code'] = $code;
		$msg = Lang::code($lang, $code, $ans);
		return Ans::err($ans, $msg);
	}
	//С кодом ошибки в сообщении
	public static function fail($ans, $lang = null, $code = null)
	{
		if (is_null($code)) return Ans::err($ans);
		$ans['code'] = $code;
		$msg = Lang::code($lang, $code);
		if (!in_array($msg[strlen($msg) - 1], ['.', '!', '?'])) $msg .= '.';
		$msg .= ' <code>' . $code . '</code>';
		return Ans::err($ans, $msg);
	}
	public static function failtpl($ans, $lang = null, $code = null)
	{
		if (is_null($code)) return Ans::err($ans);
		$ans['code'] = $code;
		$msg = Lang::code($lang, $code, $ans);
		if (!in_array($msg[strlen($msg) - 1], ['.', '!', '?'])) $msg .= '.';
		$msg .= ' <code>' . $code . '</code>';
		return Ans::err($ans, $msg);
	}
	//Без кода ошибки в сообщении
	public static function ret($ans, $lang = null, $code = null)
	{
		if (is_null($code)) return Ans::ret($ans);
		$ans['code'] = $code;
		$msg = Lang::code($lang, $code);
		return Ans::ret($ans, $msg);
	}
	public static function rettpl($ans, $lang = null, $code = null)
	{
		if (is_null($code)) return Ans::ret($ans);
		$ans['code'] = $code;
		$msg = Lang::code($lang, $code, $ans);
		return Ans::ret($ans, $msg);
	}



	public static function lang($lang, $name, $str, $data = false)
	{
		$src = '-' . $name . '/i18n/';

		if (Path::theme($src . $lang . '.json')) {
			$langs = Load::loadJSON($src . $lang . '.json');
			if (!empty($langs[$str])) {
				return $data ? Template::parse([$langs[$str]], $data) : $langs[$str];
			}
		}

		if (Path::theme($src . $lang . '.server.json')) {
			$langs = Load::loadJSON($src . $lang . '.server.json');
			if (!empty($langs[$str])) {
				return $data ? Template::parse([$langs[$str]], $data) : $langs[$str];
			}
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
