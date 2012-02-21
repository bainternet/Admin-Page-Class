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
 * @version 0.2
 * @copyright 2012 
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
	     * @access private
	     * @var array
	     * @since 0.1
	     */
	    private $_fields;
	    
	    /**
	     * True if the table is opened, false if it is not opened
	     * 
	     * @access private
	     * @var boolean
	     * @since 0.1
	     */
	    private $table = false;
		
		/**
	     * True if the tab div is opened, false if it is not opened
	     * 
	     * @access private
	     * @var boolean
	     * @since 0.1
	     */
	    private $tab_div = false;
	    
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
		 * Builds a new Page 
		 * @param $args (string|mixed array) - 
		 *
		 * Possible keys within $args:
		 *  > menu (array|string) - (string) -> this the name of the parent Top-Level-Menu or a TopPage object to create 
		 *									    this page as a sub menu to.
		 *							(array)  -> top - Slug for the New Top level Menu page to create.
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
			// If we are not in admin area exit.
			if ( ! is_admin() )
				return;

			//set defualts
			$this->_div_or_row = true;
			//store args
			$this->args = $args;
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
								$this->Top_Slug = $menu;
							}
					}
				}
				add_action('admin_menu', array($this, 'AddMenuSubPage'));
			}else{
				//top page
				$this->Top_Slug = $args['menu']['top'];
				add_action('admin_menu', array($this, 'AddMenuTopPage'));
			}
			if(is_array($args)) {
				if (isset($args['option_group'])){
					$this->option_group = $args['option_group'];
				}
				$this->args = $args;
			} else {
				$array['page_title'] = $args;
				$this->args = $array;
			}

			// Assign page values to local variables and add it's missed values.
			$this->_Page_Config = $args;
			$this->_fields = &$this->_Page_Config['fields'];
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
			}
			else{
				$this->SelfPath = plugins_url( 'admin-page-class', plugin_basename( dirname( __FILE__ ) ) );
			}

			// Load common js, css files
			// Must enqueue for all pages as we need js for the media upload, too.
			
			
			//add_action('admin_head', array($this, 'loadScripts'));
			add_filter('gettext',array($this,'edit_insert_to_post_text'));
			// Delete file via Ajax
			add_action( 'wp_ajax_apc_delete_mupload', array( $this, 'wp_ajax_delete_image' ) );
			
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
				'id' => 'id',
				'icon_url' => '',
				'position' => null
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
			//scripts and styles
			add_action( 'admin_print_styles', array( &$this, 'load_scripts_styles' ) );
			//panel script
			add_action('admin_footer-' . $page, array($this,'panel_script'));
			//add mising scripts
			add_action('admin_enqueue_scripts',array($this,'Finish'));
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
				jQuery(document).ready(function() {	
					//hide all
					jQuery(".setingstab").hide();
			
					//set first_tab as active
					jQuery(".panel_menu li:first").addClass("active_tab");
					var tab  = jQuery(".panel_menu li:first a").attr("href");
					jQuery(tab).show();
			
					//bind click on menu action to show the right tab.
					jQuery(".panel_menu li").bind("click", function(event){
						event.preventDefault()
						if (!jQuery(this).hasClass("active_tab")){
							//hide all
							jQuery(".setingstab").hide("slow");
							jQuery(".panel_menu li").removeClass("active_tab");
							tab  = jQuery(this).find("a").attr("href");
							jQuery(this).addClass("active_tab");
							jQuery(tab).show("fast");
						}
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
					
					//delete Image button
					//jQuery(".apc_delete_image_button").click(function(e){
						
					//});


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
		 * @author	ohad raz
		 * @since 0.1
		 * @param  string $input insert to post text
		 * @return string
		 */
		public function edit_insert_to_post_text( $input ) {

			if( is_admin() && 'Insert into Post' == $input && isset($_GET['apc']) && 'insert_file' == $_GET['apc'] )
				return 'Select Image';

			return $input;
		}

		/* print out panel Style
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
	    	echo '<div class="wrap">';
	    	echo '<form method="post" action="" enctype="multipart/form-data">
			    <div class="header_wrap">
					<div style="float:left">';
					echo '<h2>'.$this->args['page_title'].'</h2>'.((isset($this->args['page_header_text']))? $this->args['page_header_text'] : '').' 
					</div>
					<div style="float:right;margin:32px 0 0 0">
						<input type="submit" style="margin-left: 25px;" value="Save changes" name="Submit" class="btn-info btn"><br><br>
					</div>
				<br style="clear:both"><br>
				</div>';
	    	wp_nonce_field( basename(__FILE__), 'BF_Admin_Page_Class_nonce' );

		    if(isset($_POST['action']) && $_POST['action'] == 'save') {
	    	    echo '<div class="alert alert-success"><p><strong>'.__('Settings saved.').'</strong></p></div>';
	    	    $this->save();
	      	}
	      	
	        $saved = get_option($this->option_group);
	        
	        $skip = array('title','paragraph','subtitle','TABS','CloseDiv','TABS_Listing','OpenTab','custom');
	      
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
					if ($this->_div_or_row){
						//echo '<tr valign="top">';
						//echo '<td width="25%"><label for="'.$field['id'].'">'.$field['name'].':</label></td>';
					}else{
						//echo '<div class="f_row">';
						//echo '<label for="'.$field['id'].'">'.$field['name'].':</label></div>';
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
		    	if (isset($field['standard']) && $data === '')
		      		$data = $field['standard'];


	        	if (method_exists(&$this,'show_field_' . $field['type'])){
	        		if ($this->_div_or_row){echo '<td>';}else{echo '<div class="field">';}
	        		call_user_func ( array( &$this, 'show_field_' . $field['type'] ), $field, $data );
	        		if ($this->_div_or_row){echo '</td>';}else{echo '</div>';}
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
								echo '<li><a class="nav_tab_link" href="#'.$id.'">'.$name.'</a></li>';
			            	}
							echo '</ul></div><div class="sections">';
			                break;
						case 'OpenTab':
							$this->tab_div = true;
			                echo '<div class="setingstab" id="'.$field['id'].'">';
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
				            $this->output_repeater_fields($field,$data);
				            break;
			        }
	        	}
	        	if (!in_array($field['type'],$skip)){ echo '</tr>';}
	     	}
			if($this->table) echo '</table>';
			if($this->tab_div) echo '</div>';
			echo '</div><div style="clear:both"></div><div class="footer_wrap">
					<div style="float:right;margin:32px 0 0 0">
						<input type="submit" style="margin-left: 25px;" name="Submit" class="btn-info btn" value="'.esc_attr(__('Save Changes')).'" />
						<br><br>
					</div>
					<br style="clear:both"><br>
				</div>';
			echo '<input type="hidden" name="action" value="save" />';
			echo '</form></div></div>';
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
			$args['standard'] = '';
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
			$args['id'] = 'CloseDiv';
			$args['standard'] = '';
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
	      $args['id'] = 'TABS_Listing';
	      $args['standard'] = '';
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
	      $args['id'] = $name;
	      $args['standard'] = '';
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
			$args['id'] = 'CloseDiv';
			$args['standard'] = '';
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
	    	    'standard' => '',
	    		'id' => ''
	   		);
		    $args = array_merge($default, $args);
		    $this->buildOptions($args);
		    $this->_fields[] = $args;
	    }

	    /**
	     * Builds all the options with their standard values
	     * 
	     * @access public
	     * @param $args (mixed|array) contains everything needed to build the field
	     * @since 0.1
	     * @access private
	     */
	    private function buildOptions($args) {
	    	$default = array(
	        	'standard' => '',
	    		'id' => ''
	      	);
	      	$args = array_merge($default, $args);
	      	$saved = get_option($this->option_group);
	    	if (isset($saved[$args['id']])){
	    		if($saved[$args['id']] === false) {
	      			$saved[$args['id']] = $args['standard'];
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
			$args['type'] = 'title';
			$args['standard'] = '';
			$args['label'] = $label;
			$args['id'] = 'title'.$label;
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
			$args['type'] = 'subtitle';
			$args['label'] = $label;
			$args['id'] = 'title'.$label;
			$args['standard'] = '';
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
			$args['id'] = 'paragraph';
			$args['standard'] = '';
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
		
		$this->check_field_upload();
		$this->check_field_color();
		$this->check_field_date();
		$this->check_field_time();
		$this->check_field_code();

		// @TODO only load styles and js when needed
			wp_enqueue_script('common');
			//wp_enqueue_script('jquery-color');
			//wp_admin_css('thickbox');
			//wp_print_scripts('post');
			//wp_print_scripts('media-upload');
			//wp_print_scripts('jquery');
			//wp_print_scripts('jquery-ui-core');
			if ($this->has_Field('TABS')){
				wp_print_scripts('jquery-ui-tabs');
			}
			if ($this->has_Field('editor')){
				global $wp_version;
				if ( version_compare( $wp_version, '3.2.1' ) < 1 ) {
					wp_print_scripts('tiny_mce');
					wp_print_scripts('editor');
					wp_print_scripts('editor-functions');
				}
			}
			
			wp_enqueue_script('utils');
			// Enqueue admin page Style
			wp_enqueue_style( 'Admin_Page_Class', $plugin_path . '/css/Admin_Page_Class.css' );
			wp_enqueue_style('iphone_checkbox',$plugin_path. '/js/iphone-style-checkboxes/style.css');
			
			// Enqueue admin page Scripts
			wp_enqueue_script( 'Admin_Page_Class', $plugin_path . '/js/Admin_Page_Class.js', array( 'jquery' ), null, true );
			wp_enqueue_script('iphone_checkbox',$plugin_path. '/js/iphone-style-checkboxes/iphone-style-checkboxes.js',array('jquery'),null,true);
		
		
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
	 * Check the Field Upload, Add needed Actions
	 *
	 * @since 0.1
	 * @access public
	 */
	public function check_field_upload() {
		
		// Check if the field is an image or file. If not, return.
		if ( ! $this->has_field( 'image' ) && ! $this->has_field( 'file' ) )
			return;
		
		// Add data encoding type for file uploading.	
		add_action( 'post_edit_form_tag', array( &$this, 'add_enctype' ) );
		
		// Make upload feature work event when custom post type doesn't support 'editor'
		wp_enqueue_script( 'media-upload' );
		add_thickbox();
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		
		// Add filters for media upload.
		add_filter( 'media_upload_gallery', array( &$this, 'insert_images' ) );
		add_filter( 'media_upload_library', array( &$this, 'insert_images' ) );
		add_filter( 'media_upload_image', 	array( &$this, 'insert_images' ) );
		
		// Delete all attachments when delete custom post type.
		add_action( 'wp_ajax_at_delete_file', 		array( &$this, 'delete_file' ) );
		add_action( 'wp_ajax_at_reorder_images', 	array( &$this, 'reorder_images' ) );
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
				
			$li 	 = "<li id='item_{$attachment_id}'>";
			$li 	.= "<img src='{$attachment['url']}' alt='image_{$attachment_id}' />";
			//$li 	.= "<a title='" . __( 'Delete this image' ) . "' class='at-delete-file' href='#' rel='{$nonce}|{$post_id}|{$id}|{$attachment_id}'>" . __( 'Delete' ) . "</a>";
			$li 	.= "<a title='" . __( 'Delete this image' ) . "' class='at-delete-file' href='#' rel='{$nonce}|{$post_id}|{$id}|{$attachment_id}'><img src='" . $this->SelfPath. "/images/delete-16.png' alt='" . __( 'Delete' ) . "' /></a>";
			$li 	.= "<input type='hidden' name='{$id}[]' value='{$attachment_id}' />";
			$li 	.= "</li>";
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
		if (strpos($field_id, '[') === false){
			check_admin_referer( "at-delete-mupload_".urldecode($field_id));
			$temp = get_option($this->args['option_group']);
			unset($temp[$field_id]);
			update_option($this->args['option_group'],$temp);
			$ok =  wp_delete_attachment( $attachment_id );
		}else{
			$f = explode('[',urldecode($field_id));
			$f_fiexed = array();
			foreach ($f as $k => $v){
				$f[$k] = str_replace(']','',$v);
			}
			$temp = get_option($this->args['option_group']);
			$saved = $temp[$f[0]];
			if (isset($saved[$f[1]][$f[2]])){
				unset($saved[$f[1]][$f[2]]);
				$temp[$f[0]] = $saved;
				update_option($this->args['option_group'],$temp);
				$ok = wp_delete_attachment( $attachment_id );
			}
		}

		if ( $ok ){
			echo json_encode( array('status' => 'success' ));
			die();
		}else{
			echo json_encode(array('message' => __( 'Cannot delete file. Something\'s wrong.')));
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
		
		if ( $this->has_field( 'color' ) && $this->is_edit_page() ) {
			// Enqueu built-in script and style for color picker.
			wp_enqueue_style( 'farbtastic' );
			wp_enqueue_script( 'farbtastic' );
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
			// Enqueu JQuery UI, use proper version.
			wp_enqueue_style( 'at-jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/' . $this->get_jqueryui_ver() . '/themes/base/jquery-ui.css' );
			wp_enqueue_script( 'at-jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/' . $this->get_jqueryui_ver() . '/jquery-ui.min.js', array( 'jquery' ) );
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
			// Enqueu JQuery UI, use proper version.
			wp_enqueue_style( 'at-jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/' . $this->get_jqueryui_ver() . '/themes/base/jquery-ui.css' );
			wp_enqueue_script( 'at-jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/' . $this->get_jqueryui_ver() . '/jquery-ui.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'at-timepicker', $plugin_path . '/js/time-and-date/jquery-ui-timepicker-addon.js', array( 'jquery' ), null, true );
		
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
			add_meta_box( $this->_meta_box['id'], $this->_meta_box['title'], array( &$this, 'show' ), $page, $this->_meta_box['context'], $this->_meta_box['priority'] );
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
			call_user_func ( array( &$this, 'show_field_' . $field['type'] ), $field, $meta );
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
	 * @access public
	 */
	public function show_field_repeater( $field, $meta ) {
		// Get Plugin Path
		$plugin_path = $this->SelfPath;
		$this->show_field_begin( $field, $meta );
		echo "<div class='at-repeat' id='{$field['id']}'>";
		
		$c = 0;

		$meta = isset($this->_saved[$field['id']])? $this->_saved[$field['id']]: '';
		
    	if (count($meta) > 0 && is_array($meta) ){
   			foreach ($meta as $me){
   				//for labling toggles
   				$mmm =  $me[$field['fields'][0]['id']];
   				echo '<div class="at-repater-block">'.$mmm.'<br/><table class="repeater-table" style="display: none;">';
   				if ($field['inline']){
   					echo '<tr class="at-inline" VALIGN="top">';
   				}
				foreach ($field['fields'] as $f){
					//reset var $id for repeater
					$id = '';
					$id = $field['id'].'['.$c.']['.$f['id'].']';
					$m = $me[$f['id']];
					$m = ( $m !== '' ) ? $m : $f['std'];
					if ('image' != $f['type'] && $f['type'] != 'repeater')
						$m = is_array( $m) ? array_map( 'esc_attr', $m ) : esc_attr( $m);
					//set new id for field in array format
					$f['id'] = $id;
					if (!$field['inline']){
						echo '<tr>';
					} 
					call_user_func ( array( &$this, 'show_field_' . $f['type'] ), $f, $m);
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
				echo '" alt="'.__('Remove').'" title="'.__('Remove').'" id="remove-'.$field['id'].'"></div>';
				$c = $c + 1;
				
    		}
    	}

		echo '<img src="';
		if ($this->_Local_images){
			echo $plugin_path.'/images/add.png';
		}else{
			echo 'http://i.imgur.com/w5Tuc.png';
		}
		echo '" alt="'.__('Add').'" title="'.__('Add').'" id="add-'.$field['id'].'"><br/></div>';
		
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
			call_user_func ( array( &$this, 'show_field_' . $f['type'] ), $f, '');
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
		echo '" alt="'.__('Remove').'" title="'.__('Remove').'" id="remove-'.$field['id'].'"></div>';
		$counter = 'countadd_'.$field['id'];
		$js_code = ob_get_clean ();		
		$js_code = str_replace("'","\"",$js_code);
		$js_code = str_replace("CurrentCounter","' + ".$counter." + '",$js_code);
		echo '<script>
				jQuery(document).ready(function() {
					var '.$counter.' = '.$c.';
					jQuery("#add-'.$field['id'].'").live(\'click\', function() {
						'.$counter.' = '.$counter.' + 1;
						jQuery(this).before(\''.$js_code.'\');						
						update_repeater_fields();
					});
        			jQuery("#remove-'.$field['id'].'").live(\'click\', function() {
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
		if (isset($field['group'])){
			if ($field['group'] == "start"){
				echo "<td class='at-field'>";
			}
		}else{
			echo "<td class='at-field'>";
		}
		if ( $field['name'] != '' || $field['name'] != FALSE ) {
			echo "<div class='at-label'>";
				echo "<label for='{$field['id']}'>{$field['name']}</label>";
			echo "</div>";
		}
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
		if (isset($field['group'])){
			if ($group == 'end'){
				if ( $field['desc'] != '' ) {
					echo "<div class='desc-field'>{$field['desc']}</div></td>";
				} else {
					echo "</td>";
				}
			}else {
				if ( $field['desc'] != '' ) {
					echo "<div class='desc-field'>{$field['desc']}</div><br/>";	
				}else{
					echo '<br/>';
				}	
			}		
		}else{
			if ( $field['desc'] != '' ) {
				echo "<div class='desc-field'>{$field['desc']}</div></td>";
			} else {
				echo "</td>";
			}
		}
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
		echo "<input type='text' class='at-text' name='{$field['id']}' id='{$field['id']}' value='{$meta}' size='30' />";
		$this->show_field_end( $field, $meta );
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
		echo "<textarea class='code_text' name='{$field['id']}' id='{$field['id']}' data-lang='{$field['syntax']}' data-theme='{$field['theme']}'>".stripslashes($meta)."</textarea>";
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
		//$this->show_field_begin( $field, $meta );
		echo "<input type='hidden' class='at-text' name='{$field['id']}' id='{$field['id']}' value='{$meta}'/>";
		//$this->show_field_end( $field, $meta );
	}
	
	/**
	 * Show Field Paragraph.
	 *
	 * @param string $field 
	 * @since 0.1
	 * @access public
	 */
	public function show_field_paragraph( $field) {	
		//$this->show_field_begin( $field, $meta );
		echo '<p>'.$field['value'].'</p>';
		//$this->show_field_end( $field, $meta );
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
			echo "<textarea class='at-textarea large-text' name='{$field['id']}' id='{$field['id']}' cols='60' rows='10'>{$meta}</textarea>";
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
			echo "<select class='at-select' name='{$field['id']}" . ( $field['multiple'] ? "[]' id='{$field['id']}' multiple='multiple'" : "'" ) . ">";
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
				echo "<input type='radio' class='at-radio' name='{$field['id']}' value='{$key}'" . checked( in_array( $key, $meta ), true, false ) . " /> <span class='at-radio-label'>{$value}</span>";
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
		echo "<input type='checkbox' class='rw-checkbox' name='{$field['id']}' id='{$field['id']}'" . checked(!empty($meta), true, false) . " /> {$field['desc']}</td>";
			
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
			echo "<textarea class='at-wysiwyg theEditor large-text' name='{$field['id']}' id='{$field['id']}' cols='60' rows='10'>{$meta}</textarea>";
		}else{
			// Use new wp_editor() since WP 3.3
			wp_editor( stripslashes(stripslashes(html_entity_decode($meta))), $field['id'], array( 'editor_class' => 'at-wysiwyg' ) );
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
				echo '<div style="margin-bottom: 10px"><strong>' . __('Uploaded files') . '</strong></div>';
				echo '<ol class="at-upload">';
				foreach ( $meta as $att ) {
					// if (wp_attachment_is_image($att)) continue; // what's image uploader for?
					echo "<li>" . wp_get_attachment_link( $att, '' , false, false, ' ' ) . " (<a class='at-delete-file' href='#' rel='{$nonce}|{$post->ID}|{$field['id']}|{$att}'>" . __( 'Delete' ) . "</a>)</li>";
				}
				echo '</ol>';
			}

			// show form upload
			echo "<div class='at-file-upload-label'>";
				echo "<strong>" . __( 'Upload new files' ) . "</strong>";
			echo "</div>";
			echo "<div class='new-files'>";
				echo "<div class='file-input'>";
					echo "<input type='file' name='{$field['id']}[]' />";
				echo "</div><!-- End .file-input -->";
				echo "<a class='at-add-file button' href='#'>" . __( 'Add more files' ) . "</a>";
			echo "</div><!-- End .new-files -->";
		echo "</td>";
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
		if (is_array($meta)){
			if(isset($meta[0]) && is_array($meta[0]))
			$meta = $meta[0];
		}
		if (is_array($meta) && isset($meta['src']) && $meta['src'] != ''){
			$html .= "<span class='mupload_img_holder'><img src='".$meta['src']."' style='height: 150px;width: 150px;' /></span>";
			$html .= "<input type='hidden' name='".$field['id']."[id]' id='".$field['id']."[id]' value='".$meta['id']."' />";
			$html .= "<input type='hidden' name='".$field['id']."[src]' id='".$field['id']."[src]' value='".$meta['src']."' />";
			$html .= "<input class='at-delete_image_button' type='button' rel='".$field['id']."' value='Delete Image' />";
		}else{
			$html .= "<span class='mupload_img_holder'></span>";
			$html .= "<input type='hidden' name='".$field['id']."[id]' id='".$field['id']."[id]' value='' />";
			$html .= "<input type='hidden' name='".$field['id']."[src]' id='".$field['id']."[src]' value='' />";
			$html .= "<input class='at-upload_image_button' type='button' rel='".$field['id']."' value='Upload Image' />";
		}
		echo $html;
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
			echo "<input class='at-color' type='text' name='{$field['id']}' id='{$field['id']}' value='{$meta}' size='8' />";
		//	echo "<a href='#' class='at-color-select button' rel='{$field['id']}'>" . __( 'Select a color' ) . "</a>";
			echo "<input type='button' class='at-color-select button' rel='{$field['id']}' value='" . __( 'Select a color' ) . "'/>";
			echo "<div style='display:none' class='at-color-picker' rel='{$field['id']}'></div>";
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
				$html[] = "<input type='checkbox' class='at-checkbox_list' name='{$field['id']}[]' value='{$key}'" . checked( in_array( $key, $meta ), true, false ) . " /> {$value}";
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
			echo "<input type='text' class='at-date' name='{$field['id']}' id='{$field['id']}' rel='{$field['format']}' value='{$meta}' size='30' />";
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
			echo "<input type='text' class='at-time' name='{$field['id']}' id='{$field['id']}' rel='{$field['format']}' value='{$meta}' size='30' />";
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
				echo "<input type='checkbox' name='{$field['id']}[]' value='$p->ID'" . checked(in_array($p->ID, $meta), true, false) . " /> $p->post_title<br/>";
			}
		}
		// select
		else {
			echo "<select name='{$field['id']}" . ($field['multiple'] ? "[]' multiple='multiple' style='height:auto'" : "'") . ">";
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
				echo "<input type='checkbox' name='{$field['id']}[]' value='$term->slug'" . checked(in_array($term->slug, $meta), true, false) . " /> $term->name  ";
			}
		}
		// select
		else {
			echo "<select name='{$field['id']}" . ($field['multiple'] ? "[]' multiple='multiple' style='height:auto'" : "'") . ">";
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
					echo "<input type='checkbox' name='{$field['id']}[]' value='$n'" . checked(in_array($n, $meta), true, false) . " /> $n<br/>";
				}
			}
			// select
			else {
				echo "<select name='{$field['id']}" . ($field['multiple'] ? "[]' multiple='multiple' style='height:auto'" : "'") . ">";
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

		$skip = array('title','paragraph','subtitle','TABS','CloseDiv','TABS_Listing','OpenTab');
		
		//check nonce
		if ( ! check_admin_referer( basename( __FILE__ ), 'BF_Admin_Page_Class_nonce') )
			return;
		
		foreach ( $this->_fields as $field ) {
			if(!in_array($field['type'],$skip)){
			
				$name = $field['id'];
				$type = $field['type'];
				$old = isset($saved[$name])? $saved[$name]: NULL;
				$new = ( isset( $_POST[$name] ) ) ? $_POST[$name] : ( ( $field['multiple'] ) ? array() : '' );
							

				// Validate meta value
				if ( class_exists( 'BF_Admin_Page_Class_Validate' ) && method_exists( 'BF_Admin_Page_Class_Validate', $field['validate_func'] ) ) {
					$new = call_user_func( array( 'BF_Admin_Page_Class_Validate', $field['validate_func'] ), $new );
				}
				
				// Call defined method to save meta value, if there's no methods, call common one.
				$save_func = 'save_field_' . $type;
				if ( method_exists( $this, $save_func ) ) {
					call_user_func( array( &$this, 'save_field_' . $type ), $field, $old, $new );
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
		if ( $field['multiple'] ) {
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
			//	remove old meta if exists
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
		foreach ( $this->_fields as $field ) {
			if ( $type == $field['type'] ) 
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
	 *  Add Text Field to Page
	 *  @author Ohad Raz
	 *  @since 0.1
	 *  @access public
	 *  @param $id string  field id, i.e. the meta key
	 *  @param $args mixed|array
	 *  	'name' => // field name/label string optional
	 *  	'desc' => // field description, string optional
	 *  	'std' => // default value, string optional
	 *  	'style' => 	// custom style for field, string optional
	 *  	'validate_func' => // validate function, string optional
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
	 *  Add Hidden Field to Page
	 *  @author Ohad Raz
	 *  @since 0.1
	 *  @access public
	 *  @param $id string  field id, i.e. the meta key
	 *  @param $args mixed|array
	 *  	'name' => // field name/label string optional
	 *  	'desc' => // field description, string optional
	 *  	'std' => // default value, string optional
	 *  	'style' => 	// custom style for field, string optional
	 *  	'validate_func' => // validate function, string optional
	 *   @param $repeater bool  is this a field inside a repeatr? true|false(default) 
	 */
	public function addHidden($id,$args,$repeater=false){
		$new_field = array('type' => 'hidden','id'=> $id,'std' => '','desc' => '','style' =>'','name' => 'Text Field');
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
	 *  	'name' => // field name/label string optional
	 *  	'desc' => // field description, string optional
	 *  	'std' => // default value, string optional
	 *  	'style' => 	// custom style for field, string optional
	 *  	'syntax' => 	// syntax language to use in editor (php,javascript,css,html)
	 *  	'validate_func' => // validate function, string optional
	 *   @param $repeater bool  is this a field inside a repeatr? true|false(default) 
	 */
	public function addCode($id,$args,$repeater=false){
		$new_field = array('type' => 'code','id'=> $id,'std' => '','desc' => '','style' =>'','name' => 'Code Editor Field','syntax' => 'php', 'theme' => 'defualt');
		$new_field = array_merge($new_field, $args);
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
	 *  	'name' => // field name/label string optional
	 *  	'desc' => // field description, string optional
	 *  	'std' => // default value, string optional
	 *  	'validate_func' => // validate function, string optional
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
	 *  Add CheckboxList Field to Page
	 *  @author Ohad Raz
	 *  @since 0.1
	 *  @access public
	 *  @param $id string  field id, i.e. the meta key
	 *  @param $options (array)  array of key => value pairs for select options
	 *  @param $args mixed|array
	 *  	'name' => // field name/label string optional
	 *  	'desc' => // field description, string optional
	 *  	'std' => // default value, string optional
	 *  	'validate_func' => // validate function, string optional
	 *  @param $repeater bool  is this a field inside a repeatr? true|false(default)
	 *  
	 *   @return : remember to call: $checkbox_list = get_post_meta(get_the_ID(), 'meta_name', false); 
	 *   which means the last param as false to get the values in an array
	 */
	public function addCheckboxList($id,$options,$args,$repeater=false){
		$new_field = array('type' => 'checkbox_list','id'=> $id,'std' => '','desc' => '','style' =>'','name' => 'Checkbox List Field');
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
	 *  	'name' => // field name/label string optional
	 *  	'desc' => // field description, string optional
	 *  	'std' => // default value, string optional
	 *  	'style' => 	// custom style for field, string optional
	 *  	'validate_func' => // validate function, string optional
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
	 *  	'name' => // field name/label string optional
	 *  	'desc' => // field description, string optional
	 *  	'std' => // default value, (array) optional
	 *  	'multiple' => // select multiple values, optional. Default is false.
	 *  	'validate_func' => // validate function, string optional
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
	 *  Add Radio Field to Page
	 *  @author Ohad Raz
	 *  @since 0.1
	 *  @access public
	 *  @param $id string field id, i.e. the meta key
	 *  @param $options (array)  array of key => value pairs for radio options
	 *  @param $args mixed|array
	 *  	'name' => // field name/label string optional
	 *  	'desc' => // field description, string optional
	 *  	'std' => // default value, string optional
	 *  	'validate_func' => // validate function, string optional 
	 *  @param $repeater bool  is this a field inside a repeatr? true|false(default)
	 */
	public function addRadio($id,$options,$args,$repeater=false){
		$new_field = array('type' => 'radio','id'=> $id,'std' => array(),'desc' => '','style' =>'','name' => 'Radio Field','options' => $options);
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
	 *  	'name' => // field name/label string optional
	 *  	'desc' => // field description, string optional
	 *  	'std' => // default value, string optional
	 *  	'validate_func' => // validate function, string optional
	 *  	'format' => // date format, default yy-mm-dd. Optional. Default "'d MM, yy'"  See more formats here: http://goo.gl/Wcwxn
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
	 *  	'name' => // field name/label string optional
	 *  	'desc' => // field description, string optional
	 *  	'std' => // default value, string optional
	 *  	'validate_func' => // validate function, string optional
	 *  	'format' => // time format, default hh:mm. Optional. See more formats here: http://goo.gl/83woX
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
	 *  	'name' => // field name/label string optional
	 *  	'desc' => // field description, string optional
	 *  	'std' => // default value, string optional
	 *  	'validate_func' => // validate function, string optional
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
	 *  	'name' => // field name/label string optional
	 *  	'desc' => // field description, string optional
	 *  	'validate_func' => // validate function, string optional
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
	 *  	'name' => // field name/label string optional
	 *  	'desc' => // field description, string optional
	 *  	'std' => // default value, string optional
	 *  	'style' => 	// custom style for field, string optional Default 'width: 300px; height: 400px'
	 *  	'validate_func' => // validate function, string optional 
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
	 *  	'taxonomy' =>    // taxonomy name can be category,post_tag or any custom taxonomy default is category
			'type' =>  // how to show taxonomy? 'select' (default) or 'checkbox_list'
			'args' =>  // arguments to query taxonomy, see http://goo.gl/uAANN default ('hide_empty' => false)  
	 *  @param $args mixed|array
	 *  	'name' => // field name/label string optional
	 *  	'desc' => // field description, string optional
	 *  	'std' => // default value, string optional
	 *  	'validate_func' => // validate function, string optional 
	 *  @param $repeater bool  is this a field inside a repeatr? true|false(default)
	 */
	public function addTaxonomy($id,$options,$args,$repeater=false){
		$q = array('hide_empty' => 0);
		$tax = 'category';
		$type = 'select';
		$temp = array('taxonomy'=> $tax,'type'=>$type,'args'=>$q);
		$options = array_merge($temp,$options);
		$new_field = array('type' => 'taxonomy','id'=> $id,'desc' => '','name' => 'Taxonomy Field','options'=> $options);
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
	 *  	'type' =>  // how to show taxonomy? 'select' (default) or 'checkbox_list'
	 *  @param $args mixed|array
	 *  	'name' => // field name/label string optional
	 *  	'desc' => // field description, string optional
	 *  	'std' => // default value, string optional
	 *  	'validate_func' => // validate function, string optional 
	 *  @param $repeater bool  is this a field inside a repeatr? true|false(default)
	 */
	public function addRoles($id,$options,$args,$repeater=false){
		$type = 'select';
		$temp = array('type'=>$type);
		$options = array_merge($temp,$options);
		$new_field = array('type' => 'WProle','id'=> $id,'desc' => '','name' => 'WP Roles Field','options'=> $options);
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
	 *  	'post_type' =>    // post type name, 'post' (default) 'page' or any custom post type
			'type' =>  // how to show posts? 'select' (default) or 'checkbox_list'
			'args' =>  // arguments to query posts, see http://goo.gl/is0yK default ('posts_per_page' => -1)  
	 *  @param $args mixed|array
	 *  	'name' => // field name/label string optional
	 *  	'desc' => // field description, string optional
	 *  	'std' => // default value, string optional
	 *  	'validate_func' => // validate function, string optional 
	 *  @param $repeater bool  is this a field inside a repeatr? true|false(default)
	 */
	public function addPosts($id,$options,$args,$repeater=false){
		$q = array('posts_per_page' => -1);
		$temp = array('post_type' =>'post','type'=>'select','args'=>$q);
		$options = array_merge($temp,$options);
		$new_field = array('type' => 'posts','id'=> $id,'desc' => '','name' => 'Posts Field','options'=> $options);
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
	 *  	'name' => // field name/label string optional
	 *  	'desc' => // field description, string optional
	 *  	'std' => // default value, string optional
	 *  	'style' => 	// custom style for field, string optional
	 *  	'validate_func' => // validate function, string optional
	 *  	'fields' => //fields to repeater  
	 */
	public function addRepeaterBlock($id,$args){
		$new_field = array('type' => 'repeater','id'=> $id,'name' => 'Reapeater Field','fields' => array(),'inline'=> false);
		$new_field = array_merge($new_field, $args);
		$this->_fields[] = $new_field;
	}
	
	
	/**
	 * Finish Declaration of Page
	 * @author Ohad Raz
	 * @since 0.1
	 * @access public
	 */
	public function Finish() {
		$this->add_missed_values();
		$this->check_field_upload();
		$this->check_field_color();
		$this->check_field_date();
		$this->check_field_time();
		$this->check_field_code();
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
	
} // End Class

endif; // End Check Class Exists