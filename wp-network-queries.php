<?php
/*
Plugin Name: WP Network Queries
Version: 0.1-alpha
Description: PLUGIN DESCRIPTION HERE
Author: Nícholas André Pinho de Oliveira
Author URI: nicholasandre.com.br
Text Domain: wp-network-queries
Domain Path: /languages
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'public/class-wpnq.php');

register_activation_hook( __FILE__, array( 'WPNQ', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WPNQ', 'deactivate' ) );


add_action( 'after_setup_theme', array( 'WPNQ', 'get_instance' ) );


