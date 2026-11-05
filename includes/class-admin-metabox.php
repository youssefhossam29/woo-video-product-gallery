<?php
/**
 * Admin Meta Box Class
 *
 * @package WooCommerce_Video_Product_Gallery
 */

defined( 'ABSPATH' ) || exit;

class Woo_Video_Gallery_Admin_Metabox {

    /**
     * Singleton instance.
     *
     * @var Woo_Video_Gallery_Admin_Metabox|null
     */
    private static $instance = null;

    /**
     * Get singleton instance.
     *
     * @return Woo_Video_Gallery_Admin_Metabox
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
        add_action( 'add_meta_boxes', array( $this, 'register_meta_box' ), 25 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'save_post_product', array( $this, 'save_meta_box_data' ), 10, 2 );
    }

    /**
     * Register Meta Box on Product edit screen.
     */
    public function register_meta_box() {
        add_meta_box(
            'woo_video_gallery_metabox',
            __( 'Product Video (YouTube)', 'woo-video-product-gallery' ),
            array( $this, 'render_meta_box_html' ),
            'product',
            'side',
            'default'
        );
    }

    /**
     * Enqueue Admin scripts and styles.
     *
     * @param string $hook Page hook name.
     */
    public function enqueue_admin_assets( $hook ) {
        if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
            return;
        }

        $screen = get_current_screen();
        if ( ! $screen || 'product' !== $screen->post_type ) {
            return;
        }

        // Enqueue WP Media modal scripts
        wp_enqueue_media();

        // Enqueue Admin CSS
        wp_enqueue_style(
            'woo-video-gallery-admin-css',
            WOO_VIDEO_GALLERY_URL . 'assets/css/admin.css',
            array(),
            WOO_VIDEO_GALLERY_VERSION
        );

        // Enqueue Admin JS
        wp_enqueue_script(
            'woo-video-gallery-admin-js',
            WOO_VIDEO_GALLERY_URL . 'assets/js/admin.js',
            array( 'jquery' ),
            WOO_VIDEO_GALLERY_VERSION,
            true
        );

        // Localize strings for media modal
        wp_localize_script(
            'woo-video-gallery-admin-js',
            'wooVideoGalleryL10n',
            array(
                'modalTitle'     => __( 'Select Video Thumbnail Image', 'woo-video-product-gallery' ),
                'modalButton'    => __( 'Use This Image', 'woo-video-product-gallery' ),
                'noThumbnail'    => __( 'No thumbnail selected', 'woo-video-product-gallery' ),
                'defaultYtThumb' => __( 'Default YouTube Thumbnail', 'woo-video-product-gallery' ),
            )
        );
    }

    /**
     * Render the Meta Box HTML with English labels and clean UI.
     *
     * @param WP_Post $post Current post object.
     */
    public function render_meta_box_html( $post ) {
        wp_nonce_field( 'woo_video_gallery_save_meta', 'woo_video_gallery_nonce' );

        $youtube_url  = get_post_meta( $post->ID, '_product_youtube_url', true );
        $thumbnail_id = get_post_meta( $post->ID, '_product_video_thumbnail_id', true );
        $thumb_url    = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'medium' ) : '';
        $video_id     = woo_video_gallery_extract_id( $youtube_url );
        ?>
        <div class="woo-yt-admin-metabox">
            <!-- YouTube URL Field -->
            <div class="woo-yt-field-group">
                <label for="_product_youtube_url" class="woo-yt-label">
                    <?php esc_html_e( 'YouTube Video URL', 'woo-video-product-gallery' ); ?>
                </label>
                <input type="text"
                       id="_product_youtube_url"
                       name="_product_youtube_url"
                       value="<?php echo esc_attr( $youtube_url ); ?>"
                       placeholder="https://www.youtube.com/watch?v=..."
                       class="widefat woo-yt-input" />
                <p class="description woo-yt-desc">
                    <?php esc_html_e( 'Enter a YouTube video URL (supports standard watch, youtu.be, shorts, and embed links).', 'woo-video-product-gallery' ); ?>
                </p>
            </div>

            <hr class="woo-yt-divider" />

            <!-- Custom Thumbnail Field -->
            <div class="woo-yt-field-group">
                <label class="woo-yt-label">
                    <?php esc_html_e( 'Video Thumbnail', 'woo-video-product-gallery' ); ?>
                </label>

                <input type="hidden"
                       id="_product_video_thumbnail_id"
                       name="_product_video_thumbnail_id"
                       value="<?php echo esc_attr( $thumbnail_id ); ?>" />

                <!-- Preview Area -->
                <div id="woo-yt-thumb-preview" class="woo-yt-preview-box">
                    <?php if ( $thumb_url ) : ?>
                        <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php esc_attr_e( 'Custom Thumbnail', 'woo-video-product-gallery' ); ?>" />
                    <?php elseif ( $video_id ) : ?>
                        <img src="https://img.youtube.com/vi/<?php echo esc_attr( $video_id ); ?>/hqdefault.jpg" alt="<?php esc_attr_e( 'YouTube Thumbnail', 'woo-video-product-gallery' ); ?>" />
                        <span class="woo-yt-fallback-badge"><?php esc_html_e( 'Default YouTube Thumbnail', 'woo-video-product-gallery' ); ?></span>
                    <?php else : ?>
                        <span class="woo-yt-placeholder-text"><?php esc_html_e( 'No thumbnail selected', 'woo-video-product-gallery' ); ?></span>
                    <?php endif; ?>
                </div>

                <!-- Action Buttons -->
                <div class="woo-yt-btn-row">
                    <button type="button" class="button" id="woo-yt-select-thumb-btn">
                        <?php esc_html_e( 'Choose Custom Thumbnail', 'woo-video-product-gallery' ); ?>
                    </button>
                    <button type="button" class="button button-link-delete" id="woo-yt-remove-thumb-btn" style="<?php echo empty( $thumbnail_id ) ? 'display: none;' : ''; ?>">
                        <?php esc_html_e( 'Remove Thumbnail', 'woo-video-product-gallery' ); ?>
                    </button>
                </div>

                <p class="description woo-yt-desc">
                    <?php esc_html_e( 'If left empty, YouTube\'s default thumbnail will be used automatically.', 'woo-video-product-gallery' ); ?>
                </p>
            </div>
        </div>
        <?php
    }

    /**
     * Save Meta Box data.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     */
    public function save_meta_box_data( $post_id, $post ) {
        // Nonce check
        if ( ! isset( $_POST['woo_video_gallery_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['woo_video_gallery_nonce'] ) ), 'woo_video_gallery_save_meta' ) ) {
            return;
        }

        // Autosave check
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Capability check
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Save or delete YouTube URL
        if ( isset( $_POST['_product_youtube_url'] ) ) {
            $raw_url = sanitize_text_field( wp_unslash( $_POST['_product_youtube_url'] ) );
            $url     = esc_url_raw( trim( $raw_url ) );
            if ( ! empty( $url ) ) {
                update_post_meta( $post_id, '_product_youtube_url', $url );
            } else {
                delete_post_meta( $post_id, '_product_youtube_url' );
            }
        }

        // Save or delete Thumbnail ID
        if ( isset( $_POST['_product_video_thumbnail_id'] ) ) {
            $thumb_id = absint( $_POST['_product_video_thumbnail_id'] );
            if ( $thumb_id > 0 ) {
                update_post_meta( $post_id, '_product_video_thumbnail_id', $thumb_id );
            } else {
                delete_post_meta( $post_id, '_product_video_thumbnail_id' );
            }
        }
    }
}
