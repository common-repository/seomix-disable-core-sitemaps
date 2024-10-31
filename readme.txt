=== SX Disable WordPress XML sitemaps ===
Contributors: confridin, seokey, seomix
Text Domain: seomix-disable-core-sitemaps      
Donate link: https://www.seomix.fr
Tags: seo,sitemaps,xml,sitemap index,xml sitemap,google
Version: 1.0
Requires at least: 5.5
Tested up to: 6.6
Stable tag: trunk
Requires PHP: 5.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Disable and redirect core WordPress XML sitemaps

== Description ==

WordPress 5.5 add core XML sitemap functionnality, but there is no user interface to disable it or to mange contents within theses files.

Disable WordPress XML sitemaps simply deactivate all WordPress XML sitemaps and redirect them to your home URL, preventing 404 page generation for Google and other Search Engines.

This SEO enhancement is already included in our <a href="https://wordpress.org/plugins/seo-key/">**SEOKEY plugin**</a>. Not only do we deactivate WordPress native XML sitemaps (which are not optimized), but SEOKEY replaces them with much more powerful sitemaps (better crawl, not any harmful content).

More information here about it : <a href="https://www.seomix.fr/wordpress-xml-sitemaps/">Core XML Sitemap Guide</a> (French)

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/seomix-disable-core-sitemaps` directory, or install the plugin through the WordPress plugins screen directly.
2. Just activate the plugin through the 'Plugins' screen in WordPress.
3. You're done

== Frequently Asked Questions ==

= Installation =

Just activate the plugin, and you're done

= Does it redirect old sitemap URL ? =

Yes, it does redirect all sitemap URL to homepage URL (301 redirect). It prevents bad 404 for Google and other search engines.

= Can I redirect to another URL ? =

Yes, you can use a filter for that :

	apply_filters( 'seomix_core_sitemaps_redirect_target', $home, $current_url );

$home is the default redirect URL
$current_url is the current 404 sitemap URL being processed
Exemple usage :

	add_filter( 'seomix_core_sitemaps_redirect_target', 'seomix_function' );
	function seomix_function( $home, $current_url ) {
		return "https://shiny-new-url.fr";
	}

== Screenshots ==


== Changelog ==


== Upgrade Notice ==

