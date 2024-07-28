<?php

/**
 * Plugin Name: List Things
 * Version: 1.0
 * Author: Sam Glover
 * Author URI: https://samglover.net
 * Text Domain: list-things
 */

namespace List_Things;

if (!defined('ABSPATH')) exit;

/**
 * Constants
 */
define('LIST_THINGS_VERSION', '1.0');
define('LIST_THINGS_DIR_PATH', plugin_dir_path(__FILE__));
define('LIST_THINGS_DIR_URL', plugin_dir_url(__FILE__));

/**
 * Plugin Files
 */

// Common
// require_once(LIST_THINGS_DIR_PATH . 'common/search-things.php');
require_once(LIST_THINGS_DIR_PATH . 'common/things.php');
require_once(LIST_THINGS_DIR_PATH . 'common/ajax-things.php');
require_once(LIST_THINGS_DIR_PATH . 'common/utilities.php');

// Frontend
if (!is_admin()) {
  require_once(LIST_THINGS_DIR_PATH . 'frontend/form-search-things.php');
  require_once(LIST_THINGS_DIR_PATH . 'frontend/form-sort-things.php');
  require_once(LIST_THINGS_DIR_PATH . 'frontend/shortcode.php');
}

add_action('wp_enqueue_scripts', __NAMESPACE__ . '\frontend_stylesheets_scripts');
function frontend_stylesheets_scripts() {
  global $post;
  wp_register_script('search-things', LIST_THINGS_DIR_URL . 'assets/js/search-things.js', ['jquery'], LIST_THINGS_VERSION, true);
  wp_localize_script('search-things', 'vars', ['ajaxurl' => admin_url('admin-ajax.php')]);
  wp_register_script('sort-things', LIST_THINGS_DIR_URL . 'assets/js/sort-things.js', ['jquery'], LIST_THINGS_VERSION, true);
  wp_localize_script('sort-things', 'vars', ['ajaxurl' => admin_url('admin-ajax.php')]);

  if (is_main_query() && has_shortcode($post->post_content, 'list-things')) {
    wp_enqueue_style('list-things', LIST_THINGS_DIR_URL . 'assets/css/thing-styles.css', [], LIST_THINGS_VERSION);
    wp_enqueue_script('search-things');
    wp_enqueue_script('sort-things');
  }
}