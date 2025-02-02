<?php
namespace List_Things;

function sort_things_form( $args, $options ) {
	$post_type_name = format_list_of_things( get_post_type_names( $args['post_type'] ), 'and' );
	?>
	<div id="thing-sorter-<?php echo esc_attr( $options['things_section_id'] ); ?>" class="thing-sorter" role="search" onsubmit="return false;">
		<label class="margin-bottom-xxs">
			<?php
			printf(
				wp_kses_post(
					// Translators: %s is the post type name.
					__( 'Sort %s', 'list-things' )
				),
				esc_html( strtolower( $post_type_name ) )
			);
			?>
		</label>
		<div class="row gap-xxs">
			<?php echo wp_kses_post( get_sort_buttons( $options['sort_buttons'] ) ); ?>
		</div>
	</div>
	<?php
}

function get_sort_buttons( $selected_buttons ) {
	if ( ! $selected_buttons ) {
		return;
	}
	$selected_buttons = format_vals( $selected_buttons );
	// echo '<pre>';
	// var_dump($selected_buttons);
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
	foreach ( $selected_buttons as $button ) {
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
			>
				<?php echo esc_html( $all_buttons[ $button ]['label'] ); ?>
			</button>
		<?php
	}
	return ob_get_clean();
}