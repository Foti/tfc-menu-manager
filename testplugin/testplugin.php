<?php
/*
 * Plugin Name:			TFC | TestPlugin
 * Plugin URI:			http://www.foti.nl
 * Description:			Test with classses.
 * Author:				Tony Foti Cuzzola
 * Author URI:			http://www.foti.nl
 * Text Domain:			test
 * License:				GPL-2.0+
 * License URI:			http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:			/languages
 * Version:				1.2.1
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}
define( 'WP_DEBUG', true );

use TFC\Plugin;
use TFC\SettingsPage;

use TFC\FaqPage;
use TFC\Content;

spl_autoload_register( 'tfc_autoloader' );
function tfc_autoloader( $class_name ) {
  if ( false !== strpos( $class_name, 'TFC' ) ) {

	// path to the directory where our classes reside
    $classes_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;

	// class_file resolves the path to the class definition file
    $class_file = str_replace( '\\', DIRECTORY_SEPARATOR, $class_name ) . '.php';

    require_once $classes_dir . $class_file;
  }
}

	function tfc_init() {
		$plugin = new Plugin(); // Create container
  		$plugin['path'] = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR;
  		$plugin['url'] = plugin_dir_url( __FILE__ );
  		$plugin['version'] = '1.0.0';
  		$plugin['settings_page_properties'] = array(
    		'parent_slug' => 'options-general.php',
    		'page_title' =>  'Test plugin',
    		'menu_title' =>  'Testmenu',
    		'capability' => 'manage_options',
    		'menu_slug' => 'tfc-settings',
    		'option_group' => 'tfc_option_group',
    		'option_name' => 'tfc_option_name'
 		);
		$plugin['settings_page'] = 'tfc_settings_service';
  		$plugin->run();
	}
	add_action( 'plugins_loaded', 'tfc_init' );
	add_action( 'plugins_loaded', 'addFaqPage' );

	function tfc_settings_service( $plugin ){
		// Singleton
		static $object;

	  	if (null !== $object) {
	    	return $object;
	  	}

	  	$object = new SettingsPage( $plugin['settings_page_properties'] );
	  	return $object;
	}
	
	function addFaqPage(){
		$plugin = new Plugin(); // Create container
  		$plugin['path'] = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR;
  		$plugin['url'] = plugin_dir_url( __FILE__ );
  		$plugin['version'] = '1.0.0';

  		$plugin['settings_page_properties'] = array(
    		'parent_slug' => null, 							// set to null for creating toplevel menu
			'icon_url' => '',								// only for toplevel menu
			'position' => null,								// only for toplevel menu

    		'page_title' =>  'Test plugin page 2',
    		'menu_title' =>  'Testmenu2',
    		'capability' => 'manage_options',
    		'menu_slug' => 'tfc-faq-settings',
    		'option_group' => 'tfc_faq_option_group',
    		'option_name' => 'tfc_faq_option_name'
 		);
		$plugin['settings_page'] = 'tfc_faq_service';
  		$plugin->run();
	}
	
	function tfc_faq_service( $plugin ){
		// Singleton
		static $object;

	  	if (null !== $object) {
	    	return $object;
	  	}

	  	$object = new FaqPage( $plugin['settings_page_properties'] );
	  	return $object;
	}
	
	public static function activate(){
		// Do nothing
	}

	        /**
	         * Deactivate the plugin
	         */     
	public static function deactivate(){
		// Do nothing
	}