<?php
/**
 * Common utility functions
 *
 * @file utilities.php
 * @package List_Things
 * @subpackage Common
 * @since 0.1.0
 */

namespace List_Things;

/**
 * Returns the default list parameters.
 *
 * @param string $format Optional. Merges the 'args' and 'options' sub-arrays for use in the list-things shortcode. Accepts 'merged'. Default null.
 * @return array.
 */
function get_default_params( $format = null ) {
	$default_params = array(
		'args'    => array(
			'order'          => 'ASC',
			'orderby'        => 'post_title',
			'paged'          => 1,
			'post_parent'    => null,
			'post_status'    => 'publish',
			'post_type'      => 'post',
			'post__in'       => null,
			'post__not_in'   => null,
			'posts_per_page' => get_option( 'posts_per_page' ),
			's'              => '',
		),
		'options' => array(
			'classes'        => false,
			'grid_cols'      => 3,
			'hide_title'     => false,
			'layout'         => 'list',
			'show_excerpt'   => false,
			'paginate'       => true,
			'show_read_more' => false,
			'show_search'    => false,
			'show_sort'      => false,
			'spacing'        => false,
			'sort_buttons'   => array( 'a-to-z', 'z-to-a', 'new-to-old', 'old-to-new', 'randomize' ),
			'show_thumbnail' => false,
			'title_tag'      => 'h3',
		),
	);

	if ( 'merged' === $format ) {
		$default_params = array_merge( $default_params['args'], $default_params['options'] );
	}

	return $default_params;
}


/**
 * Formats a list of thing/post type names as a comma-separated list.
 *
 * @param array  $things A list of thing/post type names.
 * @param string $and_or Accepts 'and' or 'or'. Default 'and'.
 * @return string.
 */
function format_list_of_things( $things, $and_or = 'and' ) {
	if ( ! $things ) {
		return;
	}

	if ( ! is_array( $things ) ) {
		return $things;
	}

	$things         = format_vals( $things );
	$num_things     = count( $things );
	$formatted_list = $things[0];

	if ( 2 === $num_things ) {
		$formatted_list .= ' ' . $and_or . ' ' . $things[1];
	} elseif ( $num_things > 2 ) {
		$i = 1;

		while ( $i < $num_things - 1 ) {
			$formatted_list .= ', ' . $things[ $i ];
			++$i;
		}

		$formatted_list .= ' ' . $and_or . ' ' . $things[ $i ];
	}

	return $formatted_list;
}


/**
 * Formats an array of values (such as query args) as follows:
 * - Strings are sanitized
 * - Numeric strings are converted to integers
 * - Truthy and falsey values are replaced with booleans
 *
 * @param array $vals An array of values.
 * @return array.
 */
function format_vals( $vals ) {
	if ( ! $vals ) {
		return;
	}

	$truthy_vals = array( 1, '1', 'true' );
	$falsey_vals = array( 0, '0', 'false' );

	foreach ( $vals as $key => $val ) {
		if ( is_bool( $val ) ) {
			continue;
		}

		if ( is_array( $val ) ) {
			$vals[ $key ] = format_vals( $val );
		} elseif ( is_string( $val ) ) {
			$val = sanitize_text_field( $val );
			if ( is_numeric( $val ) ) {
				$vals[ $key ] = intval( $val );
			} elseif ( in_array( strtolower( $val ), $truthy_vals, true ) ) {
				$val = true;
			} elseif ( in_array( strtolower( $val ), $falsey_vals, true ) ) {
				$val = false;
			}
			$vals[ $key ] = $val;
		}
	}

	return $vals;
}


/**
 * Gets a list of thing/post type names.
 *
 * @param string|array $post_types Optional. A single post type as a string, an array of post types. Default 'things'.
 *
 * @return string|array.
 */
function get_post_type_names( $post_types ) {
	if ( is_string( $post_types ) ) {
		$post_type_obj = get_post_type_object( $post_types );
		if ( $post_type_obj ) {
			$post_type_names = get_post_type_labels( $post_type_obj )->name;
		} else {
			$post_type_names = $post_types;
		}
	} elseif ( is_array( $post_types ) ) {
		foreach ( $post_types as $post_type ) {
			$post_type_obj = get_post_type_object( $post_type );
			if ( $post_type_obj ) {
				$post_type_names[] = get_post_type_labels( $post_type_obj )->name;
			} else {
				$post_type_names[] = $post_type;
			}
		}
	} else {
		$post_type_names = 'things';
	}

	return $post_type_names;
}
