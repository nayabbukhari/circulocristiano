=== User Role Editor Pro ===
Contributors: Vladimir Garagulya (https://www.role-editor.com)
Tags: user, role, editor, security, access, permission, capability
Requires at least: 4.0
Tested up to: 4.4
Stable tag: 4.21.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

With User Role Editor WordPress plugin you may change WordPress user roles and capabilities easy.

== Description ==

With User Role Editor WordPress plugin you can change user role (except Administrator) capabilities easy, with a few clicks.
Just turn on check boxes of capabilities you wish to add to the selected role and click "Update" button to save your changes. That's done. 
Add new roles and customize its capabilities according to your needs, from scratch of as a copy of other existing role. 
Unnecessary self-made role can be deleted if there are no users whom such role is assigned.
Role assigned every new created user by default may be changed too.
Capabilities could be assigned on per user basis. Multiple roles could be assigned to user simultaneously.
You can add new capabilities and remove unnecessary capabilities which could be left from uninstalled plugins.
Multi-site support is provided.

== Installation ==

Installation procedure:

1. Deactivate plugin if you have the previous version installed. If you have free version you should deactivate it too.
2. Extract "user-role-editor-pro.zip" archive content to the "/wp-content/plugins/user-role-editor-pro" directory.
3. Activate "User Role Editor Pro" plugin via 'Plugins' menu in WordPress admin menu. 
4. Go to the "Settings"-"User Role Editor" and adjust plugin options according to your needs. For WordPress multisite URE options page is located under Network Admin Settings menu.
5. Go to the "Users"-"User Role Editor" menu item and change WordPress roles and capabilities according to your needs.


== Changelog ==
= [4.21.1] 17.12.2015 =
* Core version: 4.21.1
* Fix: 'Update' button did not work at User's Capabilities page due to confirmation dialog call error.
* Fix: post custom fields 'post_access_error_action', 'post_access_error_message' are hidden now from the users without 'ure_view_posts_access' capability. 
* Update: German translation

= [4.21] 12.12.2015 =
* Core version: 4.21
* It's possible to switch off the update role confirmation (Settings - User Role Editor - General tab).
* Standard confirm box before role update was replaced with custom one to exclude 'Prevent this page from creating additional dialogs' option in the Google Chrome browser.
* Option "Show plugins/themes notices to admin only" was added.
* "Additional options" section was added to the user role editor page. Currently it contains the only "Hide admin bar". The list of options may be customized/extended by developers via "ure_role_additonal_options" filter.
* "Meta Boxes Access" add-on allows to manage access for roles to meta boxes of editor (posts, pages, custom post types) and dashboard pages. 
* "Activate "Create" capability" was extended to the "Attachments".
* License key is checked in the real time after its input to help exclude input errors.
* 'ure_default_post_access_error_action' filter added to allow modify default value for the post view access error action: 1 - 404 HTTP error or 2 - show error message
* Fix: create_posts capability was lost for custom post types in spite of 'activate create capability option'.
* Fix: Content view restrictions: roles list is saved now for attachments.
* Fix: Removed hard coded folder name (user-role-editor) from the used paths. User Role Editor Pro is hidden now from user without permissions (administrator or ure_edit_roles), even if user has access to the 'activate_plugins' capability.
* Fix: Translation strings
* German translation added

= [4.20] 14.10.2015 =
* Core version: 4.19.3
* Added option to force all custom posts types to use its own custom capabilities set instead of usage of one built on 'post', e.g. 'edit_videos' instead of 'edit_posts'.
* User Role Editor Options page help section was updated. 
* Fix: Admin menu access restrictions were not applied at 'new-user.php' page under multisite for the single site administrator role with 'allow_edit_users_to_not_super_admin' option turned on. Special flag was set to indicate that single site admin gets raised (superadmin) permissions temporary for the 'user-new.php' page, but current user is not the superadmin really. 
  (This temporary permissions raising is done to allow single site admin to add new users under multisite.)
* Fix: Custom posts types selection query was updated to include all custom post types except 'built-in' types when adding custom capabilities for them.
* Fix: Admin menu access: URLs beyound admin menu are not blocked for the "block not selected" model now. This allows to work with posts under this model of blocking for example. 

= [4.19.2] 01.10.2015 =
* Core version: 4.19.2
* Fix: Default role value has not been refreshed automatically after change at the "Default Role" dialog.
* Fix: global $post variable was changed in some cases by the posts view restrictions add-on.
* Fix: Admin menu access add-on: User could upload new media at the Post Editor with "Media -> Add New" menu item blocked. "File Upload" tab is removed in this case now. User may select from the existing Media Library items only.
* More detailed notice messages are shown after default role change - to reflect a possible error or problem.
* Other default roles (in addition to the primary role) has been assigned to a new registered user for requests from the admin back-end only. Now this feature works for the requests from the front-end user registration forms too (including multisite).
* Interface to Posts bulk action "Edit access" was available to the users without "ure_edit_posts_access" capability - fixed. Action itself was not fulfilled (blocked at server side) due to obvious permissions error.
* Content view restrictions add-on: custom post types selection enhanced in order to include types which are not public 
* Content view restrictions add-on: processes now custom post type content beyond the main loops, including 'wlbdash'(Dashboard) post type from "White Label Branding for WordPress Multisite" plugin.
* Content edit restrictions add-on: supports unique create custom post type capability even in case it does not use 'edit_' in its name. For example for 'wlbdash' post type, create post capability will get name 'create_wlbdashs' instead of default 'wlb_dashboard_tool'.
* Admin menu access add-on: bug was fixed for URL starting from 'admin.php?page='
* Added new filter 'ure_get_allowed_gf_forms'. It allows to modify array of Gravity Forms ID available to the current user.
* CSS enhanced to exclude column wrapping for the capabilities with the long names.
* The translation text domain was changed to the plugin slug (user-role-editor) for the compatibility with translations.wordpress.org

= [4.19] 04.08.2015 =
* It is possible to assign to the user multiple roles directly through a user profile edit page. 
* Custom SQL-query (checked if the role is in use and slow on the huge data) was excluded and replaced with WordPress built-in function call. [Thanks to Aaron](https://wordpress.org/support/topic/poorly-scaling-queries).
* Bulk role assignment to the users without role was rewritten for cases with a huge quant of users. It processes just 50 users without role for the one request to return the answer from the server in the short time.
* Admin menu access add-on: 
*   1) 'block not selected' access model was added to the default 'block selected' one. It is more convenient in cases when you wish to block automatically all new added menu items. 
*   2) use top checkbox control to select/unselect all checkboxes. Click on it with 'Shift' key inverts current selection.
* Other roles access add-on: 
*   1) 'block not selected' access model was added to the default 'block selected' one. It is more convenient in cases when you wish to block automatically all new added roles.
*    2) use top checkbox control to select/unselect all checkboxes. Click on it with 'Shift' key inverts current selection.
* It is possible to set restrictions to the main site widgets at the Network Admin and replicate them to the whole network. 
* Content view restrictions add-on: 
*   1) It is possible to set what categories (tags/custom taxonomies) are allowed/prohitited to view for the selected role.
*   2) It is possible to select between HTTP 404 error or custom error message for the case of access error.
*   3) Fixed to work for the custom post types with own user capabilities set.
*   4) "No role for this site" item is available in the roles list at a post level interface.
*   5) Restriction is not applied to the post by default if logged in user can edit it. It is possible to change this rule
*   using filter 'ure_restrict_content_view_for_authors_and_editors'. It takes and returns 1 boolean parameter: false - do not restrict, true - restrict.
*   6) Enhanced compatibility with the Events Manager plugin ( https://wordpress.org/plugins/events-manager ).
*   7) Fixed bug which did not allow to open roles list for a new (not saved) post.
*   8) It is possible to retrieve post view access restrictions data for the post ID from other plugins, 
    for example do not sent new post notification to the users, who don't have access to view it. 
*   Function ure_get_post_view_access_users() returns the object with properties: 
*     1) restriction: string: prohibited/allowed; 
*     2) roles - array of roles, for which this restriction is applied; 
*     3) users: array of user ID, which have those roles.   
* Edit posts/pages restrictions add-on: 
*   1) Bug fix: when user with posts/pages edit restrictions may access restricted posts/pages directly by post ID and got 'Edit' URL for the restricted post at the front-end.  
*   2) If you set 'edit posts/pages with author user ID' restriction, it is applied to ALL post types. That is if author does not have any posts at some post type, user will see the empty list of posts at that type. 
If you set 'edit posts/pages/custom post types with ID' only then restrictions are applied only to the post types to which posts belongs.
*   3) It is possible now to set edit restrictions for the user by category/taxonomy ID.
*   4) Pages filtering enhanced for compatibility with other plugins, respecting "get_pages" filter (like "CMS Tree Page View" one).
*   5) User with post/pages edit restrictions applied can see own unattached media library items in additions to the allowed posts attachments.
*   6) If posts/pages restrictions were not set for the user, full list of media library items is available.
*   7) Filter ure_attachments_show_full_list allows to show full Media Library items list to the user with editing restrictions set.
*   8) Filter ure_posts_show_full_list allows to show full posts/pages/custom posts types list to the user with editing restrictions set.

= 4.18.5 =
* 14.06.2015
* It is possible to input license code to the wp-config.php now. Add this line: define(URE_LICENSE_KEY, 'your-license-code-here');
Users uncomfortable with wp-config.php editing may still input license code at "Settings->User Role Editor->General" tab.
* License code saved at the "Settings->User Role Editor->General" tab is not removed anymore after change of site absolute path, host or database name.
* Bug was fixed: "Network Update" did not work at FireFox due to JavaScript bug.
* PHP notice was removed. It was shown at the Plugins page, when an update to the URE Pro was available.

= 4.18.4 =
* 28.05.2015
* Edit posts/pages restrictions add-on: Now user can not edit prohibited post/page manually inserting its ID to the edit URL.
* Admin menu access add-on: 'Customize' menu item is available now for non-English WordPress default languages too.

= 4.18.3 =
* 06.05.2015
* Bug fix for "Admin menu access" add-on: direct access to the wp-admin/customize.php link (Appearance->Customize menu item) was not blocked properly. 
* As additional security measure "Welcome" panel is removed for the role with access restriction to the "Customize" admin menu item.

= 4.18.2 =
* 30.04.2015
* Calls to the functions add_query_arg(), remove_query_arg() are escaped with esc_url_raw() to exclude potential XSS vulnerabilities.

= 4.18.1 =
* 24.02.2015
* Fixed PHP fatal error for "Reset" roles operation.
* Fixed current user capability checking before URE Options page open.
* 3 missed phrases were added to the translations files.

= 4.18 =
* 11.02.2015
* Own custom user capabilities, e.g. 'ure_edit_roles' are used to restrict access to User Role Editor functionality.
* Posts/pages edit access restriction add-on functionality was extended to the Media Library. Posts/pages attachments becomes unavailable automatically if correspondent post/page edit is prohibited.
* Posts/pages edit access restriction add-on works with custom post types now.
* Posts/pages view access restriction works with custom post types now.
* Admin menu items with empty user capability are available in "Admin menu access" add-on now. "Participants Database" plugin defines its menu this way.
* Some plugins use meta capabilities instead of real user capabilities, like 'jetpack_admin_page' in "JetPack" or 'wpcf7_read_contact_forms' in "Contact Form 7". "Admin menu access" add-on recognizes such meta capabilities now. These meta-caps are replaced at "Admin menu" window with correspondent (mapped) real user capabilities for your further reference.
* Admin menu access add-on updated: 'Howdy, ...' menu including 'Logout' menu item at top bar admin menu will not disappear after blocking 'Profile' menu. 
* Top bar menu 'SEO' from "WP SEO from Yoast" plugin is blocked if user has no 'manage_options' capability or correspondent admin menu is blocked.
* Admin menu blocking is available for 'administrator' role under multisite. You should be superadmin. Do not give administrator access to URE in this case.
* More universal checking applied to the custom post type capabilities creation to exclude not existing property notices.
* New option "Edit user capabilities" was added. If it is unchecked - capabilities section of selected user will be shown in the readonly mode. Administrator (except superadmin for multisite) can not assign capabilities to the user directly. He should make it using roles only.
* Fixed JavaScript bug with 'Reset Roles' for FireFox v.34.

Click [here](http://role-editor.com/changelog)</a> to look at [the full list of changes](http://role-editor.com/changelog) of User Role Editor plugin.
