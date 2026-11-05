<?php
/**
 * Frontend Gallery Integration Class
 *
 * @package WooCommerce_Video_Product_Gallery
 */

defined( 'ABSPATH' ) || exit;

class Woo_Video_Gallery_Frontend {

    /**
     * Singleton instance.
     *
     * @var Woo_Video_Gallery_Frontend|null
     */
    private static $instance = null;

    /**
     * Get singleton instance.
     *
     * @return Woo_Video_Gallery_Frontend
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct() {
        add_action( 'woocommerce_product_thumbnails', array( $this, 'render_video_slide' ), 30 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
    }

    /**
     * Get video data for current product.
     *
     * @return array|false
     */
    public function get_current_video_data() {
        static $cached_data = null;
        if ( null !== $cached_data ) {
            return $cached_data;
        }

        $product_id = 0;
        global $product;
        if ( $product && is_a( $product, 'WC_Product' ) ) {
            $product_id = $product->get_id();
        } elseif ( is_singular( 'product' ) ) {
            $product_id = get_the_ID();
        }

        if ( ! $product_id ) {
            $cached_data = false;
            return false;
        }

        $youtube_url = get_post_meta( $product_id, '_product_youtube_url', true );
        if ( empty( $youtube_url ) ) {
            $cached_data = false;
            return false;
        }

        $video_id = woo_video_gallery_extract_id( $youtube_url );
        if ( empty( $video_id ) ) {
            $cached_data = false;
            return false;
        }

        $thumbnail_id = absint( get_post_meta( $product_id, '_product_video_thumbnail_id', true ) );

        $cached_data = array(
            'product_id'   => $product_id,
            'video_id'     => $video_id,
            'thumbnail_id' => $thumbnail_id,
        );

        return $cached_data;
    }

    /**
     * Enqueue CSS and JS assets on single product pages with a video configured.
     */
    public function enqueue_frontend_assets() {
        if ( ! $this->get_current_video_data() ) {
            return;
        }

        wp_enqueue_style(
            'woo-video-gallery-frontend-css',
            WOO_VIDEO_GALLERY_URL . 'assets/css/frontend.css',
            array(),
            WOO_VIDEO_GALLERY_VERSION
        );

        wp_enqueue_script(
            'woo-video-gallery-frontend-js',
            WOO_VIDEO_GALLERY_URL . 'assets/js/frontend.js',
            array( 'jquery' ),
            WOO_VIDEO_GALLERY_VERSION,
            true
        );
    }

    /**
     * Output the YouTube video slide inside the WooCommerce product gallery wrapper.
     */
    public function render_video_slide() {
        $data = $this->get_current_video_data();
        if ( ! $data ) {
            return;
        }

        $video_id     = $data['video_id'];
        $thumbnail_id = $data['thumbnail_id'];

        // Determine Thumbnail URLs
        if ( $thumbnail_id && wp_attachment_is_image( $thumbnail_id ) ) {
            $full_thumb_url = wp_get_attachment_image_url( $thumbnail_id, 'large' );
            if ( ! $full_thumb_url ) {
                $full_thumb_url = wp_get_attachment_image_url( $thumbnail_id, 'full' );
            }
            $nav_thumb_url = wp_get_attachment_image_url( $thumbnail_id, 'woocommerce_gallery_thumbnail' );
            if ( ! $nav_thumb_url ) {
                $nav_thumb_url = wp_get_attachment_image_url( $thumbnail_id, 'thumbnail' );
            }
        } else {
            $full_thumb_url = 'https://img.youtube.com/vi/' . esc_attr( $video_id ) . '/hqdefault.jpg';
            $nav_thumb_url  = 'https://img.youtube.com/vi/' . esc_attr( $video_id ) . '/hqdefault.jpg';
        }

        if ( empty( $full_thumb_url ) ) {
            $full_thumb_url = 'https://img.youtube.com/vi/' . esc_attr( $video_id ) . '/hqdefault.jpg';
        }
        if ( empty( $nav_thumb_url ) ) {
            $nav_thumb_url = $full_thumb_url;
        }
        ?>
        <div data-thumb="<?php echo esc_url( $nav_thumb_url ); ?>"
             data-thumb-alt="<?php echo esc_attr__( 'Product Video', 'woo-video-product-gallery' ); ?>"
             class="woocommerce-product-gallery__image yt-video-slide"
             data-yt-id="<?php echo esc_attr( $video_id ); ?>">
            <div class="yt-gallery-slide-card">
                <!-- Poster view with Play button -->
                <div class="yt-gallery-poster" role="button" tabindex="0" aria-label="<?php echo esc_attr__( 'Play product video', 'woo-video-product-gallery' ); ?>">
                    <img src="<?php echo esc_url( $full_thumb_url ); ?>"
                         alt="<?php echo esc_attr__( 'Product Video', 'woo-video-product-gallery' ); ?>"
                         class="yt-gallery-poster-img"
                         loading="lazy" />
                    <div class="yt-gallery-play-btn" title="<?php echo esc_attr__( 'Play Video', 'woo-video-product-gallery' ); ?>">
                        <svg viewBox="0 0 68 48" width="68" height="48" class="yt-play-svg">
                            <path class="yt-play-bg" d="M66.52,7.74c-0.78-2.93-2.49-5.41-5.42-6.19C55.79,.13,34,0,34,0S12.21,.13,6.9,1.55 C3.97,2.33,2.27,4.81,1.48,7.74C0.06,13.05,0,24,0,24s0.06,10.95,1.48,16.26c0.78,2.93,2.49,5.41,5.42,6.19 C12.21,47.87,34,48,34,48s21.79-0.13,27.1-1.55c2.93-0.78,4.64-3.26,5.42-6.19C67.94,34.95,68,24,68,24S67.94,13.05,66.52,7.74z"></path>
                            <path d="M 45,24 27,14 27,34" fill="#ffffff"></path>
                        </svg>
                    </div>
                </div>
                <!-- Video Player wrapper, populated dynamically on play -->
                <div class="yt-gallery-player-wrap" style="display: none;">
                    <button type="button" class="yt-gallery-close-btn" aria-label="<?php echo esc_attr__( 'Close video', 'woo-video-product-gallery' ); ?>">&times;</button>
                    <div class="yt-gallery-iframe-holder"></div>
                </div>
            </div>
        </div>
        <?php
    }
}
