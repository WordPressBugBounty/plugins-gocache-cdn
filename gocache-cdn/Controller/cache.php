<?php
namespace GoCache;


if ( ! function_exists( 'add_action' ) ) {
	exit(0);
}

class Cache_Controller
{
    public $setting;

	public function __construct()
	{
		$this->setting = new Setting();

		if ( $this->setting->auto_clear_cache == 'yes' ) {
			add_action( 'init', [ $this, 'handle_actions' ] );
		}

		add_action( 'wp_ajax_jt3WHdVr42nM9HfT', [ $this, 'ajax_settings' ] );
		add_action( 'wp_ajax_Tk5FhDBt68mW8GlP', [ $this, 'ajax_settings' ] );
		add_action( 'wp_ajax_VwtDUTW92c2B8Yjf', [ $this, 'delete_by_url' ] );
		add_action( 'wp_ajax_mbuceP3nRNUqXzR5', [ $this, 'delete_all' ] );
	}

	public function delete_all()
	{
		if ( ! Utils::is_request_ajax() ) {
			exit(0);
		}

		$request  = new Request();
		$response = $request->delete_all_cache();

		if ( isset( $response->msg ) && $response->msg == 'Success' ) {
			Utils::success_server_json( 'config_save_success', __( 'All cache was deleted.', App::TEXTDOMAIN ) );
			exit(1);
		}

		Utils::error_server_json( 'not_connected', __( 'Could not delete cache.', App::TEXTDOMAIN ) );
		http_response_code( 500 );

		exit(0);
	}

	public function delete_by_url()
	{
		if ( ! Utils::is_request_ajax() ) {
			exit(0);
		}

		$urls = Utils::post( 'urls', false, 'wp_strip_all_tags' );

		if ( ! $urls ) {
			Utils::error_server_json( 'empty_field', __( 'Empty field.', App::TEXTDOMAIN ) );
			http_response_code( 411 );
			exit(0);
		}

		$list    = preg_split( "/\\r\\n|\\r|\\n/", $urls );
		$list    = $this->prepare_urls($list);
		$request = new Request();
		$result  = $request->delete_cache( $list, true );

		if ( ! $result ) {
			Utils::error_server_json( 'invalid_urls', __( 'Não foi possível fazer a limpeza, verifique sua conexão!', App::TEXTDOMAIN ) );
			http_response_code( 411 );
			exit(0);
		}

		if ( ! isset( $result->response->urls_accepted ) ) {
			Utils::error_server_json( 'invalid_urls', __( 'Invalid URL.', App::TEXTDOMAIN ) );
			http_response_code( 411 );
			exit(0);
		}

		$accepted = implode( ', ', $result->response->urls_accepted );

		Utils::success_server_json( 'success', __( 'Clear URL: ', App::TEXTDOMAIN ) . $accepted );

		exit(1);
	}


	public function handle_actions()
	{
		$actions    = [];
		$post_types = get_post_types( [ 'public' => true ] );

		unset( $post_types['attachment'] );

		if ( ! empty( $post_types ) ) {
			foreach ( $post_types as $post_type_name ) {
				$actions[] = "save_post_{$post_type_name}";
			}
		}

		foreach ( $actions as $action ) {
			add_action( $action, [ $this, 'purge_post' ], 5, 1 );
		}

		//Comments
		add_action( 'wp_insert_comment', [ $this, 'purge_on_insert_comment' ], 99, 2 );
		add_action( 'transition_comment_status', [ $this, 'purge_on_change_comment_status' ], 99, 3 );

		//after delete post or attachment
		add_action( 'after_delete_post', [ $this, 'purge_post' ], 5, 1 );
		add_action( 'delete_attachment', [ $this, 'purge_attachment_after_delete' ], 5, 1 );
	}

	public function purge_post( $post )
	{
		$post_id           = ( is_object( $post ) ) ? $post->ID : intval( $post );
		$valid_post_status = [ 'publish', 'trash', 'future' ];
		$post_status       = get_post_status( $post_id );
		$post_type         = get_post_type( $post_id );

		if ( ! in_array( $post_status, $valid_post_status ) ) {
			return;
		}

		$urls_list = [];

		$this->get_main_domain_url( $urls_list );
		$this->get_post_link( $urls_list, $post_id );
		$this->get_term_urls( $urls_list, $post_type, $post_id );
		$this->get_author_urls( $urls_list, $post_id );
		$this->get_feeds_urls( $urls_list, $post_id );
		$this->get_thumbnail_link( $urls_list, $post_id );
		$this->get_attachment_links( $urls_list, $post_id );
		$this->get_rest_api_urls( $urls_list );
		$this->get_sitemap_url( $urls_list );
		$this->get_amp_url( $urls_list, $post_id );
		$this->get_archive_urls( $urls_list, $post_id, $post_type );

		if ( get_option( 'show_on_front' ) == 'page' ) {
			$page = get_option( 'page_for_posts' );
			if ( ! empty( $page ) ) {
				array_push( $urls_list, get_permalink( $page ) );
			}
		}


		$request = new Request();

		$urls    = $this->prepare_urls( $urls_list );
		$urls    = $request->append_custom_strings( $urls );
		$list    = array_chunk( $urls, 50, true );
		foreach( $list as $items ) {
			$request->delete_cache( $items );
		}
	}

	public function purge_on_insert_comment( $comment_id, $comment )
	{
		if ( $comment->comment_approved != 1 ) {
			return;
		}

		$this->purge_post( $comment->comment_post_ID );
	}

	public function purge_on_change_comment_status( $new_status, $old_status, $comment )
	{
		if ( $new_status == $old_status ) {
			return;
		}

		if ( $new_status == 'approved' ) {
			$this->purge_post( $comment->comment_post_ID );
		}
	}

	public function purge_attachment_after_delete( $post )
	{
		$post_id = ( is_object( $post ) ) ? $post->ID : intval( $post );
		$attachment_info = get_post($post_id);

		$attachment_guid = $attachment_info->guid;

		$request = new Request();
		$request->delete_cache( $attachment_guid );

	}

	private function prepare_urls( $urls )
	{
		if ( ! $urls ) {
			return [];
		}

		$with_slashes  = $this->get_with_slashes_items( $urls );
		$without_slashes = $this->get_without_slashes_items( $urls );

		$data = array_merge($with_slashes, $without_slashes);
		$data = $this->force_site_protocol( $data );
		$data = array_unique( $data );

		return $data;
	}

	private function get_with_slashes_items( $urls )
	{
		foreach($urls as $key => $url) {
			if (substr($url, -1) !== '/') {
                $urls[$key] = "$url/";
			}
		}

		return $urls;
	}

	private function get_without_slashes_items( $urls )
	{
		$without_slash = [];

		foreach ( $urls as $url ) {
			array_push( $without_slash, rtrim( $url, '/' ) );
		}

		return $without_slash;
	}

	private function force_site_protocol( $url_list )
	{
		$urls = [];
		$protocol  = Utils::get_protocol();

		foreach ( $url_list as $url ) {
			array_push( $urls, str_replace( $protocol, "http*://", $url) );
		}

		return $urls;
	}

	private function get_term_urls( &$urls_list, $post_type, $post_id )
	{
		$objects = get_object_taxonomies( $post_type, 'objects' );

		if ( empty( $objects ) ) {
			return;
		}

		$taxonomies = array_filter( $objects, function( $taxonomy ) {
			return intval( $taxonomy->public ) === 1;
		});

		foreach ( $taxonomies as $taxonomy ) {
			$terms = get_the_terms( $post_id, $taxonomy->name );

			if ( empty( $terms ) || is_wp_error( $terms ) ) {
				continue;
			}

			foreach ( $terms as $term ) {
				$taxonomy_url = get_term_link( $term, $taxonomy->name );

				array_push( $urls_list, $taxonomy_url );
				//Replace "//", except for "://"
				if ( get_option('permalink_structure') ) {
					array_push( $urls_list, preg_replace('/(?:([^:])\/\/+)/', '\\1/', $taxonomy_url . "/page/*") );

				} else {
					array_push( $urls_list, preg_replace('/(?:([^:])\/\/+)/', '\\1/', $taxonomy_url . "&page=*") );

				}
			}
		}
	}

	private function get_archive_urls( &$urls_list, $post_id, $post_type )
	{

		$domain    = Utils::get_domain();
		$protocol  = Utils::get_protocol();

		$post_url      = get_permalink($post_id);
		$site_url      = $protocol . $domain . '/';

		$post_date = get_the_date( 'Y/m/d', $post_id );

		$archive_dates = $this->get_archive_dates( $site_url, $post_date );
		if ( $archive_dates ) {
			foreach ( $archive_dates as $date ) {
				array_push( $urls_list, $date );
			}
		}


		$post_type_archive = $this->get_post_type_achives( $post_type );
		$post_archives     = $this->get_archives_post( $post_url );

		if ( $post_type_archive ) array_push( $urls_list, $post_type_archive );

		if ( $post_archives ) {
			foreach ( $post_archives as $post ) {
				array_push( $urls_list, $post );
			}
		}

		$archive_pages = $this->get_archive_pages( $archive_dates );

		foreach ( $archive_pages as $page ) {
			array_push( $urls_list, $page );
		}

		$post_archive_date = $this->get_post_archive_date( $site_url, $post_id, $post_date  );
		array_push( $urls_list, $post_archive_date);

		array_merge ( $urls_list, $archive_pages );

	}

	private function get_post_archive_date( $url, $post_id, $post_date ) {

		$post = get_post( $post_id );
		$post_name = $post->post_name;

		$split_date = explode( '/', $post_date );

		$year  = $split_date[0];
		$month = $split_date[1];
		$day   = $split_date[2];

		if ( get_option('permalink_structure') ) {

			$params  = $year . '/' . $month . '/' . $day . '/' .$post_name;
			$post_archive =  $url . $params;

		} else {

			$params  = '?year=' . $year . '&month=' . $month .'&day=' . $day . '&p=' .  $post_name;
			$post_archive =  $url . $params;

		}

		return $post_archive;

	}

	private function get_post_type_achives( $post_type ) {

		$archive_link = get_post_type_archive_link( $post_type );

		if ( ! empty( $archive_link ) ) {
			return get_post_type_archive_feed_link( $post_type );
		}
	}

	private function get_archives_post( $post_url ) {

		if ( get_option('permalink_structure') ) {

			$post_arr = str_split( $post_url );
			if ( $post_arr[ count( $post_arr ) -1 ] !== '/' ) {
				$post_url = $post_url . '/';
			}

			$feed = $post_url . 'feed/';
			$rdf  = $post_url . 'feed/rdf/';
			$atom = $post_url . 'feed/atom/';

			return [ $feed, $rdf, $atom ];

		}else {
			$rdf  = $post_url . '&feed=rdf';
			$atom = $post_url . '&feed=atom';

			return [ $rdf, $atom ];
		}

	}

	private function get_archive_pages( $urls ) {

		$archive_pages = [];

		foreach ( $urls as $url ) {
			if ( get_option('permalink_structure') ) {
				$feed = $url . 'feed/';
				$rdf  = $url . 'feed/rdf/';
				$atom = $url . 'feed/atom/';

				$pages = [ $feed, $rdf, $atom ];

				$archive_pages	= array_merge( $archive_pages, $pages );
			} else {
				$rdf  = $url . '&feed=rdf';
				$atom = $url . '&feed=atom';

				$pages = [ $rdf, $atom ];

				$archive_pages	= array_merge( $archive_pages, $pages );
			}


		}

		return $archive_pages;
	}

	private function get_archive_dates( $url,  $post_date ) {

		$date_array = [];
		$split_date = explode( '/', $post_date );

		$year  = $split_date[0];
		$month = $split_date[1];
		$day   = $split_date[2];

		if ( get_option('permalink_structure') ) {

			$year_param  = $year . '/';
			$month_param = $year_param . $month . '/';
			$day_param   = $month_param . $day . '/';

			$date_array = [ $url . $year_param, $url . $month_param, $url . $day_param ];

		} else {

			$year_param  = '?year=' . $year;
			$month_param =  $year_param . '&month=' . $month;
			$day_param   =  $month_param . '&day=' . $day;

			$date_array = [ $url . $year_param, $url . $month_param, $url . $day_param ];
		}

		return $date_array;
	}

	private function get_author_urls( &$urls_list, $post_id )
	{
		$author = get_post_field( 'post_author', $post_id );

		array_push( $urls_list,
			get_author_posts_url( $author ),
			get_author_feed_link( $author )
		);
	}

	private function get_feeds_urls( &$urls_list, $post_id )
	{
		array_push( $urls_list,
			get_bloginfo_rss( 'rdf_url' ),
			get_bloginfo_rss( 'rss_url' ),
			get_bloginfo_rss( 'rss2_url' ),
			get_bloginfo_rss( 'atom_url' ),
			get_bloginfo_rss( 'comments_rss2_url' ),
			get_post_comments_feed_link( $post_id )
		);
	}

	private function get_rest_api_urls( &$urls_list )
	{
		$protocol = Utils::get_protocol( false );
		if ( get_option('permalink_structure') ) {
			array_push( $urls_list,
				get_site_url(null, '/wp-json/wp/v2/posts', $protocol ) . '*'
			);
		}else {
			array_push( $urls_list,
				get_site_url(null, '/?rest_route=/wp/v2/posts/', $protocol ) . '*'
			);
		}

	}

	private function get_thumbnail_link( &$urls_list, $post_id )
	{
		$thumbnail_id = get_post_thumbnail_id($post_id);

		if ( $thumbnail_id !== 0 ) {
			array_push( $urls_list,
				get_attachment_link($thumbnail_id)
			);
		}
	}

	private function get_attachment_links( &$urls_list, $post_id )
	{
		$args = [
			'post_parent'    => $post_id,
			'post_type'      => 'attachment',
			'numberposts'    => -1,
			'post_status'    => 'any'
		];

		$attachment = get_posts($args);
		if( $attachment ){

			foreach ( $attachment as $att ) {
				$guid = $att->guid;

				array_push( $urls_list,
					$guid
				);
			}
		}
	}

	private function get_main_domain_url( &$urls_list )
	{
		$protocol = Utils::get_protocol();
		$domain   = Utils::get_domain();

		$main_domain = $protocol . $domain . '/';
		array_push( $urls_list, $main_domain );
	}

	private function get_post_link( &$urls_list, $post_id )
	{
		array_push( $urls_list, get_permalink( $post_id ) );
	}

	private function get_sitemap_url( &$urls_list )
	{
		if ( get_option( 'gocache_option-auto_clear_sitemap_url' ) === 'yes' ){

			$protocol = Utils::get_protocol();
			$domain   = Utils::get_domain();

			$sitemap_url = $protocol . $domain . '/*sitemap*.xml*';
			array_push( $urls_list, $sitemap_url );
		}
	}

	private function get_amp_url( &$urls_list, $post_id )
	{
		if ( get_option( 'gocache_option-auto_clear_amp_url' ) === 'yes' ){

			$protocol   = Utils::get_protocol();
			$domain  	= Utils::get_domain();
			$url        = $protocol . $domain;

			$amp_urls = [];
			if ( get_option('permalink_structure') ) {

				$post_url = get_permalink( $post_id );
				$amp_arr = str_split( $post_url );

				if( $amp_arr[ count( $amp_arr ) -1 ] !== '/' ) {
					$amp_url = $post_url  . '/amp/';

					array_push( $amp_urls, $amp_url );

				} else {
					$amp_url = $post_url  . 'amp/';
					array_push( $amp_urls, $amp_url );

				}
				$home = $url . '/amp/';
				array_push( $amp_urls, $home );

			}else {
				$post_url    = get_permalink( $post_id );
				$amp_url     = $post_url   . '&amp=1';
				$home        = $url . '/&amp=1';
				$archive_url = $post_url . '/amp/';
				array_push( $amp_urls, $home, $amp_url, $archive_url );
			}
			foreach ( $amp_urls as $url ) {
				array_push( $urls_list, $url );
			}
		}
	}

	public function ajax_settings()
	{
		if ( ! Utils::is_request_ajax() ) {
			exit(0);
		}

		$params   = Utils::post( 'option', false, 'wp_strip_all_tags' );
		$option   = $params['option'];
		$response = $params['response'];

		if ( ! get_option( 'gocache_option-' . $option ) ){
			add_option('gocache_option-' .  $option, $response );
		}

		update_option( 'gocache_option-' . $option, $response );
	}

}
