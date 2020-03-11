<?php

if ( !function_exists( 'shortcode_mc_banner' ) ):

function shortcode_mc_banner( $atts, $content = null ){

	extract(shortcode_atts(array(
		'banner_image'           => '',
		'title'                  => '#',
		'subtitle'               => '',
		'banner_link'            => '',
		'banner_link_target'     => '_self',
		'banner_hover_animation' => '',
		'banner_text_position'   => '',
		'el_class'               => ''
    ), $atts));

    $element = 'mc_banner';

    $css_class = trim( $element . ' banner ' . $el_class . ' ' . $banner_text_position );

	$banner = '';
	$banner .= "\n\t" . '<div class="' . $css_class . '">';
	$banner .= "\n\t\t" . '<a href="' . $banner_link . '" target="' . $banner_link_target . '">';

	if( !empty( $title ) ) :
	$banner .= "\n\t\t\t" . '<div class="banner-text">';
	$banner .= "\n\t\t\t\t" . '<h3 class="banner-title">' . $title . '</h3>';
	
		if ( !empty( $subtitle ) ){
			$banner .= "\n\t\t\t\t" . '<span class="tagline">' . $subtitle . '</span>';
		}
	
	$banner .= "\n\t\t\t" . '</div>';
	endif;

	$banner .= "\n\t\t\t" . wp_get_attachment_image( $banner_image, 'full', false, array( 'data-hover' => 'animate', 'data-animation' => $banner_hover_animation ) );

	$banner .= "\n\t\t" . '</a>';
	$banner .= "\n\t" . '</div>';

	return $banner;
}

add_shortcode( 'mc_banner' , 'shortcode_mc_banner' );
endif;