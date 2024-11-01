=== WP Direction Detector ===

Contributors: Fayçal Tirich
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=GEUJA8MV256VE
Tags: language, direction, rtl, ltr, right to left support, translate
Requires at least: 2.0
Tested up to: 4.5.2
Stable tag: 1.2

This plugin auto dectects and apply the right direction (RTL or LTR) on post's titles, bodies and comments.

== Description ==

This plugin auto detects and apply the right direction (RTL or LTR) on post's titles, bodies and comments.

According to WordPress, adding support for language written in a Right To Left (RTL) direction is just a matter of overwriting all the horizontal positioning attributes of your CSS stylesheet in a separate stylesheet file named rtl.css.
But this solution is working only when you have just an RTL blog (or just an LTR blog using the classical style.css).
One of solutions: We will let WordPress applying the same style.css to both of RTL/LTR posts and this plugin will automatically detect the post language to correct its direction.

It's just a beta version that I tested with the WordPress default theme, please let me know your feedback with your own complexed css.

== Installation ==

1. Upload `wp-direction-detector` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Activate for titles, bodies and comments Settings

== Frequently Asked Questions ==

=  =

== Changelog ==

= 1.2 beta=

* The plugin is not working when the theme is using the_title() and get_the_title() inside HTML attributes ((eg: &lt;img alt="&lt;php? the_title(); ?&gt; ...)). you can now disable the plugin for titles and instead use get_directed_title() and get_the_directed_title()

= 1.1 beta=

* First release tested on the default WordPress theme



== Screenshots ==

1. This plugin will apply the right direction when mixing RTL and LTR posts

2. Settings