<?php
/*
 * Plugin Name:			TFC | Menu Access Control
 * Plugin URI:			http://www.foti.nl
 * Description:			Manage settings & custom Social Media content to post and pages.
 * Author:				Tony Foti Cuzzola
 * Author URI:			http://www.foti.nl
 * Text Domain:			single-post-meta-manager-locale
 * License:				GPL-2.0+
 * License URI:			http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:			/languages
 * Version:				1.2.1
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-tfc-menu-manager.php';

function run_tfc_menu_manager() {
	$tfcmm = new TFC_Menu_Manager();
	$tfcmm->run();
}

run_tfc_menu_manager();