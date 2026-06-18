<?php
/**
 * @package LinkedGroupBlocks
 */

namespace LinkedGroupBlocks;

class Helpers {

	/**
	 * Get asset info from the generated .asset.php file.
	 *
	 * @param string      $slug      Asset slug (filename without extension).
	 * @param string|null $attribute 'dependencies' or 'version'. Returns full array if null.
	 * @return mixed
	 */
	public static function get_asset_info( string $slug, ?string $attribute = null ): mixed {
		$asset = null;

		if ( file_exists( LINKED_GROUP_BLOCKS_DIST_PATH . 'js/' . $slug . '.asset.php' ) ) {
			$asset = require LINKED_GROUP_BLOCKS_DIST_PATH . 'js/' . $slug . '.asset.php';
		} elseif ( file_exists( LINKED_GROUP_BLOCKS_DIST_PATH . 'css/' . $slug . '.asset.php' ) ) {
			$asset = require LINKED_GROUP_BLOCKS_DIST_PATH . 'css/' . $slug . '.asset.php';
		}

		if ( null === $asset ) {
			return null;
		}

		if ( ! empty( $attribute ) && isset( $asset[ $attribute ] ) ) {
			return $asset[ $attribute ];
		}

		return $asset;
	}
}
