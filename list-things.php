<?php
/**
 * Plugin name: List Things
 * Version:     0.4.0
 * Author:      Sam Glover
 * Author URI:  https://samglover.net
 * Text domain: list-things
 *
 * @file    list-things.php
 * @package List_Things
 * @since   0.1.0
 */

namespace List_Things;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Constants
 */
define( 'LIST_THINGS_VERSION', '0.4.0' );
define( 'LIST_THINGS_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'LIST_THINGS_DIR_URL', plugin_dir_url( __FILE__ ) );

/*
 * Plugin Files
 */
require_once LIST_THINGS_DIR_PATH . 'common/things.php';
require_once LIST_THINGS_DIR_PATH . 'common/ajax-things.php';
require_once LIST_THINGS_DIR_PATH . 'common/utilities.php';

if ( ! is_admin() ) {
	require_once LIST_THINGS_DIR_PATH . 'frontend/form-search-things.php';
	require_once LIST_THINGS_DIR_PATH . 'frontend/form-sort-things.php';
	require_once LIST_THINGS_DIR_PATH . 'frontend/form-filter-things.php';
	require_once LIST_THINGS_DIR_PATH . 'frontend/shortcodes.php';
}
