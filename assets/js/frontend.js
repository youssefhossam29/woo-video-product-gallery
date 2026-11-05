/**
 * WooCommerce Video Product Gallery - Frontend JavaScript
 */

jQuery(function($) {
    function initYouTubeGallery() {
        var $gallery = $('.woocommerce-product-gallery');
        if (!$gallery.length) {
            return;
        }

        var $videoSlide = $gallery.find('.yt-video-slide');
        if (!$videoSlide.length) {
            return;
        }

        var videoId = $videoSlide.data('yt-id');
        if (!videoId) {
            return;
        }

        // Tag the corresponding thumbnail in flexslider navigation with the video badge
        function tagThumbnail() {
            var $allSlides = $gallery.find('.woocommerce-product-gallery__wrapper > .woocommerce-product-gallery__image');
            var videoIndex = $allSlides.index($videoSlide);
            if (videoIndex !== -1) {
                var $thumbs = $gallery.find('.flex-control-thumbs li');
                if ($thumbs.length > videoIndex) {
                    $thumbs.eq(videoIndex).addClass('yt-video-nav-thumb');
                }
            }
        }

        tagThumbnail();
        setTimeout(tagThumbnail, 150);
        setTimeout(tagThumbnail, 500);
        setTimeout(tagThumbnail, 1200);

        // Play Video
        function playVideo() {
            var $playerWrap = $videoSlide.find('.yt-gallery-player-wrap');
            var $iframeHolder = $videoSlide.find('.yt-gallery-iframe-holder');
            var $poster = $videoSlide.find('.yt-gallery-poster');

            if (!$iframeHolder.find('iframe').length) {
                var embedUrl = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(videoId) + '?autoplay=1&rel=0&playsinline=1&enablejsapi=1';
                var iframeHtml = '<iframe src="' + embedUrl + '" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                $iframeHolder.html(iframeHtml);
            }
            $poster.hide();
            $playerWrap.show();
        }

        // Stop Video
        function stopVideo() {
            var $playerWrap = $videoSlide.find('.yt-gallery-player-wrap');
            var $iframeHolder = $videoSlide.find('.yt-gallery-iframe-holder');
            var $poster = $videoSlide.find('.yt-gallery-poster');

            $iframeHolder.empty();
            $playerWrap.hide();
            $poster.show();
        }

        // Event: Click on Poster / Play Button
        $videoSlide.off('click.ytPlay').on('click.ytPlay', '.yt-gallery-poster', function(e) {
            e.preventDefault();
            e.stopPropagation();
            playVideo();
        });

        // Event: Click Close Button (X)
        $videoSlide.off('click.ytClose').on('click.ytClose', '.yt-gallery-close-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            stopVideo();
        });

        // Event: Click on Thumbnails in Navigation Strip
        $gallery.off('click.ytThumbNav', '.flex-control-thumbs li').on('click.ytThumbNav', '.flex-control-thumbs li', function() {
            var $thisThumb = $(this);
            if ($thisThumb.hasClass('yt-video-nav-thumb')) {
                // Smooth autoplay when user selects the video thumbnail
                setTimeout(function() {
                    playVideo();
                }, 250);
            } else {
                // Stop playback when selecting an image thumbnail
                stopVideo();
            }
        });

        // Event: Direction Arrows Navigation
        $gallery.off('click.ytDirNav', '.flex-direction-nav a').on('click.ytDirNav', '.flex-direction-nav a', function() {
            setTimeout(function() {
                var $activeSlide = $gallery.find('.flex-active-slide');
                if ($activeSlide.length && !$activeSlide.hasClass('yt-video-slide')) {
                    stopVideo();
                }
            }, 300);
        });

        // MutationObserver: Automatically stop playback when user swipes away on mobile
        if (window.MutationObserver) {
            var wrapperEl = $gallery.find('.woocommerce-product-gallery__wrapper')[0];
            if (wrapperEl) {
                var observer = new MutationObserver(function() {
                    if ($videoSlide.length && !$videoSlide.hasClass('flex-active-slide')) {
                        if ($videoSlide.find('.yt-gallery-player-wrap').is(':visible')) {
                            stopVideo();
                        }
                    }
                });
                observer.observe(wrapperEl, { attributes: true, subtree: true, attributeFilter: ['class'] });
            }
        }

        // Prevent PhotoSwipe lightbox from opening on the video slide
        $videoSlide.off('click.ytPreventPS').on('click.ytPreventPS', function(e) {
            e.stopPropagation();
        });
    }

    initYouTubeGallery();
    $(window).on('load', initYouTubeGallery);
    $(document).on('wc-product-gallery-after-init', initYouTubeGallery);
});
