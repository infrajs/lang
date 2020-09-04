<?php

use infrajs\event\Event;
use infrajs\config\Config;
use infrajs\env\Env;
use infrajs\lang\Lang;
use infrajs\load\Load;
use infrajs\template\Template;

Env::add('lang', function () {
	$lang = Lang::detect();
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
