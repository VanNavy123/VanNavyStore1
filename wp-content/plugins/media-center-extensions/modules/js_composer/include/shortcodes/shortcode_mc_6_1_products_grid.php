<?php
/**
 * MC_6_1_Products_Grid class.
 *
 * @class 		MC_6_1_Products_Grid
 * @package		MediaCenter/VCExtensions/Shortcodes
 * @category	Class
 * @author 		Ibrahim Ibn Dawood
 */

class MC_6_1_Products_Grid {

	/**
	 * List products based on shortcode name and shortcode attributes
	 *
	 * @access public
	 * @param string $shortcode_name
	 * @param array $shortcode_attributes
	 * @return array
	 */
	public static function get_products( $shortcode_name, $shortcode_attributes ){
		
		$grid_products = array();
		
		$grid_products =  MC_6_1_Products_Grid::$shortcode_name( $shortcode_attributes );

		return $grid_products;
	}

	/**
	 * List products in a category shortcode
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function product_category( $atts ) {
		global $woocommerce_loop;

		if ( empty( $atts ) ) return '';

		extract( shortcode_atts( array(
			'per_page' 		=> '12',
			'columns' 		=> '4',
			'orderby'   	=> 'title',
			'order'     	=> 'desc',
			'category'		=> '',
			'operator'      => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
			), $atts ) );

		if ( ! $category ) return '';

		// Default ordering args
		$ordering_args = WC()->query->get_catalog_ordering_args( $orderby, $order );

		$args = array(
			'post_type'				=> 'product',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts'	=> 1,
			'orderby' 				=> $ordering_args['orderby'],
			'order' 				=> $ordering_args['order'],
			'posts_per_page' 		=> $per_page,
			'meta_query' 			=> array(
				array(
					'key' 			=> '_visibility',
					'value' 		=> array('catalog', 'visible'),
					'compare' 		=> 'IN'
				)
			),
			'tax_query' 			=> array(
				array(
					'taxonomy' 		=> 'product_cat',
					'terms' 		=> array( esc_attr( $category ) ),
					'field' 		=> 'slug',
					'operator' 		=> $operator
				)
			)
		);

		if ( isset( $ordering_args['meta_key'] ) ) {
			$args['meta_key'] = $ordering_args['meta_key'];
		}

		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );

		return $products;
	}

	/**
	 * Recent Products shortcode
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function recent_products( $atts ) {
		global $woocommerce_loop;

		extract( shortcode_atts( array(
			'per_page' 	=> '12',
			'columns' 	=> '4',
			'orderby' 	=> 'date',
			'order' 	=> 'desc'
		), $atts ) );

		$meta_query = WC()->query->get_meta_query();

		$args = array(
			'post_type'				=> 'product',
			'post_status'			=> 'publish',
			'ignore_sticky_posts'	=> 1,
			'posts_per_page' 		=> $per_page,
			'orderby' 				=> $orderby,
			'order' 				=> $order,
			'meta_query' 			=> $meta_query
		);

		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );

		return $products;
	}


	/**
	 * List multiple products shortcode
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function products( $atts ) {
		global $woocommerce_loop;

		if ( empty( $atts ) ) return '';

		extract( shortcode_atts( array(
			'columns' 	=> '4',
			'orderby'   => 'title',
			'order'     => 'asc'
		), $atts ) );

		$args = array(
			'post_type'				=> 'product',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts'	=> 1,
			'orderby' 				=> $orderby,
			'order' 				=> $order,
			'posts_per_page' 		=> -1,
			'meta_query' 			=> array(
				array(
					'key' 		=> '_visibility',
					'value' 	=> array('catalog', 'visible'),
					'compare' 	=> 'IN'
				)
			)
		);

		if ( isset( $atts['skus'] ) ) {
			$skus = explode( ',', $atts['skus'] );
			$skus = array_map( 'trim', $skus );
			$args['meta_query'][] = array(
				'key' 		=> '_sku',
				'value' 	=> $skus,
				'compare' 	=> 'IN'
			);
		}

		if ( isset( $atts['ids'] ) ) {
			$ids = explode( ',', $atts['ids'] );
			$ids = array_map( 'trim', $ids );
			$args['post__in'] = $ids;
		}

		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );

		return $products;
	}

	/**
	 * List all products on sale
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function sale_products( $atts ) {
		global $woocommerce_loop;

		extract( shortcode_atts( array(
			'per_page'      => '12',
			'columns'       => '4',
			'orderby'       => 'title',
			'order'         => 'asc'
		), $atts ) );

		// Get products on sale
		$product_ids_on_sale = wc_get_product_ids_on_sale();

		$meta_query   = array();
		$meta_query[] = WC()->query->visibility_meta_query();
		$meta_query[] = WC()->query->stock_status_meta_query();
		$meta_query   = array_filter( $meta_query );

		$args = array(
			'posts_per_page'	=> $per_page,
			'orderby' 			=> $orderby,
			'order' 			=> $order,
			'no_found_rows' 	=> 1,
			'post_status' 		=> 'publish',
			'post_type' 		=> 'product',
			'meta_query' 		=> $meta_query,
			'post__in'			=> array_merge( array( 0 ), $product_ids_on_sale )
		);

		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );

		return $products;
	}

	/**
	 * List best selling products on sale
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function best_selling_products( $atts ) {
		global $woocommerce_loop;

		extract( shortcode_atts( array(
			'per_page'      => '12',
			'columns'       => '4'
		), $atts ) );

		$args = array(
			'post_type' 			=> 'product',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts'   => 1,
			'posts_per_page'		=> $per_page,
			'meta_key' 		 		=> 'total_sales',
			'orderby' 		 		=> 'meta_value_num',
			'meta_query' 			=> array(
				array(
					'key' 		=> '_visibility',
					'value' 	=> array( 'catalog', 'visible' ),
					'compare' 	=> 'IN'
				)
			)
		);

		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );

		return $products;
	}

	/**
	 * List top rated products on sale
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function top_rated_products( $atts ) {
		global $woocommerce_loop;

		extract( shortcode_atts( array(
			'per_page'      => '12',
			'columns'       => '4',
			'orderby'       => 'title',
			'order'         => 'asc'
			), $atts ) );

		$args = array(
			'post_type' 			=> 'product',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts'   => 1,
			'orderby' 				=> $orderby,
			'order'					=> $order,
			'posts_per_page' 		=> $per_page,
			'meta_query' 			=> array(
				array(
					'key' 			=> '_visibility',
					'value' 		=> array('catalog', 'visible'),
					'compare' 		=> 'IN'
				)
			)
		);

		add_filter( 'posts_clauses', array( __CLASS__, 'order_by_rating_post_clauses' ) );

		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );

		remove_filter( 'posts_clauses', array( __CLASS__, 'order_by_rating_post_clauses' ) );

		return $products;
	}

	/**
	 * Output featured products
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function featured_products( $atts ) {
		global $woocommerce_loop;

		extract( shortcode_atts( array(
			'per_page' 	=> '12',
			'columns' 	=> '4',
			'orderby' 	=> 'date',
			'order' 	=> 'desc'
		), $atts ) );

		$args = array(
			'post_type'				=> 'product',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts'	=> 1,
			'posts_per_page' 		=> $per_page,
			'orderby' 				=> $orderby,
			'order' 				=> $order,
			'meta_query'			=> array(
				array(
					'key' 		=> '_visibility',
					'value' 	=> array('catalog', 'visible'),
					'compare'	=> 'IN'
				),
				array(
					'key' 		=> '_featured',
					'value' 	=> 'yes'
				)
			)
		);

		$products = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $args, $atts ) );

		return $products;
	}

	/**
	 * woocommerce_order_by_rating_post_clauses function.
	 *
	 * @access public
	 * @param array $args
	 * @return array
	 */
	public static function order_by_rating_post_clauses( $args ) {
		global $wpdb;

		$args['where'] .= " AND $wpdb->commentmeta.meta_key = 'rating' ";

		$args['join'] .= "
			LEFT JOIN $wpdb->comments ON($wpdb->posts.ID = $wpdb->comments.comment_post_ID)
			LEFT JOIN $wpdb->commentmeta ON($wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)
		";

		$args['orderby'] = "$wpdb->commentmeta.meta_value DESC";

		$args['groupby'] = "$wpdb->posts.ID";

		return $args;
	}
}


if ( !function_exists( 'shortcode_mc_6_1_products_grid' ) ):

function shortcode_mc_6_1_products_grid( $atts ){
	global $grid_products, $grid_product_details;

	extract(shortcode_atts(array(
		'title' => '',
  		'shortcode_name' => '',
		'ids' => '',
		'skus' => '',
		'category' => '',
  		'orderby' => '',
  		'order' => 'desc',
     	'per_page' => '7',
     	'el_class' => '',
    ), $atts));

    $shortcode_attributes = array();

    $per_page = 7;

    switch( $shortcode_name ){
    	case 'products_ids':
			$shortcode_attributes['ids'] = $ids ;
			$shortcode_name = 'products';
		break;
		case 'products_skus':
			$shortcode_attributes['skus'] = $skus ;
			$shortcode_attributes['columns'] = $columns ;
			$shortcode_name = 'products';
		break;
		case 'product_category':
			$shortcode_attributes['category'] = $category ;
			$shortcode_attributes['per_page'] = $per_page ; 
		break;
    	case 'recent_products':
		case 'featured_products':
		case 'best_selling_products':
		case 'sale_products':
		case 'top_rated_products':
			$shortcode_attributes['per_page'] = $per_page ;
		break;
    }

    if( $shortcode_name != 'best_selling_products'){
		$shortcode_attributes['orderby'] = $orderby ;
		$shortcode_attributes['order'] = $order ;
	}

	$grid_products = MC_6_1_Products_Grid::get_products( $shortcode_name, $shortcode_attributes );

	$grid_product_details['title'] = $title ;
	$grid_product_details['el_class'] = $el_class ;
	$grid_product_details['grid_id'] = sanitize_title( $title ) ;

	$plugin_dir = dirname( MC_VC_PLUGIN_FILE_PATH );

	ob_start();

	include ( $plugin_dir . '/templates/products-grid/mc_6_1_products_grid.php' );

	woocommerce_reset_loop();
	wp_reset_postdata();

	return ob_get_clean();
}

add_shortcode( 'mc_6_1_products_grid' , 'shortcode_mc_6_1_products_grid' );

endif;