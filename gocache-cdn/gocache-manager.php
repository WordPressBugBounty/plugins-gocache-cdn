<?php
/*
	Plugin Name: GoCache
	Plugin URI:
	Version: 1.3.5
	Author: GoCache
	Author URI: http://www.gocache.com.br/
	Text Domain: gocache
	Domain Path: /languages
	License: GPL2
	Description: Conecta seu Wordpress com a GoCache, que acelera de forma inteligente as páginas e arquivos estáticos do site, reduzindo o consumo de recursos no servidor web e banco de dados.
*/

if ( version_compare( phpversion(), '5.6' ) < 0  ) {
	wp_die( 'O plugin <strong>GoCache</strong> é incompatível com sua versão do PHP. <p>A versão do PHP deve ser no mínimo <strong>5.6</strong></p>',
		'GoCache - Error',
		array( 'back_link' => true )
	);
}

include( 'app.php' );
