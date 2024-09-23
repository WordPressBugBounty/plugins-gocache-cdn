<?php
namespace GoCache;

if ( ! function_exists( 'add_action' ) ) {
	exit(0);
}

App::uses( 'setting', 'Model' );
App::uses( 'settings', 'View' );

class Settings_Controller
{
	/**
	 * Instance of Setting class
	 * @var object
	 */
	public $model;

	public function __construct()
	{
		$this->model = new Setting();

		add_action( 'admin_menu', array( &$this, 'add_menus' ) );
		add_action( 'wp_ajax_settings_gocache_save', array( &$this, 'save' ) );
		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		add_filter( 'gocache_before_save_option', array( &$this, 'before_save_option' ), 10, 2 );
	}

	public function register_settings()
	{
		register_setting( 'gocache-manager-settings', $this->model->get_option_name( 'api_key' )  );
		register_setting( 'gocache-manager-settings', $this->model->get_option_name( 'domain' )  );
	}

	public function add_menus()
	{
		if ( ! defined( 'GOCACHE_HIDE_MENU' ) || constant( 'GOCACHE_HIDE_MENU') == false ) {
			add_menu_page(
				'GoCache',
				'GoCache',
				'manage_options',
				App::PLUGIN_SLUG . '-settings',
				array( 'GoCache\Settings_View', 'render_start_page' ),
				App::plugins_url( 'assets/images/icon-gocache.png' )
			);
		}

		add_submenu_page(
			App::PLUGIN_SLUG . '-settings',
			__( 'Start', App::TEXTDOMAIN ),
			__( 'Start', App::TEXTDOMAIN ),
			'manage_options',
			App::PLUGIN_SLUG . '-settings',
			array( 'GoCache\Settings_View', 'render_start_page' )
		);

		add_submenu_page(
			App::PLUGIN_SLUG . '-settings',
			__( 'Authentication', App::TEXTDOMAIN ),
			__( 'Authentication', App::TEXTDOMAIN ),
			'manage_options',
			App::PLUGIN_SLUG . 'authenticate-settings',
			array( 'GoCache\Settings_View', 'render_authenticate_page' )
		);

		$status = get_option( 'gocache_option-status' );
		if ( $status != 1  ) {
			return;
		}

		add_submenu_page(
			App::PLUGIN_SLUG . '-settings',
			__( 'General Settings', App::TEXTDOMAIN ),
			__( 'General', App::TEXTDOMAIN ),
			'manage_options',
			App::PLUGIN_SLUG . '-general-settings',
			array( 'GoCache\Settings_View', 'render_general_page' )
		);

		add_submenu_page(
			App::PLUGIN_SLUG . '-settings',
			__( 'Cache Settings', App::TEXTDOMAIN ),
			__( 'Cache', App::TEXTDOMAIN ),
			'manage_options',
			App::PLUGIN_SLUG . '-cache-settings',
			array( 'GoCache\Settings_View', 'render_cache_page' )
		);
	}

	public function save()
	{
		if ( ! $this->model->is_nonce() ) {
			Utils::error_server_json( 'not_permission_nonce' );
			http_response_code( 511 );
			exit(0);
		}

		$this->model->save_all_fields();

		Utils::success_server_json( 'config_save_success', __( 'Operation was successful.', App::TEXTDOMAIN ) );

		exit(1);
	}

	public function before_save_option( $name, $value )
	{
		if ( empty( $value ) ) {
			return $value;
		}

		if ( $name == $this->model->get_option_name( 'clear_cache_urls' ) ) {
			$list = preg_split( "/\\r\\n|\\r|\\n/", $value );
			
			$value = implode( PHP_EOL, $list );
		}

		if ( $name == $this->model->get_option_name( 'clear_cache_strings' ) ) {
			$list = preg_split( "/\\r\\n|\\r|\\n/", $value );
			$list = array_splice( $list, 0, 3 );
			$list = array_map(function($item){
				return strtolower( preg_replace( '/[^0-9a-z%_\-\/]+/i', '', $item ) );
			}, $list);
			$value = implode( PHP_EOL, $list );
		}

		if ( $name == $this->model->get_option_name( 'override_url_domain' ) ) {

			$list = preg_split( "/\\r\\n|\\r|\\n/", $value );
			$list = array_splice( $list, 0, 5 );
			$list = array_map(function($item){
				return strtolower( preg_replace( '/[^0-9a-z-_\.]+/i', '', $item ) );
			}, $list);
			$value = implode( PHP_EOL, $list );
		}

		return $value;
	}
}
