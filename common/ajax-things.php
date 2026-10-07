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
	if ( ! isset( $_POST['args'] ) || ! isset( $_POST['options'] ) ) {
		exit( 'Missing arguments or options.' );
	}

	$args    = format_vals( map_deep( wp_unslash( $_POST['args'] ), 'sanitize_text_field' ) );
	$options = format_vals( map_deep( wp_unslash( $_POST['options'] ), 'sanitize_text_field' ) );

	?>
		<div class="list-of-things">
			<?php echo wp_kses_post( get_things( $args, $options ) ); ?>
		</div>
	<?php

	die();
}

add_action( 'wp_ajax_new_get_things', __NAMESPACE__ . '\new_get_things' ); // Logged-in users.
add_action( 'wp_ajax_nopriv_new_get_things', __NAMESPACE__ . '\new_get_things' ); // Not-logged-in users.