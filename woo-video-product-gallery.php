<?php
/**
 * Plugin Name: WooCommerce Video Product Gallery
 * Plugin URI:  https://github.com/youssefhossam29/woo-video-product-gallery
 * Description: Seamlessly integrates YouTube videos into the WooCommerce single product image gallery with custom thumbnail support and responsive in-gallery playback.
 * Version:     1.0.1
 * Author:      Youssef Hossam
 * Author URI:  https://github.com/youssefhossam29
 * Text Domain: woo-video-product-gallery
 * Domain Path: /languages
 * WC requires at least: 5.0
 * WC tested up to: 11.1
 * Requires PHP: 7.4
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) || exit;

// Define plugin constants
define( 'WOO_VIDEO_GALLERY_VERSION', '1.0.1' );
define( 'WOO_VIDEO_GALLERY_FILE', __FILE__ );
define( 'WOO_VIDEO_GALLERY_PATH', plugin_dir_path( __FILE__ ) );
define( 'WOO_VIDEO_GALLERY_URL', plugin_dir_url( __FILE__ ) );

/**
 * Declare compatibility with WooCommerce HPOS (High-Performance Order Storage).
 */
add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
            'custom_order_tables',
            WOO_VIDEO_GALLERY_FILE,
            true
        );
    }
} );

/**
 * Check if WooCommerce is active before initializing.
 */
function woo_video_gallery_check_dependencies() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', 'woo_video_gallery_missing_wc_notice' );
        return false;
    }
    return true;
}

/**
 * Admin notice when WooCommerce is missing.
 */
function woo_video_gallery_missing_wc_notice() {
    ?>
    <div class="notice notice-error">
        <p><?php esc_html_e( 'WooCommerce Video Product Gallery requires WooCommerce to be installed and active.', 'woo-video-product-gallery' ); ?></p>
    </div>
    <?php
}

/**
 * Initialize the plugin.
 */
function woo_video_gallery_init() {
    if ( ! woo_video_gallery_check_dependencies() ) {
        return;
    }

    // Include module classes
    require_once WOO_VIDEO_GALLERY_PATH . 'includes/class-admin-metabox.php';
    require_once WOO_VIDEO_GALLERY_PATH . 'includes/class-frontend-gallery.php';

    // Initialize modules
    if ( is_admin() ) {
        Woo_Video_Gallery_Admin_Metabox::get_instance();
    }
    Woo_Video_Gallery_Frontend::get_instance();
}
add_action( 'plugins_loaded', 'woo_video_gallery_init' );

/**
 * Helper to extract YouTube video ID from various URL formats.
 *
 * @param string $url YouTube URL or raw video ID.
 * @return string Extracted 11-character video ID, or empty string.
 */
function woo_video_gallery_extract_id( $url ) {
    $url = trim( (string) $url );
    if ( empty( $url ) ) {
        return '';
    }

    if ( preg_match( '/^[a-zA-Z0-9_-]{11}$/', $url ) ) {
        return $url;
    }

    $pattern = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=|shorts/)|youtu\.be/)([^"&?/\s]{11})%i';
    if ( preg_match( $pattern, $url, $match ) ) {
        return $match[1];
    }

    return '';
}
