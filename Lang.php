<?php
namespace infrajs\lang;

use infrajs\env\Env;
use infrajs\config\Config;
use infrajs\load\Load;

class Lang 
{
	public static function lang($lang, $name, $str)
	{
		$src = '-'.$name.'/i18n/';
		$langs = Load::loadJSON($src.$lang.'.json');
		if (!empty($langs[$str])) return $langs[$str];
		else return $str;
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