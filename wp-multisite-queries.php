<?php
/**
 *
 * @link              http://nicholasandre.com.br
 * @since             1.0.0
 * @package           Wp_Multisite_Queries
 *
 * @wordpress-plugin
 * Plugin Name:       WP Multisite Queries
 * Plugin URI:        https://github.com/nicholasio/wp-multisite-queries
 * Description:       A plugin that adds useful multisite queries for WordPress Multisite
 * Version:           1.0.0
 * Author:            Nícholas André
 * Author URI:        http://nicholasandre.com.br
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-multisite-queries
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-multisite-queries-activator.php
 */
function activate_wp_multisite_queries( $network_wide ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-multisite-queries-activator.php';
	Wp_Multisite_Queries_Activator::activate( $network_wide );
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-multisite-queries-deactivator.php
 */
function deactivate_wp_multisite_queries( $network_wide ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-multisite-queries-deactivator.php';
	Wp_Multisite_Queries_Deactivator::deactivate( $network_wide );
}

register_activation_hook( __FILE__, 'activate_wp_multisite_queries' );
register_deactivation_hook( __FILE__, 'deactivate_wp_multisite_queries' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-multisite-queries.php';

require plugin_dir_path( __FILE__ ) . 'includes/functions.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_multisite_queries() {

	$plugin = new Wp_Multisite_Queries();
	$plugin->run();

}

run_wp_multisite_queries();
