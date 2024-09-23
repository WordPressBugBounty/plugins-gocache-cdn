MONKEY.ComponentWrapper( 'GocacheUpdate', function(GocacheUpdate, utils, $) {

	GocacheUpdate.overrides = ['_done'];

	GocacheUpdate.fn._done = function(response) {
		this.elements.submit.removeAttr( 'disabled' );
		this.elements.submit.spinnerHide();
		this.$el.messageShowBefore( 'updated', response.message, true );
	};

});