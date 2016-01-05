=== Plugin Name ===
Contributors: icanlocalize, jozik
Donate link: http://wpml.org/documentation/related-projects/buddypress-multilingual/
Tags: i18n, translation, localization, language, multilingual, WPML, BuddyPress
Requires at least: 3.0
Tested up to: 4.3.1
Stable tag: 1.5.2

BuddyPress Multilingual allows BuddyPress sites to run fully multilingual using the WPML plugin.

== Description ==

The plugin allows building multilingual BuddyPress sites. It works with single-site or multi-site BuddyPress installations. Both the main site and child blogs can run multilingual.

Guest blogs can choose their language and create multilingual contents. Additionally, each guest can choose the admin language individually .

This plugin requires [WPML](http://wpml.org/). It uses WPML's language API and hooks to BuddyPress to make it multilingual.

Requirements:

* WPML 3.0.x or higher. You must enable 'languages per directories' or 'languages per domain'.
* Supports BuddyPress versions up to 2.x

= Features =

 * Enables multilingual BuddyPress components
 * Filters all links to maintain right language selection
 * Allows translation of profile fields

= New! Customize BuddyPress further using Types and Views =

BPML is now compatible with [Types - The Custom Types and Custom Fields Plugin](http://wp-types.com/home/types-manage-post-types-taxonomy-and-custom-fields/) and [Views - The Custom Content Display Plugin](http://wp-types.com/home/views-create-elegant-displays-for-your-content/). Types and Views allow you to customize BuddyPress further by controlling custom content and displaying it any way you choose.

= Need Support? =

Please submit support requests to **[WPML forum](http://wpml.org/forums/forum/english-support/)**. Remember to report:

* The versions of BuddyPress, WPML and WordPress that you're using.
* A URL to your site, where we can see the problem happening.
* A description of what you expect to see and what you're seeing in practice.

== Installation ==

1. Unzip and upload contents of sitepress-bp.zip file to the `/wp-content/plugins/` directory.
2. Activate the plugin.
3. Enable WPML and BuddyPress.

== Frequently Asked Questions ==

= Why cannot I use language as parameter? =

Support for language as parameter will not be added soon as we're looking for solutions other than filtering all kinds of BuddyPress navigation and action links.

== Screenshots ==

1. Multilingual main BuddyPress page.

== Changelog ==

= 1.5.2 =
* Fixed strict standards warning for PHP 5.6
* Fixed regex for stripping language from bp_uri
* Fixed field tabs on profile screen
* Fixed profile view for radio and checkboxes fields 
* Fixed profile view for fields that have values starting with number 
* Added ability to switch off verbose rules 

= 1.5.1 =
* Improved WPML API usage
* Added BP XProfile translating Field Group names and descriptions
* Added BP XProfile registered strings cleanup
* Fixed WPML language switcher on member page
* Removed deprecated code and debug output

= 1.5 =
* Supports BP 2.x
* Supports languages per domain
* Bug fixes:
* Language switcher does not work on BuddyPress pages
* Error while creating a group from a second language
* Not able to switch to non-default language's home page
* Footer language switcher preview is missing when BuddyPress Multilingual is active
* Not able to enable debug mode in Settings > BPML
* Can't crop avatar in other language version
* Buddypress Profile URL is not correct
* Member link in the activity page is wrong
* View link in Members and Profile points to a wrong page
* Cleared a bunch of notices

= 1.4.2 =
* Support BP 1.6.x
* Bug fixes:
* Language switcher is filtering too much
* JS crop doesn't work on a secondary language
* Sitewide activity widget breaks when BPML is enabled
* Language switcher is not redirecting correctly
* Members page doesn't have language parameter
* Translation for activity streams was disabled because it relied on the discontinued Google translate API. We can bring it back with paid Google translation. Visit BuddyPress Multilingual forum page and leave a comment. We will be looking at these comments and see if there is real interest in this.

= 1.4.1 =
* Support BP 1.6.x
* All translated/duplicated pages works properly
* Pages widget doesn't exclude translated BuddyPress pages

= 1.3.0 =
* Support BP 1.5.x
* Language selector doesn't appear on the home page when a page is selected as the front page
* Small fix on main navigation menu

= 1.2.1 =
* Supports BP 1.2

= 1.1.0 =
* Added translation support for XProfile fields

= 1.0.1 =
* Supports BuddyPress 1.2.8 and WP Network mode
* Added Google translation and translation control for BuddyPress activities

= 1.0.0 =
* Supports BuddyPress 1.2

= 0.9.2 =
* Bugfixes

= 0.9.1 =
* Bugfixes

= 0.9 =
* First public release. Supports BuddyPress 1.0

= 0.1.0 =
* Developers version, not recommended for production sites - feedback welcome!

== Upgrade Notice ==

= 1.1.0 =
* Runs on BuddyPress 1.2.8

= 1.3.0 =
* Runs with BuddyPress 1.5.x

= 1.4.0 =
* Updated for BuddyPress 1.6.x
