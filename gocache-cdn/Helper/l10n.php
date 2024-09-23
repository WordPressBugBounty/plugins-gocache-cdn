<?php
/**
 * Helper Localization
 *
 * @package GoCache
 * @subpackage Utils
 */
namespace GoCache;

// Avoid that files are directly loaded
if ( ! function_exists( 'add_action' ) ) {
	exit(0);
}

class L10n
{
	public static function get_labels( $messages, $labels = array() )
	{
		extract( $messages );

		$label_lower        = strtolower( $label );
		$plural_lower       = strtolower( $plural );
		$plural_lower_limit = Utils::limit_text( $plural_lower, 12 );

		$defaults = array(
			'name'                       => $plural,
			'singular_name'              => $label,
			'menu_name'                  => $plural,
			'all_items'                  => sprintf( _n( 'All %s', 'All %s', $is_female, App::PLUGIN_SLUG ), $plural_lower_limit ),
			'edit_item'                  => sprintf( __( 'Edit %s', App::PLUGIN_SLUG ), $label ),
			'view_item'                  => sprintf( __( 'View %s', App::PLUGIN_SLUG ), $label ),
			'update_item'                => sprintf( __( 'Update %s', App::PLUGIN_SLUG ), $label ),
			'add_new_item'               => sprintf( _n( 'Add New %s', 'Add New %s', $is_female, App::PLUGIN_SLUG ), $label ),
			'new_item_name'              => sprintf( _n( 'New %s Name', 'New %s Name', $is_female, App::PLUGIN_SLUG ), $label ),
			'parent_item'                => sprintf( __( 'Parent %s', App::PLUGIN_SLUG ), $label ),
			'parent_item_colon'          => sprintf( __( 'Parent %s:', App::PLUGIN_SLUG ), $label ),
			'search_items'               => sprintf( __( 'Search %s', App::PLUGIN_SLUG ), $plural ),
			'popular_items'              => sprintf( __( 'Popular %s', App::PLUGIN_SLUG ), $plural ),
			'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', App::PLUGIN_SLUG ), $plural_lower ),
			'add_or_remove_items'        => sprintf( __( 'Add or remove %s', App::PLUGIN_SLUG ), $plural_lower ),
			'choose_from_most_used'      => sprintf( _n( 'Choose from the most used %s', 'Choose from the most used %s', $is_female, App::PLUGIN_SLUG ), $plural_lower ),
			'not_found'                  => sprintf( _n( 'No %s found.', 'No %s found.', $is_female, App::PLUGIN_SLUG ), $plural_lower ),
			'add_new'                    => _n( 'Add New', 'Add New', $is_female, App::PLUGIN_SLUG ),
			'new_item'                   => sprintf( _n( 'New %s', 'New %s', $is_female, App::PLUGIN_SLUG ), $label ),
			'not_found_in_trash'         => sprintf( _n( 'No %s found in Trash.', 'No %s found in Trash.', $is_female, App::PLUGIN_SLUG ), $plural_lower ),
		);

		return wp_parse_args( $labels, $defaults );
	}
}