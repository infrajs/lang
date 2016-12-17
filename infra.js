Event.one('Controller.oninit', function () {
	Template.scope['Lang'] = {};
	Template.scope['Lang']['str'] = function (name, str) {
		return Lang.str(name, str);
	};
	Template.scope['Lang']['name'] = function (name) {
		return Lang.name(name);
	};
});
