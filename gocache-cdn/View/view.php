<?php
/**
 * Views
 *
 * @package GoCache
 * @subpackage Views
 * @version 1.0
 */
namespace GoCache;

// Avoid that files are directly loaded
if ( ! function_exists( 'add_action' ) ) {
	exit(0);
}

abstract class View
{
	public static function render_options( $list, $field_value, $field_text, $current )
	{
		foreach ( $list as $item ) {
			printf(
				'<option value="%1$s" %3$s>%2$s</option>',
				$item->$field_value,
				$item->$field_text,
				Utils::selected( $item->$field_value, $current )
			);
		}
	}
}