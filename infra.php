<?php

use infrajs\event\Event;
use infrajs\config\Config;
use infrajs\env\Env;
use infrajs\lang\Lang;
use infrajs\load\Load;
use infrajs\template\Template;

Env::add('lang', function () {
	$conf = Config::get('lang');
	preg_match('/^\w{2}/',$_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
	switch (strtolower($matches[0])){
	    case "ru": $lang = "ru"; break; // если русский
	    case "de": $lang = "de"; break; // если немецкий
	    case "fr": $lang = "fr"; break; // если французкий
	    case "en": case "uk": case "us": $lang = "en"; break; // если английский
	    case "ua": $lang = "ua"; break; // если украинский
	    default: $lang = $conf['lang']['def']; break;
	}
	if (!in_array($lang, $conf['lang']['list'])) $lang = $conf['lang']['def'];
	return $lang;
}, function ($newval) {
	$conf = Config::get('lang');
	return in_array($newval, $conf['lang']['list']);
});


Event::one('Controller.oninit', function () {
	Template::$scope['~lang'] = function ($name = false, $str = null) {
		return Lang::str($name, $str);
	};
	Template::$scope['Lang.str'] = function ($name = false, $str = null) {
		return Lang::str($name, $str);
	};
});
