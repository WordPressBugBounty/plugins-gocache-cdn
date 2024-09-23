<?php

namespace GoCache;

// Avoid that files are directly loaded
if ( ! function_exists( 'add_action' ) ) {
	exit(0);
}

use Exception;

class Setting
{
	/**
	 * Options
	 *
	 * @since 1.0
	 * @var array
	 */
	public $options = array(
		'api_key' => [
			'default'  => '',
			'sanitize' => 'esc_html',
		],

		'domain' => [
			'sanitize' => 'strip_tags'
		],

		'external_configs' => [
			'sanitize' => 'esc_html'
		],

		'status' => [
			'sanitize' => 'esc_html'
		],

		'auto_clear_cache' => [
			'sanitize' => 'esc_html',
			'default'  => 'yes',
		],

		'clear_cache_strings' => [
			'sanitize' => ''
		],

		'clear_cache_urls' => [
			'sanitize' => ''
		],

		'override_url_domain' => [
			'sanitize' => ''
		]
	);

	/**
	 * Nonce
	 *
	 * @since 1.0
	 * @var string
	 */
	public $nonce = 'gocache_option';
	public $api_key;
	public $setting;
	public $auto_clear_cache;
	public $override_url_domain;
	public $clear_cache_urls;
	public $clear_cache_strings;
	public $domain;

    /**
     * Magic function to setvalue of the attribute more easily.
     *
     * @since 1.0
     * @return void The attribute value
     */
    public function __construct()
    {
        foreach ($this as $key => $value) {
            if (empty($this->$key) && isset($this->options[$key])) {
                $args  = wp_parse_args( $this->options[$key], array( 'default' => '' ) );
                $name  = $this->nonce . '-' . $key;
                $this->$key = get_option( $name, $args['default'] );
            }
        }
    }

    /**
	 * Magic function to retrieve the value of the attribute more easily.
	 *
	 * @since 1.0
	 * @param string $prop_name The attribute name
	 * @return mixed The attribute value
	 */
	public function __get( $prop_name )
	{
		if ( isset( $this->$prop_name ) ) {
			return $this->$prop_name;
		}

		if ( array_key_exists( $prop_name, $this->options ) ) {
			$this->$prop_name = $this->get_option_value( $prop_name );
			return $this->$prop_name;
		}

		return $this->get_property( $prop_name );
	}

	public function get_option_value( $name )
	{
		$args  = wp_parse_args( $this->options[$name], array( 'default' => '' ) );
		$name  = $this->nonce . '-' . $name;
		$value = get_option( $name, $args['default'] );

		if ( isset( $args['sanitize'] ) && is_callable( $args['sanitize'] ) ) {
			return call_user_func( $args['sanitize'], $value );
		}

		return $value;
	}

	public function get_option_name( $name )
	{
		if ( ! array_key_exists( $name, $this->options ) ) {
			throw new Exception( 'the name option passed is not defined', 100 );
		}

		return $this->nonce . '-' . $name;
	}

	public function get_nonce_action()
	{
		return $this->nonce . '_action';
	}

	public function get_nonce_name()
	{
		return $this->nonce . '_name';
	}

	public function is_nonce()
	{
		return Utils::verify_nonce_post( $this->get_nonce_name(), $this->get_nonce_action() );
	}

	public function the_nonce_field()
	{
		wp_nonce_field( $this->get_nonce_action(), $this->get_nonce_name() );
	}

	public function save_all_fields()
	{
		foreach ( $this->options as $name => $option ) {
			$name  = $this->nonce . '-' . $name;

			if ( ! isset( $_POST[ $name ] ) ) {
				continue;
			}

			$value = Utils::post( $name, false );
			$value = apply_filters( 'gocache_before_save_option', $name, $value );

			update_option( $name, $value );
		}
	}

	public function save( $name, $value )
	{
		$name = $this->get_option_name( $name );
		update_option( $name, $value );
	}

	/**
	 * Get Property per name
	 *
	 * @since 1.0
	 * @return void
	*/
	protected function get_property( $prop_name )
	{
		return $this->$prop_name;
	}
}
