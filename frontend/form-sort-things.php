<?php
namespace List_Things;

function sort_things_form($args, $options) {
  $post_type_names = format_list_of_things(get_post_type_names($args['post_type']), 'and');
  ?>
    <div id="thing-sorter-<?php echo $options['things_section_id']; ?>" class="thing-sorter" role="search" onsubmit="return false;">
      <div class="row gap-xxs center-items">
        <label><?php printf(__('Sort %s', 'list-things'), strtolower($post_type_names)); ?></label>
      </div>
      <div class="row gap-xxs">
        <?php echo get_sort_buttons($options['sort_buttons']); ?>
      </div>
    </div>
  <?php
}

function get_sort_buttons($selected_buttons) {
  if (!$selected_buttons) return;
  $selected_buttons = sanitize_array($selected_buttons);
  // echo '<pre>';
  //   var_dump($selected_buttons);
  // echo '</pre>';
  $all_buttons = [
    'a-to-z' => [
      'order' => 'ASC',
      'orderby' => 'post_title',
      'label' => __('A to Z', 'list-things'),
    ],
    'z-to-a' => [
      'order' => 'DESC',
      'orderby' => 'post_title',
      'label' => __('Z to A', 'list-things'),
    ],
    'new-to-old' => [
      'order' => 'DESC',
      'orderby' => 'date',
      'label' => __('New to Old', 'list-things'),
    ],
    'old-to-new' => [
      'order' => 'ASC',
      'orderby' => 'date',
      'label' => __('Old to New', 'list-things'),
    ],
    'randomize' => [
      'class' => 'things-randomize-button',
      'order' => '',
      'orderby' => 'rand',
      'label' => __('Randomize', 'list-things'),
    ],
  ];
  ob_start();
    foreach($selected_buttons as $button) {
      if (!array_key_exists($button, $all_buttons)) continue;
      $class = isset($all_buttons[$button]['class']) ? ' ' . $all_buttons[$button]['class'] : '';
      echo '<button type="button" class="wp-element-button button things-sort-button' . $class . '" data-things-order="' . $all_buttons[$button]['order'] . '" data-things-orderby="' . $all_buttons[$button]['orderby'] . '">' . $all_buttons[$button]['label'] . '</button>';
    }
  return ob_get_clean();
}