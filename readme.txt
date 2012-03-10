=== The OpenHook Customizations Manager ===
Contributors: KingdomGeek
Donate link: https://www.wepay.com/donations/openhook
Tags: theme, customization, functions, display, Thesis, diythemes, hooks, actions, thesiswp, phpinfo, wp_head, wp_footer
Requires at least: 3.3.1
Tested up to: 3.3.1
Stable tag: 3.2.1

Customize your site with HTML, PHP, and Shortcodes, all from the convenience of your admin panel.

== Description ==

**If you have upgraded from OpenHook 2.x.x and wish to preserve your customizations, run the Upgrade from OpenHook 2 script from the Settings -> OpenHook panel.**

OpenHook takes the process of customizing <a href="http://get-thesis.com/">Thesis</a> and simplifies it.

Where once you would need to open and modify your theme's custom functions file, you can now easily customize Thesis via your blog administration panel.

Not only can arbitrary HTML, CSS, JavaScript, and even PHP or shortcodes be inserted into any of Thesis' hooks, you can also easily remove most of the hooked default elements within Thesis with the click of a button!

OpenHook is based heavily upon <a href="http://xentek.net/code/wordpress/plugins/k2-hook-up/">K2 Hook Up</a> by Eric Marden, and so I definitely appreciate his laying the ground work! Hundreds (thousands?) of Thesis users have benefited from OpenHook, and without Eric's plugin to inspire me, there would be no OpenHook as we know it today! Thanks, Eric!

== Installation ==

After you have downloaded the file and extracted the `thesis-openhook/` directory from the archive...

1. Upload the entire `thesis-openhook/` directory to the `wp-content/plugins/` directory.
1. Activate the plugin through the Plugins menu in WordPress.
1. Visit Settings -> OpenHook and customize to your heart's content!

Alternatively, you can use WordPress' automatic plugin installer. Go ahead, it's easier!

== Frequently Asked Questions ==

= I upgraded from OpenHook 2.x.x; where did all of my customizations go? =

OpenHook 3 does not automatically import pre-existing customizations. You will need to visit the OpenHook settings page accessible at Settings -> OpenHook; once there, you can use the "Upgrade from OpenHook 2" button to import your pre-existing customizations to the new schema. You'll then need to activate the Thesis & WordPress action groups as needed from the same settings page.

= I don't use Thesis; can I still use this plugin? =

Of course! However, what you are able to do with OpenHook will be limited. Still, you will have access to WordPress' few public-facing hooks, as well as the `phpinfo()` panel.

= Where can I get Thesis? =

Thesis can be purchased at <a href="http://get-thesis.com/">DIYthemes</a> Membership to the support board alone is worth the price.

= What about the code in my theme's custom functions file? =

If you have already modified your Thesis installation via `custom_functions.php`, you are welcome to port those changes into OpenHook to manage all of your changes in one place.

Note that your blog will use both your theme's custom functions and OpenHook, so the two are complementary.

Likewise, your theme's custom functions file will be processed *after* OpenHook, so you can override OpenHook via the custom functions file, if you need to.

= Why can't I edit my custom files with OpenHook? =

Prior to version 2.3, OpenHook provided panels for editing custom CSS & custom functions files. Thesis now provides those features by default, and so there's no reason for OpenHook to provide the same thing.

= Why can only certain users on my site access OpenHook? =

Do to the powerful nature of OpenHook, access is restricted only to the highest level of users (i.e., those with the ability to delete other users).

= What are the security risks involved in using OpenHook? =

OpenHook is a powerful tool for customizing your site; however, with great power comes, ahem, great responsibility. You are able to use any (ANY!) PHP code within your OpenHook-managed customizations; any other administrators on your site with access to OpenHook can do the same. The freedom allowed means that database credentials could be displayed, your database could be deleted, or your entire site could be defaced. These risks exist with the built-in theme and plugin file editor present in WordPress as well as with directly having access to your theme's custom functions file. Therefore, while OpenHook certainly can be dangerous, if you have only trusted administrators on your site, you have nothing to worry about.

== Upgrade Notice ==

= 3.2.1 =
**If you are updating from 2.x.x, be sure to visit the Settings -> OpenHook page immediately after updating in order to upgrade your settings to the new schema.**

== Changelog ==

= 3.2.1 =
* Bumped to make the repository update

= 3.2 =
* [feature] Hooks with customizations are marked with asterisks in the dropdown select box
* [change] Add link to phpinfo() under Tools menu
* [change] Verbiage for unhooking updated
* [fix] Many undefined variable errors
* [fix] Slashes are now properly stripped when upgrading from 2.x.x
* [fix] Default Thesis 404 content can now be properly removed

= 3.1 =
* [fix] Rare issue where the general settings panel doesn't fully appear
= 3 =
* Total rewrite of the plugin
* [feature] phpinfo() panel
* [feature] per-hook disabling of custom actions
* [feature] option to process shortcodes on custom actions
* [feature] ability to choose which actions to process (WordPress' or Thesis' or both's)
* [feature] ability to remove all OpenHook options
* [removed] several deprecated options

= 2.3.2 =
* Remember the typos fixed in 2.3.1? There were others I should have caught then. I'm a terrible proofreader, but thanks, Dean (http://www.doublejoggingstrollershq.com/), for catching them!

= 2.3.1 =
* Fixed two stupid typos that killed everything that was right with the world. Well, they broke the plugin anyway. Thanks, Jim (http://doggybytes.ca/), for reporting so quickly!

= 2.3 =
* I finally bought an SVN client, so I'm finally updating OpenHook. Sorry for the delay!
* Thesis 1.7's four new hooks are now included.
* OpenHook's file editing panels have been removed -- Thesis has these by default now.
* Readme.txt updated.

= 2.2.5 =
* Reverted change introduced in 2.2.3 regarding stripping of slashes

= 2.2.4 =
* Fixed a syntax error, reported by multiple users.

= 2.2.3 =
* Fixed a bug which prevented the After Teasers Box hook from saving properly. Thanks, Michael Curving.
* Fixed an issue where the file editors would strip slashes unnecessarily. Thanks, Kristarella.