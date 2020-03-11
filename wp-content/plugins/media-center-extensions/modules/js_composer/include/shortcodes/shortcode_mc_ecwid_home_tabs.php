<?php

if ( !function_exists( 'shortcode_mc_ecwid_home_tabs' ) ):


function shortcode_mc_ecwid_home_tabs( $atts, $content = null ) {

	extract(shortcode_atts(array(
		'title_tab_1'		=> '',
		'content_sc_tab_1'	=> '',
		'title_tab_2'		=> '',
		'content_sc_tab_2'	=> '',
		'title_tab_3'		=> '',
		'content_sc_tab_3'	=> '',
	), $atts));

	if( empty( $title_tab_1 ) && empty( $title_tab_2 ) && empty( $title_tab_3 ) ) {
		return;
	}

	$store_id = get_ecwid_store_id();

	if( ! class_exists( 'EcwidProductApi' ) ) {
		include_once WP_PLUGIN_DIR . '/ecwid-shopping-cart/lib/ecwid_product_api.php';
	}

	$ecwid_api = new EcwidProductApi( $store_id );

	$products_tab_contents = array();
	if( ! empty( $content_sc_tab_1 ) ) {
		$products_tab_content_1 = $ecwid_api->get_products_by_category_id( $content_sc_tab_1 );
		$products_tab_contents[] = array_slice( $products_tab_content_1, 0, 4 );
	} else {
		$products_tab_contents[] = $ecwid_api->get_random_products( 4 );
	}
	if( ! empty( $content_sc_tab_2 ) ) {
		$products_tab_content_2 = $ecwid_api->get_products_by_category_id( $content_sc_tab_2 );
		$products_tab_contents[] = array_slice( $products_tab_content_2, 0, 4 );
	} else {
		$products_tab_contents[] = $ecwid_api->get_random_products( 4 );
	}
	if( ! empty( $content_sc_tab_3 ) ) {
		$products_tab_content_3 = $ecwid_api->get_products_by_category_id( $content_sc_tab_3 );
		$products_tab_contents[] = array_slice( $products_tab_content_3, 0, 4 );
	} else {
		$products_tab_contents[] = $ecwid_api->get_random_products( 4 );
	}

	$tab_content = array();
	foreach ( $products_tab_contents as $products_tab_content ) {
		$product_content = '';
		$product_content .='<div class="products columns-4">';
		foreach ( $products_tab_content as $product ) {
			$product_content .= mc_ext_ecwid_product_html( $product );
		}
		$product_content .= '</div>';
		$tab_content[] = $product_content;
	}

	ob_start();
	?>
	<div class="inner-top-xs inner-bottom-sm home-page-tabs ecwid-home-page-tabs">
		<div class="tab-holder">
			<ul class="nav nav-tabs">
				<?php if( !empty( $title_tab_1 ) ) : ?>
				<li class="active"><a data-toggle="tab" href="#home-page-tab-1"><?php echo $title_tab_1; ?></a></li>
				<?php endif ; ?>
				<?php if( !empty( $title_tab_2 ) ) : ?>
				<li><a data-toggle="tab" href="#home-page-tab-2"><?php echo $title_tab_2; ?></a></li>
				<?php endif; ?>
				<?php if( !empty( $title_tab_3 ) ) : ?>
				<li><a data-toggle="tab" href="#home-page-tab-3"><?php echo $title_tab_3; ?></a></li>
				<?php endif; ?>
			</ul><!-- /.nav-tabs -->
			<div class="tab-content">
				<?php if( !empty( $tab_content[0] ) ) : ?>
				<div id="home-page-tab-1" class="tab-pane active"><?php echo $tab_content[0];?></div><!-- /.tab-pane -->
				<?php endif; ?>
				<?php if( !empty( $tab_content[1] ) ) : ?>
				<div id="home-page-tab-2" class="tab-pane"><?php echo $tab_content[1];?></div><!-- /.tab-pane -->
				<?php endif; ?>
				<?php if( !empty( $tab_content[2] ) ) : ?>
				<div id="home-page-tab-3" class="tab-pane"><?php echo $tab_content[2];?></div><!-- /.tab-pane -->
				<?php endif; ?>
			</div><!-- /.tab-content -->
		</div><!-- /.tab-holder -->
	</div><!-- /.home-page-tabs -->
	<?php

    $home_tabs = ob_get_clean();

    return $home_tabs;
}

add_shortcode( 'mc_ecwid_home_tabs' , 'shortcode_mc_ecwid_home_tabs' );
endif;