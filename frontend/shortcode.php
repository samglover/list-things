<?php
namespace List_Things;

/** 
 * The following attributes can be a comma-delimited string:
 * * post_type
 * * post__not_in
 * * sort_buttons
 */
add_shortcode('list-things', __NAMESPACE__ . '\list_things_shortcode');
function list_things_shortcode($atts) {
  $atts = shortcode_atts(get_default_params('merged'), $atts, 'list-things');
  $atts['post_status'] = preg_split('/, */', $atts['post_status'], -1, PREG_SPLIT_NO_EMPTY);
  $atts['post_type'] = preg_split('/, */', $atts['post_type'], -1, PREG_SPLIT_NO_EMPTY);
  $atts['post__not_in'] = $atts['post__not_in'] ? preg_split('/, */', $atts['post__not_in'], -1, PREG_SPLIT_NO_EMPTY) : '';
  if (!is_array($atts['sort_buttons'])) $atts['sort_buttons'] = $atts['sort_buttons'] ? preg_split('/, */', $atts['sort_buttons'], -1, PREG_SPLIT_NO_EMPTY) : '';
  $atts = sanitize_array($atts);

  // echo '<pre>';
  //   var_dump($atts['sort_buttons']);
  // echo '</pre>';
  
  $args = [
    'order' => $atts['order'],
    'orderby' => $atts['orderby'],
    'post_parent' => $atts['post_parent'],
    'post_status' => $atts['post_status'],
    'post_type' => $atts['post_type'],
    'post__not_in' => $atts['post__not_in'],
    's' => $atts['s']
  ];
  foreach ($args as $key => $val) {
    if (empty($val)) unset($args[$key]);
  }

  // echo '<pre>';
  //   var_dump($args);
  // echo '</pre>';
  
  $options = [
    'grid_cols' => $atts['grid_cols'],
    'hide_title' => $atts['hide_title'],
    'layout' => $atts['layout'],
    'show_excerpt' => $atts['show_excerpt'],
    'show_read_more' => $atts['show_read_more'],
    'show_search' => $atts['show_search'],
    'show_sort' => $atts['show_sort'],
    'sort_buttons' => $atts['sort_buttons'],
    'show_thumbnail' => $atts['show_thumbnail'],
    'title_tag' => $atts['title_tag'],
  ];
  
  // echo '<pre>';
  //   var_dump($options);
  // echo '</pre>';

  ob_start();
    list_things($args, $options);
  $list_of_things = ob_get_clean();
  return $list_of_things;
}


add_shortcode('list-child-pages', __NAMESPACE__ . '\list_child_pages_shortcode');
function list_child_pages_shortcode($atts) {
  $post__not_in = isset($atts['post__not_in']) ? preg_split('/, */', $atts['post__not_in'], -1, PREG_SPLIT_NO_EMPTY) : false;
  $atts = shortcode_atts([
    'parent' => get_the_ID(),
  ], $atts);
  $atts = sanitize_array($atts);


  $shortcode = '[list-things post_type="page" post_parent="' . $atts['parent'] . '"';
  if ($post__not_in) $shortcode .= ' post__not_in=' . implode(',', $post__not_in);
  $shortcode .= ']';
  return do_shortcode($shortcode);
}