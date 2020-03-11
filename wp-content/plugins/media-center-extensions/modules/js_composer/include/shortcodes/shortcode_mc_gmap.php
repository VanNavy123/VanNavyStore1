<?php

if ( !function_exists( 'shortcode_mc_gmap' ) ):

function shortcode_mc_gmap( $atts, $content = null ){

	extract(shortcode_atts(array(
		'api_key' 			 => '',
		'lat'                => '',
		'lon'                => '',
		'height'             => '',
		'zoom'               => '',
		'style'              => '',
		'add_get_directions' => '',
		'el_class'           => '',
		'map_min_height'	 => '460px'
    ), $atts));

    $element = 'mc_gmap';

    $css_class = trim( $element . ' banner ' . $el_class );
    $gmapID = uniqid('gmap-');

    if( $add_get_directions == 'no' ) {
    	$display_get_directions_form = false;
    } else {
    	$display_get_directions_form = true;
    }

    $gmapParams = array(
    	'latitude'  => $lat,
    	'longitude' => $lon,
    	'height'    => $height,
    	'zoom'      => $zoom,
    	'gmapID'    => $gmapID,
	);

	$gmap_api_src = '//maps.google.com/maps/api/js?key=' . $api_key . '&sensor=false&amp;language=en';

    wp_enqueue_script( 'media_center-google-maps-api', $gmap_api_src, array(), false, true );
    wp_enqueue_script( 'media_center-gmap3', get_template_directory_uri() . '/assets/js/gmap3.min.js', array( 'media_center-google-maps-api' ), false , true );
    wp_localize_script( 'media_center-theme-scripts', 'gmapParams', $gmapParams );

    $gmapHTML = '' ;
    $gmapHTML .= "\t" . '<section class="google-map map-holder">' ;
	$gmapHTML .= "\t\t" . '<div id="' . $gmapID . '" class="map center" style="min-height:' . $map_min_height . '"></div>' ;

	if( $display_get_directions_form ) :
		$gmapHTML .= "\t" . '<div class="get-direction">' ;
		$gmapHTML .= "\t\t\t" . '<div class="row">' ;
		$gmapHTML .= "\t\t\t\t" . '<div class="center-block col-lg-10">' ;
		$gmapHTML .= "\t\t\t\t\t" . '<div class="input-group">' ;
		$gmapHTML .= "\t\t\t\t\t\t" . '<input type="text" id="starting-point" class="le-input input-lg form-control" placeholder="'. __( 'Enter Your Starting Point', 'mc-ext' ). '">' ;
		$gmapHTML .= "\t\t\t\t\t\t" . '<span class="input-group-btn">' ;
		$gmapHTML .= "\t\t\t\t\t\t\t" . '<button id="get-direction" class="btn btn-lg le-button" type="button">'. __( 'Get Directions', 'mc-ext') . '</button>' ;
		$gmapHTML .= "\t\t\t\t\t\t" . '</span>' ;
		$gmapHTML .= "\t\t\t\t\t" . '</div><!-- /input-group -->' ;
		$gmapHTML .= "\t\t\t\t" . '</div><!-- /.col-lg-6 -->' ;
		$gmapHTML .= "\t\t\t" . '</div><!-- /.row -->' ;
		$gmapHTML .= "\t" . '</div>' ;
	endif;

	$gmapHTML .= "\t" . '</section>';
    return $gmapHTML ;
}

add_shortcode( 'mc_gmap' , 'shortcode_mc_gmap' );
endif;