window.Lang = {
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