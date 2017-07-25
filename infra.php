<?php

use infrajs\event\Event;
use infrajs\config\Config;
use infrajs\env\Env;
use infrajs\lang\Lang;
use infrajs\load\Load;
use infrajs\template\Template;

Env::add('lang', function () {
	$conf = Config::get('lang');
	
	if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) $accept = 'ru';
	else  $accept = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
	
	preg_match('/^\w{2}/',$accept, $matches);
	if (isset($matches[0])) $accept = $matches[0];
	else $accept = 'ru';
	
	switch (strtolower($accept)){
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
	Template::$scope['Lang'] = array();
	Template::$scope['Lang']['str'] = function ($name = false, $str = null) {
		return Lang::str($name, $str);
	};
	Template::$scope['Lang']['name'] = function ($name = false) {
		return Lang::name($name);
	};
});
