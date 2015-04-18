=== Pay2View  ===
Contributors: butterflymedia
Donate link: http://getbutterfly.com/wordpress-plugins-free/
Tags: paypal, hide content, pay to see content, free for members, shortcode
Requires at least: 4.0
Tested up to: 4.1.1
Stable tag: 0.3
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

== Description ==

Let your users pay to view content. Use it to hide download links, images, paragraphs or other shortcodes. This plugin allows the administrator to use a shortcode and hide certain content from guests until payment is completed. Uses PayPal.

To hide post/page content (text, image, shortcode) use the `[paypal]Your content here[/paypal]` shortcode. It will use the default price and currency.
To specify a price for your hidden content and override the default one, use the `[paypal amount=11]Your content here[/paypal]` shortcode.
Place another shortcode inside the Pay2View payment shortcode: `[paypal][another-shortcode][/paypal]`.

Only guests and non-members see the payment button. Members always see the hidden content.

== Installation ==

1. Upload the folder `pay2view` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings | PayPal Pay2View and configure your plugin

== Changelog ==

= 0.3 =
* UPDATE: Merged Raspberry Edition version

= 0.15 =
* Added license link
* Added donate link

= 0.14 =
* Fixed missing email address inside PayPal form

= 0.13 =
* Added capabilities
* Extended translation strings
* Fixed several validation options

= 0.12 =
* Added internationalization
* Added HTML5 field validation for email
* Updated currency list
* Updated readme.txt with correct license version
* Updated tags list

= 0.11 =
* Initial release (alpha testing only)
