<?php

if (!class_exists('TFC_Menu_Manager')) {
	class TFC_Menu_Manager {

		protected $hook_manager;
		protected $plugin_slug;
		protected $version;
		protected $plugin;

		public function __construct() {
			$this->plugin_slug = 'tfc-menu-manager-slug';
			$this->load_dependencies();
			$this->define_admin_hooks();
			
			//$this->init_plugin();
		}
		
		private function init_plugin(){
			$this->plugin = new PLugin();
			$this->plugin['version'] = '1.0.0';
			$this->plugin['path'] = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR;
			$this->plugin['url'] = plugin_dir_url( __FILE__ );
			$plugin['settings_page_properties'] = array( 
			    'parent_slug' => 'options-general.php',
			    'page_title' =>  'Simplarity',
			    'menu_title' =>  'Simplarity',
			    'capability' => 'manage_options',
			    'menu_slug' => 'simplarity-settings',
			    'option_group' => 'simplarity_option_group',
			    'option_name' => 'simplarity_option_name'
			  );
			  //$plugin['settings_page'] = new Simplarity_SettingsPage( $plugin['settings_page_properties'] );
			  //$plugin->run();
		}
		
		// plugin_dir_url( __FILE__ ) : 				http://foti.nl/public/wp-content/plugins/tfc-menu-manager/includes/
		// plugin_dir_path( dirname( __FILE__ ) ) : 	/www/htdocs/foti/public/wp-content/plugins/tfc-menu-manager/
		// realpath( plugin_dir_path( __FILE__ ) ) : 	/www/htdocs/foti/public/wp-content/plugins/tfc-menu-manager/includes
		// plugin_dir_path( __FILE__ ) : 				/www/htdocs/foti/public/wp-content/plugins/tfc-menu-manager/includes/

		private function load_dependencies() {
			
			
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tfc-menu-manager-admin.php';
			require_once plugin_dir_path( __FILE__ ) . 'class-tfc-hook-manager.php';

			$this->hook_manager = new TFC_Hook_Manager();
		}

		private function define_admin_hooks() {
			$admin = new TFC_Menu_Manager_Admin( $this->get_version() );
			$this->hook_manager->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
			$this->hook_manager->add_action( 'add_meta_boxes', $admin, 'add_meta_box' );

			//tfc
			$this->hook_manager->add_action( 'admin_init', $admin, 'register_settings_and_fields' );
			$this->hook_manager->add_action( 'admin_menu', $admin, 'set_admin_menu' );
		}

		public function run() {
			$this->hook_manager->run();
		}

		public function get_version() {
			return $this->version;
		}
	}
}