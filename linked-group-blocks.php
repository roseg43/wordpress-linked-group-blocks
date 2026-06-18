<?php
/**
 * Plugin Name:     Linked Group Blocks
 * Description:     Adds link functionality to the core Group block, including post-aware linking inside Query Loops.
 * Author:          Fueled
 * Author URI:      https://fueled.com
 * Text Domain:     linked-group-blocks
 * Version:         1.0.0
 * Update URI:      false
 *
 * @package LinkedGroupBlocks
 */

namespace LinkedGroupBlocks;

define( 'LINKED_GROUP_BLOCKS_VERSION', '1.0.0' );
define( 'LINKED_GROUP_BLOCKS_URL', plugin_dir_url( __FILE__ ) );
define( 'LINKED_GROUP_BLOCKS_PATH', plugin_dir_path( __FILE__ ) );
define( 'LINKED_GROUP_BLOCKS_DIST_URL', LINKED_GROUP_BLOCKS_URL . 'dist/' );
define( 'LINKED_GROUP_BLOCKS_DIST_PATH', LINKED_GROUP_BLOCKS_PATH . 'dist/' );

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

Core::setup();
