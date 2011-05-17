FrontEndEditor.define_field( 'group', 'input', {
	editors: [],

	ajax_get: function () {
		var
			self = this,
			data = {
				callback: 'get',
				action	: 'front-end-editor',
				nonce	: FrontEndEditor.data.nonce,
				data: jQuery.map(this.editors, function (el) {
					el.pre_ajax_get();
					return el.data;
				})
			};

		// TODO: completely eliminate sync_load()
		jQuery.post(FrontEndEditor.data.ajax_url, data, jQuery.proxy(self, 'ajax_get_handler'), 'json');
	},

	ajax_get_handler: function (response) {
		for (var i=0; i<this.editors.length; i++) {
			this.editors[i].ajax_get_handler(response[i]);
		}
	}
});
