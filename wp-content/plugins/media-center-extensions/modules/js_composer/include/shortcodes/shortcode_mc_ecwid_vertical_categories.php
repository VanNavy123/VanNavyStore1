<?php

if ( !function_exists( 'shortcode_mc_ecwid_vertical_categories' ) ):


function shortcode_mc_ecwid_vertical_categories( $atts, $content = null ){

	extract(shortcode_atts(array(
		'title'	=> ''
	), $atts));

	$output = '';
	ob_start();
	if( class_exists('EcwidVCategoriesWidget') ) {
		the_widget( 'EcwidVCategoriesWidget', array( 'title' => $title ) );
	}
	$output = ob_get_clean();

	return $output;

}

add_shortcode( 'mc_ecwid_vertical_categories' , 'shortcode_mc_ecwid_vertical_categories' );
endif;