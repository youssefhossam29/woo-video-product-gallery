<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * Deletes all plugin data, product metadata, and cached transients.
 *
 * @package WooCommerce_Video_Product_Gallery
 */

// If uninstall not called from WordPress, exit.
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/**
 * Cleanup function to remove all plugin-related metadata.
 */
function woo_video_gallery_cleanup() {
    $meta_keys = array(
        '_product_youtube_url',
        '_product_video_thumbnail_id',
        '_product_youtube_id',
    );

    foreach ( $meta_keys as $meta_key ) {
        delete_post_meta_by_key( $meta_key );
    }

    // Flush cache to ensure no stale data remains
    if ( function_exists( 'wp_cache_flush' ) ) {
        wp_cache_flush();
    }
}

// Handle Multisite or Single Site installation
if ( is_multisite() ) {
    $site_ids = get_sites( array( 'fields' => 'ids' ) );
    foreach ( $site_ids as $site_id ) {
        switch_to_blog( $site_id );
        woo_video_gallery_cleanup();
        restore_current_blog();
    }
} else {
    woo_video_gallery_cleanup();
}

