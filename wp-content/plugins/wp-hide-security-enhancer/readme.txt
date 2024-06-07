=== WP Hide & Security Enhancer ===
Contributors: nsp-code, tdgu
Donate link: https://www.nsp-code.com/
Tags: wordpress hide, security, security headers, custom login, login hide
Requires at least: 2.8
Tested up to: 6.5.3
Stable tag: 2.3.9
License: GPLv2 or later

Hide WordPress, wp-content, wp-includes, wp-admin, login URL, plugins, themes etc. Block the default URLs. Security Headers etc.

== Description ==

WP-Hide has launched the **easiest way to completely hide your WordPress** core files, login page, theme and plugins paths from being shown on front side. This is a huge improvement over Site Security, since no one will know whether you are running or not a WordPress. It also provides a simple way to clean up html by removing all WordPress fingerprints.

**No file and directory change!**
No file and directory will be changed anywhere. Everything is processed virtually. The plugin code uses URL rewrite techniques and WordPress filters to apply all internal functionality and features. Everything is done automatically without user intervention required at all.

**Real hide of WordPress core files and plugins**
The plugin not only allows you to change default URLs of you WordPress, but it also hides/blocks such defaults. Other similar plugins, just change the slugs, but the defaults are still accessible, obviously revealing WordPress as CMS.

You can change the default WordPress login URL from wp-admin and wp-login.php to something totally arbitrary. No one will ever know where to try to guess a login and hack into your site. It becomes totally invisible.

[vimeo http://vimeo.com/185046480]

<br />Full plugin documentation available at <a target="_blank" href="https://wp-hide.com/documentation/">WordPress Hide and Security Enhancer Documentation</a>

When testing with WordPress theme and plugins detector services/sites, any setting change may not reflect right away on their reports, since they use cache. So, you may want to check again later, or try a different inner URL. Homepage URL usage is not mandatory.

Being the best content management system, widely used, WordPress is susceptible to a large range of hacking attacks including brute-force, SQL injections, XSS, XSRF etc. Despite the fact the WordPress core is a very secure code maintained by a team of professional enthusiast, the additional plugins and themes make ita vulnerable spot for every website. In many cases, those are created by pseudo-developers who do not follow the best coding practices or simply do not own the experience to create a secure plugin.
Statistics reveal that every day new vulnerabilities are discovered, many affecting hundreds of thousands of WordPress websites.
Over 99,9% of hacked WordPress websites are target of automated malware scripts, which search for certain WordPress fingerprints. This plugin hides or replaces those traces, making the hacking boots attacks useless.

It works well with custom WordPress directory structures,e.g. custom plugins, themes, and upload folders.

Once configured, you need to **clear server cache data and/or any cache plugins** (e.g. W3 Cache), for a new html data to be created. If you use CDN this should be cache clear as well.

**Sample usage**
[vimeo https://vimeo.com/192011678]

**Main plugin functionality:**

* Customizes Admin URL
* Blocks default admin URL
* Blocks any direct folder access to completely hide the structure
* Customize wp-login.php filename
* Google Captcha 
* Blocks default wp-login.php
* Blocks default wp-signup.php
* Blocks XML-RPC API
* Creates New XML-RPC paths
* Adjusts theme URL
* Creates New child Theme URL
* Changes theme style file name
* Cleans any headers for theme style file
* Customizes wp-include 
* Blocks default wp-include paths
* Blocks default wp-content
* Customizes plugins URL
* Changes Individual plugin URL 
* Blocks default plugins paths
* Creates New upload URL
* Blocks default upload URL
* Removes WordPress version
* Blocks Meta Generator
* Disables the emoji and required javascript code
* Removes pingback tag
* Removes wlwmanifest Meta
* Removes rsd_link Meta
* Removes wpemoji
* Minifies Html, Css, JavaScript

* Security Headers

and many more.

**No other plugin functionality will be blocked or interfered in any way by WP-Hide**

This plugin allows to change the default Admin URL from **wp-login.php** and **wp-admin** to something else. All original links turn the default theme to “404 Not Found” page, as if nothing exists there. Besides the huge security advantage, the WP-Hide plugin saves lots of server processing time by reducing php code and MySQL usage since brute-force attacks target the weakURL.

**Important:** Compared to all other similar plugins which mainly use redirects, this plugin turns a default theme to“404 error” page for all **blocked URL** functionalities, without revealing the link existence at all.

Since version 1.2, WP-Hide change individual plugin URLs and made them unrecognizable. For example,the change of the default WooCommerce plugin URL and its dependencies from domain.com/wp-content/plugins/woocommerce/ into domain.com/ecommerce/cdn/ or anything customized.

= Plugin Sections =

**Hide -> Scan

* Exhaustive system security examination with analysis and improvements guidance and fixes


**Hide -> Rewrite > Theme**

* New Theme Path – Changes default theme path
* New Style File Path – Changes default style file name and path
* Remove description header from Style file – Replaces any WordPress metadata information (like theme name, version etc.,) from style file
* Child – New Theme Path – Changes default child theme path
* Child – New Style File Path – Changes child theme style-sheet file path and name
* Child – Remove description header from Style file – Replaces any WordPress metadata information (like theme name, version etc.,) from style file

**Hide -> Rewrite > WP includes**

* New Include Path – Changes default wp-include path/URL
* Block wp-include URL – Blocks default wp-include URL

**Hide -> Rewrite > WP content**

* New Content Path – Change default wp-content path/URL
* Block wp-content URL – Blocks the default content URL

**Hide -> Rewrite > Plugins**

* New Plugin Path – Changes default wp-content/plugins path/URL
* Block plugin URL – Blocks default wp-content/plugins URL
* New path / URL for Every Active Plugin
* Customize path and name for any active plugins

**Hide -> Rewrite > Uploads**

* New Upload Path – Changes default media files path/URL
* Block upload URL – Blocks default media files URL

**Hide -> Rewrite > Comments**

* New wp-comments-post.php Path
* Block wp-comments-post.php

**Hide -> Rewrite > Author**

* New Author Path
* Block default path

**Hide -> Rewrite > Search**

* New Search Path
* Block default path

**Hide -> Rewrite > XML-RPC**

* New XML-RPC Path – Changes default XML-RPC path / URL
* Block default xmlrpc.php – Blocks default XML-RPC URL
* Disable XML-RPC authentication – Filters whether XML-RPC methods require authentication
* Remove pingback – Removes pingback link tag from theme

**Hide -> Rewrite > JSON REST**

* Clean the REST API response
* Disable JSON REST V1 service – Disables an API service for WordPress which is active by default
* Disable JSON REST V2 service – Disables an API service for WordPress which is active by default
* Block any JSON REST calls – Any call for JSON REST API service will be blocked
* Disable output the REST API link tag into page header
* Disable JSON REST WP RSD endpoint from XML-RPC responses
* Disable Sends a Link header for the REST API

**Hide -> Rewrite > Root Files**

* Block license.txt – Blocks access to license.txt root file
* Block readme.html – Blocks access to readme.html root file
* Block wp-activate.php – Blocks access to wp-activate.php file
* Block wp-cron.php – Blocks outside access to wp-cron.php file
* Block wp-signup.php – Blocks default wp-signup.php file
* Block other wp-*.php files – Blocks other wp-.php files within WordPress Root

**Hide -> Rewrite > URL Slash**

* URL’s add Slash – Add a slash to any links without it. This disguisesthe existence of a file, folder or a wrong URL, which will all be slashed.

**Hide -> General / Html > Meta**

* Remove WordPress Generator Meta
* Remove Other Generator Meta
* Remove Shortlink Meta
* Remove DNS Prefetch
* Remove Resource Hints
* Remove wlwmanifest Meta
* Remove feed_links Meta
* Disable output the REST API link tag into page header
* Remove rsd_link Meta
* Remove adjacent_posts_rel Meta
* Remove profile link
* Remove canonical link

**Hide -> General / Emulate CMS**

* Emulate CMS

**Hide -> General / Html > Admin Bar**

* Remove WordPress Admin Bar for specified urser roles

**Hide -> General / Feed**

* Remove feed|rdf|rss|rss2|atom links

**Hide -> General / Robots.txt**

* Disable admin URL within Robots.txt

**Hide -> General / Html > Emoji**

* Disable Emoji
* Disable TinyMC Emoji

**Hide -> General / Html > Styles**

* Remove Version
* Remove ID from link tags

**Hide -> General / Html > Scripts**

* Remove Version

**Hide -> General / Html > Oembed**

* Remove Oembed

**Hide -> General / Html > Headers**

* Remove Link Header
* Remove X-Powered-By Header
* Remove Server Header
* Remove X-Pingback Header

**Hide -> General / Html > HTML**

* Remove HTML Comments
* Minify Html, CSS, JavaScript
* Remove general classes from body tag
* Remove ID from Menu items
* Remove class from Menu items
* Remove general classes from post
* Remove general classes from images

**Hide -> General / Html > User Interactions**

* Disable Mouse right click
* Disable Text Selection
* Disable Copy
* Disable Cut
* Disable Paste
* Disable Print
* Disable Print Screen
* Disable Developer Tools
* Disable View Source
* Disable Drag / Drop

**Hide -> Admin > wp-login.php**

* New wp-login.php – Maps a new wp-login.php instead of the default one
* Block default wp-login.php – Blocks default wp-login.php file from being accessible
* Customize the default login page Logo image 

**Hide -> Admin > Admin URL**

* New Admin URL – Creates a new admin URL instead of the default ”/wp-admin”. This also applies for admin-ajax.php calls
* Disable customized Admin Url redirect to the Login page
* Block default Admin Url – Blocks default admin URL and files from being accessible

**Security -> Captcha**

* Google Captcha V2
* Google Captcha V3
* CloudFlare Turnstile ( PRO )


**Settings -> CDN**  

* CDN Url – Sets-up CDN if applied. Some providers replace site assets with custom URLs.

**Security -> Headers**

HTTP Response Headers are a powerful tool to Harden Your Website Security.
* Cross-Origin-Embedder-Policy (COEP)
* Cross-Origin-Opener-Policy (COOP)
* Cross-Origin-Resource-Policy (CORP)
* Referrer-Policy
* X-Content-Type-Options
* X-Download-Options
* X-Frame-Options (XFO)
* X-Permitted-Cross-Domain-Policies
* X-XSS-Protection

<br />This free version works with Apache and IIS server types. For all server types, check with <a target="_blank" href="https://wp-hide.com/">WP Hide PRO</a>

<br />This is a basic version that can hide everything for basic sites, example <a target="_blank" href="https://demo.wp-hide.com/">https://demo.wp-hide.com/</a>. When using complex plugins and themes, the WP Hide PRO may be required. We provide free assistance to hide everything on your site, along with the commercial product. 

<br />Anything wrong with this plugin on your site? Just use the forum or get in touch with us at <a target="_blank" href="https://wp-hide.com/contact/">Contact</a> and we'll check it out.

<br />A website example can be found at <a target="_blank" href="https://demo.wp-hide.com/">https://demo.wp-hide.com/</a> or our website <a target="_blank" href="https://wp-hide.com/">WP Hide and Security Enhancer</a>

<br />Plugin homepage at <a target="_blank" href="https://wp-hide.com/">WordPress Hide and Security Enhancer</a>

<br />
<br />This plugin is developed by <a target="_blank" href="https://www.nsp-code.com">Nsp-Code</a>

== Installation ==

1. Install the plugin through the WordPress plugins interface or upload the package to `/wp-content/plugins/wp-hide-security-enhancer` directory.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the WP Hide menu screen to configure the plugin.

== Frequently Asked Questions ==

Feel free to contact us at contact@wp-hide.com for fast support.

= Does the plugin change anything on my server?  =

Absolutely Nothing! 
No files and directories will be changed on your server, since everything is processed virtually. The plugin code use URL rewrite techniques and WordPress filters to apply all internal functionalities and features.

= Since I have no PHP knowledge at all, is this plugin for me? =

There is no requirement for php knowledge. All plugin features and functionalities are applied automatically, controlled through a descriptive admin interface.

= Is there any demo I can check? =

A demo instance can be found at <a target="_blank" href="https://demo.wp-hide.com/">https://demo.wp-hide.com/</a> or our own website <a target="_blank" href="https://wp-hide.com/">WP Hide and Security Enhancer</a>

= Can I use the plugin on my Nginx server?  =

If the server runs full-stack Nginx, the free plugin can’t generate the required format Nginx rewrite rules. It works with Apache, LiteSpeed, IIS, Nginx as a reverse proxy and compatible.

= Can I still update WordPress, my plugins and themes?  =

Everything works as before, no functionality is being broken. You can run updates at any time.

= Does the plugin affect the SEO aspects of my website?  =

No, the plugin changes only asset links (CSS, JavaScript, media files),but not actual content URLs. There will be no negative impact from SEO perspective, whatsoever.

= Does the plugin work with my site cache?  =

Yes, the plugin works with any cache plugin deployed on your site.

= What are HTTP Security Headers?  =

HTTP Response Headers are a powerful tool to Harden Your Website Security. The plugin provides an easy way to add Security Response Headers through a graphical interface. No additional codding and file editing is necessary.

= What servers this plugin can work with? =

This free code/WP-Hide can work with Apache, IIS server types and any other set-up which rely on .htaccess usage.
For all other cases, check the PRO version at  <a target="_blank" href="https://wp-hide.com">WP Hide PRO</a>

= How to make it work with my OpenLiteSpeed server? =

There are few things to consider when you run on litespeed servers:

* Ensure the liteserveractually processes the .htaccess file, where the rewrite data is being saved. Check with the following topic regarding this issue  <a target="_blank" href="https://www.litespeedtech.com/support/forum/threads/htaccess-is-ignored.15500/">Post</a>

* If you use Litespeed Cache plugin, in the Optimization Settings area, disable the CSS / JS Minify

* If your litespeed server requires to place the rewrite lines in a different file,e.g. config file or interface, consider upgrading to PRO version which includes a Setup page where you can get the rewrite code <a href="https://wp-hide.com/wp-hide-pro-now-available/">WP Hide PRO</a>.


= How to use on my Bitnami setup? =
As default, on Bitnami LAMP set-ups, the system will not process the .htaccess file, so none of the rewrites will work. You can change this behavior by updating the main config file located at /opt/bitnami/apps/APPNAME/conf/httpd-app.conf , update the line
<pre><code>AllowOverride None</code></pre>
to
<pre><code>AllowOverride All</code></pre>
Restart the Apache service through SSH
<pre><code>sudo /opt/bitnami/ctlscript.sh restart</code></pre>
More details can be found at <a href="https://docs.bitnami.com/general/apps/redmine/administration/use-htaccess/">Bitnami Default .Htaccess
</a>

You can still keep the configuration as it is using the <a target="_blank" href="https://wp-hide.com">WP Hide PRO</a>, more details at <a href="https://wp-hide.com/documentation/setup-the-plugin-on-bitnami-wordpress-lamp-stack/">Setup the plugin on Bitnami WordPress LAMP stack
</a>


= .htaccess file writing error – Unable to write custom rules to your .htaccess. Is this file writable? =

I’m seeing this error “Unable to write custom rules to your .htaccess. Is this file writable”? What does it mean?
The error appears when the plugin is not able to write to .htaccess file located in your WordPress root directory. You can try the followings to make a fix:

* Check if your .htaccess file is writable. This can be different from server to server, but usually require rw-rw-r– / 0664. Also ensure the file owner is the same group as php.

* Sometimes the other codes wrongly use the flush_rules() which hijack the default filters for rewrite. Try to disable the other plugins and theme to figure out which ones produce the issue.

* De-activate and RE-activate the plugin, apparently worked for some users.

* Create a backup of .htaccess, then delete it from the server. Go to Settings > Permalinks > update once, this should create the file again on the WordPress root. If so, try to change any WP Hide options which will update the .htaccess content accordingly.

= Something is wrong, what can I do? How can I recover my site? =

* There will be no harm.
* Go to admin and change some of the plugin options to see which one causes the problem. Then report it to the forum or get in touch with us to fix it.
* If you can’t log in to admin, use the Recovery Link which has been sent to your e-mail. This will reset the login to default.
* If you can’t find the recovery link or none of the above worked, delete the plugin from your wp-content/plugins directory. Then remove any lines in your .htaccess file between: 
 BEGIN WP Hide & Security Enhancer
..
 END WP Hide & Security Enhancer 

* At this point, the site should run as before. If for some reason still not working, you missed something, please get in touch with us at contact@wp-hide.com and we’ll fix it for you in no time!

= How to use the Recovery Link? =

The Recovery Link can be used to reset all plugin options and restore the site to the default state.
The link should be entered into the browser URL bar. After the operation is completed, a system message will show “The plugin options have been reset successfully”.
If the message does not show, there is a cache on your site that prevents the code to run. Locate your cache data, usually at /wp-content/cache/ and remove the files. Then re-load the recovery link.

= What to do if I can’t find a functionality that I’m looking for? =

Please get in touch with us and we’ll do our best to include it inthe next version.

== Screenshots ==

1. Admin Interface.
2. Sample front html code.

== Changelog ==

= 2.3.9 =
* New feature: Customize the default login page Logo
* Improve the default plugin set-up with more options and include the Headers sample settings.
* Slight visual improvements. 
* Inform to restart the LiteSpeed on certain servers (e.g. Hostinger ).
* Use preg_replace to sanitize the input for security improvements. 
* Compatibility file for WPForms Lite
* WordPress 6.5.3 compatibility check and tag update

= 2.3.8.2 =
* Disable the filter wph/components/rewrite-default/superglobal_variables_replacements and the ignore for _wp_http_referer as produce issues with specific plugins

= 2.3.8.1 =
* Fix Too few arguments to function WPH_module_rewrite_default::_array_replacements_recursivelly()

= 2.3.8 =
* Ignore the _wp_http_referer when reversing urls, to ensure when compared with existing is not failing. 
* Fix for WPForms Lite plugin when using a custom admin URL.

= 2.3.7 =
* Preserve the field types when replacing superglobals data. 

= 2.3.6 =
* Ensure the is_user_logged_in function is available before calling it. 

= 2.3.5 =
* Update the plugin headers
* New module - Disable Admin Url redirect to Login page
* Remove deprecated admin-new-_wp-login_php file
* WordPress 6.5 compatibility check and tag update

= 2.3.1 =
* New filter wp-hide/interface/process/minimum_slug_length for customizing the minimum length of the admin and login slug   https://wp-hide.com/documentation/wp-hide-interface-process-minimum_slug_length/
* Oxygen builder compatibility file updates.
* Add end slash for admin custom slug, into the rewrite, to ensure exact match.
* Add the filter wph/components/force_run_on_admin to more options for allowing to run into the admin https://wp-hide.com/documentation/wph-components-force_run_on_admin/
* WordPress 6.4.2 compatibility check and tag update

= 2.2.9 =
* Allow custom login URL without requiring a PHP extension. 
* Require at least 5 chars for the customization of login and admin URL to avoid words conflicts. 
* Scan XML RPC update, check if the service is disabled to avoid returning false positive. 
* Compatibility with Redirection plugin; show the default redirect URLs within the interfaces. 
* Add FLYING_PRESS_VERSION and LiteSpeed Purge to the internal site_cache_clear()
* WordPress 6.4.1 compatibility tag update

= 2.2.4 =
* Fix Undefined array key "file" warning.
* Ignore wp-admin, wp-content, wp-includes as custom slugs for any of the options, to avoid code conflicts. 

= 2.2.1 =
* Reverse the replacements for $_FILES super global variable too. 
* Adjust the login form width, when using the Google Captcha or Cloudflare Turnstile Captcha
* Use init action, to send the customized login e-mail, to avoid sending multiple time on certain servers environment.
* Use debug_backtrace to avoid looping, in conjunction with certain plugins, for login_url filter.
* Add a filter for site_url to apply the login customisation when the scheme is 'login' or 'login_post'
* Fix reset options form and submit buttons.
* Fix various texts and instances.
* Tested for WordPress 6.4

= 2.1.8 =
* New feature Captcha for Login, Register, Password Forget pages etc. 
* New Captcha - Google Captcha V2
* New Captcha - Google Captcha V3
* Tested for PHP 8.2.4

= 2.1.5 =
* Use transient for domain_get_ip to avoid execution delays with certain hosts.
* Separate options for Copy / Cut / Paste into the User Interactions interface for better control over the options
* Few Typos fix
* Compatibility updates for TranslatePress - Multilingual

= 2.1.1 =
* New filter wph/components/components_run/ignore_component which allows selective disabling for specific components to apply on the front site
https://wp-hide.com/documentation/wph-components-components_run-ignore_component/
* Set minimum required WordPress version as 4.0
* Set minimum required PHP version as 5.4

= 2.1 =
* Relocate the plugins_themes_compatibility prior module components initialization.
* Avoid looping with certain 3rd codes by caching the home url.
* HTML Comments removal regex updates. 
* Compatibility update for qTranslate-XT plugin, when using the option redirect to language and customizing the default login url through WP Hide

= 2.0.6 =
* Use regex patterns for Scan - Replacements, for better accuracy in the identification of the fingerprints proposed to be changed. 
* Deprecated Expect-CT.
* Remove the Expect-CT from the recommended headers. 

= 2.0.4 =
* Suppress the option to block the Developer Tools / Inspect when page/post preview.  
* Add to cache clear for Autoptimize, Perfmatters, Breeze, Site Ground Cache,  when flushing the caches.
* Site Ground Cachepress plugin compatibility update
* WordPress compatibility check for 6.2
* WordPress compatibility tag update.

= 1.9.9 =
* Decrease the Scan progress background AJAX update, to avoid time-outs on slow connections.
* Improvement: When using the Disable Developer Tools option, check if iPhone device and disable, through JavaScript instead PHP, to avoid caching.
* New Screenshot for better pre-visualization of the actual interface. 
* Fix: Scan Admin component, Fix button URL.

= 1.9.7 =
* New Security Headers component - Referrer-Policy.
* Check the post meta and option value if serialized ( double serialization ), before reversing the URLs.
* Code improvements.
* Updated translation PO file.

= 1.9.5 =
* Replaced the deprecated Feature-Policy with Permissions-Policy security header.
* Fix: Scan disable redirects when testing firewall, to ensure correct results
* Fix count() error for not countable variable.
* PO language file updates

= 1.9.3 =
* Add additional description for potentially dangerous files found within WordPress root.
* Typo fix for "Dangerous Files"
* Fix: Tipsy JavaScript error
* Fix: Undefined variable $site_score within render_overview()
* Fix: Divided by zero when calculating the overall scan progress
* Fix: Wrong remote_html variable

= 1.9.1 =
* New feature - Security Scan.
* Security Scan dashboard widget
* Inform on possible LiteSpeed service restart if use such system.
* Check if HTTP_USER_AGENT environment variable exists before making comparison. 
* Fix Oxigen compatibility when using the HTML Minify.
* Fix: Cache Enable static call.

= 1.8.8 =
* New component  Headers -> Remove Server Header.
* Prevent output of "document.addEventListener" unless an user-interaction option is active.
* Add X-XSS-Protection into the headers list, to avoid reporting as not used as security header.
* Code Improvements and clean-up.
* PO language file update.

= 1.8.6 =
* Ignore the "Disable Developer Tools" on iPhone
* WordPress 6.1 compatibility tag
* Fix: Security headers progress comparison step. 
* Slight css changes 

= 1.8.5 =
* Improved Disable Developer Tools feature, by returning an empty page.
* W3 Total Cache - implements support for Push CDN and custom folders
* Compatibility fix with JCH Optimize.
* Ignore invalid SSL certificate when testing rewrites, to allow local instances.
* Fix: static to public functions for a2-optimized compatibility class.
* Fix: use preg_match to ensure the HTML data is valid and avoid faulty code with multiple head tags.
* Slight text changes within some options, for better explanations.

= 1.8.3 =
* New options interface - User Interactions: Disable Mouse right click, Disable Text Selection, Disable Copy / Paste, Disable Print, Disable Print Screen, Disable Developer Tools, Disable View Source, Disable Drag / Drop
* Better accessibility for additional details regarding each of the options.
* Improved progress score calculation for Headers.
* A2 Optimized WP - compatibility fix.
* WordPress 6.0.2 tag compatibility update
* Fix CDN option external help page URL.

= 1.8.1 =
* Improved server environment rewrite test checking routines.
* Separate rewrite tests for static files and PHP files. This avoids reporting issues for servers not supporting rewrites for php-files.

= 1.8 =
* Add a new button to reset the current page options.
* Use regex to sanitize the URL arguments
* Relocated the Reset All Settings button to the bottom of the interface.
* Compatibility for Super Page Cache for Cloudflare
* Slight layout improvements and changes.
* WordPress 6.0.1 compatibilit tag

= 1.7.9.2 =
* Change the advanced_notice class within the interfaces to avoid issues caused by 3rd theme.
* Do not remove comments when json request
* WordPress 6.0 compatibilit tag

= 1.7.8.1 =
* When checking and calculating the the Headers protection score, ignore the SSL verification for the domain, to allow usage of invalid certificates.
* Check if set headers are actually passed-through on the front side, as some servers may block that. 
* Set WP_ROCKET_WHITE_LABEL_FOOTPRINT to remove the footer comment for WP Rocket, when active

= 1.7.8 =
* New Security Functionality - Headers. HTTP Response Headers are a powerful tool to Harden Your Website Security.
* Security Headers - Cross-Origin-Embedder-Policy (COEP), Cross-Origin-Opener-Policy (COOP), Cross-Origin-Resource-Policy (CORP), X-Content-Type-Options, X-Download-Options, X-Frame-Options (XFO), X-Permitted-Cross-Domain-Policies, X-XSS-Protection.
* Security Headers - Protection Level graph
* Security Headers - Sample Setup
* Security Headers - Recovery functionality
* Styles and layout improvements
* Code clean-up
* Fix: Append URL arguments to login URL, if exists

= 1.7.6 =
* Run on revision posts, to match URLs and revert to default WordPress  ( e.g. when using Gutenberg editor )
* Require a .php for the customization of the default wp-login.php to avoid cookie issues on password change area. 
* WooCommerce 5.9 compatibility check and tag.

= 1.7.3 =
* Fix: If Emulate CMS active, ensure the buffer is an HTML content

= 1.7.3 =
* New functionality, block wp-json for everyone or non-logged-in users. 
* Fix Emulate CMS documentation url.
* Removed Twitter share.

= 1.7.1 =
* New plugin feature: Emulate CMS
* Update PO language file
* Skip comment removal when admin dashboard.
* Fix: Ignore comment removal when Gutenberg JSON call for blocks, to avoid formatting issues.

= 1.6.4 =
* Ensure compatibility with PHP 8.0
* Update PO language file
* Update documentation URLs within the plugin interfaces, with the  non-www domain wp-hide.com

= 1.6.3.9 =
* Include the "Clean the REST API response" within Sample Setup. 

= 1.6.3.8 =
* New option for JSON REST module - "Clean the REST API response"
* Relocated Feed tab to Rewrite module 

= 1.6.3.7 =
* Output the help title only if there's an help section available through the module settings
* Fix undefined $found_issues
* WordPress 5.8 compatibility tag

= 1.6.3.6 =
* Add dashboard and cpanel to system reserved to avoid permalinks conflicts
* LiteSpeed Cache compatibility update
* WP-Optimize compatibility update 

= 1.6.3.4 =
* Update compatibility file for TranslatePress - Multilingual

= 1.6.3.3 =
* Fix attachment_url_to_postid
* Fix undefined get_metadata_raw

= 1.6.3.2 =
* Confirmation page for Recovery link
* Use home_url() instead site_url() for recovery links to ensure the format is correct for WordPress instances using own directory
* Trigger the login URL change e-mail at shutdown instead init
* Compatibility with TranslatePress - Multilingual
* Fix undefined notice
* Fix meta Uncaught TypeError: count(): Argument
* Slight code improvements 

= 1.6.3.1 =
* Improved description for Test Rewrite procedure, when the server fails to provide a valid response,  rewrite engine is not active or the custom urls are not allowd.
* Fixed Undefined Property Notice

= 1.6.3 =
* Server Environment Check to ensure there are no rewrite issues and the plugin can be safely deployed.
* Interactive feedback with hints and explanations for environment issues.
* Improved UI
* Clear fusion cache when plugin options changed if avada active
* Fix New Search Path replacement to include an end slash, to avoid catch wrong urls
* Check and tag for WordPress 5.7

= 1.6.2.5 =
* Fix: Add slash for "New Search Path" to avoid wrong replacements with urls containing the new search slug.

= 1.6.2.4 =
* Reverse URLs when saving a options, to avoid custom urls to be writted within the database.
* Check if string before making a replacement on metadata
* Compatibility file for Oxigen editor, when using Signatures
* Simple Firewall compatibility file update - check if FernleafSystems\Wordpress\Plugin\Shield\Controller\Controller class exists before apply
                                                         
= 1.6.2.0.4 =
* Update Compatibility file with Oxygen editor, for image with link wrapper
* WordPress 5.6 compatibility tag update

= 1.6.2.0.3 =
* Compatibility file with Oxygen editor

= 1.6.2.0.2 =
* Fix: Check the replacements for update_post_metadata method on text and array types.

= 1.6.2 =
* Reverse URLs when saving a meta data field, to avoid custom urls to be writted within the database.
* Trigger a system notice when deployed on MultiSite, as not being compatible.
* Don't run _init_remove_html_new_lines when AJAX call to avoid front side processing errors.
* WP Rocket compatibility file updates, to works with combined CSS assets
* Shield Security compatibility update, to works with version 10 and up.
* Prevent nottices and errors when attempt to rite on .htaccess file.
* New filter wph/components/wp_oembed_add_discovery_links to allow disabling the Remove Oembed - wp_oembed_add_discovery_links
* New filter wph/components/wp_oembed_add_host_js to allow disabling the Remove Oembed - wp_oembed_add_host_js
* New compatibility file for wePOS plugin
* New compatibility file for Asset CleanUp Pro Page Speed Booster plugin


= 1.6.1.3 =
* Compatibility with Hyper Cache
* Update JSON REST service disable, remove the json_enabled as being deprecated, rely on rest_authentication_errors filter
* Fix WooCommerce Update Database link when changing the default /wp-admin/ slug
* Fix password forget return URL
* Remove callback for Compatibility file for Shield Security within new-admin module

= 1.6.1.1 =
* Fix: Remove callback for Compatibility file for Shield Security within custom login module

= 1.6.1 =
* Compatibility file fix for Shield Security
* WordPress 5.5.1 compatibility tag

= 1.6.0.9.1 =
* Ignore CDN value check for domain name similitude

= 1.6.0.9 =
* LiteSpeed guide on Setup interface
* New functionality - Disable mouse right click
* Compatibility file - JobBoardWP
* Compatibility WP-Optimize - Clean, Compress, Cache
* WordPress 505 compatibility tag

= 1.6.0.8 =
* Avoid using domain name as replacement for any option, or might conclude to wrong replacements within the outputted HTML or wrong reversed urls.
* Add system reserved words as 'wp', 'admin', 'admin-ajax.php'
* Slight General code improvements
* Clean cookie for the new custom slug, if set.
* Integration with WP-Optimize - Clean, Compress, Cache

= 1.6.0.6 =
* WP Job Manager - compatibility update

= 1.6.0.5 =
* New Setup interface with helps and hints on how to use the plugin.
* New Sample Setup, which deploy a basic set-up of plugin options
* Remove internal wp_mail and rely on WordPress core
* Improved FAQ area
* Updated base language file

= 1.6.0.4 =
* Purge cache for Fast Velocity Minify plugin, when clearing internal cache
* Return new admin slug when calling admin_url() and if default admin is customized
* Use no-protocol when loading the files, to ensure they are being loaded over current domain protocol
* BuddyPress compatibility file update
* Elementor compatibility file update
* ShortPixel Adaptive Images compatibility file update
* WooCommerce compatibility file update
* WP Rocket compatibility file update
* New compatibility file for Fast Velocity Minify
* New compatibility file for LiteSpeed Cache
* New compatibility file for Swift Performance
* New compatibility file for WP Speed of Light

= 1.6 =
* New filter wp-hide/content_urls_replacement
* Compatibility with Ultimate Member, user picture upload fix
* Updated compatibility with W3 Cache, using postprocessorRequire option
* Fluentform compatibility updates
* Outputs the whole stack of errors for $wp_filesystem if exists
* Typo fix in Uploads module


= 1.5.9.9 =
* Updated procedure for server type identification
* Add new type text/plain for filterable content
* Add server_nginx_config to main class, to be used within other modules
* Updated rewrite quantifier for IIS from .+ to .*
* Ignore wp-content block if agent is LiteSpeed-Image

= 1.5.9.5 =
* Updated is_filterable_content_type method, return TRUE if no Content-Type header found

= 1.5.9.4 =
* Fix readme demo site protocol

= 1.5.9.4 =
* Fix "undefined method WPH_functions::get_site_module_saved_value()" when content type is text/xml

= 1.5.9.3 =
* Check for filterable buffer content type, before doing replacements, to prevent erroneously changes
* Update only URLs on XML content type
* Updated plugin demo site URL on readme file
* Compatibility update for ShortPixel Image Optimizer plugin 
* Notice possible issue for Cron block on certain servers

= 1.5.9 =
* New admin interfaces skin.
* Relocated plugin assets within a different folder for better organisator. 
* Updated mu-loader module
* Add help and hints for each options for easier understanding.
* Allow same base slug to be used for individual plugins
* Updated language file
* Check if environment file is not available and outputs admin messages
* Environment class with relocated environment json, to avoid security scanners false reports.
* Cache Enabler plugin compatibility module 
* WoodMart theme compatibility
* Compatibility module for WP Smush  and WP Smush PRO plugins
* Add the new filter available for WP Rocket to change css content
* WebARX compatibility module update
* W3 Cache module update

= 1.5.8.2 =
* Ensure base slug (e.g. base_slug/slug ) is not being used for another option to prevent rewrite conflicts
* Return correct home path when using own directory for WordPress and hosting account use the same slug in the path.
* Relocated get_default_variables() function on a higher priority execution level, to get default system details. 
* Switched Yes / No options selection, to outputs first No then Yes ( more logical )

= 1.5.8 =
* Add reserved option names to avoid conflicts e.g. wp
* Always clear any code plugin cache when plugin update
* Easy Digital Downloads compatibility
* Elementor plugin compatibility
* Fusion Builder plugin compatibility
* Divi theme compatibility updates
* WP Fastest Cache plugin compatibility updates
* Check if ob_gzhandler and zlib.output_compression before using 'ob_gzhandler' output buffering handler


= 1.5.7 =
* Autoptimize css/js cache and minify compatibility
* Wp Hummingbird and WP Hummingbird PRO assets cache compatibility

= 1.5.6.9 =
* New functionality: Remove Link Header 

= 1.5.6.8 =
* Fix: Call for invalid method WP_Error::has_errors()
* Fix: Attempt to clear Opcache if API not being restricted

= 1.5.6.7 =
* Allow internal cron jobs to run even if wp-cron.php is blocked.
* Check with wp_filesystem for any errors and output the messages, before attempt to write any content
* Trigger site cache clear on settings changed or code update
* Slight css updates
* Mark block option in red text for better visibility and user awareness

= 1.5.6.4 =
* Fix: Keep double quote at the start of the replacements when doing JSON matches to avoid replacing strings for other domains
* Fix: Run compatibility pachage for "ShortPixel Adaptive Images" only when plugin is active

= 1.5.6.3 =
* Fix: remove javascript comments produce worng replacements on specific format.

= 1.5.6.2 =
* Use current site prefix when retreiving 'user_roles'

= 1.5.6 =
* Fix BBPress menus by calling directly the wp_user_roles option ratter get_roles()
* Replace comments within inline JavaScript code when Remove Comments active
* Possible login conflict notices when using WebArx, WPS Hide Login
* New action wp-hide/admin_notices when plugin admin notices
* Return updated url when calling admin_url instead replaced when buffer outputs to ensure compatibility with specific plugins

= 1.5.5.9 =
* Compatibility module for ShortPixel Adaptive Image plugin
* Add support for texarea fields within plugin options interface
* Fixed urls for minified files when using WP Rocket cache plugin

= 1.5.5.7 =
* Filter remove fix

= 1.5.5.6 =
* Fix log-in page when using Wp Rocket cache

= 1.5.5.5 =
* Fix admin dashboard replacements when using Wp Rocket cache
 

See full list of changelogs at https://wp-hide.com/plugin-changelogs/

== Upgrade Notice ==

Always keep plugin up to date.

== Localization ==
Please help and translate this plugin to your language at <a href="https://translate.wordpress.org/projects/wp-plugins/wp-hide-security-enhancer">https://translate.wordpress.org/projects/wp-plugins/wp-hide-security-enhancer</a>

You are kindly asked to promote this plugin if it comes up to your expectations via an article on your site or any other place. If you liked this code/WP-Hide or if it helped with your project, why not leave a 5 star review on this board.

