Event.one('Controller.oninit', function () {
	Template.scope['~lang'] = function (name, str) {
		
		if (!name) {
			var src = '-index/i18n/';
			var name = 'lang';
		} else {
			var src = '-'+name+'/i18n/';
		}
		var ext = Config.get(name);
		ext = ext['lang'];

		var lang = Lang.name(ext);
		if (!str) return lang;
		
		
		
		var langs = Load.loadJSON(src+lang+'.json');
		if (langs && langs[str]) return langs[str];
		else return str;
		
	};
});
