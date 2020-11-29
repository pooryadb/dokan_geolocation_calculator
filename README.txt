=== Plugin Name ===
Contributors: poorya.db
Donate link: http://romroid.ir
Tags: wordpress, dokan, plugin, geolocation, rest-api, api, php
Requires at least:
Tested up to: 5.4.4
Stable tag: 5.4.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Admin set coordinates for each store and plugin find nearest store from user location in a API request.

== Description ==

This wordpress plugin adds menu to beside dokan-menu and admin can set latitude, longitude, acceptable diameter for each store.
Also, set new WP-api endpoint that accept lat & lng args as user location and response only nearest store name or message.

== Installation ==

1. Upload `zip-file` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. go to `Dokan Stores PDB` menu and set latitude, longitude, diameter, enabled options
4. enable WP-API and set message for no store found condition

Prerequisites:
- installed Dokan-lite & Dokan-pro.
- WP-API enabled.

== Frequently Asked Questions ==

= Why Dokan-pro must be installed? =

every vendor/store must have `City` field that Dokan-pro provide this option.

== Screenshots ==


== Changelog ==

== Upgrade Notice ==
