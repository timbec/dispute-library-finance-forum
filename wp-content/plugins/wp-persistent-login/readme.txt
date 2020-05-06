=== WordPress Persistent Login ===
Contributors: lukeseager, freemius
Donate link: 
Tags: login, persistent login, keep users logged in, remember users, remember user, login cookie, remember me cookie, remember login, stay logged in, login, auto login
Requires at least: 4.9.4
Tested up to: 5.3
Stable tag: 1.3.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Persistent Login keeps users logged into your website securely, unless they explicitly log out.

== Description ==

Persistent Login is a simple plugin that keeps users logged into your website unless they explicitly choose to log-out. 

It requires no set-up, simply install and save your users time by keeping them logged into your website securely, avoiding the annoyance of forgetting usernames & passwords.

For added security, users can visit their Profile page in the WP Admin area to see how many sessions they have, what device was used and when they were last active. The user can choose to end any session with the click of a button!

## Features
* Selects the 'Remember Me' box by default. 
  * If left checked, users will be kept logged in for 1 year
* Each time a user revisits your website their login is extended to 1 year again
* Dashboard stats show you how many users are being kept logged in
* Force log-out all users with the click of a button
* Users can manage their active sessions from the Profile page in the admin area
* Support for common plugins out of the box
* Secure, fast and simple to use!

### Top Tip
Once the plugin is installed, click the **End all Sessions** button on the plugin settings page to encourage users to login again and be kept logged in forever!

### Supported Plugins

* PeepSo
* Theme My Login
* Ultimate Member
* Ultimate Member - Terms and Conditions Extension
* WooCommerce Social Login *(Premium)*
* Ultimate Member Social Login Extension *(Premium)*

These plugins are supported out of the box. No hassle and no settings to change!

### Note
This plugin honours the 'Remember Me' checkbox. It is checked by default, but if it is unchecked the user won't be remembered.

The premium plan allows you to hide the 'Remember Me' checkbox, so that users are always remembered.

== Installation ==

1. Download and install the plugin onto your WordPress website
2. Activate the plugin
3. (optional) Click the End all Sessions button on the plugin settings page to force all users to login again


== Frequently Asked Questions ==

= How long will it keep users logged in? =

If a user visits your website more than once a year, they will be kept logged in forever. 

The only way for them to be logged out is if they clear their cookies, click logout, or don't return within 1 year. 

= Is it secure? =

You bet! 

WP Persistent Login uses core WordPress methods to ensure that we're logging in the right user. 

= Support =

Support for a bug can be requested from the WordPress Plugin Directory. Premium users can request support directly from the WP Admin area.

= The Remember Me box isn't checked =

If the Remember Me box on a login form isn't checked by default, please open a support request on the Plugin Directory. 

It is most likely a conflict with another plugin or theme, which can usually be fixed. 

= Can I hide the Remember Me box? =

On the free version, no. You can write your own CSS or JavaScript to remove the Remember Me box from a page if you'd like. You will need FTP access to achieve this.

The premium version has a simple setting to hide the Remember Me box by default, and it also works with supported plugins like Theme My Login!

= I don't stay logged in on multiple devices =

If you're not being kept logged in on multiple devices, try turning on 'Allow duplicate sessions' from the settings page. 

This is most common if you're trying to login to two machines with the same operating system and browser on the same network.

= The plugin doesn't work =

If the plugin isn't working on your website, please open a support request on the Plugin Directory. It is likely that there is a conflict with another plugin. 

= Is it compatible with WordPress Multisite =

No. WordPress Persistent login isn't compatible with multisite installations at the moment.


= Is it free? =

Yes. The free forever version is and always will be free. All of your users will be kept logged-in when they revisit your website. 

A premium version of the plugin is available if you want to:
* Control which User Roles are kept logged in
* Hide Remember Me boxes from users
* Change the maximum time users are kept logged in for
* Allow users to manage their own sessions from the front-end (supports WordPress 5 block editor)
* Allow admins to manage all users sessions from the WP Admin area
* Priority support

== Screenshots ==

1. Settings page for the Free Forever plan. Shows usage stats with a breakdown based on user roles.
2. Support is built right into the admin area.
3. Premium Plan settings page. Shows what settings are available for premium users. 
4. Manage your sessions from the Your Profile page in the WP Admin area (premium admins can manage all users sessions).

== Changelog == 

= 1.3.8 =
* New feature: Admin option to allow duplicate sessions. Useful if you're having trouble staying logged in on multiple devices.

= 1.3.7 =
* Improving settings page UI
* General bug fixes
* New premium option: Hide Remember Me boxes from users **(premium)**

= 1.3.6 =
* Minor bug fixes
* Improvement to compatibility with WooCommerce - Social Login Plugin **(premium)**

= 1.3.5 =
* Added support for Ultimate Member Terms & Conditions Extension
* Improved Remember Me box detection

= 1.3.4 =
* Security patch
* Users can now manage their own sessions from their Profile page in the WP Admin area
* Premium: Admins can manage all user sessions from the WP Admin area

= 1.3.3 = 
* Support for Ultimate Member plugin
* Support for Ultimate Member - Social Login Extension **(premium)**
* Added option to disable dashboard 'at a glance' stats to improve dashboard page speed

= 1.3.2 =
* Added fix for where Remember Me box wasn't auto checked on certain themes

= 1.3.1 =
* Improved login form detection
* Minor bug fixes
* **Premium:** Updated browser detection definitions

= 1.3.0 =
* **Major update:** Removed the dependancy of an additional database table & re-writing of plugin
* Big improvements to stability and performance
* **New premium feature:** Front end session management with Gutenberg & Shortcode support

= 1.2.3 =
* Logic to handle removal of data for users that are deleted from WordPress
* Added login timestamps to database in preparation for future feature
* Fixed a bug related to auto-login on Linux operating systems (thanks Paul)
* Minor bug fixes

= 1.2.2 =
* Important GDPR compliance update
* Added usage stats based on user roles to settings page
* Improved settings page for free users
  * De-cluttered the settings page
  * Removed a lot of sales messages
  * 7 day free trial added
* Improved settings page for paid users
  * Improved look and feel
  * Easier to differentiate between information & updatable settings
* Minor bug fixes

= 1.2.1 =
* Added usage stats to the 'at a glance' box on the Dashboard homepage
* Fixed auto-install upgrade bug
* Minor fixes to the admin area pages

= 1.2.0 =
* New Premium Feature: Allow admin to set maximum time persistent login lasts before the user has to login again
* New Premium Feature: Allow admin to end all persistent login sessions from the Dashboard
* New Premium Feature: Added support for "WooCommerce - Social Login" plugin
* Added usage figures to admin area: Allows admins to see how many users are logged in using Persistent Login
* Fixed issue with cookies not being set across the entire domain
* Fixed issue with removing individual users information from the database when failing to login correctly

= 1.1.4 =
* Fixing database column creation bug

= 1.1.3 =
* Fixing minor bugs
* Added multi-device persistent login support. Users can now stay logged in on more than one device
* Added security notification email to user if suspicious login attempts are made
* Added functionality to track user IP addresses (prep-work for future update)

= 1.1.2 =
* Fixing minor bugs
* Improved upgrade process to premium version

= 1.1.1 =
* Fixing minor bugs

= 1.1.0 =
* Plugin re-launch
* Updated logic to improve security
* Uninstall features to remove database table and all data correctly
* Freemium model adopted

= 1.0.2 =
* Updates plugin to be compatible with WP 4.1
* Fixes login/logout redirect issues

= 1.0.1 =
* Removes requirement to have ACF installed, please disable ACF if you don’t use it for anything else
* Updated logic
* General bug fixing

= 1.0.0 =
* WordPress Persistent Login Plugin launch