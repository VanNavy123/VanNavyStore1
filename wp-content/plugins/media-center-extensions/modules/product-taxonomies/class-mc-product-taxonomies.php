<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Handles taxonomies for Media Center Theme
 *
 * @class 		MC_Product_Taxonomies
 * @version		1.0.6
 * @package		MediaCenter/Framework
 * @category	Class
 * @author 		Ibrahim Ibn Dawood
 */
class MC_Product_Taxonomies {

	/**
	 * Constructor
	 */
	public function __construct() {

		// hook into the init action and call create_book_taxonomies when it fires
		add_action( 'init', array( $this, 'register_product_taxonomies' ), 0 );

		add_action( 'admin_enqueue_scripts', array( $this, 'load_wp_media_files' ), 0 );

		// Brand/term ordering
		add_action( "create_term", array( $this, 'create_term' ), 5, 3 );
		add_action( "delete_term", array( $this, 'delete_term' ), 5 );

		// Add form
		add_action( 'product_brand_add_form_fields', array( $this, 'add_brand_fields' ) );
		add_action( 'product_brand_edit_form_fields', array( $this, 'edit_brand_fields' ), 10, 2 );
		add_action( 'created_term', array( $this, 'save_brand_fields' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'save_brand_fields' ), 10, 3 );

		add_action( 'product_label_add_form_fields', array( $this, 'add_label_fields' ) );
		add_action( 'product_label_edit_form_fields', array( $this, 'edit_label_fields' ), 10, 2 );
		add_action( 'created_term', array( $this, 'save_label_fields' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'save_label_fields' ), 10, 3 );

		// Add columns
		add_filter( 'manage_edit-product_brand_columns', array( $this, 'product_brand_columns' ) );
		add_filter( 'manage_product_brand_custom_column', array( $this, 'product_brand_column' ), 10, 3 );

		add_filter( 'manage_edit-product_label_columns', array( $this, 'product_label_columns' ) );
		add_filter( 'manage_product_label_custom_column', array( $this, 'product_label_column' ), 10, 3 );
	}


	/**
	 * Loads WP Media Files
	 *
	 * @access public
	 * @return void
	 */
	public function load_wp_media_files() {
		wp_enqueue_media();
	}

	/**
	 * Create brand and product label taxonomy for the post type "product"
	 *
	 * @access public
	 * @return void
	 */
	public function register_product_taxonomies() {

		if( apply_filters( 'mc_register_product_taxonomy_brand', true ) ) {

			// Add new taxonomy, Brand, NOT hierarchical (like tags)
			$labels = array(
				'name'                       => _x( 'Brands', 'taxonomy general name', 'mc-ext' ),
				'singular_name'              => _x( 'Brand', 'taxonomy singular name', 'mc-ext' ),
				'search_items'               => __( 'Search Brands', 'mc-ext' ),
				'popular_items'              => __( 'Popular Brands', 'mc-ext' ),
				'all_items'                  => __( 'All Brands', 'mc-ext' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'                  => __( 'Edit Brand', 'mc-ext' ),
				'update_item'                => __( 'Update Brand', 'mc-ext' ),
				'add_new_item'               => __( 'Add New Brand', 'mc-ext' ),
				'new_item_name'              => __( 'New Brand Name', 'mc-ext' ),
				'separate_items_with_commas' => __( 'Separate brands with commas', 'mc-ext' ),
				'add_or_remove_items'        => __( 'Add or remove brands', 'mc-ext' ),
				'choose_from_most_used'      => __( 'Choose from the most used brands', 'mc-ext' ),
				'not_found'                  => __( 'No brands found.', 'mc-ext' ),
				'menu_name'                  => __( 'Brands', 'mc-ext' ),
			);

			$args = apply_filters( 'mc_register_product_taxonomy_brand_args', array(
				'hierarchical'          => false,
				'labels'                => $labels,
				'show_ui'               => true,
				'show_admin_column'     => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var'             => true,
				'rewrite'               => array( 'slug' => 'brand' ),
			) );

			register_taxonomy( 'product_brand', 'product', $args );
		}

		if( apply_filters( 'mc_register_product_taxonomy_label', true ) ) {
			
			// Add new taxonomy, Product Label, NOT hierarchical (like tags)
			$labels = array(
				'name'                       => _x( 'Labels', 'taxonomy general name', 'mc-ext' ),
				'singular_name'              => _x( 'Label', 'taxonomy singular name', 'mc-ext' ),
				'search_items'               => __( 'Search Labels', 'mc-ext' ),
				'popular_items'              => __( 'Popular Labels', 'mc-ext' ),
				'all_items'                  => __( 'All Labels', 'mc-ext' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'                  => __( 'Edit Label', 'mc-ext' ),
				'update_item'                => __( 'Update Label', 'mc-ext' ),
				'add_new_item'               => __( 'Add New Label', 'mc-ext' ),
				'new_item_name'              => __( 'New Label Name', 'mc-ext' ),
				'separate_items_with_commas' => __( 'Separate labels with commas', 'mc-ext' ),
				'add_or_remove_items'        => __( 'Add or remove labels', 'mc-ext' ),
				'choose_from_most_used'      => __( 'Choose from the most used labels', 'mc-ext' ),
				'not_found'                  => __( 'No labels found.', 'mc-ext' ),
				'menu_name'                  => __( 'Labels', 'mc-ext' ),
			);

			$args = apply_filters( 'mc_register_product_taxonomy_label_args', array(
				'hierarchical'          => false,
				'labels'                => $labels,
				'show_ui'               => true,
				'show_admin_column'     => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var'             => true,
				'rewrite'               => array( 'slug' => 'label' ),
			) );
		
			register_taxonomy( 'product_label', 'product', $args );
		}
	}

	/**
	 * Order term when created (put in position 0).
	 *
	 * @access public
	 * @param mixed $term_id
	 * @param mixed $tt_id
	 * @param mixed $taxonomy
	 * @return void
	 */
	public function create_term( $term_id, $tt_id = '', $taxonomy = '' ) {
		if ( $taxonomy != 'product_label' && $taxonomy != 'product_brand' && ! taxonomy_is_product_attribute( $taxonomy ) )
			return;

		$meta_name = taxonomy_is_product_attribute( $taxonomy ) ? 'order_' . esc_attr( $taxonomy ) : 'order';

		update_woocommerce_term_meta( $term_id, $meta_name, 0 );
	}

	/**
	 * When a term is deleted, delete its meta.
	 *
	 * @access public
	 * @param mixed $term_id
	 * @return void
	 */
	public function delete_term( $term_id ) {

		$term_id = (int) $term_id;

		if ( ! $term_id )
			return;

		global $wpdb;
		$wpdb->query( "DELETE FROM {$wpdb->woocommerce_termmeta} WHERE `woocommerce_term_id` = " . $term_id );
	}

	/**
	 * Brand thumbnail fields.
	 *
	 * @access public
	 * @return void
	 */
	public function add_brand_fields() {
		?>
		<div class="form-field">
			<label><?php _e( 'Thumbnail', 'mc-ext' ); ?></label>
			<div id="product_brand_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo wc_placeholder_img_src(); ?>" width="60px" height="60px" /></div>
			<div style="line-height:60px;">
				<input type="hidden" id="product_brand_thumbnail_id" name="product_brand_thumbnail_id" />
				<button type="button" class="upload_image_button button"><?php _e( 'Upload/Add image', 'mc-ext' ); ?></button>
				<button type="button" class="remove_image_button button"><?php _e( 'Remove image', 'mc-ext' ); ?></button>
			</div>
			<script type="text/javascript">

				 // Only show the "remove image" button when needed
				 if ( ! jQuery('#product_brand_thumbnail_id').val() )
					 jQuery('.remove_image_button').hide();

				// Uploading files
				var file_frame;

				jQuery(document).on( 'click', '.upload_image_button', function( event ){

					event.preventDefault();

					// If the media frame already exists, reopen it.
					if ( file_frame ) {
						file_frame.open();
						return;
					}

					// Create the media frame.
					file_frame = wp.media.frames.downloadable_file = wp.media({
						title: '<?php _e( 'Choose an image', 'mc-ext' ); ?>',
						button: {
							text: '<?php _e( 'Use image', 'mc-ext' ); ?>',
						},
						multiple: false
					});

					// When an image is selected, run a callback.
					file_frame.on( 'select', function() {
						attachment = file_frame.state().get('selection').first().toJSON();

						jQuery('#product_brand_thumbnail_id').val( attachment.id );
						jQuery('#product_brand_thumbnail img').attr('src', attachment.url );
						jQuery('.remove_image_button').show();
					});

					// Finally, open the modal.
					file_frame.open();
				});

				jQuery(document).on( 'click', '.remove_image_button', function( event ){
					jQuery('#product_brand_thumbnail img').attr('src', '<?php echo wc_placeholder_img_src(); ?>');
					jQuery('#product_brand_thumbnail_id').val('');
					jQuery('.remove_image_button').hide();
					return false;
				});

			</script>
			<div class="clear"></div>
		</div>
		<?php
	}

	/**
	 * Edit brand thumbnail field.
	 *
	 * @access public
	 * @param mixed $term Term (brand) being edited
	 * @param mixed $taxonomy Taxonomy of the term being edited
	 */
	public function edit_brand_fields( $term, $taxonomy ) {

		$image 			= '';
		$thumbnail_id 	= absint( get_woocommerce_term_meta( $term->term_id, 'thumbnail_id', true ) );
		if ( $thumbnail_id )
			$image = wp_get_attachment_thumb_url( $thumbnail_id );
		else
			$image = wc_placeholder_img_src();
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e( 'Thumbnail', 'mc-ext' ); ?></label></th>
			<td>
				<div id="product_brand_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo $image; ?>" style="max-width: 150px; height: auto;" /></div>
				<div style="line-height:60px;">
					<input type="hidden" id="product_brand_thumbnail_id" name="product_brand_thumbnail_id" value="<?php echo $thumbnail_id; ?>" />
					<button type="submit" class="upload_image_button button"><?php _e( 'Upload/Add image', 'mc-ext' ); ?></button>
					<button type="submit" class="remove_image_button button"><?php _e( 'Remove image', 'mc-ext' ); ?></button>
				</div>
				<script type="text/javascript">

					// Uploading files
					var file_frame;

					jQuery(document).on( 'click', '.upload_image_button', function( event ){

						event.preventDefault();

						// If the media frame already exists, reopen it.
						if ( file_frame ) {
							file_frame.open();
							return;
						}

						// Create the media frame.
						file_frame = wp.media.frames.downloadable_file = wp.media({
							title: '<?php _e( 'Choose an image', 'mc-ext' ); ?>',
							button: {
								text: '<?php _e( 'Use image', 'mc-ext' ); ?>',
							},
							multiple: false
						});

						// When an image is selected, run a callback.
						file_frame.on( 'select', function() {
							attachment = file_frame.state().get('selection').first().toJSON();

							jQuery('#product_brand_thumbnail_id').val( attachment.id );
							jQuery('#product_brand_thumbnail img').attr('src', attachment.url );
							jQuery('.remove_image_button').show();
						});

						// Finally, open the modal.
						file_frame.open();
					});

					jQuery(document).on( 'click', '.remove_image_button', function( event ){
						jQuery('#product_brand_thumbnail img').attr('src', '<?php echo wc_placeholder_img_src(); ?>');
						jQuery('#product_brand_thumbnail_id').val('');
						jQuery('.remove_image_button').hide();
						return false;
					});

				</script>
				<div class="clear"></div>
			</td>
		</tr>
		<?php
	}

	/**
	 * save_brand_fields function.
	 *
	 * @access public
	 * @param mixed $term_id Term ID being saved
	 * @param mixed $tt_id
	 * @param mixed $taxonomy Taxonomy of the term being saved
	 * @return void
	 */
	public function save_brand_fields( $term_id, $tt_id, $taxonomy ) {

		if ( isset( $_POST['product_brand_thumbnail_id'] ) )
			update_woocommerce_term_meta( $term_id, 'thumbnail_id', absint( $_POST['product_brand_thumbnail_id'] ) );

		delete_transient( 'wc_term_counts' );
	}

	/**
	 * Thumbnail column added to brand admin.
	 *
	 * @access public
	 * @param mixed $columns
	 * @return array
	 */
	public function product_brand_columns( $columns ) {
		$new_columns          = array();
		$new_columns['cb']    = $columns['cb'];
		$new_columns['thumb'] = __( 'Image', 'mc-ext' );

		unset( $columns['cb'] );

		unset( $columns['description'] );

		return array_merge( $new_columns, $columns );
	}

	/**
	 * Thumbnail column value added to brand admin.
	 *
	 * @access public
	 * @param mixed $columns
	 * @param mixed $column
	 * @param mixed $id
	 * @return array
	 */
	public function product_brand_column( $columns, $column, $id ) {

		if ( $column == 'thumb' ) {

			$image 			= '';
			$thumbnail_id 	= get_woocommerce_term_meta( $id, 'thumbnail_id', true );

			if ($thumbnail_id)
				$image = wp_get_attachment_thumb_url( $thumbnail_id );
			else
				$image = wc_placeholder_img_src();

			// Prevent esc_url from breaking spaces in urls for image embeds
			// Ref: http://core.trac.wordpress.org/ticket/23605
			$image = str_replace( ' ', '%20', $image );

			$columns .= '<img src="' . esc_url( $image ) . '" alt="Thumbnail" class="wp-post-image" style="max-width: 150px; height: auto;" />';

		}

		return $columns;
	}

	/**
	 * Label Background and Text Color.
	 *
	 * @access public
	 * @return void
	 */
	public function add_label_fields() {
		?>
		<div class="form-field">
			<label class="label_background_color"><?php _e( 'Background Color', 'mc-ext' ); ?></label>
			<input name="label_background_color" id="label_background_color" type="text" value autocomplete="off">
			<p class="description"><?php echo __( 'Background color as a hex value. For example : #000000 is black and #FFFFFF is white', 'mc-ext' ); ?></p>
		</div>
		<div class="form-field">
			<label class="label_text_color"><?php _e( 'Text Color', 'mc-ext' ); ?></label>
			<input name="label_text_color" id="label_text_color" type="text" value autocomplete="off">
			<p class="description"><?php echo __( 'Text color as a hex value. For example : #000000 is black and #FFFFFF is white', 'mc-ext' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Edit label fields
	 *
	 * @access public
	 * @param mixed $term Term (brand) being edited
	 * @param mixed $taxonomy Taxonomy of the term being edited
	 */
	public function edit_label_fields( $term, $taxonomy ) {

		$background_color 	= get_woocommerce_term_meta( $term->term_id, 'background_color', true );
		$text_color 		= get_woocommerce_term_meta( $term->term_id, 'text_color', true );
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e( 'Background Color', 'mc-ext' ); ?></label></th>
			<td>
				<input name="label_background_color" id="label_background_color" type="text" value="<?php echo $background_color;?>" autocomplete="off">
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e( 'Text Color', 'mc-ext' ); ?></label></th>
			<td>
				<input name="label_text_color" id="label_text_color" type="text" value="<?php echo $text_color;?>" autocomplete="off">
			</td>
		</tr>
		<?php
	}

	/**
	 * save_label_fields function.
	 *
	 * @access public
	 * @param mixed $term_id Term ID being saved
	 * @param mixed $tt_id
	 * @param mixed $taxonomy Taxonomy of the term being saved
	 * @return void
	 */
	public function save_label_fields( $term_id, $tt_id, $taxonomy ) {

		if ( isset( $_POST['label_background_color'] ) )
			update_woocommerce_term_meta( $term_id, 'background_color', $_POST['label_background_color'] );

		if ( isset( $_POST['label_text_color'] ) )
			update_woocommerce_term_meta( $term_id, 'text_color', $_POST['label_text_color'] );

		delete_transient( 'wc_term_counts' );
	}

	/**
	 * Background and Text Color column added to product label admin.
	 *
	 * @access public
	 * @param mixed $columns
	 * @return array
	 */
	public function product_label_columns( $columns ) {
		$new_columns          = array();
		$new_columns['background_color'] = __( 'BG Color', 'mc-ext' );
		$new_columns['text_color'] = __( 'Text Color', 'mc-ext' );

		unset( $columns['description'] );
		unset( $columns['cb'] );
		unset( $columns['posts'] );

		return array_merge( $columns, $new_columns);
	}

	/**
	 * Backgroudn and Text Color column value added to product label admin.
	 *
	 * @access public
	 * @param mixed $columns
	 * @param mixed $column
	 * @param mixed $id
	 * @return array
	 */
	public function product_label_column( $columns, $column, $id ) {

		if ( $column == 'background_color' ) {

			$background_color 	= get_woocommerce_term_meta( $id, 'background_color', true );

			$columns .= $background_color;

		}elseif ( $column == 'text_color' ) {

			$text_color 	= get_woocommerce_term_meta( $id, 'text_color', true );

			$columns .= $text_color;

		}

		return $columns;
	}

}

new MC_Product_Taxonomies();