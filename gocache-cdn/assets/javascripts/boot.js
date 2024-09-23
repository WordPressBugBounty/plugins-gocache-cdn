jQuery(function() {
	var context = jQuery( 'body' );

	MONKEY.vars = {
		body : context
	};

	//set route in application
	MONKEY.dispatcher( MONKEY.GoCache, window.pagenow, [context] );
});
