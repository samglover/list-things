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
	$post_type_name = format_list_of_things( get_post_type_names( $args['post_type'] ), 'and' );
	?>
	<div 
		id="thing-filters-<?php echo esc_attr( $options['things_section_id'] ); ?>" 
		class="thing-filter" 
		role="search" 
		onsubmit="return false;"
	>
		<button class="wp-element-button has-sm-font-size button things-filters-button" type="button">
			<?php esc_html_e( 'Show filters', 'list-things' ); ?>
		</button>
	</div>
	<?php
}