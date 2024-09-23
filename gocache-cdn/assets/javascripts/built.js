;(function(factory) {
    if ( typeof window.MONKEY != 'function' ) {
        factory( jQuery || Zepto );
    }
}(function(LibraryDOM) {
(function(context, $) {

    'use strict';

    // Build a new module with the correct attributes and methods.
    function build() {
        var Constructor, Instance;

        Constructor = function() {
            // Initialize a new instance, which won't do nothing but
            // inheriting the prototype.
            var instance = new Instance();

            // Apply the initializer on the given instance.
            instance.initialize.apply( instance, arguments );

            return instance;
        };

        // Define the function that will be used to
        // initialize the instance.
        Instance = function() {};
        Instance.prototype = Constructor.prototype;

        // Save some typing and make an alias to the prototype.
        Constructor.fn = Constructor.prototype;

        // Define a noop initializer.
        Constructor.fn.initialize = function() {};

        return Constructor;
    }

    var MONKEY = function(namespace, callback, object, isGlobalScope) {
        var components = namespace.split(/[.:]+/)
          , scope      = context
          , component
          , last
        ;

        if ( !isGlobalScope ) {
            scope = scope.MONKEY = scope.MONKEY || {};
        }

        if ( typeof callback !== 'function' ) {
            object   = callback;
            callback = null;
        }

        object = object || build();

        // Process all components but the last, which will store the
        // specified object attribute.
        for ( var i = 0, count = components.length; i < count; i++ ) {
            last = ( i == count - 1 );
            scope[components[i]] = ( last ? object : ( scope[components[i]] || {} ) );
            scope = scope[components[i]];
        }

        if ( callback ) {
            callback.call( scope, scope, MONKEY.utils, $ );
        }

        return scope;
    };

    MONKEY.Wrapper = function(namespace, initializer) {
        return MONKEY(namespace, function(definition) {
            definition.fn.initialize = function(namespace, callback) {
                initializer.apply( definition, arguments );
            };

            return definition;
        }, null, true );
    };

    context.MONKEY = MONKEY;

})( window, LibraryDOM );
;(function(context) {

    'use strict';

    MONKEY.Components = {};
    MONKEY.Ajax       = {};

})( window );
;(function(context, $) {

    'use strict';

    MONKEY.utils = {
        toTitleCase : function(text) {
            text = text.replace(/(?:^|-)\w/g, function(match) {
                return match.toUpperCase();
            });

            return text.replace(/-/g, '');
        },

        toCamelCase : function(text) {
            text = text.replace(/(?:^|-)\w/g, function(match, index) {
                return ( !index ) ? match : match.toUpperCase();
            });

            return text.replace(/-/g, '');
        }
    };

})( window, LibraryDOM );
;(function(context) {

    'use strict';

    function call(callback, args) {
        if ( typeof callback === 'function' ) {
            callback.apply( null, ( args || [] ) );
        }
    }

    MONKEY.dispatcher = function(application, route, args) {
        //execute all application
        call( application.init, args );
        call( application[route], args );
    };

})( window );
;(function(context, $) {

    'use strict';

    var components = MONKEY.Components || {};

    //define plugin js is exist
    $.fn.isExist = function(selector, callback) {
        var element = this.find( selector );

        if ( element.length && typeof callback == 'function' ) {
            callback.call( null, element, this );
        }

        return element.length;
    };

    $.fn.getComponent = function() {
        return this.data( '_component' );
    };

    MONKEY.factory = {
        create : function(container) {
            container.isExist( '[data-component]', this.constructor.bind( this ) );
        },

        constructor : function(elements) {
            elements.each( this.each.bind( this ) );
        },

        extend : function(name, reflection) {
            var mirror
              , method
            ;

            if ( typeof components[name] != 'function' ) {
                return;
            }

            mirror = components[name].fn;

            for ( method in mirror ) {
                if ( !~( reflection.overrides || [] ).indexOf( method ) ) {
                    reflection.fn[method] = mirror[method];
                }
            }
        },

        each : function(index, target) {
            var $el       = $( target )
              , extend    = $el.data( 'extend' )
              , name      = MONKEY.utils.toTitleCase( $el.data( 'component' ) )
              , instance  = null
            ;

            if ( typeof components[name] != 'function' ) {
                return;
            }

            if ( extend ) {
                this.extend( MONKEY.utils.toTitleCase( extend ), components[name] );
            }

            instance = components[name].call( null, $el );
            $el.data( '_component', instance );
        }
    };

})( window, LibraryDOM );
;MONKEY.Wrapper( 'MONKEY.ComponentWrapper', function(namespace, callback) {

    'use strict';

    MONKEY( ['Components', namespace].join( '.' ), function(Model, utils, $) {
        Model.fn.initialize = function(container) {
            this.$el      = container;
            this.elements = {};
            this.on       = null;
            this.fire     = null;

            //start component
            this.loadDefaultMethods();
            this.init();
        };

        Model.fn.loadDefaultMethods = function() {
            this.setAttrs();
            this.setElements();
            this.emitter();
        };

        Model.fn.setElements = function() {
            this.$el
                .find( '[data-element]' )
                    .each( this._assignEachElements.bind( this ) )
            ;
        };

        Model.fn._assignEachElements = function(index, element) {
            var target = $( element )
              , name   = target.data( 'element' )
            ;

            this._insertElement( target, name );
        };

        Model.fn._insertElement = function(target, name) {
            name = utils.toCamelCase( name );

            //ser flag for captured element
            target.attr( 'data-eobj', true );

            //case is exist element
            if ( this.elements[name] ) {
                this.elements[name] = this.elements[name].add( target );
                return;
            }

            //set attr in object elements
            this.elements[name] = target;
        };

        Model.fn.reloadElements = function() {
            this.$el
                .find( '[data-element]:not([data-eobj])' )
                    .each( this._assignEachElements.bind( this ) )
            ;
        };

        Model.fn.getElement = function(name) {
            var target = this.$el.find( '[data-element="' + name + '"]' );

            if ( !target.length ) {
                return false;
            }

            this._insertElement( target, name );
            return target;
        };

        Model.fn.setAttrs = function() {
            var attrs = this.$el.data();

            for ( var name in attrs ) {
                this[name] = attrs[name];
            }
        };

        Model.fn.emitter = function() {
            this.on   = $.proxy( this.$el, 'on' );
            this.fire = $.proxy( this.$el, 'trigger' );
        };

        Model.fn.addEvent = function(event, action) {
            var handle = utils.toCamelCase( [ '_on', event, action ].join( '-' ) );

            this.on(
                  event
                , '[data-action="' + action + '"]'
                , ( this[handle] || $.noop ).bind( this )
            );
        };

        Model.fn.init = function() {

        };

        callback( Model, utils, $ );
    });

});
}));;;(function($, window) {

	var HTML = '<div class="[type] render-message"><p><strong>[message]</strong></p></div>';

	function compile(type, message) {
		return HTML.replace( /\[type\]/, type ).replace( /\[message\]/, message );
	};

	$.fn.messageShowAfter = function(type, message, isRemove) {
		( isRemove && this.messageHideAfter() );
		this.after( compile( type, message ) );
	};

	$.fn.messageShowBefore = function(type, message, isRemove) {
		( isRemove && this.messageHideBefore() );
		this.before( compile( type, message ) );
	};

	$.fn.messageHideBefore = function() {
		this.prev( '.render-message' ).remove();
	};

	$.fn.messageHideAfter = function() {
		this.next( '.render-message' ).remove();
	};

})( jQuery, window );
;;(function($, window) {
	
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

})( jQuery, window );;;(function($) {

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
;MONKEY.Wrapper( 'MONKEY.AjaxWrapper', function(namespace, callback) {

	MONKEY( 'Ajax.' + namespace, function(Model) {

		Model.fn.initialize = function() {
			this.emitter();
		};

		Model.fn.emitter = function() {
			var emitter = jQuery({});
			this.on     = jQuery.proxy( emitter, 'on' );
			this.fire   = jQuery.proxy( emitter, 'trigger' );
		};

		Model.fn._done = function(identifier, response) {
			this.fire( 'ajax.done' + identifier, [ response ] );
		};

		Model.fn._fail = function(identifier, throwError, status) {
			this.fire( 'ajax.fail' + identifier, [ throwError.responseJSON, status ] );
		};

		Model.fn._request = function(identifier, params, options) {
			var defaults = {
				type     :'GET',
				dataType :'json',
				data     :( params || {} )
			};

			var ajax = jQuery.ajax( jQuery.extend( defaults, options ) );

			ajax.done( jQuery.proxy( this, '_done', identifier ) );
			ajax.fail( jQuery.proxy( this, '_fail', identifier ) );
		};

		callback( Model );

	});

});
;MONKEY( 'GoCache', function(GoCache) {

	GoCache.init = function(container) {
		MONKEY.factory.create( container );
	};

}, {} );
;MONKEY.ComponentWrapper( 'Clear', function(Clear) {

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
;MONKEY.ComponentWrapper( 'GocacheAjax', function(GocacheAjax) {

	GocacheAjax.fn.init = function() {
		this.addEventListener();
	};

	GocacheAjax.fn.addEventListener = function() {
		this.$el
			.on( 'submit', this._onSubmit.bind( this ) )
		;
	};

	GocacheAjax.fn._onSubmit = function(event) {
		event.preventDefault();
		this.fire( 'before-submit' );
		this.send();
	};

	GocacheAjax.fn.beforeSend = function() {
		this.elements.submit.attr( 'disabled', 'disabled' );
		this.elements.submit.spinnerShow( 'after' );
	};

	GocacheAjax.fn.send = function() {
		var url    = this.$el.attr( 'action' )
		  , params = this.$el.serialize()
		;

		var ajax = jQuery.ajax({
			url       : url,
			data      : params,
			dataType  : 'json',
			type      : 'POST'
		});

		this.beforeSend();
		ajax.done( this._done.bind( this ) );
		ajax.fail( this._fail.bind( this ) );
	};

	GocacheAjax.fn._done = function(response) {
		this.elements.submit.removeAttr( 'disabled' );
		this.elements.submit.spinnerHide();
		this.$el.messageShowBefore( 'updated', response.message, true );

		setTimeout(function(){
			location.href = location.href + '&v=' + Date.now();
		}, 1000 );
	};

	GocacheAjax.fn._fail = function(throwError, status) {
		var response = ( throwError.responseJSON || {} );

		this.elements.submit.removeAttr( 'disabled' );
		this.elements.submit.spinnerHide();
		this.$el.messageShowBefore( 'error', response.message, true );
	};

});;MONKEY.ComponentWrapper( 'GocacheUpdate', function(GocacheUpdate, utils, $) {

	GocacheUpdate.overrides = ['_done'];

	GocacheUpdate.fn._done = function(response) {
		this.elements.submit.removeAttr( 'disabled' );
		this.elements.submit.spinnerHide();
		this.$el.messageShowBefore( 'updated', response.message, true );
	};

});;MONKEY( 'Utils', function(Utils) {
	
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
;jQuery(function() {
	var context = jQuery( 'body' );

	MONKEY.vars = {
		body : context
	};

	//set route in application
	MONKEY.dispatcher( MONKEY.GoCache, window.pagenow, [context] );
});
