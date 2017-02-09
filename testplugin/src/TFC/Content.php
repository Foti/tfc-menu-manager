<?php

namespace TFC;

class Content{
	public static function showTest(){
	    $html = '<div class="wrap">';
	    $html .= '<h2>Test from static class</h2>';
	    $html .= '</div>';
	    echo $html;
	}
	
	public function show_display_options(){
	    $html = '<div class="wrap">';
	    $html .= '<h2>Display Options</h2>';
	    $html .= '</div>';
	    echo $html;

	}

	public function display_social_options() {
	    $html = '<div class="wrap">';
	    $html .= '<h2>Social Options</h2>';
	    $html .= '</div>';
	    echo $html;
	}
	
	public function display_metabox_options(){
	    $html = '<div class="wrap">';
	    $html .= '<h2>Metabox Options</h2>';
	    $html .= '</div>';
	    echo $html;
	}
}

?>