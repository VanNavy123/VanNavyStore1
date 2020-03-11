<?php
function media_center_product_comparison() {
	ob_start();
	if( class_exists( 'YITH_Woocompare_Frontend' ) ) {
		global $yith_woocompare;
		wc_get_template( 'compare.php', array( 
			'products' 			  => $yith_woocompare->obj->get_products_list(), 
			'fields' 			  => $yith_woocompare->obj->fields(),
	        'repeat_price' 		  => get_option( 'yith_woocompare_price_end' ),
	        'repeat_add_to_cart'  => get_option( 'yith_woocompare_add_to_cart_end' )
		) );
	}else{
		echo '<p class="alert alert-danger">' . __( 'You need to enable YITH Compare plugin for product comparison to work', 'mc-ext' ) . '</p>';
	}
	return ob_get_clean();
}

add_shortcode( 'mc_product_comparison', 'media_center_product_comparison' );

function media_center_add_to_compare_button( $product_id, $compare_text = '' ) {
	$btn = '';

	$compare_text = !empty($compare_text) ? $compare_text : __( 'Compare', 'mc-ext' );

	if ( !is_admin() && class_exists( 'YITH_Woocompare_Frontend' ) ) {
		global $yith_woocompare;
		$add_to_compare_url = $yith_woocompare->obj->add_product_url( $product_id );
		$btn = sprintf( '<a href="%s" class="%s" data-product_id="%d"><i class="fa fa-exchange"></i> %s</a>', $add_to_compare_url, 'btn-add-to-compare compare', $product_id, $compare_text );
	}
	return $btn;
}