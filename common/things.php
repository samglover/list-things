<?php
namespace List_Things;

function list_things($args, $options) {
  static $things_section_id = 42;
  $default_params = get_default_params();
  $args = wp_parse_args($args, $default_params['args']);
  $args = format_vals($args);
  $options = wp_parse_args($options, $default_params['options']);
  $options = format_vals($options);
  $options['things_section_id'] = $things_section_id;
  $post_type_names = get_post_type_names($args['post_type']);
  $div_classes = [
    'list-of-things__container',
    'layout-' . $options['layout'],
    $options['spacing'],
  ];
  if ($options['layout'] == 'grid') $div_classes[] = 'things-grid-cols-' . $options['grid_cols'];
  if ($options['classes']) $div_classes = array_merge($options['classes'], $div_classes);
  ?>
    <div 
      id="list-of-things-<?php echo $options['things_section_id']; ?>"
      class="<?php echo implode(' ', $div_classes); ?>"
      data-things-section-id="<?php echo $options['things_section_id']; ?>"
      data-things-nonce="<?php echo wp_create_nonce('things_nonce_' . $options['things_section_id']); ?>"
      data-things-args=<?php echo json_encode($args); ?>
      data-things-options=<?php echo json_encode($options); ?>
    >
      <?php if ($options['show_search'] || $options['show_sort']) { ?>
        <div class="search-sort-things row gap-sm wrap">
          <?php if ($options['show_search']) search_things_form($args, $options); ?>
          <?php if ($options['show_sort']) sort_things_form($args, $options); ?>
        </div>
      <?php } ?>
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
        if ($options['layout'] == 'grid') $post_classes[] = 'card thing-card';
        if ($options['show_excerpt']) $post_classes[] = 'thing-has-excerpt';
        if ($options['show_thumbnail'] && has_post_thumbnail()) $post_classes[] = 'thing-has-thumbnail';
        $title_classes = [
          'thing-title',
          'entry-title',
          'wp-block-heading',
        ];
        if ($options['hide_title']) $title_classes = 'screen-reader-text'
        ?>
          <article <?php post_class($post_classes); ?>>
            <?php if ($options['show_thumbnail'] && has_post_thumbnail()) { ?>
              <div class="thing-col thing-thumbnail__container wp-post-image__container">
                <a href="<?php echo get_the_permalink(); ?>"><?php the_post_thumbnail('medium', ['class' => 'thing-thumbnail']); ?></a>
              </div>  
            <?php } ?>
            <div class="thing-col thing-title__container">
              <?php do_action('list_things_before_title'); ?>
              <<?php echo $options['title_tag']; ?> class="<?php echo implode(' ', $title_classes); ?>"><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></<?php echo $options['title_tag']; ?>>
              <?php do_action('list_things_after_title'); ?>
              <?php if ($options['show_excerpt'] && get_the_excerpt()) { ?>
                <p class="thing-excerpt entry-excerpt"><?php echo get_the_excerpt(); ?></p>
                <?php } ?>
              <?php do_action('list_things_after_excerpt'); ?>
              <?php if ($options['show_read_more']) { ?>
                <a href="<?php echo get_the_permalink(); ?>" class="button wp-element-button thing-read-more-button"><?php _e('Read more', 'list-things'); ?></a>
              <?php } ?>
            </div>
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