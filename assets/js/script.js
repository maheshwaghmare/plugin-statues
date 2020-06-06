(function($) {

	PluginStatus = {

		init: function()
		{
			this._addButtons();
		},

		_addButtons: function() {
			$button = $( '.page-title-action:first' );

			$button.after('<a href="'+pluginStatus.export_url+'" class="page-title-action">Export</a>' );
			$button.after('<a href="'+pluginStatus.import_url+'" class="page-title-action">Import</a>');
		}
	};

	/**
	 * Initialize PluginStatus
	 */
	$(function(){
		PluginStatus.init();
	});

})(jQuery);