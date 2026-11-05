# WooCommerce Video Product Gallery

Seamlessly integrate YouTube videos into your WooCommerce single product gallery with custom thumbnail support, native FlexSlider compatibility, and responsive in-gallery playback.

## System Requirements

| Requirement | Minimum Supported |
| :--- | :--- |
| **WordPress** | 5.8 or higher |
| **WooCommerce** | 5.0 or higher (Tested up to 11.1) |
| **PHP Version** | 7.4 or higher (PHP 8.0 - 8.3 fully supported) |
| **Browser** | Chrome, Firefox, Safari, Edge, or any modern standards-compliant browser |
| **Theme / Gallery** | Any WooCommerce theme using standard gallery features (FlexSlider / Elementor Product Images) |

## Features

- **Direct YouTube URL Support**: Works with standard links (`watch?v=`), short links (`youtu.be/`), Shorts (`/shorts/`), and embed URLs (`/embed/`).
- **Custom or Automatic Thumbnails**: Select a custom thumbnail image from the WordPress Media Library, or let the plugin automatically fetch YouTube's high-definition `hqdefault` cover image.
- **In-Gallery Playback**: Plays directly inside the main gallery container without opening intrusive popups or leaving the product page.
- **Thumbnail Strip Play Badge**: Displays an attractive red YouTube play icon badge on the video thumbnail in the navigation strip.
- **Smart Playback Controls**:
  - Automatically stops video playback and clears audio when switching to other product images.
  - Supports mobile touch swiping (auto-pause on swipe via MutationObserver).
  - Includes a quick close button (`×`) to return to the cover photo anytime.
- **No Third-Party Bloat**: Built to work natively with WooCommerce's standard gallery templates and Elementor Pro's Product Images widget.
- **Privacy-Friendly**: Uses `youtube-nocookie.com` for embedded player requests.
- **Optimized Performance**: Assets (CSS/JS) only load on product pages that actually have a configured video.

## Installation

1. In your WordPress Admin dashboard, navigate to **Plugins > Add New > Upload Plugin**.
2. Upload the `woo-video-product-gallery.zip` file.
3. Click **Install Now**, then click **Activate**.

## How to Use

1. Go to **Products > All Products** and edit any product.
2. In the right sidebar, locate the **Product Video (YouTube)** meta box.
3. Paste your YouTube video URL into the **YouTube Video URL** field.
4. (Optional) Click **Choose Custom Thumbnail** to select a custom cover photo from your Media Library. If left empty, YouTube's default thumbnail will be used automatically.
5. Click **Update** to save the product.
6. View the product page on your site to test your new video gallery slide!

## License

This project is open-source software licensed under the [GNU General Public License v2.0 or later](LICENSE).
