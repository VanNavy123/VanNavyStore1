<?php

if ( !function_exists( 'shortcode_mc_ecwid_products_carousel' ) ):


function shortcode_mc_ecwid_products_carousel( $atts, $content = null ){

	extract(shortcode_atts(array(
		'title' 			=> '',
		'limit' 			=> '12',
		'is_category'		=> false,
		'category_id'		=> '',
		'columns' 			=> '4',
		'autoplay'			=> 'no',
		'el_class' 			=> ''
	), $atts));

	$ecwid_api = mc_ext_ecwid_api();
	if( $is_category && ! empty( $category_id ) ) {
		$products = $ecwid_api->get_products_by_category_id( $category_id );
		$products = array_slice( $products, 0, $limit );
	} else {
		$products = $ecwid_api->get_random_products( $limit );
	}
	// echo '<pre>'.print_r( $products,1 ).'</pre>';exit;

	$carouselID = uniqid();

	$products_html = '';
	$products_html .='<div data-autoplay="' . esc_attr( $autoplay ) . '" id="' . esc_attr( $carouselID ) . '" class="products products-carousel-'. esc_attr( $columns ) .' enable-hover owl-carousel owl-theme owl-loaded">';
	foreach ( $products as $product ) {
		$products_html .= mc_ext_ecwid_product_html( $product );
	}
	$products_html .= '</div>';

	$products_html = wpb_js_remove_wpautop( $products_html );

	$element = 'mc_products_carousel';

    $css_class = trim( $element . ' ' . $el_class );

	$output = '';
	
	$output .= "\n\t" . '<div class="' . $css_class . '">';

	$output .= "\n\t\t" . '<section id="section-' . $carouselID . '" class="inner-top-xs">';
	$output .= "\n\t\t\t" . '<div class="carousel-holder hover">';
	$output .= "\n\t\t\t\t" . '<div class="title-nav">';
	$output .= "\n\t\t\t\t\t" . '<h2 class="h1">' . $title . '</h2>';
	$output .= "\n\t\t\t\t\t" . '<div class="nav-holder">';
	if( is_rtl() ) {
		$output .= "\n\t\t\t\t\t\t" . '<a href="#prev" data-target="#' . $carouselID . '" class="slider-prev btn-prev fa fa-angle-right"></a>';
		$output .= "\n\t\t\t\t\t\t" . '<a href="#next" data-target="#' . $carouselID . '" class="slider-next btn-next fa fa-angle-left"></a>';
	} else {
		$output .= "\n\t\t\t\t\t\t" . '<a href="#prev" data-target="#' . $carouselID . '" class="slider-prev btn-prev fa fa-angle-left"></a>';
		$output .= "\n\t\t\t\t\t\t" . '<a href="#next" data-target="#' . $carouselID . '" class="slider-next btn-next fa fa-angle-right"></a>';
	}
	$output .= "\n\t\t\t\t\t" . '</div>';
	$output .= "\n\t\t\t\t" . '</div>';
	$output .= "\n\t\t\t" . $products_html; 
	$output .= "\n\t\t\t" . '</div>';
	$output .= "\n\t\t" . '</section>';
	
	$output .= "\n\t" . '</div>';

	return $output;

}

add_shortcode( 'mc_ecwid_products_carousel' , 'shortcode_mc_ecwid_products_carousel' );
endif;