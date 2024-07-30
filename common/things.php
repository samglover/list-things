<?php
namespace List_Things;

function list_things($args, $options) {
  static $things_section_id = 42;
  $default_params = get_default_params();
  $args = wp_parse_args($args, $default_params['args']);
  $options = wp_parse_args($options, $default_params['options']);
  $options['things_section_id'] = $things_section_id;
  $post_type_names = get_post_type_names($args['post_type']);
  ?>
    <div 
      id="list-of-things-<?php echo $options['things_section_id']; ?>"
      class="list-of-things_container layout-<?php echo $options['layout']; ?>"
      data-things-section-id="<?php echo $options['things_section_id']; ?>"
      data-things-nonce="<?php echo wp_create_nonce('things_nonce_' . $options['things_section_id']); ?>"
      data-things-args=<?php echo json_encode($args); ?>
      data-things-options=<?php echo json_encode($options); ?>
    >
      <div class="search-sort-things row gap-sm wrap">
        <?php if ($options['show_search']) search_things_form($args, $options); ?>
        <?php if ($options['show_sort']) sort_things_form($args, $options); ?>
      </div>
      <div class="list-of-things">
        <?php echo get_things($args, $options); ?>
      </div>
    </div>
  <?php
  $things_section_id++;
}

function get_things($args, $options) {
  // echo '<pre>';
  //   var_dump($args);
  // echo '</pre>';
  $things_query = new \WP_Query($args);
  if ($things_query->have_posts()):
    ob_start();
      while ($things_query->have_posts()): $things_query->the_post();
        $post_classes = ['thing'];
        if ($options['layout'] == 'grid') $post_classes[] = 'card';
        if ($options['show_excerpt']) $post_classes[] = 'thing-has-excerpt';
        if ($options['show_thumbnail'] && has_post_thumbnail()) $post_classes[] = 'thing-has-thumbnail';
        ?>
          <article <?php post_class($post_classes); ?>>
            <header class="thing-header entry-header">
              <?php if ($options['show_thumbnail'] && has_post_thumbnail()) { ?>
                <div class="thing-thumbnail__container wp-post-image__container">
                  <a href="<?php echo get_the_permalink(); ?>"><?php the_post_thumbnail('medium', ['class' => 'thing-thumbnail']); ?></a>
                </div>
              <?php } ?>
              <<?php echo $options['title_tag']; ?> class="thing-title entry-title"><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></<?php echo $options['title_tag']; ?>>
            </header>
            <?php if ($options['show_excerpt'] && get_the_excerpt()) { ?>
              <p class="thing-excerpt entry-excerpt"><?php echo get_the_excerpt(); ?></p>
            <?php } ?>
            <?php if ($options['show_read_more']) { ?>
              <footer class="thing-footer entry-footer">
                <a href="<?php echo get_the_permalink(); ?>" class="button wp-element-button"><?php _e('Read more', 'list-things'); ?></a>
              </footer>
            <?php } ?>
          </article>
        <?php 
      endwhile;
    $things = ob_get_clean();
  else:
    $post_type_names = format_list_of_things(get_post_type_names($args['post_type']), 'or');
    ob_start();
      ?>
        <p class="no-things-found">
          <?php printf(__('No %s found.', 'list-things'), strtolower($post_type_names)); ?>
        </p>
      <?php
    $things = ob_get_clean();
  endif;
  wp_reset_postdata();
  return $things;
}