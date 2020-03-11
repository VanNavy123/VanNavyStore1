<?php
/**
 * Class to setup Brands attribute
 *
 * @package MediaCenter/WooCommerce
 */

if( ! class_exists( 'MediaCenter_Product_Taxonomies' ) ) :

class MediaCenter_Product_Taxonomies {

	public function __construct() {

		$brand_taxonomy = mc_get_brands_taxonomy();
		$label_taxonomy = mc_get_labels_taxonomy();

		// Add scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'load_wp_media_files' ), 0 );

		if( ! empty( $brand_taxonomy ) ) {
			
			// Add form
			add_action( "{$brand_taxonomy}_add_form_fields",	array( $this, 'add_brand_fields' ), 10 );
			add_action( "{$brand_taxonomy}_edit_form_fields",	array( $this, 'edit_brand_fields' ), 10, 2 );
			add_action( 'create_term',							array( $this, 'save_brand_fields' ), 	10, 3 );
			add_action( 'edit_term',							array( $this, 'save_brand_fields' ), 	10, 3 );

			// Add columns
			add_filter( "manage_edit-{$brand_taxonomy}_columns",	array( $this, 'product_brand_columns' ) );
			add_filter( "manage_{$brand_taxonomy}_custom_column",	array( $this, 'product_brand_column' ), 10, 3 );
		}

		if( ! empty( $label_taxonomy ) ) {
			
			// Add form
			add_action( "{$label_taxonomy}_add_form_fields",	array( $this, 'add_label_fields' ), 10 );
			add_action( "{$label_taxonomy}_edit_form_fields",	array( $this, 'edit_label_fields' ), 10, 2 );
			add_action( 'create_term',							array( $this, 'save_label_fields' ), 	10, 3 );
			add_action( 'edit_term',							array( $this, 'save_label_fields' ), 	10, 3 );

			// Add columns
			add_filter( "manage_edit-{$label_taxonomy}_columns",	array( $this, 'product_label_columns' ) );
			add_filter( "manage_{$label_taxonomy}_custom_column",	array( $this, 'product_label_column' ), 10, 3 );
		}
	}

	/**
	 * Loads WP Media Files
	 *
	 * @return void
	 */
	public function load_wp_media_files() {
		wp_enqueue_media();
	}

	/**
	 * Brand thumbnail fields.
	 *
	 * @return void
	 */
	public function add_brand_fields() {
		?>
		<div class="form-field">
			<label><?php esc_html_e( 'Thumbnail', 'mediacenter' ); ?></label>
			<div id="product_brand_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo wc_placeholder_img_src(); ?>" width="60px" height="60px" alt="" /></div>
			<div style="line-height:60px;">
				<input type="hidden" id="product_brand_thumbnail_id" name="product_brand_thumbnail_id" />
				<button type="button" class="ec_br_upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'mediacenter' ); ?></button>
				<button type="button" class="ec_br_remove_image_button button"><?php esc_html_e( 'Remove image', 'mediacenter' ); ?></button>
			</div>
			<script type="text/javascript">

				 // Only show the "remove image" button when needed
				 if ( ! jQuery('#product_brand_thumbnail_id').val() )
					 jQuery('.ec_br_remove_image_button').hide();

				// Uploading files
				var file_frame;

				jQuery(document).on( 'click', '.ec_br_upload_image_button', function( event ){

					event.preventDefault();

					// If the media frame already exists, reopen it.
					if ( file_frame ) {
						file_frame.open();
						return;
					}

					// Create the media frame.
					file_frame = wp.media.frames.downloadable_file = wp.media({
						title: '<?php esc_html_e( 'Choose an image', 'mediacenter' ); ?>',
						button: {
							text: '<?php esc_html_e( 'Use image', 'mediacenter' ); ?>',
						},
						multiple: false
					});

					// When an image is selected, run a callback.
					file_frame.on( 'select', function() {
						attachment = file_frame.state().get('selection').first().toJSON();

						jQuery('#product_brand_thumbnail_id').val( attachment.id );
						jQuery('#product_brand_thumbnail img').attr('src', attachment.url );
						jQuery('.ec_br_remove_image_button').show();
					});

					// Finally, open the modal.
					file_frame.open();
				});

				jQuery(document).on( 'click', '.ec_br_remove_image_button', function( event ){
					jQuery('#product_brand_thumbnail img').attr('src', '<?php echo wc_placeholder_img_src(); ?>');
					jQuery('#product_brand_thumbnail_id').val('');
					jQuery('.ec_br_remove_image_button').hide();
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
			<th scope="row" valign="top"><label><?php esc_html_e( 'Thumbnail', 'mediacenter' ); ?></label></th>
			<td>
				<div id="product_brand_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo esc_url( $image ); ?>" alt="" style="max-width: 150px; height: auto;" /></div>
				<div style="line-height:60px;">
					<input type="hidden" id="product_brand_thumbnail_id" name="product_brand_thumbnail_id" value="<?php echo esc_attr( $thumbnail_id ); ?>" />
					<button type="submit" class="ec_br_upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'mediacenter' ); ?></button>
					<button type="submit" class="ec_br_remove_image_button button"><?php esc_html_e( 'Remove image', 'mediacenter' ); ?></button>
				</div>
				<script type="text/javascript">

					// Uploading files
					var file_frame;

					jQuery(document).on( 'click', '.ec_br_upload_image_button', function( event ){

						event.preventDefault();

						// If the media frame already exists, reopen it.
						if ( file_frame ) {
							file_frame.open();
							return;
						}

						// Create the media frame.
						file_frame = wp.media.frames.downloadable_file = wp.media({
							title: '<?php esc_html_e( 'Choose an image', 'mediacenter' ); ?>',
							button: {
								text: '<?php esc_html_e( 'Use image', 'mediacenter' ); ?>',
							},
							multiple: false
						});

						// When an image is selected, run a callback.
						file_frame.on( 'select', function() {
							attachment = file_frame.state().get('selection').first().toJSON();

							jQuery('#product_brand_thumbnail_id').val( attachment.id );
							jQuery('#product_brand_thumbnail img').attr('src', attachment.url );
							jQuery('.ec_br_remove_image_button').show();
						});

						// Finally, open the modal.
						file_frame.open();
					});

					jQuery(document).on( 'click', '.ec_br_remove_image_button', function( event ){
						jQuery('#product_brand_thumbnail img').attr('src', '<?php echo wc_placeholder_img_src(); ?>');
						jQuery('#product_brand_thumbnail_id').val('');
						jQuery('.ec_br_remove_image_button').hide();
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
	 * @param mixed $columns
	 * @return array
	 */
	public function product_brand_columns( $columns ) {
		$new_columns          = array();
		$new_columns['cb']    = $columns['cb'];
		$new_columns['thumb'] = esc_html__( 'Image', 'mediacenter' );

		unset( $columns['cb'] );

		unset( $columns['description'] );

		return array_merge( $new_columns, $columns );
	}

	/**
	 * Thumbnail column value added to brand admin.
	 *
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
			<label class="label_background_color"><?php _e( 'Background Color', 'mediacenter' ); ?></label>
			<input name="label_background_color" id="label_background_color" type="text" value autocomplete="off">
			<p class="description"><?php echo __( 'Background color as a hex value. For example : #000000 is black and #FFFFFF is white', 'mediacenter' ); ?></p>
		</div>
		<div class="form-field">
			<label class="label_text_color"><?php _e( 'Text Color', 'mediacenter' ); ?></label>
			<input name="label_text_color" id="label_text_color" type="text" value autocomplete="off">
			<p class="description"><?php echo __( 'Text color as a hex value. For example : #000000 is black and #FFFFFF is white', 'mediacenter' ); ?></p>
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
			<th scope="row" valign="top"><label><?php _e( 'Background Color', 'mediacenter' ); ?></label></th>
			<td>
				<input name="label_background_color" id="label_background_color" type="text" value="<?php echo $background_color;?>" autocomplete="off">
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e( 'Text Color', 'mediacenter' ); ?></label></th>
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
		$new_columns['background_color'] = __( 'BG Color', 'mediacenter' );
		$new_columns['text_color'] = __( 'Text Color', 'mediacenter' );

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

		} elseif ( $column == 'text_color' ) {

			$text_color 	= get_woocommerce_term_meta( $id, 'text_color', true );

			$columns .= $text_color;

		}

		return $columns;
	}
}

new MediaCenter_Product_Taxonomies();

endif;