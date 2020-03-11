<?php

if ( !function_exists( 'shortcode_mc_vertical_menu' ) ):

function mc_wp_list_categories( $args ) {
	$args['taxonomy'] = 'product_category';
	return wp_list_categories( $args );
}

function shortcode_mc_vertical_menu( $atts ){
	global $mc_plugin_dir;

	$vertical_menu = '';

	extract(shortcode_atts(array(
		'title'                  => __( 'All Departments' , 'mc-ext' ),
		'icon_class'             => 'fa-list',
		'menu'                   => '',
		'el_class'               => '',
		'dropdown_trigger'		 => 'click',
		'dropdown_animation'	 => 'none',
    ), $atts));

    $element = 'mc_vertical_menu';

    $extra_class = trim( $element . ' ' . $el_class ) ;

    if( empty ( $menu ) ) {

    	if( ! class_exists( 'wp_bootstrap_categorieswalker') ) {
    		require_once $mc_plugin_dir . 'modules/js_composer/include/classes/wp_bootstrap_categorieswalker.php';
    	}

    	$side_menu = wp_list_categories( 
    		array(
    			'container'				=> false,
    			'menu_class'			=> 'nav',
	            'echo' 					=> false,
	            'taxonomy'				=> 'product_cat',
	            'title_li'				=> '',
	            'hide_empty'			=> 0,
	            'walker'				=> new wp_bootstrap_categorieswalker(),
	            'dropdown_animation' 	=> $dropdown_animation,
	            'dropdown_trigger'		=> $dropdown_trigger
			)
		);
		$side_menu = str_replace( 'children', 'dropdown-submenu', $side_menu );
    	$side_menu = '<ul class="nav">' . $side_menu . '</ul>';
    } else {
    	
    	if( ! class_exists( 'wp_bootstrap_categorieswalker') ) {
    		require_once $mc_plugin_dir . 'modules/js_composer/include/classes/wp_bootstrap_navwalker.php';
    	}
    	
    	$side_menu = wp_nav_menu( 
	    	array(
				'menu' 					=> $menu,
	            'container' 			=> false, 
	            'menu_class' 			=> 'nav',
	            'walker' 				=> new wp_bootstrap_navwalker(),
	            'echo' 					=> false,
	            'dropdown_animation' 	=> $dropdown_animation,
	            'dropdown_trigger'		=> $dropdown_trigger
	        )
	    );
    }

	

    $sidemenu_holder_class = 'sidemenu-holder';

    if( $dropdown_animation != 'none' ){
    	$sidemenu_holder_class .= ' animate-dropdown';
    	$custom_css = 
    		'.sidemenu-holder .open > .dropdown-menu,
			.sidemenu-holder .open > .dropdown-menu > .dropdown-submenu > .dropdown-menu {
				animation-name: ' . $dropdown_animation . '; 
			}';
		$vertical_menu .= '<style type="text/css">' . $custom_css . '</style>';

    }

	$vertical_menu .= '<div class="' . $sidemenu_holder_class . '">' ;
	$vertical_menu .= "\t" . '<div class="side-menu">' ;
	$vertical_menu .= "\t\t" . '<div class="head">' ;
	$vertical_menu .= "\t\t\t" . '<i class="fa ' . $icon_class . '"></i> ' . $title ;
	$vertical_menu .= "\t\t" . '</div>' ;
	$vertical_menu .= "\t\t" . '<nav class="yamm megamenu-horizontal" role="navigation">';
	$vertical_menu .= "\t\t\t" . $side_menu ;
	$vertical_menu .= "\t\t" . '</nav>';
	$vertical_menu .= "\t" . '</div>' ;
	$vertical_menu .= '</div>' ;

	$vertical_menu = '<div class="' . $extra_class . '">' . $vertical_menu . '</div>' ;

	return $vertical_menu;
}

add_shortcode( 'mc_vertical_menu' , 'shortcode_mc_vertical_menu' );
endif;