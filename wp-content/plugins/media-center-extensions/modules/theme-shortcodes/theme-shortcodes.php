<?php

// Custom WP Query
include_once 'class-mc-query-shortcode.php';

// Wishlist
include_once 'wishlist.php';

// Compare
include_once 'compare.php';


#-----------------------------------------------------------------
# Shortcodes using [query] calss
#-----------------------------------------------------------------


// [blog] shortcode
//................................................................
if ( ! function_exists( 'blog_query' ) ) :
	function blog_query($args = null, $content = null) {

		// default template
		if ( ! isset( $args['template'] ) || empty( $args['template'] ) ) {
			$args['template'] = 'blog-sidebar-right';
		}

		// default Style
		if ( ! isset( $args['blog_style'] ) || empty( $args['blog_style'] ) ) {
			$args['blog_style'] = 'normal';
		}

		$args[ 'template' ] .= '.php';

		return custom_wp_query( $args, $content, true );
	}
	
	add_shortcode('blog', 'blog_query');
endif;

// [shop] shortcode
//................................................................
if ( ! function_exists( 'shop_query' ) ) :
	function shop_query($args = null, $content = null) {
		
		global $woocommerce_loop;

		if( class_exists( 'WooCommerce' ) ){
			
			$catalog_ordering_args = WC()->query->get_catalog_ordering_args();

			if( $args == null ){
				$args = array();
			}

			if( is_array( $args ) ){
				$args = array_merge( $args, $catalog_ordering_args );
			}
		}


		// default layout
		if ( ! isset( $args['layout'] ) || empty( $args['layout'] ) ) {
			$args['layout'] = 'left-sidebar';
		}

		// post type
		if ( ! isset( $args['post_type'] ) || empty( $args['post_type'] ) ) {
			$args['post_type'] = 'product';
		}

		// orderby
		if ( ! isset( $args['orderby'] ) || empty( $args['orderby'] ) ) {
			$args['orderby'] = 'menu_order'; // default by sort order
		}
		
		if ( ! isset( $args['order'] ) || empty( $args['order'] ) ) {
			$args['order'] = 'ASC'; // default order
		}
		
		// categories 
		if ( isset( $args['category'] ) ) {
			$args['taxonomy_slug'] = 'product_category';
			unset( $args['category'] );
		}

		if( ! isset( $args[ 'columns' ] ) ) {
			$args[ 'columns' ] = 3;
		}

		$args[ 'template' ] = 'archive-product.php';

		$woocommerce_loop[ 'columns' ] = $args[ 'columns' ];

		return custom_wp_query( $args, $content, true );
	}
	
	add_shortcode('shop', 'shop_query');
endif;

if ( ! function_exists( 'mc_vc_terms' ) ) :

	function mc_vc_terms( $atts, $content = null ){

		$atts = shortcode_atts( array(
			'taxonomy'       => 'category',
			'orderby'        => 'name',
			'order'          => 'ASC',
			'hide_empty'     => 0,
			'include'        => '',
			'exclude'        => '',
			'number'         => 0,
			'offset'         => 0,
			'name'           => '',
			'slug'           => '',
			'hierarchical'   => true,
			'child_of'       => 0,
			'parent'         => '',
			'include_parent' => 1,
		), $atts, 'mc_terms' );

		// Unset empty optional args
		$optional_args = array( 'include', 'exclude', 'name', 'slug', 'parent' );

		foreach( $optional_args as $optional_arg ) {
			if ( empty ( $atts[ $optional_arg ] ) ) {
				unset( $atts[ $optional_arg ] );
			}
		}

		// Check for comma separated and convert into arrays
		$comma_separated_args = array( 'taxonomy', 'include', 'exclude', 'name', 'slug' );

		foreach ( $comma_separated_args as $comma_separated_arg ) {
			if ( !empty( $atts[ $comma_separated_arg ] ) ) {
				$atts[$comma_separated_arg] = explode( ',', $atts[$comma_separated_arg] );
			}
		}

		//Cast int or number
		$int_args = array( 'hide_empty', 'number', 'offset', 'hierarchical', 'child_of', 'include_parent', 'parent' );

		foreach ( $int_args as $int_arg ) {
			if ( !empty( $atts[ $int_arg ] ) ) {
				$atts[ $int_arg ] = (int) $atts[ $int_arg ];
			}
		}

		$terms = get_terms( $atts );

		$html = '';

		foreach ( $terms as $term ) {
			$html .= '<li><a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a></li>';
		}

		if ( $atts['include_parent'] && $atts['child_of'] ) {
			$parent_term = get_term( $atts['child_of'] );
			if ( ! is_wp_error( $parent_term ) ) {
				$parent_term_item = '<h3><a href="' . esc_url( get_term_link( $parent_term ) ) . '">' . $parent_term->name . '</a></h3>';
				$html = $parent_term_item . $html;
			}
		}

		if ( ! empty( $html ) ) {
			$html = '<ul>' . $html . '</ul>';
		}

	    return $html;
	}

	add_shortcode( 'mc_terms' , 'mc_vc_terms' );

endif;