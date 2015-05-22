=== Ambassador Program ===
Contributors: bryanmonzon
Tags: ambassadors
Requires at least: 4.0
Tested up to: 4.2.2
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Build a basic ambassador program for your company or organizations.

== Description ==
An ambassador program can be a good way to keep your project pipeline full. This is a simple plugin that allows people to create an account, login to see their dashboard and refer clients to your company. 

- Creates a post type called \"Projects\"
- Update Project Cost (project post meta)
- Update Project Status (project post meta)
- Update referral status (currently using post status controls)
- Update user commission rate
- Update default commission rate
- Register & Login Short codes
- Dashboard Short codes
- Future: Welcome emails, Intro Emails, Admin Emails

== Installation ==
1. Upload this plugin to the `/wp-content/plugins/` directory
1. Activate the plugin through the \'Plugins\' menu in WordPress
1. By default 3 pages are created (dashboard, apply, login) with short codes in it.
1. Set default commission rates and off you go!

== Frequently Asked Questions ==
= Can I customize the form fields? =
Not yet. This is a quick first run with basic capabilities. You can use Gravity Forms user registration or roll your own if you want. There isn\'t anything unusual about the sign up process at the moment.

= Can I use `single-ambprog_projects.php` to display more information? =
Currently, no. I don\'t have a solution for this just yet. I\'m working on some short code and the possibility of using templates but it\'s not ready for this version of the plugin yet. 


== Changelog ==
= 1.1: May 21, 2015 =
- Added some ABSPATH checks
- Added/Cleaned up some doc blocks

= 1.0: May 20, 2015 =
Initial launch