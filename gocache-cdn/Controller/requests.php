<?php
namespace GoCache;

if ( ! function_exists( 'add_action' ) ) {
	exit(0);
}

App::uses( 'request', 'Model' );

class Requests_Controller
{
	public function __construct()
	{
		add_action( 'wp_ajax_CBS93bVqAwWnVFHw', array( &$this, 'update_configs' ) );
	}

	public function update_configs()
	{
		if ( ! Utils::is_request_ajax() ) {
			exit(0);
		}

		$request = new Request();

		if ( ! $request->verify_connection() ) {
			Utils::error_server_json( 'not_connected', __( 'Connection failed.', App::TEXTDOMAIN ) );
			http_response_code( 511 );
			exit(0);
		}

		$items = array(
			'smart_status' => Utils::post( 'smart_status', 'false' ),
			'smart_ttl'    => Utils::post( 'smart_ttl', -1 ),
			'gzip_status'  => Utils::post( 'gzip_status', 'false' ),
			'expires_ttl'  => Utils::post( 'expires_ttl', -1 ),
			'deploy_mode'  => Utils::post( 'deploy_mode', 'false' )
		);

		$response = $request->update_remote_configs( $items );

		if ( ! $response || $response->status_code != 1 ) {
			Utils::error_server_json( 'not_connected', __( 'Remote settings were not saved.', App::TEXTDOMAIN ) );
			http_response_code( 500 );
			exit(0);
		}

		Utils::success_server_json( 'config_save_success', __( 'Remote settings were saved.', App::TEXTDOMAIN ) );

		exit(1);
	}
}