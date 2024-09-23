<?php
namespace GoCache;

// Avoid that files are directly loaded
if ( ! function_exists( 'add_action' ) ) {
	exit(0);
}

class App
{
	const PLUGIN_SLUG = 'gocache-manager';
	const TEXTDOMAIN  = 'gocache';

	public static function uses( $class_name, $location )
	{
		include "{$location}/{$class_name}.php";
	}

	public static function plugins_url( $path )
	{
		return plugins_url( $path, __FILE__ );
	}

	public static function plugin_dir_path( $path )
	{
		return plugin_dir_path( __FILE__ ) . $path;
	}

	public static function filemtime( $path )
	{
		return filemtime( self::plugin_dir_path( $path ) );
	}

	public static function load_textdomain()
	{
		load_plugin_textdomain( self::TEXTDOMAIN, false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}
}

App::uses( 'core', 'Config' );

$core = new Core();

register_activation_hook( __DIR__ . '/gocache-manager.php', array( $core, 'activate' ) );