<?php
/*
Plugin Name: Demo Admin Page
Plugin URI: http://en.bainternet.info
Description: My Admin Page Class usage demo
Version: 1.0.0
Author: Bainternet, Ohad Raz
Author URI: http://en.bainternet.info
*/



  //include the main class file
  require_once("admin-page-class/admin-page-class.php");
  
  
  /**
   * configure your admin page
   */
  $config = array(    
       'menu'=> 'settings',             //sub page to settings page
      'page_title' => 'Demo Admin Page',       //The name of this page 
       'capability' => 'edit_themes',         // The capability needed to view the page 
       'option_group' => 'demo_options',       //the name of the option to create in the database
       'id' => 'admin_page',            // meta box id, unique per page
       'fields' => array(),            // list of fields (can be added by field arrays)
    'local_images' => false,          // Use local or hosted images (meta box images for add/remove)
    'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
  );  
  
  /**
   * Initiate your admin page
   */
  $options_panel = new BF_Admin_Page_Class($config);
  $options_panel->OpenTabs_container('');
  
  /**
   * define your admin page tabs listing
   */
  $options_panel->TabsListing(array(
    'links' => array(
    'options_1' =>  __('Simple Options'),
    'options_2' =>  __('Fancy Options'),
    'options_3' => __('Editor Options'),
    'options_4' => __('WordPress Options'),
    'options_5' =>  __('Advanced Options'),
  'options_6' =>  __('Import Export'),
    )
  ));
  
  /**
   * Open admin page first tab
   */
  $options_panel->OpenTab('options_1');

  /**
   * Add fields to your admin page first tab
   * 
   * Simple options:
   * input text, checbox, select, radio 
   * textarea
   */
  //title
  $options_panel->Title("Simple Options");
  //An optionl descrption paragraph
  $options_panel->addParagraph("This is a simple paragraph");
  //text field
  $options_panel->addText('text_field_id',array('name'=> 'My Text ', 'std'=> 'std TEXT'));
  //textarea field
  $options_panel->addTextarea('textarea_field_id',array('name'=> 'My Textarea ', 'std'=> 'std TEXTarea'));
  //checkbox field
  $options_panel->addCheckbox('checkbox_field_id',array('name'=> 'My Checkbox ', 'std' => true));
  //select field
  $options_panel->addSelect('select_field_id',array('selectkey1'=>'Select Value1','selectkey2'=>'Select Value2'),array('name'=> 'My select ', 'std'=> array('selectkey2')));
  //radio field
  $options_panel->addRadio('radio_field_id',array('radiokey1'=>'Radio Value1','radiokey2'=>'Radio Value2'),array('name'=> 'My Radio Filed', 'std'=> array('radiokey2')));
  /**
   * Close first tab
   */   
  $options_panel->CloseTab();


  /**
   * Open admin page Second tab
   */
  $options_panel->OpenTab('options_2');
  /**
   * Add fields to your admin page 2nd tab
   * 
   * Fancy options:
   *   image uploader
   *  date picker
   *  time picker
   *  color picker
   */
  //title
  $options_panel->Title("Fancy Options");
  //Typography field
  $options_panel->addTypo('typography_field_id',array('name' => "My Typography",'std' => array('size' => '14px', 'color' => '#000000', 'face' => 'arial', 'style' => 'normal')));
  //Image field
  $options_panel->addImage('image_field_id',array('name'=> 'My Image ','preview_height' => '120px', 'preview_width' => '440px'));
  //date field
  $options_panel->addDate('date_field_id',array('name'=> 'My Date '));
  //Time field
  $options_panel->addTime('time_field_id',array('name'=> 'My Time '));
  //Color field
  $options_panel->addColor('color_field_id',array('name'=> 'My Color '));
  
  /**
   * Close second tab
   */ 
  $options_panel->CloseTab();



  /**
   * Open admin page 3rd tab
   */
  $options_panel->OpenTab('options_3');
  /**
   * Add fields to your admin page 3rd tab
   * 
   * Editor options:
   *   WYSIWYG (tinyMCE editor)
   *  Syntax code editor (css,html,js,php)
   */
  //title
  $options_panel->Title("Editor Options");
  //wysiwyg field
  $options_panel->addWysiwyg('wysiwyg_field_id',array('name'=> 'My wysiwyg Editor '));
  //code editor field
  $options_panel->addCode('code_field_id',array('name'=> 'Code Editor ','syntax' => 'php'));
  /**
   * Close 3rd tab
   */ 
  $options_panel->CloseTab();


  /**
   * Open admin page 4th tab
   */
  $options_panel->OpenTab('options_4');
  
  /**
   * Add fields to your admin page 4th tab
   * 
   * WordPress Options:
   *   Taxonomies dropdown
   *  posts dropdown
   *  Taxonomies checkboxes list
   *  posts checkboxes list
   *  
   */
  //title
  $options_panel->Title("WordPress Options");
  //taxonomy select field
  $options_panel->addTaxonomy('taxonomy_field_id',array('taxonomy' => 'category'),array('name'=> 'My Taxonomy Select'));
  //posts select field
  $options_panel->addPosts('posts_field_id',array('post_type' => 'post'),array('name'=> 'My Posts Select'));
  //Roles select field
  $options_panel->addRoles('roles_field_id',array(),array('name'=> 'My Roles Select'));
  //taxonomy checkbox field
  $options_panel->addTaxonomy('taxonomy2_field_id',array('taxonomy' => 'category','type' => 'checkbox_list'),array('name'=> 'My Taxonomy Checkboxes'));
  //posts checkbox field
  $options_panel->addPosts('posts2_field_id',array('post_type' => 'post','type' => 'checkbox_list'),array('name'=> 'My Posts Checkboxes'));
  //Roles checkbox field
  $options_panel->addRoles('roles2_field_id',array('type' => 'checkbox_list'),array('name'=> 'My Roles Checkboxes'));


  /**
   * Close 4th tab
   */
  $options_panel->CloseTab();
  /**
   * Open admin page 5th tab
   */
  $options_panel->OpenTab('options_5');
  //title
  $options_panel->Title("Advanced Options");

  //sortable field
   $options_panel->addSortable('sortable_field_id',array('1' => 'One','2'=> 'Two', '3' => 'three', '4'=> 'four'),array('name' => "my sortable field"));

  /*
   * To Create a reapeater Block first create an array of fields
   * use the same functions as above but add true as a last param
   */
  $repeater_fields[] = $options_panel->addText('re_text_field_id',array('name'=> 'My Text '),true);
  $repeater_fields[] = $options_panel->addTextarea('re_textarea_field_id',array('name'=> 'My Textarea '),true);
  $repeater_fields[] = $options_panel->addImage('image_field_id',array('name'=> 'My Image '),true);
  
  /*
   * Then just add the fields to the repeater block
   */
  //repeater block
  $options_panel->addRepeaterBlock('re_',array('sortable' => true, 'inline' => true, 'name' => 'This is a Repeater Block','fields' => $repeater_fields));
  
  /**
   * To Create a Conditional Block first create an array of fields (just like a repeater block
   * use the same functions as above but add true as a last param
   */
  $Conditinal_fields[] = $options_panel->addText('con_text_field_id',array('name'=> 'My Text '),true);
  $Conditinal_fields[] = $options_panel->addTextarea('con_textarea_field_id',array('name'=> 'My Textarea '),true);
  $Conditinal_fields[] = $options_panel->addImage('con_image_field_id',array('name'=> 'My Image '),true);
  
  /**
   * Then just add the fields to the repeater block
   */
  //conditinal block 
  $options_panel->addCondition('conditinal_fields',
      array(
        'name'=> __('Enable conditinal fields? '),
        'desc' => __('<small>Turn ON if you want to enable the <strong>conditinal fields</strong>.</small>'),
        'fields' => $Conditinal_fields,
        'std' => false
      ));
  /**
   * Close 5th tab
   */
  $options_panel->CloseTab();
   
  /**
   * Open admin page 6th tab
   */
  $options_panel->OpenTab('options_6');
  
  //title
  $options_panel->Title("Import Export");
  
  /**
   * add import export functionallty
   */
  $options_panel->addImportExport();

  /**
   * Close 6th tab
   */
  $options_panel->CloseTab();
  $options_panel->CloseTab();

  //Now Just for the fun I'll add Help tabs
  $options_panel->HelpTab(array(
    'id'=>'tab_id',
    'title'=>'My help tab title',
    'content'=>'<p>This is my Help Tab content</p>'
  ));
  $options_panel->HelpTab(array(
    'id' => 'tab_id2',
    'title' => 'My 2nd help tab title',
    'callback' => 'help_tab_callback_demo'
  ));
  
  //help tab callback function
  function help_tab_callback_demo(){
    echo '<p>This is my 2nd Help Tab content from a callback function</p>';
  }