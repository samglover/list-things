<?php
/**
 * Common things functions
 *
 * @file things.php
 * @package List_Things
 * @subpackage Common
 * @since 0.1.0
 */

namespace List_Things;

/**
 * Outputs a list of things in a container, with search, sort, and filter options.
 *
 * @param array $args    Optional. Array of query arguments.
 * @param array $options Optional. Array of display options.
 */
function list_things( $args, $options ) {
	static $things_section_id = 42; // Arbitrary starting index for multiple lists of things.

	$default_params = get_default_params();

	$args = wp_parse_args( $args, $default_params['args'] );
	$args = format_vals( $args );

	$options                      = wp_parse_args( $options, $default_params['options'] );
	$options                      = format_vals( $options );
	$options['things_section_id'] = $things_section_id;

	$post_type_names = get_post_type_names( $args['post_type'] );

	$div_classes = array(
		'list-of-things__container',
		'layout-' . $options['layout'],
		$options['spacing'],
	);

	if ( 'grid' === $options['layout'] ) {
		$div_classes[] = 'things-grid-cols-' . $options['grid_cols'];
	}

	if ( $options['classes'] ) {
		$div_classes = array_merge( $options['classes'], $div_classes );
	}

	?>
	<div 
		id="list-of-things-<?php echo esc_attr( $options['things_section_id'] ); ?>"
		class="<?php echo esc_attr( implode( ' ', $div_classes ) ); ?>"
		data-things-section-id="<?php echo esc_attr( $options['things_section_id'] ); ?>"
		data-things-nonce="<?php echo esc_attr( wp_create_nonce( 'things_nonce_' . $options['things_section_id'] ) ); ?>"
		data-things-args=<?php echo esc_attr( wp_json_encode( $args ) ); ?>
		data-things-options=<?php echo esc_attr( wp_json_encode( $options ) ); ?>
	>
		<?php if ( $options['show_search'] || $options['show_sort'] ) { ?>
		<div class="search-sort-things row gap-sm wrap">
			<?php
			if ( $options['show_search'] ) {
				search_things_form( $args, $options );}
			?>
			<?php
			if ( $options['show_sort'] ) {
				sort_things( $args, $options );}
			?>
		</div>
		<?php } ?>
		<div class="list-of-things">
			<?php echo wp_kses_post( get_things( $args, $options ) ); ?>
		</div>
	</div>

	<?php
	++$things_section_id;
}


/**
 * Returns the list of things only (no container, search, sort, or filters).
 *
 * @param array $args    Array of query arguments.
 * @param array $options Array of display options.
 *
 * @return string
 */
function get_things( $args, $options ) {
	// echo '<pre>';
	// var_dump( $args );
	// echo '</pre>';

	$things_query = new \WP_Query( $args );

	if ( $things_query->have_posts() ) :
		ob_start();
		while ( $things_query->have_posts() ) :
			$things_query->the_post();

			$post_classes = get_post_class( 'thing' );

			// Adds a class for the post type if it is not present, because AJAX queries strip the post-type class for some reason.
			if ( ! in_array( get_post_type(), $post_classes, true ) ) {
				$post_classes[] = get_post_type();
			}

			if ( 'grid' === $options['layout'] ) {
				$post_classes[] = 'card thing-card';
			}

			if ( $options['show_excerpt'] ) {
				$post_classes[] = 'thing-has-excerpt';
			}

			if ( $options['show_thumbnail'] && has_post_thumbnail() ) {
				$post_classes[] = 'thing-has-thumbnail';
			}

			$title_classes = array(
				'thing-title',
				'entry-title',
				'wp-block-heading',
			);

			if ( $options['hide_title'] ) {
				$title_classes = 'screen-reader-text';
			}

			?>
				<article <?php post_class( $post_classes ); ?>>
					<?php if ( $options['show_thumbnail'] && has_post_thumbnail() ) { ?>
						<div class="thing-col thing-thumbnail__container wp-post-image__container">
							<a href="<?php echo esc_url( get_the_permalink() ); ?>">
								<?php the_post_thumbnail( 'medium', array( 'class' => 'thing-thumbnail' ) ); ?>
							</a>
						</div>  
					<?php } ?>

					<div class="thing-col thing-title__container">
						<?php do_action( 'list_things_before_title' ); ?>

						<?php if ( get_the_title() ) { ?>
							<<?php echo esc_attr( $options['title_tag'] ); ?> class="<?php echo esc_attr( implode( ' ', $title_classes ) ); ?>">
								<a href="<?php echo esc_url( get_the_permalink() ); ?>">
								<?php echo esc_html( get_the_title() ); ?>
								</a>
							</<?php echo esc_attr( $options['title_tag'] ); ?>>
						<?php } ?>

						<?php do_action( 'list_things_after_title' ); ?>

						<?php if ( $options['show_excerpt'] && get_the_excerpt() ) { ?>
							<p class="thing-excerpt entry-excerpt">
								<?php echo wp_kses_post( get_the_excerpt() ); ?>
							</p>
						<?php } ?>

						<?php do_action( 'list_things_after_excerpt' ); ?>

					</div>

					<?php do_action( 'list_things_after_title_container' ); ?>
					
					<?php if ( $options['show_read_more'] ) { ?>
						<a href="<?php echo esc_url( get_the_permalink() ); ?>" class="button wp-element-button thing-read-more-button">
							<?php esc_html_e( 'Read more', 'list-things' ); ?>
						</a>
					<?php } ?>
				</article>
			<?php
		endwhile;
		$things = ob_get_clean();
	else :
		$post_type_names = format_list_of_things( get_post_type_names( $args['post_type'] ), 'or' );
		ob_start();
		?>
		<p class="no-things-found">
			<?php
			printf(
				wp_kses_post(
					// Translators: %s is a list of the post type names.
					__( 'No %s found.', 'list-things' )
				),
				esc_html( strtolower( $post_type_names ) )
			);
			?>
		</p>

		<?php
		$things = ob_get_clean();
	endif;

	wp_reset_postdata();
	return $things;
}