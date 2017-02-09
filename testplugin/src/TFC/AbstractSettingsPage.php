<?php

/*
Abstract Class enables reusable code for the settings page to another class or submenu page.
Notice that this is an abstract class. This will prevent intantiating this class directly. 
To use it you need to extend it on another class.
*/

namespace TFC;

abstract class AbstractSettingsPage {

	protected $settings_page_properties;

	public function __construct( $settings_page_properties ){
    	$this->settings_page_properties = $settings_page_properties;
  	}

	public function run() {
    	add_action( 'admin_menu', array( $this, 'add_menu_and_page' ) );
    	add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public function add_menu_and_page() { 
		if($this->settings_page_properties['parent_slug'] == null){
			//add top level menu
			add_menu_page( 
      			$this->settings_page_properties['page_title'],
      			$this->settings_page_properties['menu_title'],
      			$this->settings_page_properties['capability'],
      			$this->settings_page_properties['menu_slug'], 
				array( $this, 'render_settings_page' ),
				$this->settings_page_properties['icon_url'],
				$this->settings_page_properties['position']
			);
		}else{
			add_submenu_page(
      			$this->settings_page_properties['parent_slug'],
      			$this->settings_page_properties['page_title'],
      			$this->settings_page_properties['menu_title'],
      			$this->settings_page_properties['capability'],
      			$this->settings_page_properties['menu_slug'],
        		array( $this, 'render_settings_page' )
    		); 
		}   
	}

	public function register_settings() {

    	register_setting(
      		$this->settings_page_properties['option_group'],
      		$this->settings_page_properties['option_name']
    	);
$this->add_sections();
	}

	public function get_settings_data(){
    	return get_option( $this->settings_page_properties['option_name'], $this->get_default_settings_data() );
	}

	public function render_settings_page(){

	}

	public function get_default_settings_data(){
    	$defaults = array();
    	return $defaults;
	}
}