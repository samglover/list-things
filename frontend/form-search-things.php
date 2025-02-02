<?php
namespace List_Things;

function search_things_form( $args, $options ) {
	$post_type_name = format_list_of_things( get_post_type_names( $args['post_type'] ), 'and' );
	?>
		<form 
			id="thing-searcher-<?php echo esc_attr( $options['things_section_id'] ); ?>" 
			class="thing-searcher" 
			role="search" 
			onsubmit="return false;"
		>
			<label class="margin-bottom-xxs" for="search-things-<?php echo esc_attr( $options['things_section_id'] ); ?>">
				<?php
				printf(
					wp_kses_post(
						// Translators: %s is the post type name.
						__( 'Search %s', 'list-things' )
					),
					esc_html( strtolower( $post_type_name ) )
				);
				?>
			</label>
			<div class="row gap-xxs center-items">
				<div class="input__container">
					<input 
						id="search-things-<?php echo esc_attr( $options['things_section_id'] ); ?>" 
						name="search-things-input" 
						class="search-things-input" 
						type="search" 
						<?php if ( isset( $args['s'] ) && ! empty( $args['s'] ) ) { ?>
							value="<?php esc_attr( $args['s'] ); ?>"
						<?php } ?>
					/>
					<button type="button" class="clear-search-things row center-items" style="display: none;">
						<span class="screen-reader-text">
							<?php esc_html_e( 'Clear search', 'list-things' ); ?>
						</span>
					</button>
				</div>
				<button type='submit' class="wp-element-button has-sm-font-size">
					<?php esc_html_e( 'Search', 'list-things' ); ?>
				</button>
			</div>
		</form>
	<?php
}