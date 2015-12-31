=== Library ===
Contributors: developdaly, mikemanger, adamkrawiec
Tags: strings, library, content, shortcodes, dictionary, wp-strings
Requires at least: 3.8.1
Tested up to: 4.4
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create a library of reusable terms (strings) and display their contents anywhere on your site with a shortcode.

== Description ==

Create a library of reusable terms (strings) and display their contents anywhere on your site with a shortcode.

Library lets you create a library of reusable terms (or strings) without any code necessary and access a term's content with a shortcode.

You can add a term (just like you'd add a post or page) called "Copyright Line" and its content could be "©2014 Your Company" and then use that anywhere on the site with `[library term="copyright-line"]` or `<?php do_shortcode( '[library term="copyright-line"] ); ?>`.

Because the library terms are a post type they inherit WordPress features like revision history, draft/scheduled/published, ability to change the term slug, full HTML, and so on.

**Business Cases**

These are real-world examples of how this plugin is being used or could be used. Each of these uses a shortcode to display the most up-to-date version of a term's content.

A financial institution is advertising an interest rate across many pages on a large website. There are instances of this percentage in content and in templates. Within a matter of seconds all of these numbers can be changed rather than crossing your fingers and hoping you caught all of the rates.

In order to quickly change the price of a fluctuating product a company put their price in a term to update across the whole site.

A health insurance company uses the same disclosures on many pages and UI contexts and keeping them compliant with changing regulations was difficult until they kept them stored in the Library.

The copyright line in the footer of a website might change sometimes to reflect a new year, a new trademark, new ownership, etc.

[**Contribute on Github**](https://github.com/developdaly/library) | [**Report Bugs**](https://github.com/developdaly/library/issues?labels=bug&milestone=&page=1&state=open)

== Installation ==

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'library'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `library.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `library.zip`
2. Extract the `library` directory to your computer
3. Upload the `library` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard

== Changelog ==

= 1.1.0 =
* Adds a media button to easily insert shortcodes
* Improved error handling

= 1.0.2 =
* Localized admin screen strings
* Escaped admin screen strings

= 1.0.1 =
* Updated base language file
* Typo fixes
* Removed blank index.php files

= 1.0 =
* First version
