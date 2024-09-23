<?php
namespace GoCache;

if ( ! function_exists( 'add_action' ) ) {
	exit(0);
}

class Request
{
	public $header  = null;
	public $domain  = null;
	public $setting = null;

	public function __construct()
	{
		$this->setting = new Setting();

		if ( $this->setting->api_key == '' ) {
			return false;
		}

		$this->header = [
			'GoCache-Token' => $this->setting->api_key
		];

		$this->domain = $this->setting->domain;
	}

	public function verify_connection()
	{
		if ( ! $this->header ) {
			return;
		}

		$url = 'https://api.gocache.com.br/v1/domain/' . $this->domain;
		$args = [
			'headers' => $this->header
		];

		$result = wp_remote_get( $url, $args );

		if ( ! is_wp_error( $result ) ) {
			return $result;
		}

		return false;
	}

	public function update_remote_configs( $configs )
	{
		if ( ! $this->header ) {
			return;
		}

		$url = 'https://api.gocache.com.br/v1/domain/' . $this->domain;
		$args = [
			'headers' => $this->header,
			'method'  => 'PUT',
			'body'    => $configs
		];

		$result = wp_remote_request( $url, $args );

		if ( ! is_wp_error( $result ) ) {
			return json_decode( $result['body'] );

		} else {
			return false;
		}

	}

	public function delete_all_cache()
	{
		$domain = $this->domain;

		$url = "https://api.gocache.com.br/v1/cache/{$domain}/all";
		$args = [
			'headers' => $this->header,
			'method'  => 'DELETE'
		];

		$result = wp_remote_request( $url, $args );

		if ( ! is_wp_error( $result ) ) {
			return json_decode( $result['body'] );

		} else {
			return false;
		}

	}

	public function delete_cache( $urls, $append_custom_strings = false )
	{
		if ( empty( $urls ) || ! $this->header ) {
			return;
		}

		$data = [];

		$urls = $this->override_urls_domain($urls);

		foreach ( $urls as $key => $url ) {
			$data['urls'][] = trim( $url );
		}

		$data['urls'] = $this->append_custom_urls( $data['urls'] );

		if ( $append_custom_strings ) {
			$data['urls'] = $this->append_custom_strings( $data['urls'] );
		}

		$domain = $this->domain;
		
		$url = "https://api.gocache.com.br/v1/cache/{$domain}";
		$args = [
			'headers' => $this->header,
			'body'    => http_build_query( $data ),
			'method'  => 'DELETE'
		];

		$result = wp_remote_request( $url, $args );

		if ( ! is_wp_error( $result ) ) {
			return json_decode( $result['body'] );

		} else {
			return false;
		}
	}

	public function append_custom_strings( $urls )
	{
		$strings       = $this->setting->clear_cache_strings;
		$modified_urls = array();

		if ( empty( $strings ) ) {
			return $urls;
		}

		$list = preg_split( "/\\r\\n|\\r|\\n/", $strings );

		if ( ! is_array( $list ) ) {
			return $urls;
		}
		
		foreach( $list as $string ) {
			foreach ( $urls as $url ) {
				array_push( $modified_urls, $url . substr( $string, 0, 20 ) );
			}
		}

		return array_merge( $urls, $modified_urls );
	}

	public function append_custom_urls( $urls ) 
	{
		$data = $this->setting->clear_cache_urls;
		$list = preg_split( "/\\r\\n|\\r|\\n/", $data );

		if ( ! empty( $list ) ) {
			$urls = array_merge( $urls, $list );
		}

		return $urls;
	}

	public function override_urls_domain( $urls )
	{
		$overud = $this->setting->override_url_domain;
		$new_urls = array();

		if ( ! is_array( $urls ) ) {
			$urls = [$urls];
		}

		if ( empty( $overud ) ) {
			return $urls;
		}

		$list = preg_split( "/\\r\\n|\\r|\\n/", $overud );

		if ( ! is_array( $list ) ) {
			return $urls;
		}

		foreach ( $urls as $url ) {
			foreach( $list as $domain ) {

				if(strpos($domain, "/") !== false || strpos($domain, ".") === false ) {
					continue;
				}

				array_push( $new_urls, preg_replace('/:\/\/[^\/]+/', "://".$domain, $url, 1) );
			}
		}

		if( count($new_urls) == 0 ) {
			return $urls;
		}

		return $new_urls;
	}
}
