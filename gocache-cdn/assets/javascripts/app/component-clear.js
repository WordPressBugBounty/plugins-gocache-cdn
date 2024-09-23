MONKEY.ComponentWrapper( 'Clear', function(Clear) {

	Clear.fn.init = function() {
		this.addEventListener();

		var noAutoClear = document.querySelector('#gocache_clear_cache_no');

		if ( noAutoClear.checked ){
			var optionsSection = document.querySelectorAll('.optionsSection');
			optionsSection.forEach(element => {
				element.style = 'display:none;'
				
			});
		}
	};

	Clear.fn.addEventListener = function() {
		this.addEvent( 'click', 'all' );
		this.addEvent( 'click', 'by-url' );
		this.addEvent( 'click', 'sitemap-auto-clear' );
		this.addEvent( 'click', 'amp-auto-clear' );
		this.addEvent( 'click', 'auto-clear-yes' );
		this.addEvent( 'click', 'auto-clear-no' );

	};

	Clear.fn._onClickAutoClearYes = function(event) {
		var optionsSection = document.querySelectorAll('.optionsSection');
		optionsSection.forEach(element => {
			element.style = 'display:table-row;'
		});
	};

	Clear.fn._onClickAutoClearNo = function(event) {
		var optionsSection = document.querySelectorAll('.optionsSection');
		optionsSection.forEach(element => {
			element.style = 'display:none;'
			
		});
	};

	Clear.fn._onClickAll = function(event) {
		this.send( 'mbuceP3nRNUqXzR5' );
	};

	Clear.fn._onClickByUrl = function(event) {
		this.send( 'VwtDUTW92c2B8Yjf' );
	};

	Clear.fn._onClickSitemapAutoClear = function(event) {
		this.send( 'jt3WHdVr42nM9HfT' );
	};

	Clear.fn._onClickAmpAutoClear = function(event) {
		this.send( 'Tk5FhDBt68mW8GlP' );
	};

	Clear.fn.beforeSend = function() {
		//this.elements.submit.attr( 'disabled', 'disabled' );
		//this.elements.submit.spinnerShow( 'after' );
	};

	Clear.fn.send = function(action) {
		var params = { 'action' : action };

		if ( action == 'VwtDUTW92c2B8Yjf' ) {
			params.urls = this.elements.textarea.val();
		}

		if ( action == 'jt3WHdVr42nM9HfT' ) {
			let sitemap = document.getElementById( 'gocache_sitemap_checkbox' );
			if ( sitemap.checked ) {
				params.option =	{
					'option'   : 'auto_clear_sitemap_url', 
					'response' : 'yes'
				}
			} else {
				params.option =	{
					'option'   : 'auto_clear_sitemap_url', 
					'response' : 'no'
				}
			}
		}

		if ( action == 'Tk5FhDBt68mW8GlP' ) {
			let amp = document.getElementById( 'gocache_amp_checkbox' );
			if ( amp.checked ) {
				params.option =	{
					'option'   : 'auto_clear_amp_url', 
					'response' : 'yes'
				}
			} else {
				params.option =	{
					'option'   : 'auto_clear_amp_url', 
					'response' : 'no'
				}
			}
		}

		var ajax = jQuery.ajax({
			url       : MONKEY.Utils.getUrlAjax(),
			data      : params,
			dataType  : 'json',
			type      : 'POST'
		});

		this.beforeSend();
		ajax.done( this._done.bind( this ) );
		ajax.fail( this._fail.bind( this ) );
	};

	Clear.fn._done = function(response) {
		//this.elements.submit.removeAttr( 'disabled' );
		//this.elements.submit.spinnerHide();
		if ( response.message ) {
			this.$el.messageShowBefore( 'updated', response.message, true );
		}
	};

	Clear.fn._fail = function(throwError, status) {
		var response = ( throwError.responseJSON || {} );

		//this.elements.submit.removeAttr( 'disabled' );
		//this.elements.submit.spinnerHide();
		this.$el.messageShowBefore( 'error', response.message, true );
	};

});
