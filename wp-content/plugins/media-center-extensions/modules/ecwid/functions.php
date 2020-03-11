<?php

if( ! function_exists( 'mc_ext_ecwid_api' ) ) {
	function mc_ext_ecwid_api() {
		if( ! class_exists( 'EcwidProductApi' ) ) {
			include_once WP_PLUGIN_DIR . '/ecwid-shopping-cart/lib/ecwid_product_api.php';
		}

		$store_id = get_ecwid_store_id();
		// $page_url = get_page_link();
		return new EcwidProductApi( $store_id );
	}
}

if( ! function_exists( 'mc_ext_ecwid_product_html' ) ) {
	function mc_ext_ecwid_product_html( $product = array() ) {

		if( ! empty( $product ) ) {
			$ecwid_api = mc_ext_ecwid_api();
			$product = $ecwid_api->get_product( $product['id'] );
			$profile = $ecwid_api->get_profile();
			
			ob_start();
			?>
			<div class="product">
				<div class="product-inner">		
					<a href="<?php echo ecwid_get_product_url( array( 'id' => $product['id'], 'url' => $product['url'] ) ); ?>">
						<div class="ecwid-favorite">
							<span><?php echo $product['favorites']['displayedCount']; ?></span>
						</div>

						<div class="product-thumbnail-wrapper">
							<img class="attachment-shop_catalog wp-post-image" width="246" height="186" src="<?php echo $product['thumbnailUrl'];?>" alt="">
						</div>

						

						<div class="title-area">
							<h3><?php echo $product['name'];?></h3>
						</div>

						<span class="price">
							<span class="mc-price-wrapper">
								<span class="amount"><?php echo $profile['currencyPrefix'] . mc_ext_ecwid_product_tax_price( $product['price'], $product['taxes'] ) . $profile['currencySuffix']; ?></span>
							</span>
						</span>					
					</a>				
				</div>
			</div>
			<?php
			$html = ob_get_clean();
			return $html;
		}

		return;
	}
}

if( ! function_exists( 'mc_ext_ecwid_product_tax_price' ) ) {
	function mc_ext_ecwid_product_tax_price( $price = 0, $tax_arr = array() ) {
		
		$new_price = $price;

		$tax_percetage = 0;
		if( ! empty( $tax_arr ) ) {
			foreach ( $tax_arr as $key => $tax ) {
				if( $tax['value'] > 0 ) {
					$tax_percetage += $tax['value'];
				}
			}
		}

		if( $tax_percetage > 0 ) {
			$tax_amount = $price * ( $tax_percetage / 100 );
			$new_price = round( $price + $tax_amount );
		}

		return $new_price;
	}
}