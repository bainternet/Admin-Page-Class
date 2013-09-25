/**
 * Aadmin pages class
 *
 * JS used for the admin pages class and other form items.
 *
 * Copyright 2011 Ohad Raz (admin@bainternet.info)
 * @since 1.0
 */

var $ =jQuery.noConflict();
//code editor
var Ed_array = Array;
//upload button
var formfield1;
var formfield2;
var file_frame;

jQuery(document).ready(function($) {

  apc_init();  
  //editor rezise fix
  $(window).resize(function() {
    $.each(Ed_array, function() {
      var ee = this;
      $(ee.getScrollerElement()).width(100); // set this low enough
      width = $(ee.getScrollerElement()).parent().width();
      $(ee.getScrollerElement()).width(width); // set it to
      ee.refresh();
    });
  });
}); //end ready

/**
 * apc_init initate fields
 * @since 1.2.2
 * @return void
 */
function apc_init(){
  /**
   * Code Editor Field
   * @since 2.1
   */
  load_code_editor();
  //iphone checkboxs
  fancyCheckbox();
  //select 2
  fancySelect();
  // repeater edit
  bindOn('click','.at-re-toggle',function() {$(this).prev().toggle('slow');});
  /**
   * Datepicker Field.
   *
   * @since 1.0
   */
  loadDatePicker();
  /**
   * Timepicker Field.
   *
   * @since 1.0
   */
  loadTimePicker();
  /**
   * Colorpicker Field.
   *
   * @since 1.0
   * better handler for color picker with repeater fields support
   * which now works both when button is clicked and when field gains focus.
   */
  loadColorPicker();
  /**
   * Add Files.
   *
   * @since 1.0
   */
  $('.at-add-file').click( function() {
    var $first = $(this).parent().find('.file-input:first');
    $first.clone().insertAfter($first).show();
    return false;
  });
  /**
   * Delete File.
   *
   * @since 1.0
   */
  $('.at-upload').delegate( '.at-delete-file', 'click' , function() {
    
    var $this   = $(this),
        $parent = $this.parent(),
        data     = $this.attr('rel');
        
    $.post( ajaxurl, { action: 'at_delete_file', data: data }, function(response) {
      response == '0' ? ( alert( 'File has been successfully deleted.' ), $parent.remove() ) : alert( 'You do NOT have permission to delete this file.' );
    });
    
    return false;
  });
  /**
   * initiate repeater sortable option
   * since 0.4
   */
  $(".repeater-sortable").sortable();
  /**
   * initiate sortable fields option
   * since 0.4
   */
  $(".at-sortable").sortable({placeholder: "ui-state-highlight"});
  //new image upload field  
  load_images_muploader();
  //delete img button
  bindOn('click','.at-delete_image_button',function(event){
    event.preventDefault();
    remove_image($(this));
    return false;
  });
  //upload images
  bindOn('click','.at-upload_image_button',function(event){
    event.preventDefault();
    image_upload($(this));
    return false;
  });

  /**
   * listen for import button click
   * @since 0.8
   * @return void
   */
  bindOn('click','#apc_import_b',function(){do_ajax_import_export('import');});

  /**
   * listen for export button click
   * @since 0.8
   * @return void
   */
  bindOn('click','#apc_export_b',function(){do_ajax_import_export('export');});
  
  //refresh page
  bindOn('click','#apc_refresh_page_b',function(){refresh_page();});

  //status alert dismiss
  bindOn('click','[data-dismiss="alert"]',function(){$(this).parent().remove()});
}

/**
 * loadColorPicker 
 * @since 1.2.2
 * @return void
 */
function loadColorPicker(){
  if ($.farbtastic){//since WordPress 3.5
    bindOn('focus','at-color','focus', function() {load_colorPicker($(this).next());});
    bindOn('focusout','at-color','focus', function() {hide_colorPicker($(this).next());});

    /**
     * Select Color Field.
     *
     * @since 1.0
     */
    bindOn('click','.at-color-select',function(){
      if ($(this).next('div').css('display') == 'none')
        load_colorPicker($(this));
      else
        hide_colorPicker($(this));
    });

    function load_colorPicker(ele){
      colorPicker = $(ele).next('div');
      input = $(ele).prev('input');

      $.farbtastic($(colorPicker), function(a) { $(input).val(a).css('background', a); });

      colorPicker.show();
    }

    function hide_colorPicker(ele){
      colorPicker = $(ele).next('div');
      $(colorPicker).hide();
    }
    //issue #15
    $('.at-color').each(function(){
      var colo = $(this).val();
      if (colo.length == 7)
        $(this).css('background',colo);
    });
  }else{
    if ($('.at-color-iris').length>0){
      $('.at-color-iris').wpColorPicker(); 
    }
  }
}

/**
 * loadDatePicker 
 * @since 1.2.2
 * @return void
 */
function loadDatePicker(){
  $('.at-date').each( function() {
    var $this  = $(this),
        format = $this.attr('rel');
    $this.datepicker( { showButtonPanel: true, dateFormat: format } );
  });
}

/**
 * loadTimePicker 
 * @since 1.2.2
 * @return void
 */
function loadTimePicker(){
  $('.at-time').each( function() {
    var $this = $(this),
    format   =  $this.attr('rel');
    $this.timepicker( { showSecond: true, timeFormat: format } );
  });
}

/**
 * jQuery iphone style checkbox enable function
 * @since 1.1.5
 */
function fancyCheckbox(){
  $(':checkbox').each(function (){
    var $el = $(this);
    if(! $el.hasClass('no-toggle')){
      $el.FancyCheckbox();
      if ($el.hasClass("conditinal_control")){
        $el.on('change', function() {
          var $el = $(this);
          if($el.is(':checked'))
            $el.next().next().show('fast');    
          else
            $el.next().next().hide('fast');
        });
      }
    }else{
      if ($el.hasClass("conditinal_control")){
      $el.on('change', function() { 
        var $el = $(this);
        if($el.is(':checked'))
          $el.next().show('fast');    
        else
          $el.next().hide('fast');
        });
      }
    }
  });
}

/**
 * Select 2 enable function
 * @since 1.1.5
 */
function fancySelect(){
  $("select").each(function (){
    if(! $(this).hasClass('no-fancy'))
      $(this).select2();
  });
}

/**
 * remove_image description
 * @since 1.2.2
 * @param  jQuery element object
 * @return void
 */
function remove_image(ele){
  var $el = $(ele);
  var field_id = $el.attr("rel");
  var at_id = $el.prev().prev();
  var at_src = $el.prev();
  var t_button = $el;
  $(t_button).val("Upload Image");
  $(t_button).removeClass('at-delete_image_button').addClass('at-upload_image_button');
  //clear html values
  $(at_id).val('');
  $(at_src).val('');
  $(at_id).prev().html('');
  load_images_muploader();
}

/**
 * image_upload handle image upload
 * @since 1.2.2
 * @param  jquery element object
 * @return void
 */
function image_upload(ele){
  var $el = $(ele);
  formfield1 = $el.prev();
  formfield2 = $el.prev().prev();      
  if ($el.attr('data-u') == 'tk'){
    tb_show('', 'media-upload.php?post_id=0&type=image&apc=apc&TB_iframe=true');
    //store old send to editor function
    window.restore_send_to_editor = window.send_to_editor;
    //overwrite send to editor function
    window.send_to_editor = function(html) {
      imgurl = $('img',html).attr('src');
      img_calsses = $('img',html).attr('class').split(" ");
      att_id = '';
      $.each(img_calsses,function(i,val){
        if (val.indexOf("wp-image") != -1){
          att_id = val.replace('wp-image-', "");
        }
      });

      $(formfield2).val(att_id);
      $(formfield1).val(imgurl);
      load_images_muploader();
      tb_remove();
      //restore old send to editor function
      window.send_to_editor = window.restore_send_to_editor;
    }
  }else{
    // Uploading files since WordPress 3.5
    // If the media frame already exists, reopen it.
    if ( file_frame ) {
      file_frame.open();
      return;
    }
    // Create the media frame.
    file_frame = wp.media.frames.file_frame = wp.media({
      title: $el.data( 'uploader_title' ),
      button: {
        text: $el.data( 'uploader_button_text' ),
      },
      multiple: false  // Set to true to allow multiple files to be selected
    });
    // When an image is selected, run a callback.
    file_frame.on( 'select', function() {
      // We set multiple to false so only get one image from the uploader
      attachment = file_frame.state().get('selection').first().toJSON();
      // Do something with attachment.id and/or attachment.url here
      jQuery(formfield2).val(attachment.id);
      jQuery(formfield1).val(attachment.url);
      load_images_muploader();
    });
    // Finally, open the modal
    file_frame.open();
  }
}

/**
 * load_images_muploader 
 * load images after upload
 * @return void
 */
function load_images_muploader(){
  $(".mupload_img_holder").each(function(i,v){
    if ($(this).next().next().val() != ''){
      if (!$(this).children().size() > 0){
        var h = $(this).attr('data-he');
        var w = $(this).attr('data-wi');
        $(this).append('<img src="' + $(this).next().next().val() + '" style="height: '+ h +';width: '+ w +';" />');
        $(this).next().next().next().val("Delete");
        $(this).next().next().next().removeClass('at-upload_image_button').addClass('at-delete_image_button');
      }
    }
  });
}

/**
 * load_code_editor  loads code editors
 * @since 1.2.2
 * @return void
 */
function load_code_editor(){
  var e_d_count = 0;
  $(".code_text").each(function() {
    var lang = $(this).attr("data-lang");
    //php application/x-httpd-php
    //css text/css
    //html text/html
    //javascript text/javascript
    switch(lang){
      case 'php':
        lang = 'application/x-httpd-php';
        break;
      case 'less':
      case 'css':
        lang = 'text/css';
        break;
      case 'html':
        lang = 'text/html';
        break;
      case 'javascript':
        lang = 'text/javascript';
        break;
      default:
        lang = 'application/x-httpd-php';
    }
    var theme  = $(this).attr("data-theme");
    switch(theme){
      case 'default':
        theme = 'default';
        break;
      case 'light':
        theme = 'solarizedLight';
        break;
      case 'dark':
        theme = 'solarizedDark';;
        break;
      default:
        theme = 'default';
    }
    
    var editor = CodeMirror.fromTextArea(document.getElementById($(this).attr('id')), {
      lineNumbers: true,
      matchBrackets: true,
      mode: lang,
      indentUnit: 4,
      indentWithTabs: true,
      enterMode: "keep",
      tabMode: "shift"
    });
    editor.setOption("theme", theme);
    $(editor.getScrollerElement()).width(100); // set this low enough
    width = $(editor.getScrollerElement()).parent().width();
    $(editor.getScrollerElement()).width(width); // set it to
    editor.refresh();
    Ed_array[e_d_count] = editor;
    e_d_count++;
  });
}

/***************************
 * Import Export Functions *
 * ************************/

/**
 * do_ajax 
 * 
 * @author Ohad Raz <admin@bainternet.info> 
 * @since 0.8
 * @param  string which  (import|export)
 * 
 * @return void
 */
function do_ajax_import_export(which){
  before_ajax_import_export(which);
  var group = jQuery("#option_group_name").val();
  var seq_selector = "#apc_" + which + "_nonce";
  var action_selctor = "apc_" + which + "_" + group;
  jQuery.ajaxSetup({ cache: false });
  if (which == 'export')
    export_ajax_call(action_selctor,group,seq_selector,which);
  else
    import_ajax_call(action_selctor,group,seq_selector,which);
  jQuery.ajaxSetup({ cache: true });
}

/**
 * export_ajax_call make export ajax call
 * 
 * @author Ohad Raz <admin@bainternet.info> 
 * @since 0.8
 * 
 * @param  string action 
 * @param  string group
 * @param  string seq_selector
 * @param  string which   
 * @return void
 */
function export_ajax_call(action,group,seq_selector,which){
  jQuery.getJSON(ajaxurl,
    {
      group: group,
      rnd: microtime(false), //hack to avoid request cache
      action: action,
      seq: jQuery(seq_selector).val()
    },
    function(data) {
      if (data){
        export_response(data);
      }else{
        alert("Something Went Wrong, try again later");
      }
      after_ajax_import_export(which);
    }
  );
}

/**
 * import_ajax_call make import ajax call
 * 
 * @author Ohad Raz <admin@bainternet.info> 
 * @since 0.8
 * 
 * @param  string action 
 * @param  string group
 * @param  string seq_selector
 * @param  string which   
 * @return void
 */
function import_ajax_call(action,group,seq_selector,which){
  jQuery.post(ajaxurl,
    {
      group: group,
      rnd: microtime(false), //hack to avoid request cache
      action: action,
      seq: jQuery(seq_selector).val(),
      imp: jQuery("#import_code").val(),
    },
    function(data) {
      if (data){
         import_response(data);
      }else{
        alert("Something Went Wrong, try again later");
      }
      after_ajax_import_export(which);
    },
     "json"
  );
}

/**
 * before_ajax_import_export 
 * 
 * @author Ohad Raz <admin@bainternet.info> 
 * @since 0.8
 * @param  string which  (import|export)
 * 
 * @return void
 */
function before_ajax_import_export(which){
  jQuery(".import_status").hide("fast");
  jQuery(".export_status").hide("fast");
  jQuery(".export_results").html('').removeClass('alert-success').hide();
  jQuery(".import_results").html('').removeClass('alert-success').hide();
  if (which == 'import')
    jQuery(".import_status").show("fast");
  else
    jQuery(".export_status").show("fast");
}

/**
 * after_ajax_import_export
 * 
 * @author Ohad Raz <admin@bainternet.info> 
 * @since 0.8
 * @param  string which  (import|export)
 * 
 * @return void
 */
function after_ajax_import_export(which){
  if (which == 'import')
    jQuery(".import_status").hide("fast");
  else
    jQuery(".export_status").hide("fast");
}

/**
 * export_reponse
 * 
 * @author Ohad Raz <admin@bainternet.info> 
 * @since 0.8
 * @param  json data ajax response
 * @return void
 */
function export_response(data){
  if (data.code)
    jQuery('#export_code').val(data.code);
  if (data.nonce)
    jQuery("#apc_export_nonce").val(data.nonce);
  if(data.err)
    jQuery(".export_results").html(data.err).show('slow');
}

/**
 * import_reponse
 * 
 * @author Ohad Raz <admin@bainternet.info> 
 * @since 0.8
 * @param  json data ajax response
 * 
 * @return void
 */
function import_response(data){
  if (data.nonce)
    jQuery("#apc_import_nonce").val(data.nonce);
  if(data.err)
    jQuery(".import_results").html(data.err);
  if (data.success)
    jQuery(".import_results").html(data.success).addClass('alert-success').show('slow');
}

/********************
 * Helper Functions *
 *******************/

/**
 * refresh_page 
 * @since 0.8
 * @return void
 */
function refresh_page(){

  location.reload();
}

/**
 * microtime used as hack to avoid ajax cache
 * 
 * @author Ohad Raz <admin@bainternet.info> 
 * @since 0.8
 * @param  boolean get_as_float 
 * 
 * @return microtime as int or float 
 */
function microtime(get_as_float) { 
  var now = new Date().getTime() / 1000; 
  var s = parseInt(now); 
  return (get_as_float) ? now : (Math.round((now - s) * 1000) / 1000) + " " + s; 
}

/**
 * Helper Function
 *
 * Get Query string value by name.
 *
 * @since 1.0
 */
function get_query_var( name ) {

  var match = RegExp('[?&]' + name + '=([^&#]*)').exec(location.href);
  return match && decodeURIComponent(match[1].replace(/\+/g, ' '));   
}

/**
 * bindOn 
 * hel[er function to bind functions to events  using on()
 * @param  string event    event name
 * @param  string selector element selector
 * @param  callback func   callback function
 */
function bindOn(event,selector,func){
  $(document).on(event,selector,func);
}