<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://nicholasandre.com.br
 * @since      1.0.0
 *
 * @package    Wp_Multisite_Queries
 * @subpackage Wp_Multisite_Queries/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Multisite_Queries
 * @subpackage Wp_Multisite_Queries/public
 * @author     Nícholas André <nicholas@iotecnologia.com.br>
 */
class Wp_Multisite_Queries_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	public function init() {
		self::rewrite_rules();
	}

	public static function rewrite_rules() {
		//Regras para busca
		add_rewrite_rule('portal/search/([^/]+)/page/?([0-9]{1,})/?$', 'index.php?wpcpn_query_type=search&wpcpn_network_search=$matches[1]&paged=$matches[2]', 'top');
		add_rewrite_rule('portal/search/([^/]+)/?', 'index.php?wpcpn_query_type=search&wpcpn_network_search=$matches[1]', 'top');

		//Regras para taxonomias
		add_rewrite_rule('portal/categoria/([^/]+)/page/?([0-9]{1,})/?$', 	'index.php?wpcpn_query_type=taxonomy&wpcpn_network_tax=category&wpcpn_network_term=$matches[1]&paged=$matches[2]' , 'top');
		add_rewrite_rule('portal/categoria/([^/]+)/?', 	'index.php?wpcpn_query_type=taxonomy&wpcpn_network_tax=category&wpcpn_network_term=$matches[1]' , 'top');
		

		add_rewrite_rule('portal/([^/]+)/([^/]+)/page/?([0-9]{1,})/?$', 	'index.php?wpcpn_query_type=taxonomy&wpcpn_network_tax=$matches[1]&wpcpn_network_term=$matches[2]&paged=$matches[3]' , 'top');
		add_rewrite_rule('portal/([^/]+)/([^/]+)/?', 	'index.php?wpcpn_query_type=taxonomy&wpcpn_network_tax=$matches[1]&wpcpn_network_term=$matches[2]' , 'top');
	}

	/**
	 * Define o link para que a listagem de categorias sejam globais.
	 * @param  string $catlink Link para a categoria
	 * @param  id $catid   id da categoria
	 * @return string          link filtrado
	 */
	public function category_link( $catlink, $catid ) {
		$term_obj = get_term($catid, 'category');
		return get_site_url( 1 ) . '/portal/categoria/' . $term_obj->slug;
	}

	/**
	 * Define nossas próprias query_vars
	 * @param  array $vars query_vars que já estão registradas
	 * @return array       query_vars
	 */
	public function query_vars( $vars )  {
		$vars[] = 'wpcpn_network_term';
		$vars[] = 'wpcpn_network_tax';
		$vars[] = 'wpcpn_query_type';
		$vars[] = 'wpcpn_network_search';
		return $vars;
	}

	/**
	 * Carrega o template associado as nossas rewrite_rules
	 */
	public function template_redirect() {
		global $wp_query;

		if ( isset($wp_query->query_vars['wpcpn_query_type']) ) {

			$query_type = get_query_var('wpcpn_query_type');
			$wpcpn_posts = $this->query();

			switch ( $query_type) {
				case 'taxonomy':
					$tax = esc_sql( get_query_var('wpcpn_network_tax') );

					if ( locate_template("wpcpn-network-{$tax}.php") ) {
						include(locate_template("wpcpn-network-{$tax}.php"));
					}

				break;
				case 'search':

					if ( locate_template("wpcpn-network-search.php") ) {
						include(locate_template("wpcpn-network-search.php"));
					}

				break;
			}

			$this->cleanQuery();

			die();

		}
	}

	/**
	 * Realiza uma consulta pelos parâmetros passados
	 * @return array object array contendo todos os posts encontrados
	 */
	public function query() {
		$query_type = get_query_var('wpcpn_query_type');
		$wpcpn_posts = array();

		switch ($query_type) {
			case 'taxonomy':
				$wpcpn_posts = wpmq_get_network_posts(
					array(
						'taxonomy_slug' => esc_sql( get_query_var('wpcpn_network_tax') ), 
						'term_slug' 	=> esc_sql( get_query_var('wpcpn_network_term') ),
						'size'			=> 10,
						'paged'			=> max(1, get_query_var('paged') ) 
					) 
				);
			break;
			case 'search':
				$wpcpn_posts = wpmq_get_network_posts(
					array(
						'size' 		=> 10,
						'paged'		=> max(1, get_query_var('paged') ),
						's' 		=> esc_sql( get_query_var('wpcpn_network_search') )
					)
				);
			break;
		}

		return $wpcpn_posts;
	}


}
