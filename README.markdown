#WordPress Admin Page Class

The Admin Page Class is used by including it in your plugin files and using its methods to create custom Admin Pages. It is meant to be very simple and straightforward. 
###Stable tag: 1.3.0
###Tested up to Wordpress: 3.9 beta 2

##Description

The Admin Page Class is used by including it in your plugin files and using its methods to create custom Admin Pages. It is meant to be very simple and straightforward. 
for usage Take a look at the `class-usage-demo.php` file which can also be tested as a WordPress Plugin. Other options are available for each field which can be see in the 'admin-page-class.php' file,

##ScreenShots

[![Simple Options](http://i.imgur.com/oKoUqs.png "Simple Options")](http://i.imgur.com/oKoUq.png)
[![Fancy Options](http://i.imgur.com/6bqE4s.png "Fancy Options")](http://i.imgur.com/6bqE4.png)
[![Editor Options](http://i.imgur.com/geBbGs.png "Editor Options")](http://i.imgur.com/geBbG.png)
[![Advanced Options](http://i.imgur.com/uOpQzs.png "Advanced Options")](http://i.imgur.com/uOpQz.png)
[![Import Export](http://i.imgur.com/NSJ3Rs.png "Import Export")](http://i.imgur.com/NSJ3R.png)
[![Feild validation](http://i.imgur.com/qZxoos.png "Validation")](http://i.imgur.com/qZxoo.png)


##Installation
Simple steps:  

1.  Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation.
2.  Then activate the Plugin from Plugins page.
3.  Done!

##Frequently Asked Questions

####What are the requirements?
 *Requires at least WordPerss version  3
 *PHP 5.2 and up.
 
##Author
Ohad Raz http://en.bainternet.info 

[Donate](http://en.bainternet.info/donations)

##License:

Copyright © 2012 Ohad Raz, <admin@bainternet.info>  , Licensed under the [GPL](http://www.gnu.org/licenses/gpl.html).

##Changelog
###1.3.0
   *Added action hooks to allow callback support #50.
      `WP_EX_before_save`
      `WP_EX_after_save`

   *Fixed import export function #54.
   *Fixed #46.
   *Fixed #42.
   *Updated  Select2 to ver 3.4.6.

###1.2.9
   *Fixed  #38. the post type parameter should be passed inside args array. see demo plugin.

###1.2.8
   *Fixed tab cookie extra backslash issue.

###1.2.7
   *Fixed #36.
   *Fixed #37.
   * MP6 css hack Fixed.

###1.2.6
   *Better Google Fonts Hanlding (now calls the actuall api)

###1.2.5
   *Fix references for the class for PHP 5.4 compatibility.
   *Fixed google fonts not loading.

###1.2.4
   * Fixed color picker not loading

###1.2.3
   * Better handling custom validation class with `apc_validattion_class_name` filter hook.
   * Fixed #27
   * Fixed #28

###1.2.2
   * Added WordPress 3.5 media manager as image upload.
   * Fixed #26.
   * cleaned up admin-page-class.js form junk, comments and bad logic.

###1.2.1
   * Added class to checkbox labels.

##1.2.0
   * Added field description to demo plugin.
   * Fixed validation function.

###1.1.9
   * Added native field validation with error display.
   * Added validation methods (`is_email`,`is_alphanumeric`,`is_url`,`is_length`,`is_maxlength`,`is_minlength`,`is_maxvalue`,`is_minvalue`,`is_numeric`).
   * Added new filter hooks as requested in :#25 (`apc_form_name` ,`apc_form_class` ,`apc_form_id`).


###1.1.8
   * Fixed issue #22 
   * Fixed plupload field not working. function renamed!
   * cleaned up some junk code. (`addTax` `addPosts`)
   * `finish` method is now in soft deprecation.


###1.1.7
   * Fixed issue #19 
   * Fixed issue #20
   * Updated demo plugin with checkboxes in repater and conditional blocks.
   * Replaced iphone-style-checkboxes with my own [FancyCheckbox] (https://github.com/bainternet/FancyCheckbox) script.


###1.1.6 
   * Fixed #17 #21

###1.1.5
   * Added class to most field
   * Added open to disable fancy checkbox and select2 by class
   * Wrapped checkboxes in label when iphone style is turned off.
   * When in checkbox field you can how add `.no-toggle` to disable iphone style.
   * When in select field you can how add `.no-fancy` to disable select2.

###1.1.4
   * Fixed local.mo file load.
   * added textdomain to demo plugin.
   * added textdomain to for missing strings.
   * updated mo's and po's :) .
   * Fixed and closed issue #16 .
   * Fixed color picker z-index issue #17 .
   * Fixed Validating function notice #17 .
   * Fixed Conditional Field not loading saved data after re-enable #17 .

###1.1.3
   * Added Select2.
   * Better field check function calling.
   * Fixed minor css bug in select2 and Typo field.
   * Fixed classes in posts, taxonomies, roles fields.

###1.1.2
   * Fixed Typo issue #17 
   * Added support for WordPress 3.5 iris color picker.
   * fixed Notice: undefined index 'multiple' on addRole inside repeater and conditional blocks. issue #17 
   * fixed color picker height on typo field.
   * added classes to posts, taxonomies, roles fields.
   * Added Iphone styled checkboxes to posts, taxonomies, roles checkbox list fields.
   * fixed delete image from conditional block, props to @brasofilo issue #17
   * Fixed Typo in mo file. issue #16 
   * Added Spanish and Portuguese translations once angain thanks to @brasofilo issue #16

###1.1.1
   * Fixed issue #15

###1.1.0
   * Fixed issue #14

###1.0.9
   * Added Text Domain and localization to all strings.
   * fixed get_locale issue.

###1.0.8
   * Fixed issue #13 props to @cyberbitegroup
   * Fixed Conditional tag notice 

###1.0.7
   * Fixed datepicker and time picker issue #11

###1.0.6
   * Typo which solves issue #11

###1.0.5
   * Fixed jQuery UI Version conflict

###1.0.4
   * Fixed issue #9 item 4 in the list.
   * Added a new filter hook for deleting images `apc_delete_image`.
   * Added a "remember last tab" feature.

###1.0.3
   * Fixed issue #10 props to @brasofilo

###1.0.2
   * Added missing plupload files and added a plupload field to demo plugin.

##1.0.0
   * has_fields now checks in repater and conditional fields.

###0.9.9
   * Fixed Typo field.
   * added google fonts and font weights.

###0.9.8
   * added hidden field to skip fields
   * cleaned up code a bit.

###0.9.7
   * added plupload field.
   * Fixed color picker on typo field when no other color field is present.
   * Fixed image and typo fields mising description.
   * Fixed image field on repater and conditional blocks when no other image field is present. issue #5

###0.9.6
   * added filters and hooks
      * `admin_page_class_before_page` action hook
      * `admin_page_class_submit_class` filter hook
      * `admin_page_class_after_tab_open` action hook
      * `admin_page_class_before_repeater` action hook
      * `admin_page_class_after_repeater` action hook
      * `admin_page_class_import_export_tab` action hook
      * `admin_page_class_after_page` action hook

   * fixed repater id for none allowed id names
   * fixed repater fields std value
   * fixed import export tab closure.

###0.9.5
   * Fixed import export tab notice.

###0.9.4
   * Fixed APC debug error.

###0.9.3
   * Added stripslashes for repater text and textarea fields on admin panel.

###0.9.2
   * Fixed media uploader on 3.4 issue #3

###0.9.1
   * Changed private vars to protected.

###0.9
   * Fixed checkbox field default value true bug

###0.8.1
   * Fixed issue #2

###0.8
   * Added import export freature, included in demo plugin.
   * Added download export dump feature.
   * Added classes with filter hook to tab listing LI tags (to add images).
   * Fixed "insert to post" text on media uploader.

###0.7
   * Added conditinal block to demo plugin.
   * Fixed `std` selection for normal fields.

###0.6
   * Added Conditional field block

###0.5
   * Added admin branding filters.

###0.4
   * Added sortable field.
   * Added Sortable option to repeater block.
   * Added custom image preview sizes.

###0.3
   * Added Typography field type.

###0.2
   * Fixed use with theme custom path.

###0.1
   * Initial public release.

[![Analytics](https://ga-beacon.appspot.com/UA-50573135-2/admin-page-class/main)](https://github.com/bainternet/Admin-Page-Class)
