<?php

namespace TFC;

class FaqPage extends AbstractSettingsPage {
	
	private $page;
	
	public function add_sections() {		
		add_settings_section(
			'section_diplay_option',
			'Display option section',
			array($this, 'output_section_display'),
			$this->settings_page_properties['menu_slug']	// tfc-faq-settings
		);		
	}
	
	public function output_section_display($arg){
		echo '<p>Select which areas of content you wish to display.</p>';
		echo '<p>id: ' . $arg['id'] . '</p>';             	// id: section_diplay_option
		echo '<p>title: ' . $arg['title'] . '</p>';  		// title: Display option section
		echo '<p>callback: ' . $arg['callback'] . '</p>';	// callback: Array
	}
	
	public function set_fields(){
		//add_settings_field( $id, $title, $callback, $page, $section, $args )
		add_settings_field(
			'some-setting', 
			'Some Setting', 
			array($this, 'create_input_some_setting'),
			$this->settings_page_properties['menu_slug'],
			'section_diplay_option'
		);
	}
	
	public function create_input_some_setting() {
			//$options = get_option('test_plugin_main_settings_arraykey');
	        ?><input type="text" name="test_plugin_main_settings_arraykey[some-setting]" value="<?php echo $options['some-setting']; ?>" /><?php
		}



	public function render_settings_page() {

    	$option_name = $this->settings_page_properties['option_name'];
    	$option_group = $this->settings_page_properties['option_group'];
    	$settings_data = $this->get_settings_data(); 

//print_r($this->settings_page_properties).'<br>';
//print_r($settings_data).'<br>';

    	?>
    	<!-- Create a header in the default WordPress 'wrap' container -->
		    <div class="wrap">

		        <div id="icon-themes" class="icon32"></div>
		        <h2>TFC Theme Options</h2>
		        <?php settings_errors(); ?>
		
				<?php
					$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'display_options';
				?>				

		        <h2 class="nav-tab-wrapper">
				    <a href="?page=tfc-faq-settings&tab=display_options" 
					   class="nav-tab <?php echo $active_tab == 'display_options' ? 'nav-tab-active' : ''; ?>">
					   Display Options
					</a>
						
				    <a href="?page=tfc-faq-settings&tab=social_options" 
						class="nav-tab <?php echo $active_tab == 'social_options' ? 'nav-tab-active' : ''; ?>">
						Social Options
					</a>
					
					<a href="?page=tfc-faq-settings&tab=metabox_options" 
						class="nav-tab <?php echo $active_tab == 'metabox_options' ? 'nav-tab-active' : ''; ?>">
						Metaboxes
					</a>
				</h2>

		        <form method="post" action="options.php">
				 	<?php

						if( $active_tab == 'display_options' ) {
							settings_fields( $this->$option_group );
							Content::show_display_options();
							
							$this->set_fields();
							
							// Prints out all settings sections added to a particular settings page.
							do_settings_sections( 'tfc-faq-settings' );	
							
							
							//do_settings_fields('tfc-faq-settings','section_diplay_option');
							//Prints out the settings fields for a particular settings section.
						}
						elseif( $active_tab == 'social_options' ) {
							settings_fields( $this->$option_group );
							Content::display_social_options();
							Content::showTest();
							//do_settings_sections( 'tfc_faq_service' );
						} else {
							settings_fields( $this->$option_group );
							Content::display_metabox_options();
							//do_settings_sections( 'tfc_faq_service' );
						}
						submit_button();
					    ?>
		        </form>

		    </div><!-- /.wrap -->
    	<?php
	}

	
	


	public function get_default_settings_data() {
    	$defaults = array();
    	$defaults['textbox-faq'] = '';

    	return $defaults;
	}
}