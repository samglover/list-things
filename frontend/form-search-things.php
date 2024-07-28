<?php
namespace List_Things;

function search_things_form($args, $options) {
  $post_type_names = format_list_of_things(get_post_type_names($args['post_type']), 'and');
  ?>
    <form id="thing-searcher-<?php echo $options['things_section_id']; ?>" class="thing-searcher" role="search" onsubmit="return false;">
      <div class="row gap-xxs center-items">
        <label for="search-things-<?php echo $options['things_section_id']; ?>"><?php printf(__('Search %s', 'list-things'), strtolower($post_type_names)); ?></label>
        <button type="button" class="clear-search-things row center-items" style="display: none;">
          <i><?php echo file_get_contents(LIST_THINGS_DIR_PATH . 'assets/images/close.svg'); ?></i>
          <?php _e('Clear search', 'list-things'); ?></a></div>
        </button>
      <div class="row gap-xxs">
        <input 
          id="search-things-<?php echo $options['things_section_id']; ?>" 
          name="search-things-input" 
          class="search-things-input" 
          type="search" 
          <?php if (isset($args['s']) && !empty($args['s'])) echo 'value="' . $args['s'] . '"'; ?>
        />
        <button type='submit'>Search</button>
      </div>
    </form>
  <?php
}