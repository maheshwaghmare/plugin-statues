(function($) {

	PluginStatues = {

		init: function()
		{
			this._addButtons();
		},

		_addButtons: function() {
			$button = $( '.page-title-action:first' );

			$button.after('<a href="'+pluginStatues.export_url+'" class="page-title-action">Export</a>' );
			$button.after('<a href="'+pluginStatues.import_url+'" class="page-title-action">Import</a>');
		}
	};

	/**
	 * Initialize PluginStatues
	 */
	$(function(){
		PluginStatues.init();
	});

})(jQuery);