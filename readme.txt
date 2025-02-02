=== WP Users Media ===
Contributors: WebKreativ, khromov
Tags: user, users, wp admin, media, files, attachments, photos, photo, gallery, galleries
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=AJABLMWDF4RR8&source=url
Requires at least: 3.5
Tested up to: 5.7.2
Stable tag: 4.2.3
Requires PHP: 7.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

WP Users Media is a WordPress plugin that displays only the current users media files and attachments in WP Admin.

== Description ==
WP Users Media works right away when you activate the plugin. No more settings needed. If you need the options switched on for the Admin also you can do it in the Options Page located in the Settings area.

What the plugin does is to disable the ability for users to access other members files and attachments through the Media Button and Featured Image sections. This is really good because maybe you have Authors, Contributors and Subscribers that write posts etc. and you do not want them to be able to use other members media files to their own content.

To test it out create ```3 users```, ```1 Admin``` and ```2 Author's``` for an example. As Admin you are able to see all media files from all the users on the site. 

1. Upload some photos or other media files as Admin. Log out
2. Login as ```Author 1``` and click on the Add Media button for an post or a page. Now you do not see any media files at all. If the plugin was inactivated you would be able to see the photos that the Admin uploaded. Now, upload some photos as ```Author 1```. Log out.
3. Repeat step 2 for ```Author 2```. As you can see ```Author 2``` cannot see the files uploaded from the Admin and from the ```Author 1```.

This is very effective solution to keep the media files more private.

== Installation ==
1. Upload ```wp-users-media``` folder to the ```/wp-content/plugins/``` directory
2. Activate the plugin through the ```Plugins``` menu in WordPress
3. Done!

== Frequently Asked Questions ==
Use the ```support``` link in the menu above of this plugins homepage on wordpress.org if you have any qustions.

== Screenshots ==
1. General section
2. User roles section

== Changelog ==
= 4.2.3 =
* Comp check with WordPress 5.7.2

= 4.2.2 =
* Comp check with WordPress 5.5
* Added uninstall file
* Fixed better file structure

= 4.2.1 =
* Added donation link for patreon
* Modified readme.txt
* Added screenshots to plugin
* Fixed proper language attribute for translations

= 4.2.0 =
* Fixed Compatibility with WP 5.4
* Major redesign of the interface and improvement of the code
* Fixed an unsinstall file that delets all settings in the database upon deletion of plugin

= 4.1.1 =
* Compatibility check with latest WordPress 5.2.4
* Fixed a bug where incrementer broke the Admin

= 4.1.0 =
* Redesign of settings page and rename of functions

= 4.0.3 =
* Compatibility check with latest WordPress 5.2

= 4.0.2 =
* Compatibility check with latest WordPress 4.9.1
* Changed Dashicon and moved Admin menu link next to Media link
* Small changes in the code for better readability
* Small fix of JavaScript
* Fix of an error : Call to a member function get_names() on null if there are no roles present. Thanks to [@jurep](https://wordpress.org/support/users/jurep/)

= 4.0.1 =
* Compatibility check with latest WordPress 4.8.2
* Small spelling corrections

= 4.0.0 =
* Fixed so the admin now can choose what user roles that can view their own attachments in the page settings. Default is still that all user roles can view their own attachments.
* Improvement of the code
* Compatibility fix
* Fixed donate link
* Fixed small changes of translations

= 3.0.3 =
* Check of the post_type variable to see if isset to avoid notice message

= 3.0.2 =
* Fix of $wp_query where it caused unexpected error and broke the query

= 3.0.1 =
* Fix of undefined variable of $wp_query

= 3.0.0 =
* Update of plugin description
* Added option page
* Ability to switch on so the admin can only view his/hers attachments and files also

= 2.0.2 =
* Compatibility fix

= 2.0.1 =
* Compatibility fix

= 2.0.0 =
* Major redesign of code

= 1.0.2 =
* Small revision bug fix

= 1.0.1 =
* Name Bug Fix

= 1.0.0 =
* First release

== Upgrade Notice ==
Upgrading provides new compaitiblity with newer versions of WordPress.