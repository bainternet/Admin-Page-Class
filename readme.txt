=== Wordpress Admin Page Class ===
Contributors: bainternet 
Donate link:http://en.bainternet.info/donations
Tags: admin page, option page, options panel, admin options panel
Requires at least: 3
Tested up to: 3.5
Stable tag: 1.1.5

The Admin Page Class is used by including it in your plugin files and using its methods to create custom Admin Pages. It is meant to be very simple and straightforward. 
 

== Description ==

The Admin Page Class is used by including it in your plugin files and using its methods to create custom Admin Pages. It is meant to be very simple and straightforward. 
for usage Take a look at the `class-usage-demo.php` file which can also be tested as a WordPress Plugin. Other options are available for each field which can be see in the 'admin-page-class.php' file,


== Installation ==
Simple steps:  

1.  Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation.
2.  Then activate the Plugin from Plugins page.
3.  Done!

== Frequently Asked Questions ==
=What are the requirements?=

PHP 5.2 and up.


== Changelog ==
= 1.1.5 =
Added class to most field
Added open to disable fancy checkbox and select2 by class
Wrapped checkboxes in label when iphone style is turned off.
When in checkbox field you can how add `.no-toggle` to disable iphone style.
When in select field you can how add `.no-fancy` to disable select2.

= 1.1.4 = 
fixed local.mo file load.
added textdomain to demo plugin.
added textdomain to for missing strings.
updated mo's and po's :) .
Fixed and closed issue #16 .
Fixed color picker z-index issue #17 .
Fixed Validating function notice #17 .
Fixed Conditional Field not loading saved data after re-enable #17 .

= 1.1.3 =
Added Select2.
Better field check function calling.
Fixed minor css bug in select2 and Typo field.
Fixed classes in posts, taxonomies, roles fields.

= 1.1.2 =
Fixed Typo issue #17 
Added support for WordPress 3.5 iris color picker.
fixed Notice: undefined index 'multiple' on addRole inside repeater and conditional blocks. issue #17 
fixed color picker height on typo field.
added classes to posts, taxonomies, roles fields.
Added Iphone styled checkboxes to posts, taxonomies, roles checkbox list fields.
fixed delete image from conditional block, props to @brasofilo issue #17
Fixed Typo in mo file. issue #16 
Added Spanish and Portuguese translations once angain thanks to @brasofilo issue #16

= 1.1.1 =
Fixed issue #15

= 1.1.0 =
Fixed issue #14

= 1.0.9 =
Added Text Domain and localization to all strings.
fixed get_locale issue.

= 1.0.8 = 
Fixed issue #13 props to @cyberbitegroup
Fixed Conditional tag notice 

= 1.0.7 = 
Fixed datepicker and time picker issue #11
= 1.0.6 = 
Typo which solves issue #11

= 1.0.5 =
Fixed jQuery UI Version conflict

= 1.0.4 = 
Fixed issue #9 item 4 in the list.
Added a new filter hook for deleting images `apc_delete_image`.
Added a "remember last tab" feature.

= 1.0.3 = 
Fixed issue #10 props to @brasofilo

= 1.0.2 =
Added missing plupload files and added a plupload field to demo plugin.

= 1.0.0 =
has_fields now checks in repater and conditional fields.



= 0.9.9 = 
Fixed Typo field.
added google fonts and font weights.


= 9.8 = 
added hidden field to skip fields
cleaned up code a bit.

= 9.7 =
added plupload field.
Fixed color picker on typo field when no other color field is present.
Fixed image and typo fields mising description.
Fixed image field on repater and conditional blocks when no other image field is present. issue #5

= 9.6 =
added filters and hooks
admin_page_class_before_page action hook
admin_page_class_submit_class filter hook
admin_page_class_after_tab_open action hook
admin_page_class_before_repeater action hook
admin_page_class_after_repeater action hook
admin_page_class_import_export_tab action hook
admin_page_class_after_page action hook

fixed repater id for none allowed id names
fixed repater fields std value
fixed import export tab closure.

= 9.5 = 
Fixed import export tab notice.

= 9.4 = 
Fixed APC debug error.

= 0.9.3 =
Added stripslashes for repater text and textarea fields on admin panel.


= 0.9.2 =
Fixed media uploader on 3.4 issue #3

= 0.9.1 = 
Changed private vars to protected.

= 0.9 =
Fixed checkbox field default value true bug

= 0.8.1 =
Fixed issue #2

= 0.8 =
Added import export freature, included in demo plugin.
Added download export dump feature.
Added classes with filter hook to tab listing LI tags (to add images).
Fixed "insert to post" text on media uploader.

= 0.7 =
Added conditinal block to demo plugin.
Fixed `std` selection for normal fields.

= 0.6 =
Added Conditional field block

= 0.5 =
Added admin branding filters.

= 0.4 =
Added sortable field.
Added Sortable option to repeater block.
Added custom image preview sizes.

= 0.3 =
Added Typography field type.

= 0.2 =
Fixed use with theme custom path.

= 0.1 = 
Initial public release.