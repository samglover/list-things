<?php
/**
 * Handles filtering things.
 *
 * @file       form-filter-things.php
 * @package    list-things
 * @subpackage frontend
 * @since      0.4.0
 */

namespace List_Things;

/**
 * Outputs the thing-filterers.
 *
 * @param array $args    Optional. Array of query arguments.
 * @param array $options Optional. Array of display options.
 */
function filter_things( $args, $options ) {
	$term_count = get_terms(
		array(
			'fields'     => 'count',
			'hide_empty' => true,
			'taxonomy'   => get_taxonomies( array( 'object_type' => $args['post_type'] ), 'names', 'or' ),
		)
	);

	if ( ! $term_count || 0 === intval( $term_count ) ) {
		return;
	}

	$post_type_name = format_list_of_things( get_post_type_names( $args['post_type'] ), 'and' );
	?>
	<div
		class="thing-filters__button__container"
	>
		<p class="list-things-label">
			<?php
			printf(
				wp_kses_post(
					// Translators: %s is the post type name.
					__( 'Filter %s', 'list-things' )
				),
				esc_html( strtolower( $post_type_name ) )
			);
			?>
		</p>
		<button class="thing-filters__button wp-element-button has-sm-font-size button" type="button">
			<?php esc_html_e( 'Show filters', 'list-things' ); ?>
		</button>
	</div>
	<form
		id="thing-filters-<?php echo esc_attr( $options['things_section_id'] ); ?>"
		class="thing-filters__form row gap-xs wrap" 
		role="search"
		onsubmit="return false;"
	>
		<?php
		$taxonomies = get_taxonomies( array( 'object_type' => $args['post_type'] ), 'objects', 'or' );
		foreach ( $taxonomies as $tax ) {
			?>
			<fieldset class="thing-filter">
				<legend class="thing-filter__legend"><?php echo esc_attr( $tax->labels->name ); ?></legend>
				<?php
					$terms = get_terms(
						array(
							'hide_empty' => true,
							'taxonomy'   => $tax->name,
						)
					);

				foreach ( $terms as $term ) {
					?>
						<div class="thing-filter__term row gap-xxs">
							<input
								id="<?php echo esc_attr( $term->name ); ?>"
								type="checkbox"
							>
							<label for="<?php echo esc_attr( $term->name ); ?>">
								<?php esc_html_e( $term->name ); ?>
							</label>
						</div>
						<?php
				}
				?>
			</fieldset>
			<?php
		}
		?>
	</form>
	<?php
}