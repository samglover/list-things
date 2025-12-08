<?php
/**
 * Contains the following shortcodes:
 * - list-things, the main shortcode
 * - list-child-pages, a frequently used shorthand
 *
 * @file       shortcodes.php
 * @package    list-things
 * @subpackage frontend
 * @since      1.0.0
 */

namespace List_Things;

add_shortcode( 'list-things', __NAMESPACE__ . '\list_things_shortcode' );
/**
 * Shortcode handler for listing things.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string HTML shortcode output.
 */
function list_things_shortcode( $atts ) {
	$atts = shortcode_atts( get_default_params( 'merged' ), $atts, 'list-things' );

	/*
	 * The following attributes can be a comma-delimited string in the shortcode:
	 * - post_type
	 * - post__in
	 * - post__not_in
	 * - sort_buttons
	 * - classes
	 */
	$atts['post_status']  = preg_split( '/, */', $atts['post_status'], -1, PREG_SPLIT_NO_EMPTY );
	$atts['post_type']    = preg_split( '/, */', $atts['post_type'], -1, PREG_SPLIT_NO_EMPTY );
	$atts['post__in']     = $atts['post__in'] ? preg_split( '/, */', $atts['post__in'], -1, PREG_SPLIT_NO_EMPTY ) : '';
	$atts['post__not_in'] = $atts['post__not_in'] ? preg_split( '/, */', $atts['post__not_in'], -1, PREG_SPLIT_NO_EMPTY ) : '';
	if ( ! is_array( $atts['sort_buttons'] ) ) {
		$atts['sort_buttons'] = $atts['sort_buttons'] ? preg_split( '/, */', $atts['sort_buttons'], -1, PREG_SPLIT_NO_EMPTY ) : '';
	}
	$atts['classes'] = $atts['classes'] ? preg_split( '/, */', $atts['classes'], -1, PREG_SPLIT_NO_EMPTY ) : '';
	$atts            = format_vals( $atts );

	// echo '<pre>';
	// var_dump($atts);
	// echo '</pre>';

	$args = array(
		'order'          => $atts['order'],
		'orderby'        => $atts['orderby'],
		'paged'          => $atts['paged'],
		'post_parent'    => $atts['post_parent'],
		'post_status'    => $atts['post_status'],
		'post_type'      => $atts['post_type'],
		'post__in'       => $atts['post__in'],
		'post__not_in'   => $atts['post__not_in'],
		'posts_per_page' => $atts['paginate'] ? $atts['posts_per_page'] : -1,
		's'              => $atts['s'],
	);

	foreach ( $args as $key => $val ) {
		if ( empty( $val ) ) {
			unset( $args[ $key ] );
		}
	}

	// echo '<pre>';
	// var_dump($args);
	// echo '</pre>';

	$options = array(
		'grid_cols'      => $atts['grid_cols'],
		'hide_title'     => $atts['hide_title'],
		'layout'         => $atts['layout'],
		'show_excerpt'   => $atts['show_excerpt'],
		'paginate'       => $atts['paginate'],
		'show_read_more' => $atts['show_read_more'],
		'show_search'    => $atts['show_search'],
		'show_sort'      => $atts['show_sort'],
		'sort_buttons'   => $atts['sort_buttons'],
		'spacing'        => $atts['spacing'],
		'show_thumbnail' => $atts['show_thumbnail'],
		'title_tag'      => $atts['title_tag'],
		'classes'        => $atts['classes'],
	);

	// echo '<pre>';
	// var_dump($options);
	// echo '</pre>';

	wp_enqueue_style( 'list-things', LIST_THINGS_DIR_URL . 'assets/css/thing-styles.css', array(), LIST_THINGS_VERSION );

	global $post;
	wp_register_script( 'search-things', LIST_THINGS_DIR_URL . 'assets/js/search-things.js', array( 'jquery' ), LIST_THINGS_VERSION, true );
	wp_localize_script( 'search-things', 'vars', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	wp_enqueue_script( 'search-things' );

	wp_register_script( 'sort-things', LIST_THINGS_DIR_URL . 'assets/js/sort-things.js', array( 'jquery' ), LIST_THINGS_VERSION, true );
	wp_localize_script( 'sort-things', 'vars', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	wp_enqueue_script( 'sort-things' );

	wp_register_script( 'paginate-things', LIST_THINGS_DIR_URL . 'assets/js/paginate-things.js', array( 'jquery' ), LIST_THINGS_VERSION, true );
	wp_localize_script( 'paginate-things', 'vars', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	wp_enqueue_script( 'paginate-things' );

	ob_start();

	list_things( $args, $options );
	$list_of_things = ob_get_clean();

	return $list_of_things;
}


add_shortcode( 'list-child-pages', __NAMESPACE__ . '\list_child_pages_shortcode' );
/**
 * Shortcode to list child pages.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string HTML shortcode output.
 */
function list_child_pages_shortcode( $atts ) {
	$post__not_in = isset( $atts['post__not_in'] ) ? preg_split( '/, */', $atts['post__not_in'], -1, PREG_SPLIT_NO_EMPTY ) : false;
	$atts         = shortcode_atts(
		array(
			'parent' => get_the_ID(),
		),
		$atts
	);
	$atts         = format_vals( $atts );

	$shortcode = '[list-things post_type="page" post_parent="' . $atts['parent'] . '"';
	if ( $post__not_in ) {
		$shortcode .= ' post__not_in=' . implode( ',', $post__not_in );
	}
	$shortcode .= ']';

	return do_shortcode( $shortcode );
}
