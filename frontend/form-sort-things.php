<?php
/**
 * Handles sorting things.
 *
 * @file       form-sort-things.php
 * @package    list-things
 * @subpackage frontend
 * @since      0.1.0
 */

namespace List_Things;

/**
 * Outputs the thing-sorter.
 *
 * @param array $args    Query arguments.
 * @param array $options Output options.
 */
function sort_things( $args, $options ) {
	$post_type_name = format_list_of_things( get_post_type_names( $args['post_type'] ), 'and' );
	?>
	<div 
		id="thing-sorter-<?php echo esc_attr( $options['things_section_id'] ); ?>"
		class="thing-sorter"
	>
		<p class="list-things-label">
			<?php
			printf(
				wp_kses_post(
					// Translators: %s is the post type name.
					__( 'Sort %s', 'list-things' )
				),
				esc_html( strtolower( $post_type_name ) )
			);
			?>
		</p>
		<div class="row gap-xxs wrap">
			<?php
				echo wp_kses_post(
					get_sort_buttons(
						array(
							'order'   => $args['order'],
							'orderby' => $args['orderby'],
						),
						$options['sort_buttons']
					)
				);
			?>
		</div>
	</div>
	<?php
}

/**
 * Generates thing sorting buttons.
 *
 * @param array $order_vars      An array of order and orderby parameters.
 * @param array $buttons_to_show An array of strings that identify the sorting buttons to.
 *
 * @return string HTML buttons.
 */
function get_sort_buttons( $order_vars, $buttons_to_show ) {
	if ( ! $buttons_to_show ) {
		return;
	}
	$buttons_to_show = format_vals( $buttons_to_show );

	// echo '<pre>';
	// var_dump( $buttons_to_show );
	// echo '</pre>';

	$all_buttons = array(
		'a-to-z'     => array(
			'order'   => 'ASC',
			'orderby' => 'post_title',
			'label'   => __( 'A to Z', 'list-things' ),
		),
		'z-to-a'     => array(
			'order'   => 'DESC',
			'orderby' => 'post_title',
			'label'   => __( 'Z to A', 'list-things' ),
		),
		'new-to-old' => array(
			'order'   => 'DESC',
			'orderby' => 'date',
			'label'   => __( 'New to Old', 'list-things' ),
		),
		'old-to-new' => array(
			'order'   => 'ASC',
			'orderby' => 'date',
			'label'   => __( 'Old to New', 'list-things' ),
		),
		'randomize'  => array(
			'class'   => 'things-randomize-button',
			'order'   => '',
			'orderby' => 'rand',
			'label'   => __( 'Randomize', 'list-things' ),
		),
	);
	ob_start();

	foreach ( $buttons_to_show as $button ) {
		if ( ! array_key_exists( $button, $all_buttons ) ) {
			continue;
		}
		$class = isset( $all_buttons[ $button ]['class'] ) ? ' ' . $all_buttons[ $button ]['class'] : '';
		?>
			<button 
				type="button" 
				class="wp-element-button has-sm-font-size button things-sort-button<?php echo esc_attr( $class ); ?>"
				data-things-order="<?php echo esc_attr( $all_buttons[ $button ]['order'] ); ?>"
				data-things-orderby="<?php echo esc_attr( $all_buttons[ $button ]['orderby'] ); ?>"
				<?php
				if ( $order_vars['order'] === $all_buttons[ $button ]['order']
						&& $order_vars['orderby'] === $all_buttons[ $button ]['orderby']
					) {
					echo esc_attr( 'disabled' );
				}
				?>
			>
				<?php echo esc_html( $all_buttons[ $button ]['label'] ); ?>
			</button>
		<?php
	}

	return ob_get_clean();
}