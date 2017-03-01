<?php
/*
Plugin Name: Wordpress Mailerlite Integration Lite
Plugin URI: https://wordpress.org/plugins/wp-mailerlite-lite/
Description: With this plugin you can integrate your website users with mailerlite email marketing software
Version: 1.0.0
Author: Mojtaba Darvishi
Author URI: http://mojtaba.in
*/
define('WPMI_URL', plugin_dir_url( __FILE__ ));
define('WPMI_PATH', plugin_dir_path( __FILE__ ));

function wpmi_activation() {
	require_once WPMI_PATH. 'includes/wpmi-activator.class.php';
	WPMI_Activator::activate();
}
register_activation_hook( __FILE__, 'wpmi_activation' );

function wpmi_deactivation() {
	require_once WPMI_PATH. 'includes/wpmi-deactivator.class.php';
	WPMI_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'wpmi_deactivation' );

require WPMI_PATH . 'includes/wpmi-main.class.php';
function run_wpmi() {
	new WP_Mailerlite();
}
run_wpmi();