;(function($, window) {
	
	var HTML = '<span class="spinner is-active" style="display: block;"></span>';

	$.fn.spinnerShow = function(insertion) {
		this[insertion].call( this, HTML );
	};

	$.fn.spinnerHide = function() {
		this.siblings( '.spinner' )
		    	.remove()
		    .end()	
		    .find( '.spinner' )
		    	.remove()
		;
	};

})( jQuery, window );