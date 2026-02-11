=== Post Date Randomizer ===
Plugin Name: Post Date Randomizer
Author: anty
Contributors: wellbeingtips
Donate link: https://www.paypal.com/paypalme/wellbeingsupport/
Tags: post dates, comment dates, random dates, random post dates, random comment dates, bulk edit, schedule posts
Requires at least: 5.0
Tested up to: 6.7
Stable tag: 1.4.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Simple plugin that bulk changes the publication date of published posts and/or approved comments to random dates within a specified time range. Configure options in the "Date Randomizer" settings page in the WordPress dashboard menu.

== Description ==

Post Date Randomizer allows you to bulk change the publication dates of your content to random dates within a specific time range you define. You can choose to randomize dates for published posts, pages, products, custom post, approved comments, or both.

It comes with a simple but powerful settings page ("Date Randomizer" in the admin menu) where you can:

*   Select whether to randomize posts, comments, or both.
*   Set the start and end dates for the randomization range (past or future).
*   For posts, choose the specific post type to affect (auto-detects public post types like posts, pages, products, etc.).
*   For posts, optionally set the "Last Modified" date to match the new random "Published" date.

It supports any date range. Posts with dates randomized to the future will be automatically scheduled for publication. Comment dates are simply updated to the random date within the selected range.

**Important:** This plugin performs irreversible changes to your post and comment dates. Always back up your database before use! If you find this plugin useful, please consider making a donation to support its development.

= Key Features: =

*   Bulk randomize dates for published posts (of a selected post type) and/or approved comments.
*   Choose to randomize only posts, only comments, or both simultaneously.
*   Set a specific date and time range (past or future) for the randomized dates.
*   Automatically schedules posts if their new random date is in the future.
*   Optionally update the post "Last Modified" date to match the new "Published" date.
*   Auto-detects all public post types for selection.
*   Simple settings interface integrated into the WordPress dashboard.
*   Includes donation links to support the plugin author.

== Installation ==

1.  Download the plugin ZIP file.
2.  Log in to your WordPress admin area and go to Plugins > Add New.
3.  Click "Upload Plugin" and choose the ZIP file you downloaded.
4.  Activate the plugin through the 'Plugins' menu in WordPress.
5.  Go to the "Date Randomizer" menu item in your dashboard to configure the settings.
6.  **BACK UP YOUR DATABASE!**
7.  Configure your desired date range and options, click "Save Settings".
8.  Click the "Randomize Selected Item Dates Now" button to perform the randomization.

== Compatibility ==

Recommended for WordPress 5.0 and higher. Tested up to WordPress 6.7.

== Warning ==

The date changes performed by this plugin are **NOT REVERSIBLE** through the plugin interface. Please **backup your WordPress database** before you use this plugin. The authors are not responsible for any data loss. Use at your own risk.

== Upgrade Notice ==

Automatic updates should work correctly. As always, we strongly recommend backing up your site (files and database) before updating any plugin, including this one.

== Changelog ==

= 1.4.1 - [06.04.2025] =
*   Enhancement: Added donation links (readme header, settings page, plugin actions).
*   Code: Minor code cleanup and version bump.

= 1.4.0 - [05.04.2025] =
*   Enhancement: Added option to randomize Approved Comment dates alongside Post dates.
*   Enhancement: Added checkboxes to select whether to randomize Posts, Comments, or both.
*   Enhancement: Settings UI improvements - post-specific options conditionally shown (requires JavaScript).
*   Enhancement: Improved feedback mechanism using transients and redirects.
*   Enhancement: Switched randomization trigger to 'admin_action' hook.
*   Enhancement: Added basic date validation for start/end range.
*   Fix: Now correctly randomizes only 'publish' status posts.
*   Code: Refactored settings registration and handling. Added basic sanitization.
*   Readme: Updated description, features, compatibility, and instructions.

= 1.3 - 12.11.2021 =
*   Enhancements: Compatibility test.

= 1.2 - 11.09.2019 =
*   Enhancements: Option to also set the modified date to be the same as the published date. Thanks to @reflectivechimp

= 1.1 - 22.10.2018 =
*   Enhancements: Added functionality for all post types
*   Enhancements: Gutenberg tested
*   Enhancements: Description improved

= 1.0 - 12.07.2017 =
*   Initial release