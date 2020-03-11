<?php

function media_center_display_add_to_wishlist_button(){

	global $yith_wcwl, $product;

    $product_id         = mc_wc_get_product_id( $product );

	$label_option       = get_option( 'yith_wcwl_add_to_wishlist_text' );
    $icon_option        = get_option( 'yith_wcwl_add_to_wishlist_icon' ) != 'none' ? '<i class="fa ' . get_option( 'yith_wcwl_add_to_wishlist_icon' ) . '"></i>' : '';

    $localize_label     = function_exists( 'icl_translate' ) ? icl_translate( 'Plugins', 'plugin_yit_wishlist_button', $label_option ) : $label_option;

    $label              = apply_filters( 'yith_wcwl_button_label', $localize_label );
    $icon               = apply_filters( 'yith_wcwl_button_icon', $icon_option );

    $classes            = apply_filters( 'yith_wcwl_add_to_wishlist_button_classes', get_option( 'yith_wcwl_use_button' ) == 'yes' ? 'add_to_wishlist single_add_to_wishlist button alt' : 'add_to_wishlist' );

    $wishlist_url       = $yith_wcwl->get_wishlist_url();
    $default_wishlists  = is_user_logged_in() ? $yith_wcwl->get_wishlists( array( 'is_default' => true ) ) : false;

    if( ! empty( $default_wishlists ) ){
        $default_wishlist = $default_wishlists[0]['ID'];
    }
    else{
        $default_wishlist = false;
    }

    $exists = $yith_wcwl->is_product_in_wishlist( $product_id, $default_wishlist );
    $product_type = mc_wc_get_product_type( $product );

	$add_to_wishlist_url = esc_url( add_query_arg( 'add_to_wishlist', $product_id) ) ;
	$wishlist_url = $yith_wcwl->get_wishlist_url();
	$product_type = mc_wc_get_product_type( $product );
	$exists = $yith_wcwl->is_product_in_wishlist( $product_id );
	$label = apply_filters( 'yith_wcwl_button_label', get_option( 'yith_wcwl_add_to_wishlist_text' ) );
    $browse_wishlist = get_option( 'yith_wcwl_browse_wishlist_text' );
    $already_in_wishlist = get_option( 'yith_wcwl_already_in_wishlist_text' );
    $product_added = get_option( 'yith_wcwl_product_added_text' );

	$html  = '<div class="yith-wcwl-add-to-wishlist">';
    $html .= '<div class="yith-wcwl-add-button';  // the class attribute is closed in the next row

    $html .= $exists ? ' hide" style="display:none;"' : ' show"';

    $html .= '><a href="' . $add_to_wishlist_url . '" data-product-id="' . $product_id . '" data-product-type="' . $product_type . '" class="add_to_wishlist btn-add-to-wishlist"><i class="fa fa-heart"></i>' . $label . '</a>';
    $html .= '</div>';

    $html .= '<div class="yith-wcwl-wishlistaddedbrowse hide" style="display:none;"><a href="' . esc_url( $wishlist_url ) . '" class="btn-add-to-wishlist"><i class="fa fa-check"></i> ' . $product_added . '</a></div>';
    $html .= '<div class="yith-wcwl-wishlistexistsbrowse ' . ( $exists ? 'show' : 'hide' ) . '" style="display:' . ( $exists ? 'block' : 'none' ) . '"><a href="' . esc_url( $wishlist_url ) . '" class="btn-add-to-wishlist"><i class="fa-th-list fa"></i> ' . $browse_wishlist . '</a></div>';
    $html .= '<div class="yith-wcwl-wishlistaddresponse"></div>';

    $html .= '</div>';

	return apply_filters( 'mc_yith_wcwl_add_to_wishlisth_button_html', $html, $wishlist_url, $product_type, $exists );
}

add_shortcode( 'mc_yith_wcwl_add_to_wishlist', 'media_center_display_add_to_wishlist_button' );