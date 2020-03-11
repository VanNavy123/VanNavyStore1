<?php
/**
 * Search Bar
 *
 * @author      Ibrahim Ibn Dawood
 * @package     Templates/Sections
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( class_exists('Ecwid_Widget_Search') ) {
	the_widget( 'Ecwid_Widget_Search' );
}