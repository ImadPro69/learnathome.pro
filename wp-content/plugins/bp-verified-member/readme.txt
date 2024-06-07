=== Verified Member for BuddyPress ===
Contributors: themosaurus
Tags: buddypress, bp, verified, verify, member, community, badge
Requires at least: 5.4
Tested up to: 6.0
Requires PHP: 5.6
Stable tag: 1.2.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Verify your BuddyPress members and display a twitter-like verified badge on the front-end.

== Description ==
This plugin allows you to verify your BuddyPress members individually or based on WP roles. You can also allow members to request verification directly from their member profile.

Verified members will have a twitter-like "verified" badge displayed on the front-end, and you can choose to display an "unverified" badge for those who haven't been verified yet.

A dedicated settings tab allows you to choose where you want to display the badges:
+ Activities
+ Profiles
+ Member Lists (e.g.: member directory)
+ BuddyPress widgets
+ Private Messages
+ bbPress Forums
+ WordPress Comments and Posts

As well as other settings like the badge color or tooltip.

This plugin is also compatible with [BP Better Message](https://wordpress.org/plugins/bp-better-messages/)

See the plugin in action in our online demo:

+ [Members Directory](https://classic.gwangi-theme.com/members/).
+ [User Profile and Activity Feed](https://classic.gwangi-theme.com/members/michellie/).

This plugin requires [BuddyPress](https://wordpress.org/plugins/buddypress/).

== Installation ==
= AUTOMATIC INSTALLATION =
Automatic installation is the easiest option — WordPress will handles the file transfer, and you won’t need to leave your web browser. To do an automatic install of Verified Member for BuddyPress, log in to your WordPress dashboard, navigate to the Plugins menu, and click “Add New.”

In the search field type “Verified Member for BuddyPress,” then click “Search Plugins.” Once you’ve found us, you can view details about it such as the point release, rating, and description. Most importantly of course, you can install it by clicking “Install Now,” and WordPress will take it from there.

= MANUAL INSTALLATION =
Manual installation method requires downloading the Verified Member for BuddyPress plugin and uploading it to your web server via your favorite FTP application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

= UPDATING =
Automatic updates should work smoothly, but we still recommend you back up your site.

== Frequently Asked Questions ==
= How do I mark a member as verified? =
+ From your admin panel go to *Users > All Users*.
+ Click on the badge in the "Verified" column to verify or unverify your members.
+ You can also verify members from their extended profile tab.

= How do I verify / unverify multiple users at once? =
+ From your admin panel go to *Users > All Users*.
+ Check all the users you want to verify or unverify.
+ In the *Bulk Actions* dropdown, choose "Verify" or "Unverify".
+ Click "Apply".

= Can I automatically verify members with "x" role or member type? =
+ Yes, visit the settings page located in *Settings > BuddyPress*, in the *Verified Member* tab.
+ Check all the roles and/or member types that should be automatically verified.
+ All members belonging to the roles or member types you chose will be displayed as verified users.

= How do I choose where the verified badge is displayed? =
+ From your admin panel go to *Settings > BuddyPress*.
+ Open the *Verified Member* tab.
+ Check the places where you want the badge displayed and click "Save Changes".

= How can I display a list of all my verified members? =
+ You can use a filtering plugin like BP Profile Search to allow filtering your directory on whether a user is verified or not.
+ You can also create a "Verified" member type, make sure to check the "Has Directory View" option, and set that new member type to be automatically verified in our Verified Members settings.

= How do I change the style of the verified badge (tooltip, color, shape, ...)? =
You can easily change the color of the badge via a color picker in the settings page.
The settings are located in *Settings > BuddyPress* in the *Verified Member* tab.

== Screenshots ==
1. BuddyPress members directory showing some verified users
2. BuddyPress profile page showing a verified user and user activities
3. BuddyPress extended profile showing the "Mark this member as verified" checkbox
4. Plugin settings page

== Changelog ==
= 1.2.6 =
* Fix verified member checkbox appearing in backend extended profile for non-admin users
* Fix badge display issue when member name contains emojis
* Fix rare double badge issue in widgets when viewing a single media from rtMedia
* Fix display of request verification button in profile when using BuddyBoss
* Fix display of badge in profile full name when using BuddyBoss
= 1.2.5 =
* Add a new BP email to send an email notification to the site admin when a user sends a verification request
* Fix raw badge HTML being displayed in certain widgets and in private messages when using the BuddyBoss Platform plugin
* Fix raw badge HTML being displayed when clicking on the reply button on a WordPress comment
= 1.2.4 =
* Fix double badge bug in friends requests when using the bp-nouveau template pack or the BuddyBoss Platform plugin.
* Fix badge not being displayed in the members widget when clicking one of the filters.
* Fix warning in the JS console about missing .map files
= 1.2.3 =
* Add new option to choose between different styles of (un)verified badges.
* Fix the "Display in BP Widgets" option.
* Display badge on avatar inside the "Who's Online" and "Recently Active Members" widgets, if the option to display badges in widgets is enabled.
* Fix badge's HTML being printed inside BP tooltips in certain widgets, making the tooltips look "broken".
* Fix incompatibility with certain themes using an updated version of Popper.js.
* Fix "pop-in" effect of the badge's HTML transforming into the actual badge after a delay. The badge's HTML code should now never be visible to the user.
= 1.2.2 =
* Update "Tested up to" version to 5.8
* Fix a number of files not correctly added to the release package
= 1.2.1 =
* Add member-type-based verification options. All users within selected BP member types will be automatically marked as verified.
* Add compatibility with BP Profile Search to allow filtering verified members.
* Add options to enable / disable and customize content of BP notifications when a member gets verified or unverified.
* Add option to display badge in RTMedia views.
* Add compatibility with BP Global Search.
* Fix PHP warning.
= 1.2.0 =
* Add role-based verification options. All users within selected WP roles will be automatically marked as verified.
* Add option to display a "Request verification" button on members profiles. Members who request verification are then placed in a dedicated tab in the WP users table in the admin.
* Add option to display an "unverified" badge for all members that haven't been verified yet.
* Add ability to click the badge in the WP users table to directly verify/unverify members (except role-based verification).
* Add option to display verified badge in the Private Message views of BuddyPress.
* Add option to display verified badge in BuddyPress widgets.
* Add compatibility with JetPack infinite scroll.
* Display badge in friend requests.
* Fix PHP warnings and notices.
= 1.1.6 =
* Fix badge html breaking author meta-tags in the document header.
= 1.1.5 =
* Fix span tag appearing in bbPress author tooltips.
* Fix another browser warning about missing .map files.
* Fix PHP notice being raised in certain situations.
= 1.1.4 =
* Fix conflict with BP Member Reviews..
* Fix settings page access on multisite environment..
* Fix browser warning about missing .map files..
* Add option to display the verified badge next to posts authors..
* Display a tooltip when hovering on the badge..
* Add option to change the tooltip text..
* Fix raw badge HTML being displayed in some cases depending on the theme..
= 1.1.3 =
* Fix a spelling mistake that was repeatedly producing php warnings.
= 1.1.2 =
* Fix various php warnings.
= 1.1.1 =
* Fix php warning.
* Fix user name trimmed in the browser tab title.
* Fix compatibility with new bbPress version 2.6.
* Fix missing admin.js file.
= 1.1.0 =
* Add option to change the color of the badge.
* Improved options layout in settings page.
* Add Bulk Actions in users table to verify / unverify multiple users at once.
* Add column in users table to show their verified status.
* Add option to display verified badge in WordPress comments.
* Add option to display badge in bbPress Topics.
* Add option to display badge in bbPress Replies.
= 1.0.4 =
* Prevent errors and warnings that occurred with some widgets.
* Prevent double badge display.
* Add compatibility with Author Avatars List plugin.
= 1.0.3 =
* Fix bug in admin activities screen.
* Remove verified badge HTML from "Friends" widget title.
= 1.0.2 =
* Improve style with BP Nouveau template pack.
* Split "Display in Profile" option in two new options: "Display in Profile Fullname" and "Display in Profile Username" to allow for more control and compatibility with themes.
* Fix saving issue when trying to save "Verified" status when admin language is different than english.
* Remove verified badge HTML from "Public Mention" links.
* Prevent loading the plugin and add a warning message if BuddyPress is not installed.
= 1.0.1 =
* Fix missing files from the plugin package.
= 1.0 =
* Initial release.
