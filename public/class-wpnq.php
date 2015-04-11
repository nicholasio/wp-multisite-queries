<?php

class WPNQ {
	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   0.1
	 *
	 * @var     string
	 */
	const VERSION = '0.1';

	protected function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'template_redirect', array( $this, 'template_redirect' ) );
		add_filter( 'query_vars', array( $this, 'query_vars') );
		add_filter( 'category_link', array( $this, 'category_link'), 10, 2);
	}

	public static function get_instance() {

	}

	public static function activate() {

	}

	public static function deactivate() {

	}

	private static function rewrite_rules() {
		//Regras para busca
		add_rewrite_rule('portal/search/([^/]+)/page/?([0-9]{1,})/?$', 'index.php?wpcpn_query_type=search&wpcpn_network_search=$matches[1]&paged=$matches[2]', 'top');
		add_rewrite_rule('portal/search/([^/]+)/?', 'index.php?wpcpn_query_type=search&wpcpn_network_search=$matches[1]', 'top');

		//Regras para taxonomias
		add_rewrite_rule('portal/categoria/([^/]+)/page/?([0-9]{1,})/?$', 'index.php?wpcpn_query_type=taxonomy&wpcpn_network_tax=category&wpcpn_network_term=$matches[1]&paged=$matches[2]' , 'top');
		add_rewrite_rule('portal/categoria/([^/]+)/?', 	'index.php?wpcpn_query_type=taxonomy&wpcpn_network_tax=category&wpcpn_network_term=$matches[1]' , 'top');

		add_rewrite_rule('portal/([^/]+)/([^/]+)/page/?([0-9]{1,})/?$', 	'index.php?wpcpn_query_type=taxonomy&wpcpn_network_tax=$matches[1]&wpcpn_network_term=$matches[2]&paged=$matches[3]' , 'top');
		add_rewrite_rule('portal/([^/]+)/([^/]+)/?', 	'index.php?wpcpn_query_type=taxonomy&wpcpn_network_tax=$matches[1]&wpcpn_network_term=$matches[2]' , 'top');

	}


	public function init() {
		self::rewrite_rules();
		$this->load_plugin_textdomain();
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
				$wpcpn_posts = wpcpn_get_network_posts(
					array(
						'taxonomy_slug' => esc_sql( get_query_var('wpcpn_network_tax') ),
						'term_slug' 	=> esc_sql( get_query_var('wpcpn_network_term') ),
						'size'			=> 10,
						'paged'			=> max(1, get_query_var('paged') )
					)
				);
			break;
			case 'search':
				$wpcpn_posts = wpcpn_get_network_posts(
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