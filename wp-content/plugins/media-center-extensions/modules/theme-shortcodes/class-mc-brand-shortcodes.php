<?php
/**
 * MC_Brand_Shortcodes class.
 *
 * @class 		MC_Brand_Shortcodes
 * @package		MediaCenter/Framework
 * @category	Class
 * @author 		Ibrahim Ibn Dawood
 */
class MC_Brand_Shortcodes {

	/**
	 * Init shortcodes
	 */
	public static function init() {
		// Define shortcodes
		$shortcodes = array(
			'brands'                   => __CLASS__ . '::brands',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}
	}

	/**
	 * Shortcode Wrapper
	 *
	 * @param mixed $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public static function shortcode_wrapper(
		$function,
		$atts    = array(),
		$wrapper = array(
			'class'  => 'woocommerce',
			'before' => null,
			'after'  => null
		)
	) {
		ob_start();

		$before 	= empty( $wrapper['before'] ) ? '<div class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
		$after 		= empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];

		echo $before;
		call_user_func( $function, $atts );
		echo $after;

		return ob_get_clean();
	}


	/**
	 * List multiple brands shortcode
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function brands( $atts ) {
		global $woocommerce_loop;

		if ( empty( $atts ) ) return '';

		extract( shortcode_atts( array(
			'orderby'   => 'title',
			'columns'   => '6',
			'order'     => 'asc',
			'per_page'  => '6',
			'carousel'	=> 'true',
		), $atts ) );

		$brand_taxonomy = function_exists( 'mc_get_brands_taxonomy' ) ? mc_get_brands_taxonomy() : '';

		if( ! empty( $brand_taxonomy ) && get_option( 'mediacenter_tax_brand_migrated' ) ) {
			$taxonomy = $brand_taxonomy;
		} else {
			$taxonomy = 'product_brand';
		}

		$brands = get_categories( array( 'hide_empty' => 0, 'taxonomy' => $taxonomy, 'orderby' => $orderby, 'order' => $order, 'number' => $per_page ) );

		$output = '';
		$columns = intval( $columns ); 
		$columns = 12/$columns;

		foreach ( $brands as $brand ) {
			$thumbnail_id = get_woocommerce_term_meta( $brand->term_id, 'thumbnail_id' ) ;
			$image_src = wp_get_attachment_image_src( $thumbnail_id, 'full' ) ;
			$brand_link = get_term_link( $brand, $taxonomy ) ;
			$brand_item = '<div class="brand-item">';
			$brand_item .= "\t" . '<a href="' . $brand_link . '"><img alt="' .  $brand->cat_name . '" src="' . $image_src[0] . '" width="144" height="36" /></a>';
			$brand_item .= '</div>';
			if( $carousel === 'false' ){
				$output .= '<div class="col-xs-12 col-sm-' . $columns .'">' . $brand_item . '</div>';
			}else {
				$output .= $brand_item;
			}
		}

		return $output;
	}
}

MC_Brand_Shortcodes::init();
