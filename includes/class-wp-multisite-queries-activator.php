<?php

/**
 * Fired during plugin activation
 *
 * @link       http://nicholasandre.com.br
 * @since      1.0.0
 *
 * @package    Wp_Multisite_Queries
 * @subpackage Wp_Multisite_Queries/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wp_Multisite_Queries
 * @subpackage Wp_Multisite_Queries/includes
 * @author     Nícholas André <nicholas@iotecnologia.com.br>
 */
class Wp_Multisite_Queries_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate( $network_wide ) {
		if ( $network_wide ) {
			$blog_ids = Wp_Multisite_Queries::get_blog_ids();
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				self::single_activate();
				restore_current_blog();
			}
		} else {
			self::single_activate();
		}
	}

	public static function single_activate() {
		Wp_Multisite_Queries_Public::rewrite_rules();
		flush_rewrite_rules();
	}



}
