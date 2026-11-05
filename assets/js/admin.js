/**
 * WooCommerce Video Product Gallery - Admin JavaScript
 */

jQuery(document).ready(function($) {
    var mediaFrame;
    var $thumbInput = $('#_product_video_thumbnail_id');
    var $previewBox = $('#woo-yt-thumb-preview');
    var $removeBtn  = $('#woo-yt-remove-thumb-btn');
    var $urlInput   = $('#_product_youtube_url');
    var l10n        = window.wooVideoGalleryL10n || {
        modalTitle: 'Select Video Thumbnail Image',
        modalButton: 'Use This Image',
        noThumbnail: 'No thumbnail selected',
        defaultYtThumb: 'Default YouTube Thumbnail'
    };

    // Helper: Extract YouTube ID
    function extractVideoId(url) {
        url = (url || '').trim();
        if (/^[a-zA-Z0-9_-]{11}$/.test(url)) {
            return url;
        }
        var match = url.match(/(?:youtube(?:-nocookie)?\.com/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=|shorts\/)|youtu\.be\/)([^"&?\/\s]{11})/i);
        return (match && match[1]) ? match[1] : '';
    }

    // Update fallback YouTube preview when URL changes
    function updateFallbackPreview() {
        var videoId = extractVideoId($urlInput.val());
        if (videoId) {
            $previewBox.html(
                '<img src="https://img.youtube.com/vi/' + videoId + '/hqdefault.jpg" alt="Preview" />' +
                '<span class="woo-yt-fallback-badge">' + l10n.defaultYtThumb + '</span>'
            );
        } else {
            $previewBox.html('<span class="woo-yt-placeholder-text">' + l10n.noThumbnail + '</span>');
        }
    }

    // Media modal: Choose / Upload Image
    $('#woo-yt-select-thumb-btn').on('click', function(e) {
        e.preventDefault();

        if (mediaFrame) {
            mediaFrame.open();
            return;
        }

        mediaFrame = wp.media({
            title: l10n.modalTitle,
            button: { text: l10n.modalButton },
            multiple: false,
            library: { type: 'image' }
        });

        mediaFrame.on('select', function() {
            var attachment = mediaFrame.state().get('selection').first().toJSON();
            var imgUrl = (attachment.sizes && attachment.sizes.medium) ? attachment.sizes.medium.url : attachment.url;
            $thumbInput.val(attachment.id);
            $previewBox.html('<img src="' + imgUrl + '" alt="Selected Thumbnail" />');
            $removeBtn.show();
        });

        mediaFrame.open();
    });

    // Remove Custom Thumbnail
    $removeBtn.on('click', function(e) {
        e.preventDefault();
        $thumbInput.val('');
        $removeBtn.hide();
        updateFallbackPreview();
    });

    // Listen to URL input changes
    $urlInput.on('input change', function() {
        if (!$thumbInput.val()) {
            updateFallbackPreview();
        }
    });
});
