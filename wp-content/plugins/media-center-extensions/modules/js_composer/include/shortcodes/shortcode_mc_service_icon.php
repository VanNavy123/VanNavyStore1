<?php

if ( !function_exists( 'shortcode_mc_service_icon' ) ):

function shortcode_mc_service_icon( $atts, $content = null ){

	extract(shortcode_atts(array(
		'title'                  => '',
		'description'            => '',
		'icon_class'             => '',
		'el_class'               => '',
		'link'					 => '',
    ), $atts));

    $element = 'mc_service_icon';

    $css_class = trim( $element . ' service ' . $el_class );

    $icon = '<i class="fa '. esc_attr( $icon_class ) . '"></i>';

    $service_icon = '';

    $service_icon .= "\t" . '<div class="' . $css_class . '">' ;
	$service_icon .= "\t\t" . '<div class="service-icon primary-bg">' . $icon . '</div>' ;
	$service_icon .= "\t\t" . '<h3>' . $title . '</h3>' ;
	$service_icon .= "\t\t" . '<p>' . $description . '</p>' ;
	$service_icon .= "\t" . '</div>' ;

	if( !empty( $link ) ) {
    	$service_icon = '<a href="' . esc_attr( $link ) . '">' . $service_icon . '</a>';
    }

	return $service_icon;
}

add_shortcode( 'mc_service_icon' , 'shortcode_mc_service_icon' );
endif;