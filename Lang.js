let Lang = {
	lang: function (lang, name, str) {
		var src = '-'+name+'/i18n/';
		var langs = Load.loadJSON(src+lang+'.json');
		if (langs && langs[str]) return langs[str];
		else return str;
	},
	str: function (name, str, lang) {
		if (!name) name = 'lang';
		lang = lang || Lang.name(name);
		if (!str) return lang;
		return Lang.lang(lang, name, str);
	},
	name: function (ext) {
		//Определяем текущий язык сайта или расширения ext (ext.def ext.list)
		var sel = Env.get('lang');
		var lang = Config.get('lang')['lang'];
		if (lang['list'] && !~lang['list'].indexOf(sel)) sel = lang['def'];
		
		if (!ext || !ext['list']) return sel;
		
		if (!~ext['list'].indexOf(sel)) { //У расширения нет поддержки текущего языка сайта
			if (!~ext['list'].indexOf(lang['def'])) return lang['def']; //Переходим на язык по умолчанию для сайта, если возможно.
			return ext['def']; //Переходим на язык по умолчанию для расширения.
		}
		return sel;
	}
}

window.Lang = Lang
export {Lang}