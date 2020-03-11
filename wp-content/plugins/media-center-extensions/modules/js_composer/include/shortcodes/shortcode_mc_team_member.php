<?php

if ( !function_exists( 'shortcode_mc_team_member' ) ):

function shortcode_mc_team_member( $atts, $content = null ){

	extract(shortcode_atts(array(
		'profile_pic'   => '',
		'name'          => '',
		'designation'   => '',
		'link'			=> '',
		'display_style'	=> '',
		'el_class'      => ''
    ), $atts));

    $element = 'mc-team-member';

    $css_class = trim( $element . ' team-member ' . $el_class );

    $team_member = '';
	$team_member .= "\t" . '<div class="' . $css_class . '">';
	$team_member .= "\t\t" . wp_get_attachment_image( $profile_pic, 'full', false, array( 'class' => 'profile-pic img-responsive' ) ) ;
	$team_member .= "\t\t" . '<div class="profile">';
	$team_member .= "\t\t\t" . '<h3>' . $name . ' <small class="designation">' . $designation . '</small></h3>';
	$team_member .= "\t\t" . '</div><!-- /.profile -->';
	$team_member .= "\t"  . '</div><!-- /.team-member -->';

	if( !empty( $link ) ){
		$team_member = '<a class="team-member-link" href="' . $link . '">' . $team_member . '</a>';
	}

	if( $display_style == '' ){
		$display_style = 'square';
	}

	return '<div class="' . $display_style. '">' . $team_member . '</div>' ;
}

add_shortcode( 'mc_team_member' , 'shortcode_mc_team_member' );
endif;