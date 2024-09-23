<?php
/**
 * Helper Utils
 *
 * @package GoCache
 * @subpackage Utils
 */
namespace GoCache;

// Avoid that files are directly loaded
if ( ! function_exists( 'add_action' ) ) {
	exit(0);
}

class Utils
{
   /**
	* Verify nonce by $_POST param
	*
	* @return bool
	*/
	public static function verify_nonce_post( $name, $action )
	{
		return wp_verify_nonce( self::post( $name, false ), $action );
	}

	public static function verify_nonce_get( $name, $action )
	{
	    return wp_verify_nonce( self::get( $name, false ), $action );
	}

   /**
	* Print html dencode
	*
	* @return bool
	*/
	public static function html( $text )
	{
		$text = htmlspecialchars_decode( $text );
		$text = str_replace( '\\', '', $text );

		return strip_tags( $text, '<p><strong><span><br><a>' );
	}

	/**
	 * Gets the post ID
	 *
	 * Gets the post ID when the page screen is loaded
	 * and when the post is saved.
	 *
	 * @return int returns the post ID
	 */
	public static function get_post_id()
	{
		$post_id = null;

		if ( isset( $_GET['post'] ) ) :
			$post_id = intval( sanitize_text_field( $_GET['post'] ) );
		endif;

		if ( isset( $_POST['post_ID'] ) ) :
			$post_id = intval( sanitize_text_field( $_POST['post_ID'] ) );
		endif;

		return $post_id;
	}

	/**
	 * Get Ip Host Machine Acess
	 *
	 * Use this function for get ip
	 *
	 * @return string
	 */
	public static function get_ipaddress()
	{
		$ip_address = false;

		if ( isset( $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] ) )
			$ip_address = $_SERVER[ 'HTTP_X_FORWARDED_FOR' ];

		if ( empty( $ip_address ) )
			$ip_address = $_SERVER[ 'REMOTE_ADDR' ];

		if ( strpos( $ip_address, ',' ) !== false ) :
			$ip_address = explode( ',', $ip_address );
			$ip_address = $ip_address[0];
		endif;

		return esc_attr( $ip_address );
	}

	public static function is_request_ajax()
	{
		if ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) )
			$request_ajax = $_SERVER['HTTP_X_REQUESTED_WITH'];

		return ( ! empty( $request_ajax ) && strtolower( $request_ajax ) == 'xmlhttprequest' );
	}

	public static function convert_date_for_sql( $date, $format = 'Y-m-d H:i' )
	{
		return ( ! empty( $date ) ) ? self::convert_date( $date, $format, '/', '-' ) : false;
	}

	public static function convert_date_human( $date, $format = 'd/m/Y' )
	{
		return ( ! empty( $date ) ) ? self::convert_date( $date, $format, false ) : false;
	}

	public static function convert_date( $date, $format = 'Y-m-d H:i', $search = '/', $replace = '-' )
	{
		if ( $search && $replace )
			$date = str_replace( $search, $replace, $date );

		return date_i18n( $format, strtotime( $date ) );
	}

	public static function convert_float_for_sql( $value )
	{
		$value = str_replace( '.', '', $value );
		$value = str_replace( ',', '.', $value );

		return $value;
	}

	public static function get_user_agent()
	{
		if ( ! isset( $_SERVER ) )
			return 'none';

		return $_SERVER[ 'HTTP_USER_AGENT' ];
	}

	public static function get( $key, $default = '', $sanitize = 'esc_html' )
	{
		if ( ! isset( $_GET[ $key ] ) OR empty( $_GET[ $key ] ) )
			return $default;

		if ( is_array( $_GET[ $key ] ) )
			return sanitize_text_field( $_GET[ $key ] );

		return self::sanitize_type( sanitize_text_field( $_GET[ $key ] ), $sanitize );
	}

	public static function request( $key, $default = '', $sanitize = 'esc_html' )
	{
		if ( ! isset( $_REQUEST[ $key ] ) OR empty( $_REQUEST[ $key ] ) )
			return $default;

		return self::sanitize_type( $_REQUEST[ $key ], $sanitize );
	}

	public static function post( $key, $default = '', $sanitize = 'esc_html' )
	{
		if ( ! isset( $_POST[ $key ] ) OR empty( $_POST[ $key ] ) )
			return $default;

		if ( is_array( $_POST[ $key ] ) ) 
			return  $_POST[ $key ];

		return self::sanitize_type( $_POST[ $key ] , $sanitize );
	}

	public static function sanitize_type( $value, $name_function )
	{
		if ( ! $name_function )
			return $value;

		if ( ! is_callable( $name_function ) )
			return sanitize_text_field( $value );

		return call_user_func( $name_function, $value );
	}

	public static function error_server_json( $code, $message = 'Generic Message Error', $echo = true )
	{
		$response = json_encode(
			[
				'status' 	=> 'error',
				'code'   	=> $code,
				'message'	=> $message,
			]
		);

		if ( ! $echo )
			return _e($response, App::TEXTDOMAIN);;

		echo _e($response, App::TEXTDOMAIN);
	}

	public static function success_server_json( $code, $message = 'Generic Message Success', $text = '', $echo = true )
	{
		$response = json_encode(
			[
				'status' 	=> 'success',
				'code'   	=> $code,
				'message'	=> $message,
				'text'      => $text
			]
		);

		if ( ! $echo )
			return _e($response, App::TEXTDOMAIN);;

		echo _e($response, App::TEXTDOMAIN);
	}

	public static function format_array_on_string( $elements = array() )
	{
		$result = '';

		if ( empty( $elements ) ) {
			return;
		}
		foreach ( $elements as $element ){
			$result = $result . $element . "\n"; 
		}
		
		return $result;
	}

	public static function object_server_json( $args = array(), $echo = true )
	{

		$response = json_encode( $args );

		if ( ! $echo )
			return _e($response, App::TEXTDOMAIN);;

		echo _e($response, App::TEXTDOMAIN);
	}

	public static function limit_text( $text, $limit, $more = '...' )
	{
		if ( strlen( $text ) > $limit )
			$text = mb_substr( $text, 0, $limit ) . $more;

		return $text;
	}

	public static function json_decode_quoted( $data, $is_assoc = true )
	{
		return json_decode( str_replace( '&quot;', '"', $data ), $is_assoc );
	}

	public static function add_custom_capabilities( $roles, array $caps )
	{
		foreach ( (array)$roles as $role ) :
			$current_role = get_role( $role );

			array_map( array( &$current_role, 'add_cap' ), $caps );
		endforeach;
	}

	/**
	 * Escape html entities
	 * @param  string    $text The text to escape html entities
	 * @return string          The text escaped
	 */
	public static function esc_html( $text )
	{
		$safe_text = htmlentities( $text );
		return apply_filters( 'esc_html', $safe_text, $text );
	}

	public static function has_key( $list, $key )
	{
		return isset( $list[$key] ) && (bool)$list[$key];
	}

	public static function is_localhost()
	{
		return ( isset( $_SERVER['SERVER_NAME'] ) && $_SERVER['SERVER_NAME'] == 'localhost' );
	}

	public static function get_domain()
	{
		$site_url = get_bloginfo( 'url' );
		return parse_url( $site_url, PHP_URL_HOST );
	}

	public static function get_protocol( $slash = true )
	{

		$site_protocol =  isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
		if ( $slash ){
			return $site_protocol . '://';
		}
		
		return $site_protocol;
	}
}
