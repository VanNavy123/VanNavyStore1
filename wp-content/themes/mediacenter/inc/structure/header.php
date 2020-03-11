<?php
/**
 * Functions used in the header
 */

if( ! function_exists( 'media_center_header_style' ) ) {
	function media_center_header_style() {
		return apply_filters( 'mc_get_header_style', 'header-style-1' );
	}
}

if( ! function_exists( 'media_center_site_favicon' ) ) {
	function media_center_site_favicon() {
		$favicon_url = apply_filters( 'mc_site_favicon_url', get_template_directory_uri() . '/assets/images/favicon.ico' );
		echo '<link rel="shortcut icon" href="' . esc_url( $favicon_url ) . '">';
	}
}

if( ! function_exists( 'mc_output_search_bar' ) ) {
	function mc_output_search_bar() {
		if( defined( 'ECWID_DEMO_STORE_ID' ) ) {
            mc_get_template( 'ecwid/mc-search-bar.php' );
        } else {
            mc_get_template( 'header/mc-search-bar.php' );
        }
	}
}

if ( ! function_exists( 'media_center_add_data_hover_attribute' ) ) {
	function media_center_add_data_hover_attribute( $atts, $item, $args, $depth ) {
		// If item has_children add atts to a.
		$should_add_toggle = false;

		if( $args->theme_location == 'departments' ) {
			$should_add_toggle = false;
		} else {
			$should_add_toggle = $depth < 1;
		}

		if ( $args->has_children && $should_add_toggle ) {

			$dropdown_trigger = apply_filters( 'mc_' . $args->theme_location . '_dropdown_trigger', 'click', $args->theme_location );
			if( isset( $args->dropdown_trigger) && ! empty( $args->dropdown_trigger ) ) {
				$dropdown_trigger = $args->dropdown_trigger;
			}
			if( $dropdown_trigger == 'hover' ) {
				$atts['data-hover'] = 'dropdown';
				
				if( isset( $atts['data-toggle'] ) ) {
					unset( $atts['data-toggle'] );
				}
			}
		}
		
		return $atts;
	}
}

if ( ! function_exists( 'media_center_header_logo' ) ) {
	/**
	 * Displays Header Logo
	 */
	function media_center_header_logo() {
		media_center_display_header_part( 'logo' );
	}
}

if ( ! function_exists( 'has_media_center_mobile_header' ) ) {
	/**
	 * Displays HandHeld Header
	 */
	function has_media_center_mobile_header() {
		return apply_filters( 'has_media_center_mobile_header', true );
	}
}

if ( ! function_exists( 'media_center_handheld_header' ) ) {
	/**
	 * Displays HandHeld Header
	 */
	function media_center_handheld_header() {
		if( has_media_center_mobile_header() ) : ?>
			<div class="container hidden-md hidden-lg">
				<div class="handheld-header">
					<?php
					/**
					 * @hooked media_center_header_logo - 10
					 * @hooked media_center_handheld_nav - 20
					 */
					do_action( 'media_center_header_handheld' ); ?>
				</div>
			</div>
		<?php endif;
	}
}

if ( ! function_exists( 'media_center_handheld_nav' ) ) {
	/**
	 * Displays HandHeld Navigation
	 */
	function media_center_handheld_nav() {
		?>
		<div class="handheld-navigation-wrapper">
			<div class="handheld-navbar-toggle-buttons clearfix">
				<button class="navbar-toggler navbar-toggle-hamburger pull-right flip" type="button">
					<i class="fa fa-bars" aria-hidden="true"></i>
				</button>
				<button class="navbar-toggler navbar-toggle-close pull-right flip" type="button">
					<i class="fa fa-times"></i>
				</button>
			</div>

			<div class="handheld-navigation" id="default-hh-header">
				<span class="mchm-close"><?php _e( 'Close', 'mediacenter' ); ?></span>
				<?php
					wp_nav_menu( array(
						'theme_location'	=> 'hand-held',
						'container'			=> false,
						'menu_class'		=> 'nav nav-inline yamm',
						'fallback_cb'		=> 'media_center_handheld_nav_fallback',
						'walker'			=> new wp_bootstrap_navwalker()
					) );
				?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'media_center_handheld_nav_fallback' ) ) {
	/**
	 * Displays HandHeld Navigation Fallback
	 */
	function media_center_handheld_nav_fallback() {
		wp_nav_menu( array(
			'theme_location'	=> 'departments',
			'container'			=> false,
			'menu_class'		=> 'nav nav-inline yamm',
			'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
			'walker'            => new wp_bootstrap_navwalker(),
		) );
	}
}
