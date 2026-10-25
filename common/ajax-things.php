<?php
/**
 * Common AJAX functions for lists of things
 *
 * @file ajax-things.php
 * @package List_Things
 * @subpackage Common
 * @since 0.1.0
 */

namespace List_Things;

/**
 * Outputs a new list of things.
 */
function new_get_things() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Read-only request. Nonces in cached pages expire and break list updates.
	if ( ! isset( $_POST['args'] ) || ! isset( $_POST['options'] ) ) {
		exit( 'Missing arguments or options.' );
	}

	$args    = format_vals( map_deep( wp_unslash( $_POST['args'] ), 'sanitize_text_field' ) );
	$options = format_vals( map_deep( wp_unslash( $_POST['options'] ), 'sanitize_text_field' ) );
	// phpcs:enable WordPress.Security.NonceVerification.Missing
	$args = restrict_query_args( $args );

	if ( ! $args ) {
		exit( 'Invalid arguments.' );
	}

	?>
		<div class="list-of-things">
			<?php echo wp_kses_post( get_things( $args, $options ) ); ?>
		</div>
	<?php

	die();
}

/**
 * Limits AJAX query arguments to the ones List Things uses, and to post types
 * and statuses the current user is allowed to see.
 *
 * Without this, anyone could request drafts, private posts, or internal post
 * types, because WP_Query does not check permissions for an explicitly
 * requested post_type or post_status.
 *
 * @param array $args Query arguments from the AJAX request.
 *
 * @return array|false The restricted query arguments, or false if nothing the current user can see was requested.
 */
function restrict_query_args( $args ) {
	if ( ! is_array( $args ) ) {
		return false;
	}

	$default_params = get_default_params();
	$allowed_args   = array_merge( array_keys( $default_params['args'] ), array( 'tax_query' ) );
	$args           = array_intersect_key( $args, array_flip( $allowed_args ) );

	$post_types = isset( $args['post_type'] ) ? (array) $args['post_type'] : array( $default_params['args']['post_type'] );
	$post_types = normalize_query_vals( $post_types );

	// Expand 'any' the same way WP_Query does, so each post type gets checked.
	if ( in_array( 'any', $post_types, true ) ) {
		$post_types = array_values( get_post_types( array( 'exclude_from_search' => false ) ) );
	}

	// Non-public post types must have an admin screen, which excludes internal types like revisions and privacy requests.
	$post_types = array_filter(
		$post_types,
		function ( $post_type ) {
			$post_type_obj = get_post_type_object( $post_type );
			return $post_type_obj && (
				is_post_type_viewable( $post_type_obj )
				|| ( $post_type_obj->show_ui && current_user_can( $post_type_obj->cap->edit_posts ) )
			);
		}
	);

	if ( ! $post_types ) {
		return false;
	}

	$post_statuses = isset( $args['post_status'] ) ? (array) $args['post_status'] : array( $default_params['args']['post_status'] );
	$post_statuses = normalize_query_vals( $post_statuses );

	// Expand 'any' the same way WP_Query does, so each status gets checked.
	if ( in_array( 'any', $post_statuses, true ) ) {
		$post_statuses = array_values( get_post_stati( array( 'exclude_from_search' => false ) ) );
	}

	// Mirrors the read_post capability: public statuses are readable, private statuses require read_private_posts, and others require edit_others_posts.
	$post_statuses = array_filter(
		$post_statuses,
		function ( $post_status ) use ( $post_types ) {
			$post_status_obj = get_post_status_object( $post_status );

			// WP_Query skips status filtering entirely when no requested status is registered.
			if ( ! $post_status_obj ) {
				return false;
			}

			if ( $post_status_obj->public ) {
				return true;
			}

			foreach ( $post_types as $post_type ) {
				$cap = get_post_type_object( $post_type )->cap;

				if ( ! current_user_can( $post_status_obj->private ? $cap->read_private_posts : $cap->edit_others_posts ) ) {
					return false;
				}

				// Inherited posts (like attachments) take their parent's status, which may be private.
				if ( 'inherit' === $post_status && ! current_user_can( $cap->read_private_posts ) ) {
					return false;
				}
			}

			return true;
		}
	);

	if ( ! $post_statuses ) {
		return false;
	}

	$args['post_type']   = array_values( $post_types );
	$args['post_status'] = array_values( $post_statuses );

	return $args;
}

/**
 * Normalizes post types or statuses the same way WP_Query does, so permission
 * checks see what it will actually query.
 *
 * @param array $vals Post types or statuses.
 *
 * @return array
 */
function normalize_query_vals( $vals ) {
	return array_values( array_unique( array_map( 'sanitize_key', array_map( 'strval', $vals ) ) ) );
}

add_action( 'wp_ajax_new_get_things', __NAMESPACE__ . '\new_get_things' ); // Logged-in users.
add_action( 'wp_ajax_nopriv_new_get_things', __NAMESPACE__ . '\new_get_things' ); // Not-logged-in users.