<?php 
/**
 * Admin Page Class
 *
 * The Admin Page Class is used by including it in your plugin files and using its methods to 
 * create custom Admin Pages. It is meant to be very simple and 
 * straightforward. 
 *
 * This class is derived from My-Meta-Box (https://github.com/bainternet/My-Meta-Box script) which is 
 * a class for creating custom meta boxes for WordPress. 
 * 
 *  
 * @version 1.2.6
 * @copyright 2012 - 2013
 * @author Ohad Raz (email: admin@bainternet.info)
 * @link http://en.bainternet.info
 * 
 * @license GNU General Public LIcense v3.0 - license.txt
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package Admin Page Class
 * 
 * @Last Revised 
 */

if ( ! class_exists( 'BF_Admin_Page_Class') ) :

/**
 * Admin Page Class
 *
 * @package Admin Page Class
 * @since 0.1
 *
 * @todo Nothing.
 */

  class BF_Admin_Page_Class {
  
    /**
     * Contains all saved data for a page
     * 
     * @access protected
     * @var array
     * @since 0.1
     */
    protected $_saved;

    /**
     * Contains all arguments needed to build the page itself
     * 
     * @access protected
     * @var array
     * @since 0.1
     */
    protected $args;
      
    /**
     * Contains Options group name
     * @access protected
     * @var array
     * @since 0.1
     */
    protected $option_group;
    
    /**
     * Contains all the information needed to build the form structure of the page
     * 
     * @access public
     * @var array
     * @since 0.1
     */
    public $_fields;
      
    /**
     * True if the table is opened, false if it is not opened
     * 
     * @access protected
     * @var boolean
     * @since 0.1
     */
    protected $table = false;
    
    /**
     * True if the tab div is opened, false if it is not opened
     * 
     * @access protected
     * @var boolean
     * @since 0.1
     */
    protected $tab_div = false;
      
    /**
     * Contains the menu_slug for the current TopLeve-Menu
     * 
     * @access public
     * @var string
     * @since 0.1
     */
    public $Top_Slug;
    
    /**
     * Contains the menu_slug for the current page
     * 
     * @access public
     * @var string
     * @since 0.1
     */
    public $_Slug;
    
    /**
     * Contains all the information needed to build the Help tabs
     * 
     * @access public
     * @var array
     * @since 0.1
     */
    public $_help_tabs;
    
    /**
     * Use html table row or div for each field, true for row, false for div
     * 
     * @access public
     * @var boolean
     * @since 0.1
     */
    public $_div_or_row;

    /**
     * saved flag
     * @var boolean
     * @since 0.6
     */
    public $saved_flag = false;
    
    /**
     * use google fonts for typo filed?
     * @var boolean
     * @since 0.9.9
     * @access public
     */
    public $google_fonts = false;

    /**
     * Holds used field types
     * @var boolean
     * @since 1.1.3
     * @access public
     */
    public $field_types = array();
    
    /**
     * Holds validation Errors
     * @var boolean
     * @since 1.1.9
     * @access public
     */
    public $errors = array();
    
    /**
     * Holds Errors flag
     * @var boolean
     * @since 1.1.9
     * @access public
     */
    public $errors_flag = false;

    /**
     * data_type  holds type of data (options, post_meta, tax_meta, user_meta)
     * @var string
     * @since
     */
    public $data_type = 'options';
    /**
     * Builds a new Page 
     * @param $args (string|mixed array) - 
     *
     * Possible keys within $args:
     *  > menu (array|string) - (string) -> this the name of the parent Top-Level-Menu or a TopPage object to create 
     *                      this page as a sub menu to.
     *              (array)  -> top - Slug for the New Top level Menu page to create.
     *  > page_title (string) - The name of this page (good for Top level and sub menu pages)
     *  > capability (string) (optional) - The capability needed to view the page (good for Top level and sub menu pages)
     *  > menu_title (string) - The name of the Top-Level-Menu (Top level Only)
     *  > menu_slug (string) - A unique string identifying your new menu (Top level Only)
     *  > icon_url (string) (optional) - URL to the icon, decorating the Top-Level-Menu (Top level Only)
     *  > position (string) (optional) - The position of the Menu in the ACP (Top level Only)
     *  > option_group (string) (required) - the name of the option to create in the database
     *
     *
     */
    public function __construct($args) {
      if(is_array($args)) {
        if (isset($args['option_group'])){
          $this->option_group = $args['option_group'];
        }
        $this->args = $args;
      } else {
        $array['page_title'] = $args;
        $this->args = $array;
      }

      //add hooks for export download
      add_action('template_redirect',array($this, 'admin_redirect_download_files'));
      add_filter('init', array($this,'add_query_var_vars'));
      
      // If we are not in admin area exit.
      if ( ! is_admin() )
        return;

      //load translation
      $this->load_textdomain();

      //set defaults
      $this->_div_or_row = true;
      $this->saved = false;
      //store args
      $this->args = $args;
      //google_fonts
      $this->google_fonts = isset($args['google_fonts'])? true : false;

      //sub $menu
      if(!is_array($args['menu'])) {
        if(is_object($args['menu'])) {
          $this->Top_Slug = $args['menu']->Top_Slug;
        }else{
          switch($args['menu']) {
            case 'posts':
              $this->Top_Slug = 'edit.php';
              break;
            case 'dashboard':
              $this->Top_Slug = 'index.php';
              break;
            case 'media':
              $this->Top_Slug = 'upload.php';
              break;
            case 'links':
              $this->Top_Slug = 'link-manager.php';
              break;
            case 'pages':
              $this->Top_Slug = 'edit.php?post_type=page';
              break;
            case 'comments':
              $this->Top_Slug = 'edit-comments.php';
              break;
            case 'theme':
              $this->Top_Slug = 'themes.php';
              break;
            case 'plugins':
              $this->Top_Slug = 'plugins.php';
              break;
            case 'users':
              $this->Top_Slug = 'users.php';
              break;
            case 'tools':
              $this->Top_Slug = 'tools.php';
              break;
            case 'settings':
              $this->Top_Slug = 'options-general.php';
              break;        
            default:
              if(post_type_exists($args['menu'])) {
                $this->Top_Slug = 'edit.php?post_type='.$args['menu'];
              } else {
                $this->Top_Slug = $args['menu'];
              }
          }
        }
        add_action('admin_menu', array($this, 'AddMenuSubPage'));
      }else{
        //top page
        $this->Top_Slug = $args['menu']['top'];
        add_action('admin_menu', array($this, 'AddMenuTopPage'));
      }
      

      // Assign page values to local variables and add it's missed values.
      $this->_Page_Config = $args;
      $this->_fields = $this->_Page_Config['fields'];
      $this->_Local_images = (isset($args['local_images'])) ? true : false;
      $this->_div_or_row = (isset($args['div_or_row'])) ? $args['div_or_row'] : false;
      $this->add_missed_values();
      if (isset($args['use_with_theme'])){
        if ($args['use_with_theme'] === true){
          $this->SelfPath = get_stylesheet_directory_uri() . '/admin-page-class';
        }elseif($args['use_with_theme'] === false){
          $this->SelfPath = plugins_url( 'admin-page-class', plugin_basename( dirname( __FILE__ ) ) );
        }else{
          $this->SelfPath = $args['use_with_theme'];
        }
      }else{
        $this->SelfPath = plugins_url( 'admin-page-class', plugin_basename( dirname( __FILE__ ) ) );
      }

      // Load common js, css files
      // Must enqueue for all pages as we need js for the media upload, too.
      
      
      //add_action('admin_head', array($this, 'loadScripts'));
      add_filter('attribute_escape',array($this,'edit_insert_to_post_text'),10,2);

      // Delete file via Ajax
      add_action( 'wp_ajax_apc_delete_mupload', array( $this, 'wp_ajax_delete_image' ) );
      //import export
      add_action( 'wp_ajax_apc_import_'.$this->option_group, array( $this, 'import' ) );
      add_action( 'wp_ajax_apc_export_'.$this->option_group, array( $this, 'export' ) );

      //plupload ajax
      add_action('wp_ajax_plupload_action', array( $this,"Handle_plupload_action"));

    }


    /**
     * Does all the complicated stuff to build the menu and its first page
     * 
     * @since 0.1
     * @access public
     */
    public function AddMenuTopPage() {
      $default = array(
        'capability' => 'edit_themes',
        'menu_title' => '',
        'id'         => 'id',
        'icon_url'   => '',
        'position'   => null
      );

      $this->args = array_merge($default, $this->args);
      $id = add_menu_page($this->args['page_title'], $this->args['page_title'], $this->args['capability'], $this->args['id'], array($this, 'DisplayPage'), $this->args['icon_url'], $this->args['position']);
      $page = add_submenu_page($id, $this->args['page_title'], $this->args['page_title'], $this->args['capability'], $this->args['id'], array($this, 'DisplayPage'));
      if ($page){
         $this->_Slug = $page;
         // Adds my_help_tab when my_admin_page loads
         add_action('load-'.$page, array($this,'Load_page_hooker'));
      }
    }
    
    /**
     * Does all the complicated stuff to build the page
     * 
     * @since 0.1
     * @access public
     */
    public function AddMenuSubPage() {
      $default = array(
        'capability' => 'edit_themes',
      );
      $this->args = array_merge($default, $this->args);
      $page = add_submenu_page($this->Top_Slug, $this->args['page_title'], $this->args['page_title'], $this->args['capability'], $this->createSlug(), array($this, 'DisplayPage'));
      if ($page){
         $this->_Slug = $page;
         add_action('load-'.$page, array($this,'Load_page_hooker'));
      }
    }

    /**
     * loads scripts and styles for the page
     * 
     * @author ohad raz
     * @since 0.1
     * @access public
     */
    public function Load_page_hooker(){
      $page = $this->_Slug;
      //help tabs
      add_action('admin_head-'.$page, array($this,'admin_add_help_tab'));
      //pluploader code
      add_action('admin_head-'.$page, array($this,'plupload_head_js'));
      //scripts and styles
      add_action( 'admin_print_styles', array( $this, 'load_scripts_styles' ) );
      //panel script
      add_action('admin_footer-' . $page, array($this,'panel_script'));
      //add mising scripts
      //add_action('admin_enqueue_scripts',array($this,'Finish'));
      
      if(isset($_POST['action']) && $_POST['action'] == 'save') {
        $this->save();
        $this->saved_flag = true;
      }
    }

    public function plupload_head_js(){
      if ($this->has_field('plupload')){
         $plupload_init = array(
            'runtimes'            => 'html5,silverlight,flash,html4',
            'browse_button'       => 'plupload-browse-button', // will be adjusted per uploader
            'container'           => 'plupload-upload-ui', // will be adjusted per uploader
            'drop_element'        => 'drag-drop-area', // will be adjusted per uploader
            'file_data_name'      => 'async-upload', // will be adjusted per uploader
            'multiple_queues'     => true,
            'max_file_size'       => wp_max_upload_size() . 'b',
            'url'                 => admin_url('admin-ajax.php'),
            'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
            'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
            'filters'             => array(array('title' => __('Allowed Files','apc'), 'extensions' => '*')),
            'multipart'           => true,
            'urlstream_upload'    => true,
            'multi_selection'     => false, // will be added per uploader
             // additional post data to send to our ajax hook
            'multipart_params'    => array(
                '_ajax_nonce' => "", // will be added per uploader
                'action' => 'plupload_action', // the ajax action name
                'imgid' => 0 // will be added per uploader
            )
          );
          echo '<script type="text/javascript">'."\n".'var base_plupload_config=';
          echo json_encode($plupload_init)."\n".'</script>';
      }
    }
    
    /**
     * Creates an unique slug out of the page_title and the current menu_slug
     * 
     * @since 0.1
     * @access private
     */
    private function createSlug() {
      $slug = $this->args['page_title'];
      $slug = strtolower($slug);
      $slug = str_replace(' ','_',$slug);
      return $this->Top_Slug.'_'.$slug;
    }

    /** add Help Tab
     * 
     * @since 0.1
     * @access public
     * @param $args (mixed|array) contains everything needed to build the field
       *
       * Possible keys within $args:
       *  > id (string) (required)- Tab ID. Must be HTML-safe and should be unique for this menu
       *  > title (string) (required)- Title for the tab. 
       *  > content (string) (required)- Help tab content in plain text or HTML. 
       *         
       
     *
     * Will only work on wordpres version 3.3 and up
     */
    public function HelpTab($args){
      $this->_help_tabs[] = $args;
    }
    
    /* print Help Tabs for current screen
     * 
     * @access public
       * @since 0.1
       * @author Ohad
     *
     * Will only work on wordpres version 3.3 and up
     */    
    public function admin_add_help_tab(){
      $screen = get_current_screen();
      /*
       * Check if current screen is My Admin Page
       * Don't add help tab if it's not
       */
      
      if ( $screen->id != $this->_Slug )
        return;
      // Add help_tabs for current screen 
      
      foreach((array)$this->_help_tabs as $tab){

        $screen->add_help_tab($tab);
      }
    }

    /* print out panel Script
     * 
     * @access public
       * @since 0.1
     */
    public function panel_script(){
      echo '<script>';
      echo '
        /* cookie stuff */
        function setCookie(name,value,days) {
          if (days) {
            var date = new Date(); 
            date.setTime(date.getTime()+(days*24*60*60*1000));
            var expires = "; expires="+date.toGMTString();
          }
          else var expires = "";
          document.cookie = name+"="+value+expires+"; path=/";
        } 
         
        function getCookie(name) {
          var nameEQ = name + "=";
          
          var ca = document.cookie.split(";");
          for(var i=0;i < ca.length;i++) {
            var c = ca[i]; 
            while (c.charAt(0)==\' \') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
          }
          return null;
        }

        function eraseCookie(name) {setCookie(name,"",-1);}

        var last_tab = getCookie("apc_'.$this->option_group.'last");
        if (last_tab) {
           var last_tab = last_tab;
        }else{
           var last_tab = null;
        } 
        jQuery(document).ready(function() {  
          function show_tab(li){
            if (!jQuery(li).hasClass("active_tab")){
              //hide all
              jQuery(".setingstab").hide("slow");
              jQuery(".panel_menu li").removeClass("active_tab");
              tab  = jQuery(li).find("a").attr("href");
              jQuery(li).addClass("active_tab");
              jQuery(tab).show("fast");
              setCookie("apc_'.$this->option_group.'last",tab);
            }
          }
          //hide all
          jQuery(".setingstab").hide();
      
          //set first_tab as active if no cookie found
          if (last_tab == null){
            jQuery(".panel_menu li:first").addClass("active_tab");
            var tab  = jQuery(".panel_menu li:first a").attr("href");
            jQuery(tab).show();
          }else{
            show_tab(jQuery(\'[href="\' + last_tab + \'"]\').parent());
          }
      
          //bind click on menu action to show the right tab.
          jQuery(".panel_menu li").bind("click", function(event){
            event.preventDefault()
            show_tab(jQuery(this));

          });';
      if ($this->has_Field('upload')){
        echo 'function load_images_muploader(){
            jQuery(".mupload_img_holder").each(function(i,v){
              if (jQuery(this).next().next().val() != ""){
                jQuery(this).append("<img src=\"" + jQuery(this).next().next().val() + "\" style=\"height: 150px;width: 150px;\" />");
                jQuery(this).next().next().next().val("Delete");
                jQuery(this).next().next().next().removeClass("apc_upload_image_button").addClass("apc_delete_image_button");
              }
            });
          }
          //upload button
          var formfield1;
          var formfield2;
          jQuery("#image_button").click(function(e){
            if(jQuery(this).hasClass("apc_upload_image_button")){
              formfield1 = jQuery(this).prev();
              formfield2 = jQuery(this).prev().prev();
              tb_show("", "media-upload.php?type=image&amp;apc=insert_file&amp;TB_iframe=true");
              return false;
            }else{
              var field_id = jQuery(this).attr("rel");
              var at_id = jQuery(this).prev().prev();
              var at_src = jQuery(this).prev();
              var t_button = jQuery(this);
              data = {
                action: "apc_delete_mupload",
                _wpnonce: $("#nonce-delete-mupload_" + field_id).val(),
                field_id: field_id,
                attachment_id: jQuery(at_id).val()
              };

              $.post(ajaxurl, data, function(response) {
                if ("success" == response.status){
                  jQuery(t_button).val("Upload Image");
                  jQuery(t_button).removeClass("apc_delete_image_button").addClass("apc_upload_image_button");
                  //clear html values
                  jQuery(at_id).val("");
                  jQuery(at_src).val("");
                  jQuery(at_id).prev().html("");
                  load_images_muploader();
                }else{
                  alert(response.message);
                }
              }, "json");

              return false;
            }
            
          });
          


          //store old send to editor function
          window.restore_send_to_editor = window.send_to_editor;
          //overwrite send to editor function
          window.send_to_editor = function(html) {
            imgurl = jQuery("img",html).attr("src");
            img_calsses = jQuery("img",html).attr("class").split(" ");
            att_id = "";
            jQuery.each(img_calsses,function(i,val){
              if (val.indexOf("wp-image") != -1){
                att_id = val.replace("wp-image-", "");
              }
            });

            jQuery(formfield2).val(att_id);
            jQuery(formfield1).val(imgurl);
            load_images_muploader();
            tb_remove();
            //restore old send to editor function
            window.send_to_editor = window.restore_send_to_editor;
          }
          ';
      }
      echo '
        });
        </script>';
    }
    
    
    
    //rename insert to post button
    /**
     * edit_insert_to_post_text 
     * 
     * @author  ohad raz
     * @since 0.1
     * @param  string $input insert to post text
     * @return string
     */
    public function edit_insert_to_post_text( $safe_text, $text ) {
      if( is_admin() && 'Insert into Post' == $safe_text){
        if (isset($_REQUEST['apc']) && 'insert_file' == $_REQUEST['apc'] )
          return str_replace(__('Insert into Post'), __('Use this File','apc'), $safe_text);
        else
          return str_replace(__('Insert into Post'), __('Use this Image','apc'), $safe_text);
      }
      return $text;
    }

    /* print out panel Style (deprecated)
     * 
     * @access public
       * @since 0.1
     */
    public function panel_style(){
      //echo '<style></style>';
    }

    /**
     * Outputs all the HTML needed for the new page
     * 
     * @access public
     * @param $args (mixed|array) contains everything needed to build the field
     * @param $repeater (boolean)
     * @since 0.1
     */
    public function DisplayPage() {
      do_action('admin_page_class_before_page');
      echo '<div class="wrap">';
      echo '<form method="post" name="'.apply_filters('apc_form_name', 'admin_page_class',$this).'" class="'.apply_filters('apc_form_class', 'admin_page_class',$this).'" id="'.apply_filters('apc_form_id', 'admin_page_class',$this).'" action="" enctype="multipart/form-data">
        <div class="header_wrap">
        <div style="float:left">';
        echo apply_filters('admin_page_class_before_title','');
        echo '<h2>'.apply_filters('admin_page_class_h2',$this->args['page_title']).'</h2>'.((isset($this->args['page_header_text']))? $this->args['page_header_text'] : '').' 
        </div>
        <div style="float:right;margin:32px 0 0 0">
          <input type="submit" style="margin-left: 25px;" value="'.esc_attr(__('Save Changes','apc')).'" name="Submit" class="'.apply_filters('admin_page_class_submit_class', 'btn-info').' btn"><br><br>
        </div>
      <br style="clear:both"><br>
      </div>';
      wp_nonce_field( basename(__FILE__), 'BF_Admin_Page_Class_nonce' );

      if ($this->saved_flag){
        echo '<div class="update-status">';
        $this->errors = apply_filters('admin_page_class_errors', $this->errors,$this);
        if (is_array($this->errors) && count($this->errors) > 0 ){
          $this->errors_flag = true;
          $this->displayErrors();
        }else{
          echo '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button><strong>'.__('Settings saved.','apc').'</strong></div>';
        }
        echo '</div>';
      }
        
        
      $saved = get_option($this->option_group);
      $this->_saved = $saved;
      $skip = array('title','paragraph','subtitle','TABS','CloseDiv','TABS_Listing','OpenTab','custom','import_export');

      foreach($this->_fields as $field) {
        if (!in_array($field['type'],$skip)){
          if(!$this->table) {
            if ($this->_div_or_row){
              echo '<table class="form-table">';
              $this->table = true;
            }else{
              echo '<div class="form-table">';
              $this->table = true;
            }
          }
        }else{
          if($this->table) {
            if ($this->_div_or_row){echo '</table>';}else{echo '</div>';}
            $this->table = false;
          }
        }
        $data = '';
        if (isset($saved[$field['id']]))
            $data = $saved[$field['id']];
        if (isset($field['std']) && $data === '')
            $data = $field['std'];

        if (method_exists($this,'show_field_' . $field['type'])){
          if ($this->_div_or_row){echo '<td>';}else{echo apply_filters('admin_page_class_field_container_open','<div class="field">',$field);}
          call_user_func ( array( $this, 'show_field_' . $field['type'] ), $field, $data );
          if ($this->_div_or_row){echo '</td>';}else{echo apply_filters('admin_page_class_field_container_close','</div>',$field);}
        }else{
          switch($field['type']) {
            case 'TABS':
              echo '<div id="tabs">';
              break;
            case 'CloseDiv':
              $this->tab_div = false;
              echo '</div>';
              break;
            case 'TABS_Listing':
              echo '<div class="panel_menu"><ul>';
              foreach($field['links'] as $id => $name){
                $extra_classes = strtolower(str_replace(' ','-',$name)).' '.strtolower(str_replace(' ','-',$id));
                echo '<li class="'.apply_filters('APC_tab_li_extra_class',$extra_classes).'"><a class="nav_tab_link" href="#'.$id.'">'.$name.'</a></li>';
              }
              echo '</ul></div><div class="sections">';
              break;
            case 'OpenTab':
              $this->tab_div = true;
              echo '<div class="setingstab" id="'.$field['id'].'">';
              do_action('admin_page_class_after_tab_open');
              break;
            case 'title':
              echo '<h2>'.$field['label'].'</h2>';
              break;
            case 'subtitle':
              echo '<h3>'.$field['label'].'</h3>';
              break;
            case 'paragraph':
              echo '<p>'.$field['text'].'</p>';
              break;
            case 'repeater':
              do_action('admin_page_class_before_repeater');
              $this->output_repeater_fields($field,$data);
              do_action('admin_page_class_after_repeater');
              break;
            case 'import_export':
              $this->show_import_export();
              do_action('admin_page_class_import_export_tab');
              break;
          }
        }
        if (!in_array($field['type'],$skip)){ echo '</tr>';}
     }
    if($this->table) echo '</table>';
    if($this->tab_div) echo '</div>';
    echo '</div><div style="clear:both"></div><div class="footer_wrap">
        <div style="float:right;margin:32px 0 0 0">
          <input type="submit" style="margin-left: 25px;" name="Submit" class="'.apply_filters('admin_page_class_submit_class', 'btn-info').' btn" value="'.esc_attr(__('Save Changes','apc')).'" />
          <br><br>
        </div>
        <br style="clear:both"><br>
      </div>';
    echo '<input type="hidden" name="action" value="save" />';
    echo '</form></div></div>';
    do_action('admin_page_class_after_page');
    }

    /**
     * Adds tabs current page
     * 
     * @access public
     * @param $args (mixed|array) contains everything needed to build the field
     * @since 0.1
     */
    public function OpenTabs_container($text= null) {
      $args['type'] = 'TABS';
      $text = (null == $text)? '': $text;
      $args['text'] = $text;
      $args['id'] = 'TABS';
      $args['std'] = '';
      $this->SetField($args);
    }

    /**
     * Close open Div
     * 
     * @access public
     * @param $args (mixed|array) contains everything needed to build the field
     * @param $repeater (boolean)
     * @since 0.1
    */
    public function CloseDiv_Container() {
      $args['type'] = 'CloseDiv';
      $args['id']   = 'CloseDiv';
      $args['std']  = '';
      $this->SetField($args);
    }

    /**
     * Adds tabs listing in ul li
     * 
     * @access public
     * @param $args (mixed|array) contains everything needed to build the field
     * @param $repeater (boolean)
     * @since 0.1
    */
    public function TabsListing($args) {
      $args['type'] = 'TABS_Listing';
      $args['id']   = 'TABS_Listing';
      $args['std']  = '';
      $this->SetField($args);
    }

    /**
     * Opens a Div
     * 
     * @access public
     * @param $args (mixed|array) contains everything needed to build the field
     * @param $repeater (boolean)
     * @since 0.1
    */
    public function OpenTab($name) {
      $args['type'] = 'OpenTab';
      $args['id']   = $name;
      $args['std']  = '';
      $this->SetField($args);
    }
    
    /**
     * close a Div
     * 
     * @access public
     * @since 0.1
    */
    public function CloseTab() {
      $args['type'] = 'CloseDiv';
      $args['id']   = 'CloseDiv';
      $args['std']  = '';
      $this->SetField($args);
    }

    /**
     * Does the repetive tasks of adding a field
     * 
     * @param $args (mixed|array) contains everything needed to build the field
     * @param $repeater (boolean)
     * @since 0.1
     * 
     * @access private
     */
    private function SetField($args) {
      $default = array(
          'std' => '',
          'id' => ''
       );
      $args = array_merge($default, $args);
      $this->buildOptions($args);
      $this->_fields[] = $args;
    }

    /**
     * Builds all the options with their std values
     * 
     * @access public
     * @param $args (mixed|array) contains everything needed to build the field
     * @since 0.1
     * @access private
     */
    private function buildOptions($args) {
      $default = array(
          'std' => '',
          'id' => ''
        );
        $args = array_merge($default, $args);
        $saved = get_option($this->option_group);
      if (isset($saved[$args['id']])){
        if($saved[$args['id']] === false) {
            $saved[$args['id']] = $args['std'];
            update_option($this->args['option_group'],$saved);
          }
      }
    }

    /**
     * Adds a heading to the current page
     *
     * @access public
     * @param $args (mixed|array) contains everything needed to build the field
     * @param $repeater (boolean)
     * @since 0.1
     * 
     * @param string $label simply the text for your heading
     */
    public function Title($label,$repeater = false) {
      $args['type']  = 'title';
      $args['std']   = '';
      $args['label'] = $label;
      $args['id']    = 'title'.$label;
      $this->SetField($args);
    }
      
    /**
     * Adds a sub-heading to the current page
     * 
     * @access public
     * @param $args (mixed|array) contains everything needed to build the field
     * @param $repeater (boolean)
     * @since 0.1
     *
     * @param string $label simply the text for your heading
     */
    public function Subtitle($label,$repeater = false) {
      $args['type']  = 'subtitle';
      $args['label'] = $label;
      $args['id']    = 'title'.$label;
      $args['std']   = '';
      $this->SetField($args);
    }
      
    /**
     * Adds a paragraph to the current page
     * 
     * @access public
     * @param $args (mixed|array) contains everything needed to build the field
     * @param $repeater (boolean)
     * @since 0.1
     *
     * @param string $text the text you want to display
     */
    public function Paragraph($text,$repeater = false) {
      $args['type'] = 'paragraph';
      $args['text'] = $text;
      $args['id']   = 'paragraph';
      $args['std']  = '';
      $this->SetField($args);
    }
  
    
  /**
   * Load all Javascript and CSS
   *
   * @since 0.1
   * @access public
   */
  public function load_scripts_styles() {
    
    // Get Plugin Path
    $plugin_path = $this->SelfPath;
    
    //this replaces the ugly check fields methods calls
    foreach (array('upload','color','date','time','code','select','editor','plupload') as $type) {
      call_user_func ( array( $this, 'check_field_' . $type ));
    }
    
    wp_enqueue_script('common');
    if ($this->has_Field('TABS')){
      wp_print_scripts('jquery-ui-tabs');
    }

    // Enqueue admin page Style
    wp_enqueue_style( 'Admin_Page_Class', $plugin_path . '/css/Admin_Page_Class.css' );
    wp_enqueue_style('iphone_checkbox',$plugin_path. '/js/FancyCheckbox/FancyCheckbox.css');
    
    // Enqueue admin page Scripts
    wp_enqueue_script( 'Admin_Page_Class', $plugin_path . '/js/Admin_Page_Class.js', array( 'jquery' ), null, true );
    wp_enqueue_script('iphone_checkbox',$plugin_path. '/js/FancyCheckbox/FancyCheckbox.js',array('jquery'),null,true);
    
    wp_enqueue_script('utils');
    wp_enqueue_script( 'jquery-ui-sortable' );
  }


  /**
   * Check Field code editor
   *
   * @since 0.1
   * @access public
   */
  public function check_field_code() {
    
    if ( $this->has_field( 'code' ) && $this->is_edit_page() ) {
      $plugin_path = $this->SelfPath;
      // Enqueu codemirror js and css
      wp_enqueue_style( 'at-code-css', $plugin_path .'/js/codemirror/codemirror.css',array(),null);
      wp_enqueue_style( 'at-code-css-dark', $plugin_path .'/js/codemirror/solarizedDark.css',array(),null);
      wp_enqueue_style( 'at-code-css-light', $plugin_path .'/js/codemirror/solarizedLight.css',array(),null);
      wp_enqueue_script('at-code-js',$plugin_path .'/js/codemirror/codemirror.js',array('jquery'),false,true);
      wp_enqueue_script('at-code-js-xml',$plugin_path .'/js/codemirror/xml.js',array('jquery'),false,true);
      wp_enqueue_script('at-code-js-javascript',$plugin_path .'/js/codemirror/javascript.js',array('jquery'),false,true);
      wp_enqueue_script('at-code-js-css',$plugin_path .'/js/codemirror/css.js',array('jquery'),false,true);
      wp_enqueue_script('at-code-js-clike',$plugin_path .'/js/codemirror/clike.js',array('jquery'),false,true);
      wp_enqueue_script('at-code-js-php',$plugin_path .'/js/codemirror/php.js',array('jquery'),false,true);
      
    }
  }

  

  /**
   * Check For editor field to enqueue editor scripts
   * @since 1.1.3
   * @access public
   */
  public function check_field_editor(){
    if ($this->has_Field('editor')){
      global $wp_version;
      if ( version_compare( $wp_version, '3.2.1' ) < 1 ) {
        wp_print_scripts('tiny_mce');
        wp_print_scripts('editor');
        wp_print_scripts('editor-functions');
      }
    }
  }  

  /**
   * Check For select field to enqueue Select2 #see http://goo.gl/3pjY8
   * @since 1.1.3
   * @access public
   */
  public function check_field_select(){
    if ($this->has_field_any(array('select','typo')) && $this->is_edit_page()) {
      $plugin_path = $this->SelfPath;
      // Enqueu JQuery chosen library, use proper version.
      wp_enqueue_style('at-multiselect-chosen-css', $plugin_path . '/js/select2/select2.css', array(), null);

      wp_enqueue_script('at-multiselect-chosen-js', $plugin_path . '/js/select2/select2.js', array('jquery'), false, true);
    }
  }

  /**
   * Check Field Plupload
   *
   * @since 0.9.7
   * @access public
   */
  public function check_field_plupload(){
    if ( $this->has_field( 'plupload' )  && $this->is_edit_page() ) {
      $plugin_path = $this->SelfPath;
      wp_enqueue_script('plupload-all');
      wp_register_script('myplupload', $plugin_path .'/js/plupload/myplupload.js', array('jquery'));
      wp_enqueue_script('myplupload');
       wp_register_style('myplupload', $plugin_path .'/js/plupload/myplupload.css');
      wp_enqueue_style('myplupload');
    }
  }


  /**
   * Check the Field Upload, Add needed Actions
   *
   * @since 0.1
   * @access public
   */
  public function check_field_upload() {
    
    // Check if the field is an image or file. If not, return.
    if ( ! $this->has_field_any(array('image','file')) )
      return;
    
    // Add data encoding type for file uploading.  
    add_action( 'post_edit_form_tag', array( $this, 'add_enctype' ) );
    
    if( wp_style_is( 'wp-color-picker', 'registered' ) ){ //since WordPress 3.5
      wp_enqueue_media();
      wp_enqueue_script('media-upload');
    }else{
      // Make upload feature work event when custom post type doesn't support 'editor'
      wp_enqueue_script( 'media-upload' );
      add_thickbox();
      wp_enqueue_script( 'jquery-ui-core' );
      wp_enqueue_script( 'jquery-ui-sortable' );
    }
    
    
    // Add filters for media upload.
    add_filter( 'media_upload_gallery', array( $this, 'insert_images' ) );
    add_filter( 'media_upload_library', array( $this, 'insert_images' ) );
    add_filter( 'media_upload_image',   array( $this, 'insert_images' ) );
    
    // Delete all attachments when delete custom post type.
    add_action( 'wp_ajax_at_delete_file',     array( $this, 'delete_file' ) );
    add_action( 'wp_ajax_at_reorder_images',   array( $this, 'reorder_images' ) );
    // Delete file via Ajax
    add_action( 'wp_ajax_at_delete_mupload', array( $this, 'wp_ajax_delete_image' ) );
  }
  
  /**
   * Add data encoding type for file uploading
   *
   * @since 0.1
   * @access public
   */
  public function add_enctype () {
    echo ' enctype="multipart/form-data"';
  }
  
  /**
   * Process images added to meta field.
   *
   * Modified from Faster Image Insert plugin.
   *
   * @return void
   * @author Cory Crowley
   */
  public function insert_images() {
    
    // If post variables are empty, return.
    if ( ! isset( $_POST['at-insert'] ) || empty( $_POST['attachments'] ) )
      return;
    
    // Security Check
    check_admin_referer( 'media-form' );
    
    // Create Security Nonce
    $nonce = wp_create_nonce( 'at_ajax_delete' );
    
    // Get Post Id and Field Id
    $id = $_POST['field_id'];
    
    // Modify the insertion string
    $html = '';
    foreach( $_POST['attachments'] as $attachment_id => $attachment ) {
      
      // Strip Slashes
      $attachment = stripslashes_deep( $attachment );
      
      // If not selected or url is empty, continue in loop.
      if ( empty( $attachment['selected'] ) || empty( $attachment['url'] ) )
        continue;
        
      $li    = "<li id='item_{$attachment_id}'>";
      $li   .= "<img src='{$attachment['url']}' alt='image_{$attachment_id}' />";
      //$li   .= "<a title='" . __( 'Delete this image' ) . "' class='at-delete-file' href='#' rel='{$nonce}|{$post_id}|{$id}|{$attachment_id}'>" . __( 'Delete' ) . "</a>";
      $li   .= "<a title='" . __( 'Delete this image','apc' ) . "' class='at-delete-file' href='#' rel='{$nonce}|{$post_id}|{$id}|{$attachment_id}'><img src='" . $this->SelfPath. "/images/delete-16.png' alt='" . __( 'Delete' ,'apc') . "' /></a>";
      $li   .= "<input type='hidden' name='{$id}[]' value='{$attachment_id}' />";
      $li   .= "</li>";
      $html .= $li;
      
    } // End For Each
    
    return media_send_to_editor( $html );
    
  }
  
  /**
   * Delete attachments associated with the post.
   *
   * @since 0.1
   * @access public
   *
   */
  public function delete_attachments( $post_id ) {
    
    // Get Attachments
    $attachments = get_posts( array( 'numberposts' => -1, 'post_type' => 'attachment', 'post_parent' => $post_id ) );
    
    // Loop through attachments, if not empty, delete it.
    if ( ! empty( $attachments ) ) {
      foreach ( $attachments as $att ) {
        wp_delete_attachment( $att->ID );
      }
    }
    
  }
  
  
  /**
  * Ajax callback for deleting files.
  * Modified from a function used by "Verve Meta Boxes" plugin (http://goo.gl/LzYSq)
  * @since 0.1
  * @access public
  */
  public function wp_ajax_delete_image() {
    $field_id = isset( $_GET['field_id'] ) ? $_GET['field_id'] : 0;
    $attachment_id = isset( $_GET['attachment_id'] ) ? intval( $_GET['attachment_id'] ) : 0;
    $ok = false;
    $remove_meta_only = apply_filters("apc_delete_image",true);
    if (strpos($field_id, '[') === false){
      check_admin_referer( "at-delete-mupload_".urldecode($field_id));
      $temp = get_option($this->args['option_group']);
      unset($temp[$field_id]);
      update_option($this->args['option_group'],$temp);
      if (!$remove_meta_only)
        $ok =  wp_delete_attachment( $attachment_id );
      else
        $ok = true;
    }else{
      $f = explode('[',urldecode($field_id));
      $f_fiexed = array();
      foreach ($f as $k => $v){
        $f[$k] = str_replace(']','',$v);
      }
      $temp = get_option($this->args['option_group']);

      /**
       * repeater  block
       * $f[0] = repeater id
       * $f[1] = repeater item number
       * $f[2] = actuall in repeater item image field id
       *
       * conditional  block
       * $f[0] = conditional  id
       * $f[1] = actuall in conditional block image field id 
       */
      $saved = $temp[$f[0]]; 
      if (isset($f[2]) && isset($saved[$f[1]][$f[2]])){ //delete from repeater  block
        unset($saved[$f[1]][$f[2]]);
        $temp[$f[0]] = $saved;
        update_option($this->args['option_group'],$temp);
        if (!$remove_meta_only)
          $ok =  wp_delete_attachment( $attachment_id );
        else
          $ok = true;
      }elseif(isset($saved[$f[1]]['src'])){ //delete from conditional block
        unset($saved[$f[1]]);
        $temp[$f[0]] = $saved;
        update_option($this->args['option_group'],$temp);
        if (!$remove_meta_only)
          $ok =  wp_delete_attachment( $attachment_id );
        else
          $ok = true;
      }
    }

    if ( $ok ){
      echo json_encode( array('status' => 'success' ));
      die();
    }else{
      echo json_encode(array('message' => __( 'Cannot delete file. Something\'s wrong.','apc')));
      die();
    }
  }
  
  /**
   * Ajax callback for reordering Images.
   *
   * @since 0.1
   * @access public
   */
  public function reorder_images() {
    
    if ( ! isset( $_POST['data'] ) )
      die();
      
    list( $order, $post_id, $key, $nonce ) = explode( '|', $_POST['data'] );
    
    if ( ! wp_verify_nonce( $nonce, 'at_ajax_reorder' ) )
      die( '1' );
      
    parse_str( $order, $items );
    $items = $items['item'];
    $order = 1;
    foreach ( $items as $item ) {
      wp_update_post( array( 'ID' => $item, 'post_parent' => $post_id, 'menu_order' => $order ) );
      $order++;
    }
    
    die( '0' );
  
  }
  
  /**
   * Check Field Color
   *
   * @since 0.1
   * @access public
   */
  public function check_field_color() {
    
    if ( $this->has_field_any(array('color' ,'typo' ))  && $this->is_edit_page() ) {
      if( wp_style_is( 'wp-color-picker', 'registered' ) ) {
          wp_enqueue_style( 'wp-color-picker' );
          wp_enqueue_script( 'wp-color-picker' );
      }else{
        // Enqueu built-in script and style for color picker.
        wp_enqueue_style( 'farbtastic' );
        wp_enqueue_script( 'farbtastic' );
      }
    }
    
  }
  
  /**
   * Check Field Date
   *
   * @since 0.1
   * @access public 
   */
  public function check_field_date() {
    
    if ( $this->has_field( 'date' ) && $this->is_edit_page() ) {
      $plugin_path = $this->SelfPath;
      // Enqueu JQuery UI, use proper version.
      wp_enqueue_style( 'jquery-ui-css', $plugin_path.'/css/jquery-ui.css' );
      wp_enqueue_script( 'jquery-ui');
	  wp_enqueue_script( 'jquery-ui-datepicker');
    }
    
  }
  
  /**
   * Check Field Time
   *
   * @since 0.1
   * @access public
   */
  public function check_field_time() {
    
    if ( $this->has_field( 'time' ) && $this->is_edit_page() ) {
      $plugin_path = $this->SelfPath;
      
      wp_enqueue_style( 'jquery-ui-css', $plugin_path.'/css/jquery-ui.css' );
      wp_enqueue_script( 'jquery-ui');
      wp_enqueue_script( 'at-timepicker', $plugin_path . '/js/time-and-date/jquery-ui-timepicker-addon.js', array( 'jquery-ui-slider','jquery-ui-datepicker' ), null, true );
    
    }
    
  }
  
  /**
   * Add Meta Box for multiple post types.
   *
   * @since 0.1
   * @access public
   */
  public function add() {
    
    // Loop through array
    foreach ( $this->_meta_box['pages'] as $page ) {
      add_meta_box( $this->_meta_box['id'], $this->_meta_box['title'], array( $this, 'show' ), $page, $this->_meta_box['context'], $this->_meta_box['priority'] );
    }
    
  }
  
  /**
   * Callback function to show fields in Page.
   *
   * @since 0.1
   * @access public 
   */
  public function show() {
    
    global $post;
    wp_nonce_field( basename(__FILE__), 'BF_Admin_Page_Class_nonce' );
    echo '<table class="form-table">';
    foreach ( $this->_fields as $field ) {
      $meta = get_post_meta( $post->ID, $field['id'], !$field['multiple'] );
      $meta = ( $meta !== '' ) ? $meta : $field['std'];
      if ('image' != $field['type'] && $field['type'] != 'repeater')
        $meta = is_array( $meta ) ? array_map( 'esc_attr', $meta ) : esc_attr( $meta );
      echo '<tr>';
    
      // Call Separated methods for displaying each type of field.
      call_user_func ( array( $this, 'show_field_' . $field['type'] ), $field, $meta );
      echo '</tr>';
    }
    echo '</table>';
  }
  
  /**
   * Show Repeater Fields.
   *
   * @param string $field 
   * @param string $meta 
   * @since 0.1
   * @modified at 0.4 added sortable option
   * @access public
   */
  public function show_field_repeater( $field, $meta ) {
    // Get Plugin Path
    $plugin_path = $this->SelfPath;
    $this->show_field_begin( $field, $meta );
    $class = '';
    if ($field['sortable'])
      $class = " repeater-sortable";
    $jsid = ltrim(strtolower(str_replace(' ','',$field['id'])), '0123456789');
    echo "<div class='at-repeat".$class."' id='{$jsid}'>";
    
    $c = 0;
    $temp_div_row = $this->_div_or_row;
    $this->_div_or_row = true;
    $meta = isset($this->_saved[$field['id']])? $this->_saved[$field['id']]: '';
    
      if (count($meta) > 0 && is_array($meta) ){
         foreach ($meta as $me){
           //for labling toggles
           $mmm =  isset($me[$field['fields'][0]['id']])? $me[$field['fields'][0]['id']]: "";
           $mmm = (in_array($field['fields'][0]['type'],array('image','file'))? '' : $mmm);
           echo '<div class="at-repater-block">'.$mmm.'<br/><table class="repeater-table" style="display: none;">';
           if ($field['inline']){
             echo '<tr class="at-inline" VALIGN="top">';
           }
          foreach ($field['fields'] as $f){
            //reset var $id for repeater
            $id = '';
            $id = $field['id'].'['.$c.']['.$f['id'].']';
            $m = isset($me[$f['id']])? $me[$f['id']]: '';
            if ( $m !== '' ) {
              $m = $m;
            }else{
              $m = isset($f['std'])? $f['std'] : '';
            }
            if ('image' != $f['type'] && $f['type'] != 'repeater')
              $m = is_array( $m) ? array_map( 'esc_attr', $m ) : esc_attr( $m);
            if (in_array($f['type'],array('text','textarea')))
              $m = stripslashes($m);
            //set new id for field in array format
            $f['id'] = $id;
            if (!$field['inline']){
              echo '<tr>';
            }
            call_user_func ( array( $this, 'show_field_' . $f['type'] ), $f, $m);
            if (!$field['inline']){
              echo '</tr>';
            } 
          }
        if ($field['inline']){  
          echo '</tr>';
        }
        echo '</table>
        <span class="at-re-toggle"><img src="';
           if ($this->_Local_images){
             echo $plugin_path.'/images/edit.png';
           }else{
             echo 'http://i.imgur.com/ka0E2.png';
           }
           echo '" alt="Edit" title="Edit"/></span> 
        <img src="';
        if ($this->_Local_images){
          echo $plugin_path.'/images/remove.png';
        }else{
          echo 'http://i.imgur.com/g8Duj.png';
        }
        echo '" alt="'.__('Remove','apc').'" title="'.__('Remove','apc').'" id="remove-'.$field['id'].'"></div>';
        $c = $c + 1;
        
        }
      }

    echo '<img src="';
    if ($this->_Local_images){
      echo $plugin_path.'/images/add.png';
    }else{
      echo 'http://i.imgur.com/w5Tuc.png';
    }
    echo '" alt="'.__('Add','apc').'" title="'.__('Add','apc').'" id="add-'.$jsid.'"><br/></div>';
    
    //create all fields once more for js function and catch with object buffer
    ob_start();
    echo '<div class="at-repater-block"><table class="repeater-table">';
    if ($field['inline']){
      echo '<tr class="at-inline" VALIGN="top">';
    } 
    foreach ($field['fields'] as $f){
      //reset var $id for repeater
      $id = '';

      $id = $field['id'].'[CurrentCounter]['.$f['id'].']';
      $f['id'] = $id; 
      if (!$field['inline']){
        echo '<tr>';
      }
      $m = isset($f['std'])? $f['std'] : '';
      call_user_func ( array( $this, 'show_field_' . $f['type'] ), $f, $m);
      if (!$field['inline']){
        echo '</tr>';
      }  
    }
    if ($field['inline']){
      echo '</tr>';
    } 
    echo '</table><img src="';
    if ($this->_Local_images){
      echo $plugin_path.'/images/remove.png';
    }else{
      echo 'http://i.imgur.com/g8Duj.png';
    }
    
    echo '" alt="'.__('Remove','apc').'" title="'.__('Remove','apc').'" id="remove-'.$jsid.'"></div>';
    $counter = 'countadd_'.$jsid;
    $js_code = ob_get_clean ();    
    $js_code = str_replace("'","\"",$js_code);
    $js_code = str_replace("CurrentCounter","' + ".$counter." + '",$js_code);
    echo '<script>
        jQuery(document).ready(function() {
          var '.$counter.' = '.$c.';
          jQuery("#add-'.$jsid.'").live(\'click\', function() {
            '.$counter.' = '.$counter.' + 1;
            jQuery(this).before(\''.$js_code.'\');            
            update_repeater_fields();
          });
              jQuery("#remove-'.$jsid.'").live(\'click\', function() {
                  jQuery(this).parent().remove();
              });
          });
        </script>';            
    echo '<br/><style>
.at-inline{line-height: 1 !important;}
.at-inline .at-field{border: 0px !important;}
.at-inline .at-label{margin: 0 0 1px !important;}
.at-inline .at-text{width: 70px;}
.at-inline .at-textarea{width: 100px; height: 75px;}
.at-repater-block{background-color: #FFFFFF;border: 1px solid;margin: 2px;}
</style>';
  
    $this->_div_or_row = $temp_div_row;
    $this->show_field_end($field, $meta);
  }
  
  /**
   * Begin Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 0.1
   * @access public
   */
  public function show_field_begin( $field, $meta) {
    if ($this->_div_or_row){
      echo "<td class='at-field'>";
    }
    
    //check for errors
    if ($this->saved_flag && $this->errors_flag && isset($field['validate']) && isset($field['id']) && $this->has_error($field['id'])){
      echo '<div class="alert alert-error field-validation-error"><button data-dismiss="alert" class="close" type="button">×</button>';
      $ers = $this->getFieldErrors($field['id']);
      foreach ((array)$ers['m'] as $m) {
        echo "{$m}</br />";
      }
      echo '</div>';
    }
      
    if ( $field['name'] != '' || $field['name'] != FALSE )
        echo "<div class='at-label'><label for='{$field['id']}'>{$field['name']}</label></div>";
  }
  
  /**
   * End Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 0.1
   * @access public 
   */
  public function show_field_end( $field, $meta=NULL ,$group = false) {
    if ( isset($field['desc']) && $field['desc'] != '' ) 
      echo "<div class='desc-field'>{$field['desc']}</div>";
    
    if ($this->_div_or_row)
      echo "</td>";

    
  }

  /**
   * Show Sortable Field
   * @author Ohad   Raz
   * @since 0.4
   * @access public
   * @param  (array) $field 
   * @param  (array) $meta 
   * @return void
   */
  public function show_field_sortable( $field, $meta ) {
      
    $this->show_field_begin( $field, $meta );
    $re = '<div class="at-sortable-con"><ul class="at-sortable">';
    $i = 0;
    if ( ! is_array( $meta ) || empty($meta) ){
      foreach ( $field['options'] as $value => $label ) {
        $re .= '<li class="widget-sort at-sort-item_'.$i.'">'.$label.'<input type="hidden" value="'.$label.'" name="'.$field['id'].'['.$value.']">';
      }
    }
    else{
      foreach ( $meta as $value => $label ) {
        $re .= '<li class="widget-sort at-sort-item_'.$i.'">'.$label.'<input type="hidden" value="'.$label.'" name="'.$field['id'].'['.$value.']">';
      }
    }
    $re .= '</ul></div>';
    echo $re;
    $this->show_field_end( $field, $meta );
  }
  
  /**
   * Show Field Text.
   *
   * @param string $field 
   * @param string $meta 
   * @since 0.1
   * @access public
   */
  public function show_field_text( $field, $meta) {  
    $this->show_field_begin( $field, $meta );
    echo "<input type='text' class='at-text".(isset($field['class'])? " {$field['class']}": "")."' name='{$field['id']}' id='{$field['id']}' value='{$meta}' size='30' />";
    $this->show_field_end( $field, $meta );
  }

  /**
   * Show Field Plupload.
   *
   * @param string $field 
   * @param string $meta 
   * @since 0.9.7
   * @access public
   */
  public function show_field_plupload( $field, $meta) {  

    $this->show_field_begin($field,$meta);
    $id = $field['id']; // this will be the name of form field. Image url(s) will be submitted in $_POST using this key. So if $id == “img1” then $_POST[“img1”] will have all the image urls
    $multiple = $field['multiple']; // allow multiple files upload
    $m1 = ($multiple)? 'plupload-upload-uic-multiple':'';
    $m2 = ($multiple)? 'plupload-thumbs-multiple':'';
    $width = $field['width']; // If you want to automatically resize all uploaded images then provide width here (in pixels)
    $height = $field['height']; // If you want to automatically resize all uploaded images then provide height here (in pixels)
    $html = '
        <input type="hidden" name="'.$id.'" id="'. $id.'" value="'.$meta .'" />
        <div class="plupload-upload-uic hide-if-no-js '.$m1.'" id="'.$id.'plupload-upload-ui">
          <input id="'.$id.'plupload-browse-button" type="button" value="'.__('Select Files','apc').'" class="button" />
          <span class="ajaxnonceplu" id="ajaxnonceplu'.wp_create_nonce($id . 'pluploadan').'"></span>';
          if ($width && $height){
            $html .= '<span class="plupload-resize"></span><span class="plupload-width" id="plupload-width'.$width.'"></span>
              <span class="plupload-height" id="plupload-height'.$height.'"></span>';
          }
          $html .= '<div class="filelist"></div>
        </div>
        <div class="plupload-thumbs '.$m2.'" id="'.$id.'plupload-thumbs">
        </div>
        <div class="clear"></div>';
      echo $html;
      $this->show_field_end($field,$meta);
  }
  
  /**
   * Show Field code editor.
   *
   * @param string $field 
   * @author Ohad Raz
   * @param string $meta 
   * @since 0.1
   * @access public
   */
  public function show_field_code( $field, $meta) {
    $this->show_field_begin( $field, $meta );
    echo "<textarea class='code_text".(isset($field['class'])? " {$field['class']}": "")."' name='{$field['id']}' id='{$field['id']}' data-lang='{$field['syntax']}' data-theme='{$field['theme']}'>".stripslashes($meta)."</textarea>";
    $this->show_field_end( $field, $meta );
  }


  /**
   * Show Field hidden.
   *
   * @param string $field 
   * @param string|mixed $meta 
   * @since 0.1
   * @access public
   */
  public function show_field_hidden( $field, $meta) {  
    echo "<input type='hidden' class='at-hidden".(isset($field['class'])? " {$field['class']}": "")."' name='{$field['id']}' id='{$field['id']}' value='{$meta}'/>";
  }
  
  /**
   * Show Field Paragraph.
   *
   * @param string $field 
   * @since 0.1
   * @access public
   */
  public function show_field_paragraph( $field) {  
    echo '<p>'.$field['value'].'</p>';
  }
    
  /**
   * Show Field Textarea.
   *
   * @param string $field 
   * @param string $meta 
   * @since 0.1
   * @access public
   */
  public function show_field_textarea( $field, $meta ) {
    $this->show_field_begin( $field, $meta );
      echo "<textarea class='at-textarea large-text".(isset($field['class'])? " {$field['class']}": "")."' name='{$field['id']}' id='{$field['id']}' cols='60' rows='10'>{$meta}</textarea>";
    $this->show_field_end( $field, $meta );
  }
  
  /**
   * Show Field Select.
   *
   * @param string $field 
   * @param string $meta 
   * @since 0.1
   * @access public
   */
  public function show_field_select( $field, $meta ) {
    
    if ( ! is_array( $meta ) ) 
      $meta = (array) $meta;

    $this->show_field_begin( $field, $meta );
      echo "<select class='at-select".(isset($field['class'])? " {$field['class']}": "")."' name='{$field['id']}" . ((isset($field['multiple']) && $field['multiple']) ? "[]' id='{$field['id']}' multiple='multiple'" : "'" ) . ">";
      foreach ( $field['options'] as $key => $value ) {
        echo "<option value='{$key}'" . selected( in_array( $key, $meta ), true, false ) . ">{$value}</option>";
      }
      echo "</select>";
    $this->show_field_end( $field, $meta );
  }
  
  /**
   * Show Radio Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 0.1
   * @access public 
   */
  public function show_field_radio( $field, $meta ) {
    
    if ( ! is_array( $meta ) )
      $meta = (array) $meta;
      
    $this->show_field_begin( $field, $meta );
      foreach ( $field['options'] as $key => $value ) {
        echo "<input type='radio' class='at-radio".(isset($field['class'])? " {$field['class']}": "")."' name='{$field['id']}' value='{$key}'" . checked( in_array( $key, $meta ), true, false ) . " /> <span class='at-radio-label'>{$value}</span>";
      }
    $this->show_field_end( $field, $meta );
  }
  
  /**
   * Show Checkbox Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 0.1
   * @access public
   */
  public function show_field_checkbox( $field, $meta ) {
  
    $this->show_field_begin($field, $meta);
    $meta = ($meta == 'on')? true: $meta;
    echo "<input type='checkbox' class='rw-checkbox".(isset($field['class'])? " {$field['class']}": "")."' name='{$field['id']}' id='{$field['id']}'" . checked($meta, true, false) . " />";
    $this->show_field_end( $field, $meta );
  }

  /**
   * Show conditinal Checkbox Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 0.5
   * @access public
   */
  public function show_field_cond( $field, $meta ) {
  
    $this->show_field_begin($field, $meta);
    $checked = false;
    if (is_array($meta) && isset($meta['enabled']) && $meta['enabled'] == 'on'){
      $checked = true;
    }
    echo "<input type='checkbox' class='conditinal_control' name='{$field['id']}[enabled]' id='{$field['id']}'" . checked($checked, true, false) . " />";
    //start showing the fields
    $display = ($checked)? '' :  ' style="display: none;"';
    
    echo '<div class="conditinal_container"'.$display.'>';
    foreach ((array)$field['fields'] as $f){
      //reset var $id for cond
      $id = '';
      $id = $field['id'].'['.$f['id'].']';
      $m = '';
      $m = (isset($meta[$f['id']])) ? $meta[$f['id']]: '';
      $m = ( $m !== '' ) ? $m : (isset($f['std'])? $f['std'] : '');
      if ('image' != $f['type'] && $f['type'] != 'repeater')
        $m = is_array( $m) ? array_map( 'esc_attr', $m ) : esc_attr( $m);
        //set new id for field in array format
        $f['id'] = $id;
        call_user_func ( array( $this, 'show_field_' . $f['type'] ), $f, $m);
    }
    echo '</div>';
    $this->show_field_end( $field, $meta );
  }
  
  /**
   * Show Wysiwig Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 0.1
   * @access public
   */
  public function show_field_wysiwyg( $field, $meta ) {
    $this->show_field_begin( $field, $meta );
    // Add TinyMCE script for WP version < 3.3
    global $wp_version;

    if ( version_compare( $wp_version, '3.2.1' ) < 1 ) {
      echo "<textarea class='at-wysiwyg theEditor large-text".(isset($field['class'])? " {$field['class']}": "")."' name='{$field['id']}' id='{$field['id']}' cols='60' rows='10'>{$meta}</textarea>";
    }else{
      // Use new wp_editor() since WP 3.3
      wp_editor( stripslashes(stripslashes(html_entity_decode($meta))), $field['id'], array( 'editor_class' => 'at-wysiwyg'.(isset($field['class'])? " {$field['class']}": "")) );
    }
    $this->show_field_end( $field, $meta );
  }
  
  /**
   * Show File Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 0.1
   * @access public
   */
  public function show_field_file( $field, $meta ) {
    
    global $post;

    if ( ! is_array( $meta ) )
      $meta = (array) $meta;

    $this->show_field_begin( $field, $meta );
      echo "{$field['desc']}<br />";

      if ( ! empty( $meta ) ) {
        $nonce = wp_create_nonce( 'at_ajax_delete' );
        echo '<div style="margin-bottom: 10px"><strong>' . __('Uploaded files','apc') . '</strong></div>';
        echo '<ol class="at-upload">';
        foreach ( $meta as $att ) {
          // if (wp_attachment_is_image($att)) continue; // what's image uploader for?
          echo "<li>" . wp_get_attachment_link( $att, '' , false, false, ' ' ) . " (<a class='at-delete-file' href='#' rel='{$nonce}|{$post->ID}|{$field['id']}|{$att}'>" . __( 'Delete' ,'apc') . "</a>)</li>";
        }
        echo '</ol>';
      }

      // show form upload
      echo "<div class='at-file-upload-label'>";
        echo "<strong>" . __( 'Upload new files' ,'apc') . "</strong>";
      echo "</div>";
      echo "<div class='new-files'>";
        echo "<div class='file-input'>";
          echo "<input type='file' name='{$field['id']}[]' />";
        echo "</div><!-- End .file-input -->";
        echo "<a class='at-add-file button' href='#'>" . __( 'Add more files','apc' ) . "</a>";
      echo "</div><!-- End .new-files -->";
    echo "</td>";
  }
  

  public function show_field_media_manager($field,$meta){
    $this->show_field_begin( $field, $meta );
    $html = wp_nonce_field( "at-delete-mupload_{$field['id']}", "nonce-delete-mupload_".$field['id'], false, false );
    $height = (isset($field['preview_height']))? $field['preview_height'] : '150px';
    $width = (isset($field['preview_width']))? $field['preview_width'] : '150px';
    $multi = (isset($field['multiple']) && $field['multiple'] == true) ? 'true' : 'false';
    if (is_array($meta)){
      if(isset($meta[0]) && is_array($meta[0]))
      $meta = $meta[0];
    }
    if (is_array($meta) && isset($meta['src']) && $meta['src'] != ''){
      $html .= "<span class='mupload_img_holder' data-wi='".$width."' data-he='".$height."'><img src='".$meta['src']."' style='height: ".$height.";width: ".$width.";' /></span>";
      $html .= "<input type='hidden' name='".$field['id']."[id]' id='".$field['id']."[id]' value='".$meta['id']."' />";
      $html .= "<input type='hidden' name='".$field['id']."[src]' id='".$field['id']."[src]' value='".$meta['src']."' />";
      $html .= "<input class='at-delete_image_button button' type='button' rel='".$field['id']."' value='".__('Delete Image','apc')."' />";
    }else{
      $html .= "<span class='mupload_img_holder'  data-wi='".$width."' data-he='".$height."' data-multi='".$multi."'></span>";
      $html .= "<input type='hidden' name='".$field['id']."[id]' id='".$field['id']."[id]' value='' />";
      $html .= "<input type='hidden' name='".$field['id']."[src]' id='".$field['id']."[src]' value='' />";
      $html .= "<input class='at-mm-upload_image_button button' type='button' rel='".$field['id']."' value='".__('Upload Image','apc')."' />";
    }
    echo $html;
    $this->show_field_end( $field, $meta );
  }

  /**
   * Show Image Field.
   *
   * @param array $field 
   * @param array $meta 
   * @since 0.1
   * @access public
   */
  public function show_field_image( $field, $meta ) {
    $this->show_field_begin( $field, $meta );
    $html = wp_nonce_field( "at-delete-mupload_{$field['id']}", "nonce-delete-mupload_".$field['id'], false, false );
    $height = (isset($field['preview_height']))? $field['preview_height'] : '150px';
    $width = (isset($field['preview_width']))? $field['preview_width'] : '150px';
    $upload_type = (!function_exists('wp_enqueue_media')) ? 'tk' : 'mm';
    if (is_array($meta)){
      if(isset($meta[0]) && is_array($meta[0]))
      $meta = $meta[0];
    }
    if (is_array($meta) && isset($meta['src']) && $meta['src'] != ''){
      $html .= "<span class='mupload_img_holder' data-wi='".$width."' data-he='".$height."'><img src='".$meta['src']."' style='height: ".$height.";width: ".$width.";' /></span>";
      $html .= "<input type='hidden' name='".$field['id']."[id]' id='".$field['id']."[id]' value='".$meta['id']."' />";
      $html .= "<input type='hidden' name='".$field['id']."[src]' id='".$field['id']."[src]' value='".$meta['src']."' />";
      $html .= "<input class='at-delete_image_button button' type='button' data-u='".$upload_type."' rel='".$field['id']."' value='".__('Delete Image','apc')."' />";
    }else{
      $html .= "<span class='mupload_img_holder'  data-wi='".$width."' data-he='".$height."'></span>";
      $html .= "<input type='hidden' name='".$field['id']."[id]' id='".$field['id']."[id]' value='' />";
      $html .= "<input type='hidden' name='".$field['id']."[src]' id='".$field['id']."[src]' value='' />";
      $html .= "<input class='at-upload_image_button button' type='button' data-u='".$upload_type."' rel='".$field['id']."' value='".__('Upload Image','apc')."' />";
    }
    echo $html;
    $this->show_field_end( $field, $meta );
  }

  /**
   * Show Typography Field.
   *
   * @author Ohad Raz
   * @param array $field 
   * @param array $meta 
   * @since 0.3
   * @access public
   * 
   * @last modified 0.4 - faster better selected handeling
   */
  public function show_field_typo( $field, $meta ) {
    $this->show_field_begin( $field, $meta );
    if (!is_array($meta)){
      $meta = array(
        'size' => '',
        'face' => '',
        'style' => '',
        'color' => '#',
        'weight' => '',
      );
    }
    $html = '<select class="at-typography at-typography-size" name="' . esc_attr( $field['id'] . '[size]' ) . '" id="' . esc_attr( $field['id'] . '_size' ) . '">';
    $op = '';
    for ($i = 9; $i < 71; $i++) {
      $size = $i . 'px';
      $op .= '<option value="' . esc_attr( $size ) . '">' . esc_html( $size ) . '</option>';
    }
    if (isset($meta['size']))
      $op = str_replace('value="'.$meta['size'].'"', 'value="'.$meta['size'].'" selected="selected"', $op);
    $html .=$op. '</select>';

    // Font Face
    $html .= '<select class="at-typography at-typography-face" name="' . esc_attr( $field['id'] .'[face]' ) . '" id="' . esc_attr( $field['id'] . '_face' ) . '">';

    $faces = $this->get_fonts_family();
    $op = '';
    foreach ( $faces as $key => $face ) {
      $op .= '<option value="' . esc_attr( $key ) . '">' . esc_html( $face['name'] ) . '</option>';
    }
    if (isset($meta['face']))
      $op = str_replace('value="'.$meta['face'].'"', 'value="'.$meta['face'].'" selected="selected"', $op);
    $html .= $op. '</select>';

    // Font Weight
    $html .= '<select class="at-typography at-typography-weight" name="' . esc_attr( $field['id'] .'[weight]' ) . '" id="' . esc_attr( $field['id'] . '_weight' ) . '">';
    $weights = $this->get_font_weight();
    $op = '';
    foreach ( $weights as $key => $label ) {
      $op .= '<option value="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</option>';
    }
    if (isset($meta['weight']))
      $op = str_replace('value="'.$meta['weight'].'"', 'value="'.$meta['weight'].'" selected="selected"', $op);
    $html .= $op. '</select>';

    /* Font Style */    
    $html .= '<select class="at-typography at-typography-style" name="'.$field['id'].'[style]" id="'. $field['id'].'_style">';
    $styles = $this->get_font_style();
    $op = '';
    foreach ( $styles as $key => $style ) {
      $op .= '<option value="' . esc_attr( $key ) . '">'. $style .'</option>';
    }
    if (isset($meta['style']))
      $op = str_replace('value="'.$meta['style'].'"', 'value="'.$meta['style'].'" selected="selected"', $op);
    $html .= $op. '</select>';

    // Font Color
    if( wp_style_is( 'wp-color-picker', 'registered' ) ) { //iris color picker since 3.5
      $html .= "<input class='at-color-iris' type='text' name='{$field['id']}[color]' id='{$field['id']}[color]' value='".$meta['color']."' size='8' />";  
    }else{
      $html .= "<input class='at-color' type='text' name='".$field['id']."[color]' id='".$field['id']."[color]' value='".$meta['color'] ."' size='6' />";
      $html .= "<input type='button' class='at-color-select button' rel='".$field['id']."[color]' value='" . __( 'Select a color' ,'apc') . "'/>";
      $html .= "<div style='display:none' class='at-color-picker' rel='".$field['id']."[color]'></div>";
    }
    
    echo $html;
    $this->show_field_end( $field, $meta );
  }
  
  /**
   * Show Color Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 0.1
   * @access public
   */
  public function show_field_color( $field, $meta ) {
    
    if ( empty( $meta ) ) 
      $meta = '#';
      
    $this->show_field_begin( $field, $meta );
    if( wp_style_is( 'wp-color-picker', 'registered' ) ) { //iris color picker since 3.5
      echo "<input class='at-color-iris".(isset($field['class'])? " {$field['class']}": "")."' type='text' name='{$field['id']}' id='{$field['id']}' value='{$meta}' size='8' />";  
    }else{
      echo "<input class='at-color".(isset($field['class'])? " {$field['class']}": "")."' type='text' name='{$field['id']}' id='{$field['id']}' value='{$meta}' size='8' />";
      echo "<input type='button' class='at-color-select button' rel='{$field['id']}' value='" . __( 'Select a color' ,'apc') . "'/>";
      echo "<div style='display:none' class='at-color-picker' rel='{$field['id']}'></div>";
    }
    $this->show_field_end($field, $meta);
    
  }

  /**
   * Show Checkbox List Field
   *
   * @param string $field 
   * @param string $meta 
   * @since 0.1
   * @access public
   */
  public function show_field_checkbox_list( $field, $meta ) {
    
    if ( ! is_array( $meta ) ) 
      $meta = (array) $meta;
      
    $this->show_field_begin($field, $meta);
    
      $html = array();
    
      foreach ($field['options'] as $key => $value) {
        $html[] = "<label class='at-checkbox_list-label'><input type='checkbox' class='at-checkbox_list".(isset($field['class'])? " {$field['class']}": "")."' name='{$field['id']}[]' value='{$key}'" . checked( in_array( $key, $meta ), true, false ) . " />{$value}</label>";
      }
    
      echo implode( '<br />' , $html );
      
    $this->show_field_end($field, $meta);
    
  }
  
  /**
   * Show Date Field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 0.1
   * @access public
   */
  public function show_field_date( $field, $meta ) {
    $this->show_field_begin( $field, $meta );
      echo "<input type='text' class='at-date".(isset($field['class'])? " {$field['class']}": "")."' name='{$field['id']}' id='{$field['id']}' rel='{$field['format']}' value='{$meta}' size='30' />";
    $this->show_field_end( $field, $meta );
  }
  
  /**
   * Show time field.
   *
   * @param string $field 
   * @param string $meta 
   * @since 0.1
   * @access public 
   */
  public function show_field_time( $field, $meta ) {
    $this->show_field_begin( $field, $meta );
      echo "<input type='text' class='at-time".(isset($field['class'])? " {$field['class']}": "")."' name='{$field['id']}' id='{$field['id']}' rel='{$field['format']}' value='{$meta}' size='30' />";
    $this->show_field_end( $field, $meta );
  }
  
   /**
   * Show Posts field.
   * used creating a posts/pages/custom types checkboxlist or a select dropdown
   * @param string $field 
   * @param string $meta 
   * @since 0.1
   * @access public 
   */
  public function show_field_posts($field, $meta) {
    global $post;
    
    if (!is_array($meta)) $meta = (array) $meta;
    $this->show_field_begin($field, $meta);
    $options = $field['options'];
    $posts = get_posts($options['args']);
    
    // checkbox_list
    if ('checkbox_list' == $options['type']) {
      foreach ($posts as $p) {
        if (isset($field['class']) && $field['class']== 'no-toggle')
          echo "<label class='at-posts-checkbox-label'><input type='checkbox' class='at-posts-checkbox".(isset($field['class'])? " {$field['class']}": "")."' name='{$field['id']}[]' value='$p->ID'" . checked(in_array($p->ID, $meta), true, false) . " /> {$p->post_title}</label>";
        else
          echo "{$p->post_title}<input type='checkbox' class='at-posts-checkbox".(isset($field['class'])? " {$field['class']}": "")."' name='{$field['id']}[]' value='$p->ID'" . checked(in_array($p->ID, $meta), true, false) . " />";
      }
    }
    // select
    else {
      echo "<select class='at-posts-select".(isset($field['class'])? " {$field['class']}": "")."' name='{$field['id']}" . ($field['multiple'] ? "[]' multiple='multiple'  style='height:auto'" : "'") . ">";
      foreach ($posts as $p) {
        echo "<option value='$p->ID'" . selected(in_array($p->ID, $meta), true, false) . ">$p->post_title</option>";
      }
      echo "</select>";
    }
    
    $this->show_field_end($field, $meta);
  }
  
  /**
   * Show Taxonomy field.
   * used creating a category/tags/custom taxonomy checkboxlist or a select dropdown
   * @param string $field 
   * @param string $meta 
   * @since 0.1
   * @access public 
   * 
   * @uses get_terms()
   */
  public function show_field_taxonomy($field, $meta) {
    global $post;
    
    if (!is_array($meta)) $meta = (array) $meta;
    $this->show_field_begin($field, $meta);
    $options = $field['options'];
    $terms = get_terms($options['taxonomy'], $options['args']);
    
    // checkbox_list
    if ('checkbox_list' == $options['type']) {
      foreach ($terms as $term) {
        if (isset($field['class']) && $field['class'] == 'no-toggle')
          echo "<label class='at-tax-checkbox-label'><input type='checkbox' class='at-tax-checkbox".(isset($field['class'])? " {$field['class']}": "")."' name='{$field['id']}[]' value='$term->slug'" . checked(in_array($term->slug, $meta), true, false) . " /> {$term->name}</label>";
        else
          echo "{$term->name} <input type='checkbox' class='at-tax-checkbox".(isset($field['class'])? " {$field['class']}": "")."' name='{$field['id']}[]' value='$term->slug'" . checked(in_array($term->slug, $meta), true, false) . " />";
      }   
    }
    // select
    else {
      echo "<select class='at-tax-select".(isset($field['class'])? " {$field['class']}": "")."' name='{$field['id']}" . ($field['multiple'] ? "[]' multiple='multiple' style='height:auto'" : "'") . ">";
      foreach ($terms as $term) {
        echo "<option value='$term->slug'" . selected(in_array($term->slug, $meta), true, false) . ">$term->name</option>";
      }
      echo "</select>";
    }
    
    $this->show_field_end($field, $meta);
  }

  /**
   * Show Role field.
   * used creating a Wordpress roles list checkboxlist or a select dropdown
   * @param string $field 
   * @param string $meta 
   * @since 0.1
   * @access public 
   * 
   * @uses global $wp_roles;
   * @uses checked();
   */
  public function show_field_WProle($field, $meta) {
    if (!is_array($meta)) $meta = (array) $meta;
    $this->show_field_begin($field, $meta);
    $options = $field['options'];
    global $wp_roles;
    if ( ! isset( $wp_roles ) )
      $wp_roles = new WP_Roles();
    $names = $wp_roles->get_names();
    if ($names){
      // checkbox_list
      if ('checkbox_list' == $options['type']) {
        foreach ($names as $n) {
          if (isset($field['class']) && $field['class'] == 'no-toggle')
            echo "<label class='at-posts-checkbox-label'><input type='checkbox'  class='at-role-checkbox".(isset($field['class'])? " {$field['class']}": "")."' name='{$field['id']}[]' value='$n'" . checked(in_array($n, $meta), true, false) . " /> ".$n."</label>";
          else
            echo "{$n} <input type='checkbox'  class='at-role-checkbox".(isset($field['class'])? " {$field['class']}": "")."' name='{$field['id']}[]' value='$n'" . checked(in_array($n, $meta), true, false) . " />";
        }
      }
      // select
      else {
        echo "<select  class='at-role-select".(isset($field['class'])? " {$field['class']}": "")."' name='{$field['id']}" . ($field['multiple'] ? "[]' multiple='multiple' style='height:auto'" : "'") . ">";
        foreach ($names as $n) {
          echo "<option value='$n'" . selected(in_array($n, $meta), true, false) . ">$n</option>";
        }
        echo "</select>";
      }
    }
    $this->show_field_end($field, $meta);
  }
  
  /**
   * Save Data from page
   *
   * @param string $repeater (false )
   * @since 0.1
   * @access public 
   */
  public function save($repeater = false) {
    $saved  = get_option($this->option_group);
    $this->_saved = $saved;

    $post_data = isset($_POST)? $_POST : NULL;
    
    If ($post_data == NULL) return;

    $skip = array('title','paragraph','subtitle','TABS','CloseDiv','TABS_Listing','OpenTab','import_export');
    
    //check nonce
    if ( ! check_admin_referer( basename( __FILE__ ), 'BF_Admin_Page_Class_nonce') )
      return;
    
    foreach ( $this->_fields as $field ) {
      if(!in_array($field['type'],$skip)){
      
        $name = $field['id'];
        $type = $field['type'];
        $old = isset($saved[$name])? $saved[$name]: NULL;
        $new = ( isset( $_POST[$name] ) ) ? $_POST[$name] : ( ( isset($field['multiple']) && $field['multiple']) ? array() : '' );
              

        //Validate and senitize meta value
        //issue #27
        $validationClass = apply_filters('apc_validattion_class_name', 'BF_Admin_Page_Class_Validate',$this);
        if ( class_exists( $validationClass ) && isset($field['validate_func']) && method_exists( $validationClass, $field['validate_func'] ) ) {
          $new = call_user_func( array( $validationClass, $field['validate_func'] ), $new ,$this);
        }

        //native validation
        if (isset($field['validate'])){
          if (!$this->validate_field($field,$new))
            $new = $old;
        }


        // Call defined method to save meta value, if there's no methods, call common one.
        $save_func = 'save_field_' . $type;
        if ( method_exists( $this, $save_func ) ) {
          call_user_func( array( $this, 'save_field_' . $type ), $field, $old, $new );
        } else {
          $this->save_field( $field, $old, $new );
        }
      
      }//END Skip
    } // End foreach
    update_option($this->args['option_group'],$this->_saved);
  }
  
  /**
   * Common function for saving fields.
   *
   * @param string $field 
   * @param string $old 
   * @param string|mixed $new 
   * @since 0.1
   * @access public
   */
  public function save_field( $field, $old, $new ) {
    $name = $field['id'];
    unset($this->_saved[$name]);
    if ( $new === '' || $new === array() ) 
      return;
    if ( isset($field['multiple'] ) && $field['multiple'] && $field['type'] != 'plupload') {
      foreach ( $new as $add_new ) {
        $temp[] = $add_new;
      }
      $this->_saved[$name] = $temp;
    } else {
      $this->_saved[$name] = $new;
    }
  }
  
  /**
   * function for saving image field.
   *
   * @param string $field 
   * @param string $old 
   * @param string|mixed $new 
   * @since 0.1
   * @access public
   */
  public function save_field_image(  $field, $old, $new ) {
    $name = $field['id'];
    unset($this->_saved[$name]);
    if ( $new === '' || $new === array() || $new['id'] == '' || $new['src'] == '') 
      return;
    
    $this->_saved[$name] = $new;
  }
  
  /*
   * Save Wysiwyg Field.
   *
   * @param string $field 
   * @param string $old 
   * @param string $new 
   * @since 0.1
   * @access public 
   */
  public function save_field_wysiwyg(  $field, $old, $new ) {
    $this->save_field(  $field, $old, htmlentities($new) );
  }
  
  /*
   * Save checkbox Field.
   *
   * @param string $field 
   * @param string $old 
   * @param string $new 
   * @since 0.9
   * @access public 
   */
  public function save_field_checkbox(  $field, $old, $new ) {
    if ( $new === '' )
      $this->save_field(  $field, $old, false );
    else
      $this->save_field(  $field, $old, true );
  }  
    
  /**
   * Save repeater Fields.
   *
   * @param string $field 
   * @param string|mixed $old 
   * @param string|mixed $new 
   * @since 0.1
   * @access public 
   */
  public function save_field_repeater( $field, $old, $new ) {
    if (is_array($new) && count($new) > 0){
      foreach ($new as $n){
        foreach ( $field['fields'] as $f ) {
          $type = $f['type'];
          switch($type) {
            case 'wysiwyg':
                $n[$f['id']] = wpautop( $n[$f['id']] ); 
                break;
              case 'file':
                $n[$f['id']] = $this->save_field_file_repeater($f,'',$n[$f['id']]);
                break;
              default:
                   break;
          }
        }
        if(!$this->is_array_empty($n))
          $temp[] = $n;
      }
      if (isset($temp) && count($temp) > 0 && !$this->is_array_empty($temp)){
        $this->_saved[$field['id']] = $temp;
      }else{
        if (isset($this->_saved[$field['id']]))
          unset($this->_saved[$field['id']]);
      }
    }else{
      //  remove old meta if exists
      if (isset($this->_saved[$field['id']]))
        unset($this->_saved[$field['id']]);
    }
  }
  
    
    
  /**
   * Add missed values for Page.
   *
   * @since 0.1
   * @access public
   */
  public function add_missed_values() {
    
    // Default values for admin 
    //$this->_meta_box = array_merge( array( 'context' => 'normal', 'priority' => 'high', 'pages' => array( 'post' ) ), $this->_meta_box );

    // Default values for fields
    foreach ( $this->_fields as &$field ) {
      
      $multiple = in_array( $field['type'], array( 'checkbox_list', 'file', 'image' ) );
      $std = $multiple ? array() : '';
      $format = 'date' == $field['type'] ? 'yy-mm-dd' : ( 'time' == $field['type'] ? 'hh:mm' : '' );

      $field = array_merge( array( 'multiple' => $multiple, 'std' => $std, 'desc' => '', 'format' => $format, 'validate_func' => '' ), $field );
    
    } // End foreach
    
  }

  /**
   * Check if field with $type exists.
   *
   * @param string $type 
   * @since 0.1
   * @access public
   */
  public function has_field( $type ) {
    //faster search in single array.
    if (count($this->field_types) > 0){
      return in_array($type, $this->field_types);
    }

    //run once over all fields and store the types in a local array
    $temp = array();
    foreach ($this->_fields as $field) {
      $temp[] = $field['type'];
      if ('repeater' == $field['type']  || 'cond' == $field['type']){
        foreach((array)$field["fields"] as $repeater_field) {
          $temp[] = $repeater_field["type"];  
        }
      }
    }

    //remove duplicates
    $this->field_types = array_unique($temp);
    //call this function one more time now that we have an array of field types
    return $this->has_field($type);
  }

  /**
   * Check if any of the fields types exists
   * 
   * @since 1.1.3
   * @access public
   * @param  array  $types array of field types
   * @return boolean  
   */
  public function has_field_any($types){
    foreach ((array)$types as $t) {
      if ($this->has_field($t))
        return true;
    }
    return false;
  }

  /**
   * Check if current page is edit page.
   *
   * @since 0.1
   * @access public
   */
  public function is_edit_page() {
    //global $pagenow;
    return true;
    //return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
  }
  
  /**
   * Fixes the odd indexing of multiple file uploads.
   *
   * Goes from the format: 
   * $_FILES['field']['key']['index']
   * to
   * The More standard and appropriate:
   * $_FILES['field']['index']['key']
   *
   * @param string $files 
   * @since 0.1
   * @access public
   */
  public function fix_file_array( &$files ) {
    
    $output = array();
    
    foreach ( $files as $key => $list ) {
      foreach ( $list as $index => $value ) {
        $output[$index][$key] = $value;
      }
    }
    
    return $files = $output;
  
  }

  /**
   * Get proper JQuery UI version.
   *
   * Used in order to not conflict with WP Admin Scripts.
   *
   * @since 0.1
   * @access public
   */
  public function get_jqueryui_ver() {
    
    global $wp_version;
    
    if ( version_compare( $wp_version, '3.1', '>=') ) {
      return '1.8.10';
    }
    
    return '1.7.3';
  
  }
  
  /**
   *  Add Field to page (generic function)
   *  @author Ohad Raz
   *  @since 0.1
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   */
  public function addField($id,$args){
    $new_field = array('id'=> $id,'std' => '','desc' => '','style' =>'');
    $new_field = array_merge($new_field, $args);
    $this->_fields[] = $new_field;
  }

  /**
   * Add typography Field 
   * 
   * @author Ohad    Raz
   * @since 0.3
   * 
   * @access public
   * 
   * @param  $id string  id of the field
   * @param  $args mixed|array
   * @param  boolean $repeater=false 
   */
  public function addTypo($id,$args,$repeater=false){
    $new_field = array(
      'type' => 'typo', 
      'id'=> $id,
      'std' => array(
        'size' => '12px', 
        'color' => '#000000',
        'face' => 'arial', 
        'style' => 'normal',
        'weight' => 'normal'
      ),
      'desc' => '',
      'style' =>'',
      'name'=> 'Typography field'
    );
    $new_field = array_merge($new_field, $args);
    $this->_fields[] = $new_field;
  }
  
  /**
   *  Add Text Field to Page
   *  @author Ohad Raz
   *  @since 0.1
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'style' =>   // custom style for field, string optional
   *    'validate_func' => // validate function, string optional
   *   @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addText($id,$args,$repeater=false){
    $new_field = array('type' => 'text','id'=> $id,'std' => '','desc' => '','style' =>'','name' => 'Text Field');
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }

  /**
   *  Add Pluploader Field to Page
   *  @author Ohad Raz
   *  @since 0.9.7
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'style' =>   // custom style for field, string optional
   *    'validate_func' => // validate function, string optional
   *   @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addPlupload($id,$args,$repeater=false){
    $new_field = array('type' => 'plupload','id'=> $id,'std' => '','desc' => '','style' =>'','name' => 'PlUpload Field','width' => null, 'height' => null,'multiple' => false);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }

  /**
   *  Add Hidden Field to Page
   *  @author Ohad Raz
   *  @since 0.1
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'style' =>   // custom style for field, string optional
   *    'validate_func' => // validate function, string optional
   *   @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addHidden($id,$args,$repeater=false){
    $new_field = array('type' => 'hidden','id'=> $id,'std' => '','desc' => '','style' =>'','name' => '');
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }

  /**
   *  Add code Editor to page
   *  @author Ohad Raz
   *  @since 0.1
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'style' =>   // custom style for field, string optional
   *    'syntax' =>   // syntax language to use in editor (php,javascript,css,html)
   *    'validate_func' => // validate function, string optional
   *   @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addCode($id,$args,$repeater=false){
    $new_field = array('type' => 'code','id'=> $id,'std' => '','desc' => '','style' =>'','name' => 'Code Editor Field','syntax' => 'php', 'theme' => 'default');
    $new_field = array_merge($new_field, (array)$args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add Paragraph to Page
   *  @author Ohad Raz
   *  @since 0.1
   *  @access public
   *  
   *  @param $p  paragraph html
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addParagraph($p,$repeater=false){
    $new_field = array('type' => 'paragraph','id'=> '','value' => $p);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
    
  /**
   *  Add Checkbox Field to Page
   *  @author Ohad Raz
   *  @since 0.1
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addCheckbox($id,$args,$repeater=false){
    $new_field = array('type' => 'checkbox','id'=> $id,'std' => '','desc' => '','style' =>'','name' => 'Checkbox Field');
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }

  /**
   *  Add Checkbox conditional Field to Page
   *  @author Ohad Raz
   *  @since 0.5
   *  @access public
   *  @param $id string  field id, i.e. the key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional
   *    'fields' => list of fields to show conditionally.
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addCondition($id,$args,$repeater=false){
    $new_field = array('type' => 'cond','id'=> $id,'std' => '','desc' => '','style' =>'','name' => 'Conditional Field','fields' => array());
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }

  /**
   *  Add CheckboxList Field to Page
   *  @author Ohad Raz
   *  @since 0.1
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $options (array)  array of key => value pairs for select options
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default)
   *  
   *   @return : remember to call: $checkbox_list = get_post_meta(get_the_ID(), 'meta_name', false); 
   *   which means the last param as false to get the values in an array
   */
  public function addCheckboxList($id,$options=array(),$args,$repeater=false){
    $new_field = array('type' => 'checkbox_list','id'=> $id,'std' => '','desc' => '','style' =>'','name' => 'Checkbox List Field','options' => $options, 'class' => '');
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add Textarea Field to Page
   *  @author Ohad Raz
   *  @since 0.1
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'style' =>   // custom style for field, string optional
   *    'validate_func' => // validate function, string optional
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addTextarea($id,$args,$repeater=false){
    $new_field = array('type' => 'textarea','id'=> $id,'std' => '','desc' => '','style' =>'','name' => 'Textarea Field');
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add Select Field to Page
   *  @author Ohad Raz
   *  @since 0.1
   *  @access public
   *  @param $id string field id, i.e. the meta key
   *  @param $options (array)  array of key => value pairs for select options  
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, (array) optional
   *    'multiple' => // select multiple values, optional. Default is false.
   *    'validate_func' => // validate function, string optional
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addSelect($id,$options,$args,$repeater=false){
    $new_field = array('type' => 'select','id'=> $id,'std' => array(),'desc' => '','style' =>'','name' => 'Select Field','multiple' => false,'options' => $options);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }

  /**
   *  Add Sortable Field to Page
   *  @author Ohad Raz
   *  @since 0.4
   *  @access public
   *  @param $id string field id, i.e. the meta key
   *  @param $options (array)  array of key => value pairs for sortable options  as value => label
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, (array) optional
   *    'validate_func' => // validate function, string optional
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addSortable($id,$options,$args,$repeater=false){
    $new_field = array('type' => 'sortable','id'=> $id,'std' => array(),'desc' => '','style' =>'','name' => 'Select Field','multiple' => false,'options' => $options);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  
  /**
   *  Add Radio Field to Page
   *  @author Ohad Raz
   *  @since 0.1
   *  @access public
   *  @param $id string field id, i.e. the meta key
   *  @param $options (array)  array of key => value pairs for radio options
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional 
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default)
   */
  public function addRadio($id,$options,$args,$repeater=false){
    $new_field = array('type' => 'radio','id'=> $id,'std' => array(),'desc' => '','style' =>'','name' => 'Radio Field','options' => $options,'multiple' => false);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }

  /**
   *  Add Date Field to Page
   *  @author Ohad Raz
   *  @since 0.1
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional
   *    'format' => // date format, default yy-mm-dd. Optional. Default "'d MM, yy'"  See more formats here: http://goo.gl/Wcwxn
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addDate($id,$args,$repeater=false){
    $new_field = array('type' => 'date','id'=> $id,'std' => '','desc' => '','format'=>'d MM, yy','name' => 'Date Field');
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }

  /**
   *  Add Time Field to Page
   *  @author Ohad Raz
   *  @since 0.1
   *  @access public
   *  @param $id string- field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional
   *    'format' => // time format, default hh:mm. Optional. See more formats here: http://goo.gl/83woX
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addTime($id,$args,$repeater=false){
    $new_field = array('type' => 'time','id'=> $id,'std' => '','desc' => '','format'=>'hh:mm','name' => 'Time Field');
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add Color Field to Page
   *  @author Ohad Raz
   *  @since 0.1
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addColor($id,$args,$repeater=false){
    $new_field = array('type' => 'color','id'=> $id,'std' => '','desc' => '','name' => 'ColorPicker Field');
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add Image Field to Page
   *  @author Ohad Raz
   *  @since 0.1
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'validate_func' => // validate function, string optional
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default) 
   */
  public function addImage($id,$args,$repeater=false){
    $new_field = array('type' => 'image','id'=> $id,'desc' => '','name' => 'Image Field');
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }

  
  /**
   *  Add WYSIWYG Field to Page
   *  @author Ohad Raz
   *  @since 0.1
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'style' =>   // custom style for field, string optional Default 'width: 300px; height: 400px'
   *    'validate_func' => // validate function, string optional 
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default)
   */
  public function addWysiwyg($id,$args,$repeater=false){
    $new_field = array('type' => 'wysiwyg','id'=> $id,'std' => '','desc' => '','style' =>'width: 300px; height: 400px','name' => 'WYSIWYG Editor Field');
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add Taxonomy Field to Page
   *  @author Ohad Raz
   *  @since 0.1
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $options mixed|array options of taxonomy field
   *    'taxonomy' =>    // taxonomy name can be category,post_tag or any custom taxonomy default is category
   *    'type' =>  // how to show taxonomy? 'select' (default) or 'checkbox_list'
   *    'args' =>  // arguments to query taxonomy, see http://goo.gl/uAANN default ('hide_empty' => false)  
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional 
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default)
   */
  public function addTaxonomy($id,$options,$args,$repeater=false){
    $temp = array('taxonomy'=> 'category','type' => 'select','args'=> array('hide_empty' => 0));
    $options = array_merge($temp,$options);
    $new_field = array('type' => 'taxonomy','id'=> $id,'desc' => '','name' => 'Taxonomy Field','options'=> $options, 'multiple' => false);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }

  /**
   *  Add WP_Roles Field to Page
   *  @author Ohad Raz
   *  @since 0.1
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $options mixed|array options of taxonomy field
   *    'type' =>  // how to show taxonomy? 'select' (default) or 'checkbox_list'
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional 
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default)
   */
  public function addRoles($id,$options,$args,$repeater=false){
    $options = array_merge(array('type'=>'select'),$options);
    $new_field = array('type' => 'WProle','id'=> $id,'desc' => '','name' => 'WP Roles Field','options'=> $options, 'multiple' => false);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }

  /**
   *  Add posts Field to Page
   *  @author Ohad Raz
   *  @since 0.1
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $options mixed|array options of taxonomy field
   *    'post_type' =>    // post type name, 'post' (default) 'page' or any custom post type
   *    type' =>  // how to show posts? 'select' (default) or 'checkbox_list'
   *    args' =>  // arguments to query posts, see http://goo.gl/is0yK default ('posts_per_page' => -1)  
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'validate_func' => // validate function, string optional 
   *  @param $repeater bool  is this a field inside a repeatr? true|false(default)
   */
  public function addPosts($id,$options,$args,$repeater=false){
    $temp = array('type'=>'select','args'=>array('posts_per_page' => -1,'post_type' =>'post'));
    $options = array_merge($temp,$options);
    $new_field = array('type' => 'posts','id'=> $id,'desc' => '','name' => 'Posts Field','options'=> $options, 'multiple' => false);
    $new_field = array_merge($new_field, $args);
    if(false === $repeater){
      $this->_fields[] = $new_field;
    }else{
      return $new_field;
    }
  }
  
  /**
   *  Add repeater Field Block to Page
   *  @author Ohad Raz
   *  @since 0.1
   *  @access public
   *  @param $id string  field id, i.e. the meta key
   *  @param $args mixed|array
   *    'name' => // field name/label string optional
   *    'desc' => // field description, string optional
   *    'std' => // default value, string optional
   *    'style' =>   // custom style for field, string optional
   *    'validate_func' => // validate function, string optional
   *    'fields' => //fields to repeater  
   *  @modified 0.4 added sortable option
   */
  public function addRepeaterBlock($id,$args){
    $new_field = array('type' => 'repeater','id'=> $id,'name' => 'Reapeater Field','fields' => array(),'inline'=> false, 'sortable' => false);
    $new_field = array_merge($new_field, $args);
    $this->_fields[] = $new_field;
  }
  
  
  /**
   * Finish Declaration of Page
   * @author Ohad Raz
   * @since 0.1
   * @access public
   * @deprecated 1.1.8
   */
  public function Finish() {
    /*$this->add_missed_values();
    $this->check_field_upload();
    $this->check_field_plupload();
    $this->check_field_color();
    $this->check_field_date();
    $this->check_field_time();
    $this->check_field_code();*/
  }
  
  /**
   * Helper function to check for empty arrays
   * @author Ohad Raz
   * @since 0.1
   * @access public
   * @param $args mixed|array
   */
  public function is_array_empty($array){
    if (!is_array($array))
      return true;
    
    foreach ($array as $a){
      if (is_array($a)){
        foreach ($a as $sub_a){
          if (!empty($sub_a) && $sub_a != '')
            return false;
        }
      }else{
        if (!empty($a) && $a != '')
          return false;
      }
    }
    return true;
  }

  /**
   * Get the list of avialable Fonts
   * 
   * @author Ohad   Raz
   * @since 0.3
   * @access public
   * 
   * @return mixed|array
   */
  public function get_fonts_family($font = null) {
    $fonts = get_option('WP_EX_FONTS_LIST', $default = false);
    if ($fonts === false){
      $fonts = array(
          'arial' => array(
            'name' => 'Arial',
            'css' => "font-family: Arial, sans-serif;",
          ),
          'verdana' => array(
            'name' => "Verdana, Geneva",
            'css' => "font-family: Verdana, Geneva;",
        ),
        'trebuchet' => array(
            'name' => "Trebuchet",
            'css' => "font-family: Trebuchet;",
        ),
        'georgia' => array(
            'name' => "Georgia",
            'css' => "font-family: Georgia;",
        ),
        'times' => array(
            'name' => "Times New Roman",
            'css' => "font-family: Times New Roman;",
        ),
        'tahoma' => array(
            'name' => "Tahoma, Geneva",
            'css' => "font-family: Tahoma, Geneva;",
        ),
        'palatino' => array(
            'name' => "Palatino",
            'css' => "font-family: Palatino;",
        ),
        'helvetica' => array(
            'name' => "Verdana, Geneva",
            'css' => "font-family: Helvetica*;",
        ),
      );
      if ($this->google_fonts){
        $api_keys = array(
          'AIzaSyDXgT0NYjLhDmUzdcxC5RITeEDimRmpq3s',
          'AIzaSyD6j7CsUTblh29PAXN3NqxBjnN-5nuuFGU',
          'AIzaSyB8Ua6XIfe-gqbkE8P3XL4spd0x8Ft7eWo',
          'AIzaSyDJYYVPLT9JaoMPF8G5cFm1YjTZMjknizE',
          'AIzaSyDXt6e2t_gCfhlSfY8ShpR9WpqjMsjEimU'
        );
        $k = rand(0,count($api_keys) -1 );
        $gs = wp_remote_get( 'https://www.googleapis.com/webfonts/v1/webfonts?sort=popularity&key='.$api_keys[$k] ,array('sslverify' => false)); 
        if(! is_wp_error( $gs ) ) {
          $fontsSeraliazed = $gs['body'];
          $fontArray = json_decode($gs['body']);
          $fontArray = $fontArray->items;
          foreach ( $fontArray as $f ){
            $key = strtolower(str_replace(" ", "_", $f->family));
            $fonts[$key] = array(
              'name' => $f->family,
              'import' => str_replace(" ","+",$f->family),
              'css' => 'font-family: '.$f->family .';', //@import url(http://fonts.googleapis.com/css?family=
            );
          }
        }
      }
      update_option('WP_EX_FONTS_LIST',$fonts);
    }
    $fonts = apply_filters( 'WP_EX_available_fonts_family', $fonts );
    if ($font === null){
      return $fonts;
    }else{
      foreach ($fonts as $f => $value) {
          if ($f == $font)
            return $value;
      }
    }
  }

  /**
   * Get list of font faces
   * 
   * @author Ohad   Raz
   * @since 0.3
   * @access public
   * 
   * @return array
   */
  public function get_font_style(){
    $default = array(
      'normal' => 'Normal',
      'italic' => 'Italic',
      'oblique ' => 'Oblique'
    );
    return apply_filters( 'BF_available_fonts_style', $default );
  }

  /**
   * Get list of font wieght
   * 
   * @author Ohad   Raz
   * @since 0.9.9
   * @access public
   * 
   * @return array
   */
  public function get_font_weight(){
    $default = array(
      'normal' => 'Normal',
      'bold' => 'Bold',
      'bolder' => 'Bolder',
      'lighter' => 'Lighter',
      '100' => '100',
      '200' => '200',
      '300' => '300',
      '400' => '400',
      '500' => '500',
      '600' => '600',
      '700' => '700',
      '800' => '800',
      '900' => '900',
      'inherit' => 'Inherit'
    );
    return apply_filters( 'BF_available_fonts_weights', $default );
  }

  /**
   *  Export Import Functions
   */

  /**
   *  Add import export to Page
   *  @author Ohad Raz
   *  @since 0.8
   *  @access public
   *  
   *  @return void
   */
  public function addImportExport(){
    $new_field = array('type' => 'import_export','id'=> '','value' => '');
    $this->_fields[] = $new_field;
  }


  public function show_import_export(){
    $this->show_field_begin(array('name' => ''),null);
    $ret ='
    <div class="apc_ie_panel field">
      <div style="padding 10px;" class="apc_export"><h3>'.__('Export','apc').'</h3>
        <p>'. __('To export saved settings click the Export button bellow and you will get the export Code in the box bellow, which you can later use to import.','apc').'</p>
        <div class="export_code">
          <label for="export_code">'. __('Export Code','apc').'</label><br/>
          <textarea id="export_code"></textarea>        
          <input class="button-primary" type="button" value="'. __('Get Export','apc').'" id="apc_export_b" />'.$this->create_export_download_link().'
          <div class="export_status" style="display: none;"><img src="http://i.imgur.com/l4pWs.gif" alt="loading..."/></div>
          <div class="export_results alert" style="display: none;"></div>
        </div>
      </div>
      <div style="padding 10px;" class="apc_import"><h3>'.__('Import','apc').'</h3>
        <p>'. __('To Import saved settings paste the Export output in to the Import Code box bellow and click Import.','apc').'</p>
        <div class="import_code">
          <label for="import_code">'. __('Import Code','apc').'</label><br/>
          <textarea id="import_code"></textarea>
                  <input class="button-primary" type="button"  value="'. __('Import','apc').'" id="apc_import_b" />
          <div class="import_status" style="display: none;"><img src="http://i.imgur.com/l4pWs.gif" alt="loading..."/></div>
          <div class="import_results alert" style="display: none;"></div>
        </div>
      </div>
      <input type="hidden" id="option_group_name" value="'.$this->option_group.'" />
      <input type="hidden" id="apc_import_nonce" name="apc_Import" value="'.wp_create_nonce("apc_import").'" />
      <input type="hidden" id="apc_export_nonce" name="apc_export" value="'.wp_create_nonce("apc_export").'" />
    ';
    echo apply_filters('apc_import_export_panel',$ret);
    $this->show_field_end(array('name' => '','desc' => ''),null);
  }

  /**
   * Ajax export 
   * 
   * @author Ohad   Raz
   * @since 0.8
   * @access public
   * 
   * @return json object
   */
  public function export(){
    check_ajax_referer( 'apc_export', 'seq' );
    if (!isset($_GET['group'])){
      $re['err'] = __('error in ajax request! (1)','apc');
      $re['nonce'] = wp_create_nonce("apc_export");
      echo json_encode($re);
      die();
    }

    $options = get_option($this->option_group,false);
    if ($options !== false)
      $re['code']= "<!*!* START export Code !*!*>\n".base64_encode(serialize($options))."\n<!*!* END export Code !*!*>";
    else
      $re['err'] = __('error in ajax request! (2)','apc');
    
    //update_nonce
    $re['nonce'] = wp_create_nonce("apc_export");
    echo json_encode($re);
    die();

  }

  /**
   * Ajax import 
   * 
   * @author Ohad   Raz
   * @since 0.8
   * @access public
   * 
   * @return json object
   */
  public function import(){
    check_ajax_referer( 'apc_import', 'seq' );
    if (!isset($_POST['imp'])){
      $re['err'] = __('error in ajax request! (3)','apc');
      $re['nonce'] = wp_create_nonce("apc_import");
      echo json_encode($re);
      die();
    }
    $import_code = $_POST['imp'];
    $import_code = str_replace("<!*!* START export Code !*!*>\n","",$import_code);
    $import_code = str_replace("\n<!*!* END export Code !*!*>","",$import_code);
    $import_code = base64_decode($import_code);
    $import_code = unserialize($import_code);
    if (is_array($import_code)){
      update_option($this->option_group,$import_code);
      $re['success']= __('Setting imported, make sure you ','apc'). '<input class="button-primary" type="button"  value="'. __('Refresh this page','apc').'" id="apc_refresh_page_b" />';
    }else{
      $re['err'] = __('Could not import settings! (4)','apc');
    }
    //update_nonce
      $re['nonce'] = wp_create_nonce("apc_import");
    echo json_encode($re);
    die();
  }


  //then define the function that will take care of the actual download
  public function download_file($content = null, $file_name = null){
    if (! wp_verify_nonce($_REQUEST['nonce'], 'theme_export_options') ) 
        wp_die('Security check'); 

    //here you get the options to export and set it as content, ex:
    $options= get_option($_REQUEST['option_group']);
    $content = "<!*!* START export Code !*!*>\n".base64_encode(serialize($options))."\n<!*!* END export Code !*!*>";
    $file_name = 'theme_export.txt';
    header('HTTP/1.1 200 OK');

    if ( !current_user_can('edit_themes') )
        wp_die('<p>'.__('You do not have sufficient permissions to edit templates for this site.','apc').'</p>');
    
    if ($content === null || $file_name === null){
        wp_die('<p>'.__('Error Downloading file.','apc').'</p>');     
    }
    $fsize = strlen($content);
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-Description: File Transfer');
    header("Content-Disposition: attachment; filename=" . $file_name);
    header("Content-Length: ".$fsize);
    header("Expires: 0");
    header("Pragma: public");
    echo $content;
    exit;
  }

  public function create_export_download_link($echo = false){
    $site_url = get_bloginfo('url');
    $args = array(
        'theme_export_options' => 'safe_download',
        'nonce' => wp_create_nonce('theme_export_options'),
        'option_group' => $this->option_group
    );
    $export_url = add_query_arg($args, $site_url);
    if ($echo === true)
        echo '<a href="'.$export_url.'" target="_blank">'.__('Download Export','apc').'</a>';
    elseif ($echo == 'url')
        return $export_url;
    return '<a class="button-primary" href="'.$export_url.'" target="_blank">'.__('Download Export','apc').'</a>';
  }

  //first  add a new query var
  public function add_query_var_vars() {
      global $wp;
      $wp->add_query_var('theme_export_options');
  }

  //then add a template redirect which looks for that query var and if found calls the download function
  public function admin_redirect_download_files(){
      global $wp;
      global $wp_query;
      //download theme export
      if (array_key_exists('theme_export_options', $wp->query_vars) && $wp->query_vars['theme_export_options'] == 'safe_download' && $this->option_group == $_REQUEST['option_group'] ){
          $this->download_file();
          die();
      }
  }

  public function Handle_plupload_action(){
    // check ajax noonce
    $imgid = $_POST["imgid"];
    check_ajax_referer($imgid . 'pluploadan');
 
    // handle file upload
    $status = wp_handle_upload($_FILES[$imgid . 'async-upload'], array('test_form' => true, 'action' => 'plupload_action'));
 
    // send the uploaded file url in response
    echo $status['url'];
    exit;
  }

  /**
   * load_textdomain 
   * @author Ohad Raz
   * @since 1.0.9
   * @return void
   */
  public function load_textdomain(){
    //In themes/plugins/mu-plugins directory
    load_textdomain( 'apc', dirname(__FILE__) . '/lang/' . get_locale() .'.mo' );
  }

  /**
   * Validation functions 
   */
  
  /**
   * validate field 
   * @access public
   * @author Ohad Raz <admin@bainternet.info>
   * @since 1.1.9
   * @param  array $field field data
   * @param  mixed $meta  value to validate
   * @return boolean
   */
  public function validate_field($field,$meta){
    if (!isset($field['validate']) || !is_array($field['validate'] ))
      return true;

    $ret = true;
    foreach ($field['validate'] as $type => $args) {
      if (method_exists($this,'is_' . $type)){  
        if (call_user_func ( array( $this, 'is_' . $type ), $meta ,$args['param']) === false){
          $this->errors_flag = true;
          $this->errors[$field['id']]['name'] = $field['name'];
          $this->errors[$field['id']]['m'][] = (isset($args['message'])? $args['message'] : __('Not Valid ','apc') . $type);
          $ret = false;
        }
      }
    }
    return $ret;
  }

  /**
   * displayErrors function to print out validation errors.
   * @access public
   * @author Ohad Raz <admin@bainternet.info>
   * @since 1.1.9
   * @return void
   */
  public function displayErrors(){
    if ($this->errors_flag){
      echo '<div class="alert alert-error"><button data-dismiss="alert" class="close" type="button">×</button>';
      echo '<h4>'.__('Errors in saving changes', 'apc').'</h4>';
      foreach ($this->errors as $id => $arr) {
        echo "<strong>{$arr['name']}</strong>: ";
        foreach ($arr['m'] as $m) {
          echo "<br />&nbsp;&nbsp;&nbsp;&nbsp;{$m}";
        }
        echo '<br />';
      }
      echo '</div>';
    }
  }

  /**
   * getFieldErrors return field errors
   * @access public
   * @author Ohad Raz <admin@bainternet.info>
   * @since 1.1.9
   * @param  string $field_id 
   * @return array
   */
  public function getFieldErrors($field_id){
    if ($this->errors_flag){
      if (isset($this->errors[$field_id]))
        return $this->errors[$field_id];
    }
    return __('Unkown Error','apc');
  }

  /**
   * has_error check if a field has errors
   * @access public
   * @author Ohad Raz <admin@bainternet.info>
   * @since 1.1.9
   * @param  string  $field_id field ID
   * @return boolean
   */
  public function has_error($field_id){
    //exit if not saved or no validation errors
    if (!$this->saved_flag || !$this->errors_flag)
      return false;
    //check if this field has validation errors
    if (isset($this->errors[$field_id]))
      return true;
    return false;
  }

  /**
   * valid email
   * @access public
   * @author Ohad Raz <admin@bainternet.info>
   * @since 1.1.9
   * @param   string
   * @return  boolean 
   */
  public function is_email($val){
    return (bool)(preg_match("/^([a-z0-9+_-]+)(.[a-z0-9+_-]+)*@([a-z0-9-]+.)+[a-z]{2,6}$/ix",$val));
  }

  /**
   * check a number optional -,+,. values
   * @access public
   * @author Ohad Raz <admin@bainternet.info>
   * @since 1.1.9
   * @param   string
   * @return  boolean
   */
  public function is_numeric($val){
    return (bool)preg_match('/^[-+]?[0-9]*.?[0-9]+$/', (int)$val);
  }

  /**
   * check given number below value
   * @access public
   * @author Ohad Raz <admin@bainternet.info>
   * @since 1.1.9
   * @param   string
   * @return  boolean
   */
  public function is_minvalue($number,$max){
    return (bool)((int)$number > (int)$max);
  }

  /**
   * check given number exceeds max values
   * @access public
   * @author Ohad Raz <admin@bainternet.info>
   * @since 1.1.9
   * @param   string
   * @return  boolean
   */
  public function is_maxvalue($number,$max){
    return ((int)$number < (int)$max);
  }

  /**
   * Check the string length has minimum length
   * @access public
   * @author Ohad Raz <admin@bainternet.info>
   * @since 1.1.9
   * @param   string
   * @return  boolean
   */
  public function is_minlength($val, $min){
    return (strlen($val) >= (int)$min);
  }

  /**
   * check string length exceeds maximum length
   * @access public
   * @author Ohad Raz <admin@bainternet.info>
   * @since 1.1.9
   * @param   string
   * @return  boolean
   */
  public function is_maxlength($val, $max){
    return (strlen($val) <= (int)$max);
  }

  /**
   * check for exactly length of string
   * @access public
   * @author Ohad Raz <admin@bainternet.info>
   * @since 1.1.9
   * @param   string
   * @return  boolean
   */
  public function is_length($val, $length){
    return (strlen($val) == (int)$length);
  }

  /**
   * Valid URL or web address
   * @access public
   * @author Ohad Raz <admin@bainternet.info>
   * @since 1.1.9
   * @param   string
   * @return  boolean
   */
  public function is_url($val){
    return  (bool)preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $val);
  }

  /**
   * Matches alpha and numbers only
   * @access public
   * @author Ohad Raz <admin@bainternet.info>
   * @since 1.1.9
   * @param   string
   * @return  boolean
   */
  public function is_alphanumeric($val){
    return (bool)preg_match("/^([a-zA-Z0-9])+$/i", $val);
  }


} // End Class

endif; // End Check Class Exists