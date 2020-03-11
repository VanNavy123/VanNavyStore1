<?php
/**
 * Functions used in the footer
 */

if ( ! function_exists( 'mc_display_footer_social_links' ) ) {
	/**
	 * Display social links in the footer
	 */
	function mc_display_footer_social_links() {
		$social_networks 		= apply_filters( 'mc_set_social_networks', mc_get_social_networks() );
		$social_links_output 	= '';
		$social_link_html		= apply_filters( 'mc_footer_social_link_html', '<a class="%1$s" href="%2$s"></a>' );
		
		foreach ( $social_networks as $social_network ) {
			if ( isset( $social_network[ 'link' ] ) && !empty( $social_network[ 'link' ] ) ) {
				$social_links_output .= sprintf( '<li>' . $social_link_html . '</li>', $social_network[ 'icon' ], $social_network[ 'link' ] );
			}
		}

		if ( !empty ( $social_links_output ) ) {
			$social_links_output = '<div class="social-icons">
										<h3>' . apply_filters( 'media_center_get_in_touch_text', __( 'Get in touch', 'mediacenter' ) ) . '</h3>
										<ul>' . $social_links_output . '</ul>
									</div>';
			echo apply_filters( 'mc_footer_social_links_html', $social_links_output );
		}
	}
}

if ( ! function_exists( 'has_media_center_mobile_footer' ) ) {
	/**
	 * Displays HandHeld Footer
	 */
	function has_media_center_mobile_footer() {
		return apply_filters( 'has_media_center_mobile_footer', true );
	}
}

if ( ! function_exists( 'mc_handheld_footer_bar' ) ) {
	/**
	 * Display a menu intended for use on handheld devices
	 *
	 * @since 2.4.0
	 */
	function mc_handheld_footer_bar() {
		$links = array(
			'my-account' => array(
				'priority' => 10,
				'callback' => 'mc_handheld_footer_bar_account_link',
			),
			'search'     => array(
				'priority' => 20,
				'callback' => 'mc_handheld_footer_bar_search',
			),
			'cart'       => array(
				'priority' => 30,
				'callback' => 'mc_handheld_footer_bar_cart_link',
			)
		);
		if ( ! function_exists( 'wc_get_page_id' ) || wc_get_page_id( 'myaccount' ) === -1 ) {
			unset( $links['my-account'] );
		}
		if ( ! function_exists( 'wc_get_page_id' ) || wc_get_page_id( 'cart' ) === -1 || mc_is_catalog_mode_enabled() == true ) {
			unset( $links['cart'] );
		}

		if ( is_yith_wcwl_activated() ) {
			$links['wishlist'] = array(
				'priority' => 40,
				'callback' => 'mc_handheld_footer_bar_wishlist_link',
			);
		}

		if( is_yith_woocompare_activated() ) {
			$links['compare'] = array(
				'priority' => 50,
				'callback' => 'mc_handheld_footer_bar_compare_link',
			);
		}

		$links = apply_filters( 'mc_handheld_footer_bar_links', $links );
		if( ! empty( $links ) && has_media_center_mobile_footer() ) {
			?>
			<div class="mc-handheld-footer-bar hidden-md hidden-lg">
				<ul class="columns-<?php echo count( $links ); ?>">
					<?php foreach ( $links as $key => $link ) : ?>
						<li class="<?php echo esc_attr( $key ); ?>">
							<?php
							if ( $link['callback'] ) {
								call_user_func( $link['callback'], $key, $link );
							}
							?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'mc_handheld_footer_bar_search' ) ) {
	/**
	 * The search callback function for the handheld footer bar
	 *
	 * @since 2.4.0
	 */
	function mc_handheld_footer_bar_search() {
		?>
		<a class="has-icon" href="#">
			<i class="fa fa-search"></i><span class="sr-only"><?php echo esc_html__( 'Search', 'mediacenter' );?></span>
		</a>
		<?php
		mc_product_search();
	}
}

if ( ! function_exists( 'mc_handheld_footer_bar_cart_link' ) ) {
	/**
	 * The cart callback function for the handheld footer bar
	 *
	 * @since 2.4.0
	 */
	function mc_handheld_footer_bar_cart_link() {
		if ( is_woocommerce_activated() ) {
		?>
			<a class="footer-cart-contents has-icon" href="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'mediacenter' ); ?>">
				<i class="fa fa-shopping-cart"></i><span class="cart-items-count count"><?php echo wp_kses_data( WC()->cart->get_cart_contents_count() );?></span>
			</a>
		<?php
		}
	}
}

if ( ! function_exists( 'mc_handheld_footer_bar_account_link' ) ) {
	/**
	 * The account callback function for the handheld footer bar
	 *
	 * @since 2.4.0
	 */
	function mc_handheld_footer_bar_account_link() {
		if ( is_woocommerce_activated() ) {
		?>
			<a class="has-icon" href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>">
				<i class="fa fa-user"></i><span class="sr-only"><?php echo esc_html__( 'My Account', 'mediacenter' );?></span>
			</a>
		<?php
		}
	}
}

if ( ! function_exists( 'mc_product_search' ) ) {
	/**
	 * Display Product Search
	 *
	 * @since  2.4.0
	 * @uses  is_woocommerce_activated() check if WooCommerce is activated
	 * @return void
	 */
	function mc_product_search() {
		if ( is_woocommerce_activated() ) { ?>
			<div class="site-search">
				<?php the_widget( 'WC_Widget_Product_Search', 'title=' ); ?>
			</div>
		<?php
		}
	}
}