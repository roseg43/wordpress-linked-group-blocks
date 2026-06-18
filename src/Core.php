<?php
/**
 * @package LinkedGroupBlocks
 */

namespace LinkedGroupBlocks;

class Core {

	/**
	 * Register all hooks.
	 *
	 * @return void
	 */
	public static function setup(): void {
		add_action( 'enqueue_block_editor_assets', [ self::class, 'enqueue_block_editor_assets' ] );
		add_action( 'init', [ self::class, 'enqueue_frontend_styles' ] );
		add_filter( 'render_block_core/group', [ self::class, 'render_group_link' ], 10, 2 );
	}

	/**
	 * Enqueue editor script and styles.
	 *
	 * @return void
	 */
	public static function enqueue_block_editor_assets(): void {
		wp_enqueue_script(
			'linked-group-blocks-editor-script',
			LINKED_GROUP_BLOCKS_DIST_URL . 'js/block-editor-assets.js',
			Helpers::get_asset_info( 'block-editor-assets', 'dependencies' ),
			Helpers::get_asset_info( 'block-editor-assets', 'version' ),
			true
		);

		wp_enqueue_style(
			'linked-group-blocks-editor-style',
			LINKED_GROUP_BLOCKS_DIST_URL . 'css/block-editor-assets.css',
			[],
			Helpers::get_asset_info( 'block-editor-assets', 'version' ) ?? LINKED_GROUP_BLOCKS_VERSION
		);
	}

	/**
	 * Register frontend block styles so they only load when a Group block is on the page.
	 *
	 * @return void
	 */
	public static function enqueue_frontend_styles(): void {
		wp_enqueue_block_style(
			'core/group',
			[
				'handle' => 'linked-group-blocks-frontend-style',
				'src'    => LINKED_GROUP_BLOCKS_DIST_URL . 'css/frontend.css',
				'path'   => LINKED_GROUP_BLOCKS_DIST_PATH . 'css/frontend.css',
				'ver'    => LINKED_GROUP_BLOCKS_VERSION,
			]
		);
	}

	/**
	 * Inject an overlay anchor into Group blocks that have a link set.
	 *
	 * @param string               $block_content Rendered block HTML.
	 * @param array<string, mixed> $block         Block data including attributes.
	 * @return string
	 */
	public static function render_group_link( string $block_content, array $block ): string {
		$link_to_current_post = ! empty( $block['attrs']['groupLinkToCurrentPost'] );

		$url = $link_to_current_post
			? get_permalink()
			: ( $block['attrs']['groupLinkUrl'] ?? '' );

		if ( empty( $url ) ) {
			return $block_content;
		}

		$open_in_new_tab = ! empty( $block['attrs']['groupLinkOpenInNewTab'] );
		$target_attr     = $open_in_new_tab ? ' target="_blank" rel="noopener noreferrer"' : '';

		$processor = new \WP_HTML_Tag_Processor( $block_content );

		if ( ! $processor->next_tag() ) {
			return $block_content;
		}

		$processor->add_class( 'has-group-link' );
		$block_content = $processor->get_updated_html();

		$overlay = sprintf(
			'<a href="%s" class="wp-block-group__link-overlay" aria-hidden="true" tabindex="-1"%s></a>',
			esc_url( $url ),
			$target_attr
		);

		return preg_replace( '/(<[^>]+has-group-link[^>]*>)/', '$1' . $overlay, $block_content, 1 );
	}
}
