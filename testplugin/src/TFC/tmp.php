public function render_settings_page() {
	$option_name = $this->settings_page_properties['option_name'];
	$option_group = $this->settings_page_properties['option_group'];
	$settings_data = $this->get_settings_data(); 

print_r($settings_data).'<br>';

	?>
	<div class="wrap">
  		<h2>En een tweede pagina</h2>
  		<p>This plugin is alwo working.</p>
  		<form method="post" action="options.php">
    		<?php settings_fields( $option_group ); ?>
    		<table class="form-table">
      			<tr>
          			<th><label for="textbox">Textbox:</label></th>
          			<td>
              			<input type="text" id="textbox-faq"
                  		name="<?php echo esc_attr( $option_name."[textbox-faq]" ); ?>"
                  		value="<?php echo esc_attr( $settings_data['textbox-faq'] ); ?>" />
          			</td>
      			</tr>
    		</table>
    		<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Options">
  		</form>
	</div>
	<?php
}