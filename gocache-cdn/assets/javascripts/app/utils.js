MONKEY( 'Utils', function(Utils) {
	
	Utils.addQueryVars = function(params, url) {
		var listParams = [];

		for ( var item in params ) {
			listParams.push( item + '=' + params[ item ] );
		}

		return url + ( url.match(/\/\?/) ? '&' : '?' ) + listParams.join( '&' );
	};

	Utils.getUrlAjax = function() {
		return ( window.AdminGlobalVars || {} ).urlAjax;
	};

}, MONKEY.utils );
