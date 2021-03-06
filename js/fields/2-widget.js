FrontEndEditor.define_field( 'widget', 'textarea', {

	start: function () {
		this._rich = (0 === this.data.widget_id.indexOf('text-') && typeof GENTICS !== 'undefined');
		this._super();
	},

	create_input: jQuery.noop,

	content_to_input: function (content) {
		this.input = jQuery(content);

		this.form.prepend(content);

		if (this._rich) {
			GENTICS.Aloha.wpSaveCancel.saveButton.hide();
			GENTICS.Aloha.wpSaveCancel.cancelButton.hide();
			this.form.find('textarea').aloha();
		}
	},

	ajax_args: function (args) {
		var self = this, raw_data;

		args = self._super(args);

		if ( 'get' === args.callback )
			return args;

		if (self._rich) {
			GENTICS.Aloha.wpSaveCancel.saveButton.show();
			GENTICS.Aloha.wpSaveCancel.cancelButton.show();
			jQuery.each(GENTICS.Aloha.editables, function (i, editable) {
				self.form.find('textarea').val( editable.getContents() );
			});
		}

		raw_data = self.form.find(':input').serializeArray();

		jQuery.each(args, function (name, value) {
			raw_data.push({'name': name, 'value': value});
		});

		jQuery.each(args.data, function (name, value) {
			raw_data.push({'name': 'data[' + name + ']', 'value': value});
		});

		return raw_data;
	}
});
