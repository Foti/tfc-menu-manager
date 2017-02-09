<?php

if (!class_exists('TFC_Menu_Manager_Admin')) {

	class TFC_Menu_Manager_Admin {

		private $version;
		public $options;
		private $menuArr;
		private $subMenuArr;
		public $message;
		public $type;

		public function __construct( $version ) {
			$this->version = $version;
			$this->menuArr = array();
			$this->options = get_option('tfc_mm_plugin_options'); 	// Fill options array (WP options API)
		}
		
		private function get_top_level_menus(){
			global $menu;
			foreach ( $menu as $item ) {
				$name = $item[0];	// Get name of menu item
				$file = $item[2];	// Get slugname menu file
				
				// remove tags (with numbers) from name						
				$name = strip_tags($name);					
				$name = $this->remove_white_space($name);

				if($name && strlen($name) > 0){
					$this->menuArr[$name] = $file;
				}
			}
		}
		
		private function get_sub_menus($parent_slug){
			global $submenu;
			$submenu_list = $submenu[$parent_slug];
			if(count($submenu_list) > 0){
				$tmp_arr = array();
				foreach ( $submenu_list as $submenu_item  ) {
						$name = $submenu_item[0];	// Get name of menu item
						$file = $submenu_item[2];	// Get slugname menu file
						
						// remove tags (with numbers) from name						
						$name = strip_tags($name);					
						$name = $this->remove_white_space($name);
						
						if($name && strlen($name) > 0){
							$this->subMenuArr[$name] = $file;
							$tmp_arr[$name] = $file;							
						}				
				}
				return $tmp_arr;				
			}
		}
		
		private function remove_white_space($str){
			do {
			    $tmp = $str;
			    $str = preg_replace( '#<([^ >]+)[^>]*>([[:space:]]|&nbsp;)*</\1>#', '', $str );
			} while ( $str !== $tmp );
			
			// check for numbers at the end of the string
			$r = preg_match_all("/.*?(\d+)$/", $str, $matches);

			if($r > 0) {
				$number_of_digits = strlen($matches[count($matches)-1][0]);
				$end = -($number_of_digits + 1);
				$str = substr($str, 0, $end);
			}
			return $str;
		}

		public function enqueue_styles() {
			wp_enqueue_style(
				'tfc-menu-manager-admin',
				plugin_dir_url( __FILE__ ) . 'css/tfc-menu-manager-admin.css',
				array(),
				$this->version,
				FALSE
			);
		}

		public function add_meta_box() {
			add_meta_box(
				'tfc-menu-manager-admin',
				'Single Post Meta Manager',
				array( $this, 'render_meta_box' ),
				'post',
				'normal',
				'core'
			);
		}

		public function render_meta_box() {
			require_once plugin_dir_path( __FILE__ ) . 'partials/tfc-menu-manager-content.php';
		}

		// Hook: admin_init. Runs at the beginning of every admin page before the page is rendered.
		public function register_settings_and_fields(){
			// $option_group, $option_name, $sanitize_callback (optional)
			// register_setting( 'tfc_mm_plugin_options', 'tfc_mm_plugin_options', array($this,'tfc_validate_settings'));
			register_setting( 'tfc_mm_plugin_options', 'tfc_mm_plugin_options');

			// Output nonce, action, and option_page fields for a settings page.
			settings_fields( 'tfc_mm_plugin_options' );

			// Prints out all settings sections added to a particular settings page.
			do_settings_sections( __FILE__ );

			// $id, $title, $callback, $page
			add_settings_section('tfc_mm_section_items', 'Hide Menu items', array($this,'tfc_cb_items'), __FILE__);
			add_settings_section('tfc_mm_section_menus', 'Meta Boxes', array($this,'tfc_cb_menus'), __FILE__);

			// $id, $title, $callback, $page, $section, $args
			add_settings_field('menu_checkboxes', 'Hide Menu Items: ', array($this,'set_up_menu_checkboxes'), __FILE__, 'tfc_mm_section_items');
		}

		public function tfc_cb_items(){}

		public function tfc_cb_menus(){}
			
			

		public function set_up_menu_checkboxes(){
			global $menu;
			$html = '<p><em>Select menu item\'s to be invisible for other users.</em></p><br>';
			
			foreach ($this->menuArr as $key => $file) {
				//$this->get_sub_menus($file);
				$sub_menu_list = $this->get_sub_menus($file);
				$menuName = preg_replace('/\s+/', '_', $key);echo  $menuName;
				$menuName = strtolower($menuName);
				
				if(!isset($this->options['cb_'.$menuName.'_set'])){
					$this->options['cb_'.$menuName.'_set'] = false;
				}
				$html .= '<p><input type="checkbox" id="cb_'.$menuName.'" name="tfc_mm_plugin_options[cb_'.$menuName.'_set]" value="1"' . checked( 1, $this->options['cb_'.$menuName.'_set'], false ) . '/>';
			    $html .= '<label for="cb_'.$menuName.'">' . $key . '</label><br>';
				
				
				if(isset($sub_menu_list) && count($sub_menu_list) > 0){	
							
					foreach ( $sub_menu_list as $sub_menu_key => $val ) {
						//create name based on toplevel menu name and submenu name.
						$subMenuName = preg_replace('/\s+/', '_', $sub_menu_key);
						$subMenuName = $inputName .'_'. strtolower($subMenuName);
						
						if(!isset($this->options['cb_'.$subMenuName.'_set'])){
							$this->options['cb_'.$subMenuName.'_set'] = false;
						}
						
						$html .= '<p style="padding-left:20px"><input type="checkbox" id="cb_'.$subMenuName.'" name="tfc_mm_plugin_options[cb_'.$subMenuName.'_set]" value="1"' . checked( 1, $this->options['cb_'.$subMenuName.'_set'], false ) . '/>';
					    $html .= '<label for="cb_'.$subMenuName.'">' . $sub_menu_key . '</label><br>';
					}
				}				
				$html .= '</p><br>';				
			}
			echo $html;
		}

		// Hook: admin_menu. Runs after the basic admin panel menu structure is in place
		public function set_admin_menu(){
			$this->get_top_level_menus();
			
			$page_title = 'TFC | Menu Manager';
			$menu_title = 'Menu Manager';
			$capability = 'administrator';
			$menu_slug = __FILE__;
			$function = array( $this, 'display_options_page' );
			$icon_url = ''; 
			$position = 3;

			add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
			$this->add_submenus();
			
			$this->hide_menu_items();	
		}
		
		private function add_submenus(){
			$parent_slug = plugin_basename( __FILE__);
			//add_menu_page('Theme page title', 'Theme menu label', 'manage_options', 'theme-options', 'wps_theme_func');

			add_submenu_page( $parent_slug, 'Metaboxes', 'Metaboxes', 'manage_options', 'theme-op-settings', array( $this, 'wps_theme_func_settings'));
			add_submenu_page( $parent_slug, 'tfcFAQ', 'tfcFAQ', 'manage_options', 'theme-op-faq', array( $this, 'wps_theme_func_faq'));

		}


		public function wps_theme_func_settings(){
			?>
			<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
		    	<h2>Manage Metaboxes</h2>
			<?php	
			
			/*global $wp_meta_boxes;
			print_r($wp_meta_boxes['dashboard']['normal']);
			$dashboard_boxes = get_hidden_meta_boxes( 'dashboard', 'normal' );
			print_r($dashboard_boxes);*/
			
			?>
			</div>
			<?php
		}
		public function wps_theme_func_faq(){
			?>
			<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
				<h2>Manage Other</h2>
			</div>
			<?php
		}
		
		private function hide_menu_items(){	
			//global $menu;
			//echo print_r($this->menuArr). '<br><br>';
			//foreach ( $menu as $menu_item ) {
				//if(isset($menu_item[0]) && strlen($menu_item[0]) > 0){
					//echo $menu_item[0] .' = '.$menu_item[2].'<br>';
				//}
			//}
			
			// let op, names submenu's are different
			
			foreach ( $this->options as $option => $val ) {
				// Convert checkbox name (without cb_xxx_set) to array key where first letter is a capital
				$name = ucfirst(substr($option, 3,-4));

				if (array_key_exists($name, $this->menuArr)) {
					remove_menu_page($this->menuArr[$name]);
				}
			}
			//remove_menu_page('tfc-menu-manager/admin/class-tfc-menu-manager-admin.php');
		}

		public function display_options_page(){
			?>

			<div class="wrap">
				<?php screen_icon(); ?>
				<h2>Manage Admin Menu Items</h2>
				<?php
					//$message = __("Developer note! Metaboxes are added 'hard-coded' to posttypes in add_meta_boxes.php");
					//$type = 'warning';
					//add_settings_error(self::SETTINGS_ERROR_SLUG, 'info', $message, $type);
				?>

				<?php settings_errors(); ?>
				<form method="post" enctype="multipart/form-data" action="options.php">
					<?php
						// Security hidden fields
						settings_fields('tfc_mm_plugin_options');

						// Prints out all settings sections added to a particular settings page.
						do_settings_sections(__FILE__);
					?>
					<p class="submit">
					<input type="submit" name="submit" class="button-primary" value="Save changes">
					</p>
				</form>
				<em>&copy;2017 T. Foti - Version <?php echo $this->version; ?></em>
			</div>

			<?php
		}


		private function get_admin_menu_slug(){
			
		}
		
		
		/**
		 * Get the URL of an admin menu item
		 *
		 * @param   string $menu_item_file admin menu item file
		 *          - can be obtained via array key #2 for any item in the global $menu or $submenu array
		 * @param   boolean $submenu_as_parent
		 * 
		 * @return  string URL of admin menu item, NULL if the menu item file can't be found in $menu or $submenu 
		 */
		private function get_admin_menu_item_url( $menu_item_file, $submenu_as_parent = true ) {
			global $menu, $submenu, $self, $parent_file, $submenu_file, $plugin_page, $typenow;

			$admin_is_parent = false;
			$item = '';
			$submenu_item = '';
			$url = '';

			// 1. Check if top-level menu item
			foreach( $menu as $key => $menu_item ) {
				if ( array_keys( $menu_item, $menu_item_file, true ) ) {
					$item = $menu[ $key ];
				}

				if ( $submenu_as_parent && ! empty( $submenu_item ) ) {
					$menu_hook = get_plugin_page_hook( $submenu_item[2], $item[2] );
					$menu_file = $submenu_item[2];

					if ( false !== ( $pos = strpos( $menu_file, '?' ) ) )
						$menu_file = substr( $menu_file, 0, $pos );
					if ( ! empty( $menu_hook ) || ( ( 'index.php' != $submenu_item[2] ) && file_exists( WP_PLUGIN_DIR . "/$menu_file" ) && ! file_exists( ABSPATH . "/wp-admin/$menu_file" ) ) ) {
						$admin_is_parent = true;
						$url = 'admin.php?page=' . $submenu_item[2];
					} else {
						$url = $submenu_item[2];
					}
				}

				elseif ( ! empty( $item[2] ) && current_user_can( $item[1] ) ) {
					$menu_hook = get_plugin_page_hook( $item[2], 'admin.php' );
					$menu_file = $item[2];

					if ( false !== ( $pos = strpos( $menu_file, '?' ) ) )
						$menu_file = substr( $menu_file, 0, $pos );
					if ( ! empty( $menu_hook ) || ( ( 'index.php' != $item[2] ) && file_exists( WP_PLUGIN_DIR . "/$menu_file" ) && ! file_exists( ABSPATH . "/wp-admin/$menu_file" ) ) ) {
						$admin_is_parent = true;
						$url = 'admin.php?page=' . $item[2];
					} else {
						$url = $item[2];
					}
				}
			}

			// 2. Check if sub-level menu item
			if ( ! $item ) {
				$sub_item = '';
				foreach( $submenu as $top_file => $submenu_items ) {

					// Reindex $submenu_items
					$submenu_items = array_values( $submenu_items );

					foreach( $submenu_items as $key => $submenu_item ) {
						if ( array_keys( $submenu_item, $menu_item_file ) ) {
							$sub_item = $submenu_items[ $key ];
							break;
						}
					}					

					if ( ! empty( $sub_item ) )
						break;
				}

				// Get top-level parent item
				foreach( $menu as $key => $menu_item ) {
					if ( array_keys( $menu_item, $top_file, true ) ) {
						$item = $menu[ $key ];
						break;
					}
				}

				// If the $menu_item_file parameter doesn't match any menu item, return false
				if ( ! $sub_item )
					return false;

				// Get URL
				$menu_file = $item[2];

				if ( false !== ( $pos = strpos( $menu_file, '?' ) ) )
					$menu_file = substr( $menu_file, 0, $pos );

				// Handle current for post_type=post|page|foo pages, which won't match $self.
				$self_type = ! empty( $typenow ) ? $self . '?post_type=' . $typenow : 'nothing';
				$menu_hook = get_plugin_page_hook( $sub_item[2], $item[2] );

				$sub_file = $sub_item[2];
				if ( false !== ( $pos = strpos( $sub_file, '?' ) ) )
					$sub_file = substr($sub_file, 0, $pos);

				if ( ! empty( $menu_hook ) || ( ( 'index.php' != $sub_item[2] ) && file_exists( WP_PLUGIN_DIR . "/$sub_file" ) && ! file_exists( ABSPATH . "/wp-admin/$sub_file" ) ) ) {
					// If admin.php is the current page or if the parent exists as a file in the plugins or admin dir
					if ( ( ! $admin_is_parent && file_exists( WP_PLUGIN_DIR . "/$menu_file" ) && ! is_dir( WP_PLUGIN_DIR . "/{$item[2]}" ) ) || file_exists( $menu_file ) )
						$url = add_query_arg( array( 'page' => $sub_item[2] ), $item[2] );
					else
						$url = add_query_arg( array( 'page' => $sub_item[2] ), 'admin.php' );
				} else {
					$url = $sub_item[2];
				}
			}

			return esc_url( $url );
		}
	}
}