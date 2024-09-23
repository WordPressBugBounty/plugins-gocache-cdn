;(function($) {

	$.fn.byElement = function(name) {
		return this.find( '[data-element="' + name + '"]' );
	};	

	$.fn.compileHandlebars = function() {
		return Handlebars.compile( this.html() );
	};

	$.fn.fadeOutRemove = function(time) {
		var _self = this;

		_self.fadeOut(time , function() {
			_self.remove();
		});
	};

	$.fn.isEmptyValue = function() {
		return !( $.trim( this.val() ) );
	};

	$.fn.valInt = function() {
		return parseInt( this.val(), 10 );
	};

	$.fn.addClassReFlow = function(name) {
		this.css( 'display', 'block' );
		//force reflow
		this[0].offsetWidth;
		this.addClass( name );
	};

})( jQuery );
