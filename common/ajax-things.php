<?php
namespace List_Things;

add_action( 'wp_ajax_new_get_things', __NAMESPACE__ . '\new_get_things' ); // Logged-in users.
add_action( 'wp_ajax_nopriv_new_get_things', __NAMESPACE__ . '\new_get_things' ); // Not-logged-in users.
function new_get_things() {
	if ( ! isset( $_POST['_ajax_nonce'] ) ) {
		exit( 'Missing nonce.' );
	}

	if ( ! isset( $_POST['_ajax_nonce_action'] ) ) {
		exit( 'Missing nonce action.' );
	}

	$nonce        = sanitize_text_field( wp_unslash( $_POST['_ajax_nonce'] ) );
	$nonce_action = sanitize_text_field( wp_unslash( $_POST['_ajax_nonce_action'] ) );

	if ( ! isset( $_POST['_ajax_nonce'] ) || ! wp_verify_nonce( $nonce, $nonce_action ) ) {
		exit( 'Invalid nonce.' );
	}

	if ( ! isset( $_POST['args'] ) || ! isset( $_POST['options'] ) ) {
		exit( 'Missing arguments or options.' );
	}

	$args    = format_vals( map_deep( wp_unslash( $_POST['args'] ), 'sanitize_text_field' ) );
	$options = format_vals( map_deep( wp_unslash( $_POST['options'] ), 'sanitize_text_field' ) );

	// echo '<pre>';
	// var_dump( $args );
	// echo '</pre>';

	?>
		<div class="list-of-things">
			<?php echo wp_kses_post( get_things( $args, $options ) ); ?>
		</div>
	<?php

	die();
}
