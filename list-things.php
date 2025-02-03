<?php

/**
 * Plugin Name: List Things
 * Version: 0.3.1
 * Author: Sam Glover
 * Author URI: https://samglover.net
 * Text Domain: list-things
 */

namespace List_Things;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Constants
 */
define( 'LIST_THINGS_VERSION', '0.3.1' );
define( 'LIST_THINGS_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'LIST_THINGS_DIR_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin Files
 */

// Common
require_once LIST_THINGS_DIR_PATH . 'common/things.php';
require_once LIST_THINGS_DIR_PATH . 'common/ajax-things.php';
require_once LIST_THINGS_DIR_PATH . 'common/utilities.php';

// Frontend
if ( ! is_admin() ) {
	require_once LIST_THINGS_DIR_PATH . 'frontend/form-search-things.php';
	require_once LIST_THINGS_DIR_PATH . 'frontend/form-sort-things.php';
	require_once LIST_THINGS_DIR_PATH . 'frontend/shortcode.php';
}
