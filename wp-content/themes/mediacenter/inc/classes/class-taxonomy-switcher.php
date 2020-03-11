<?php
/**
 * Class MC_Taxonomy_Switcher.
 */
class MC_Taxonomy_Switcher {

	/**
	 * Taxonomy to switch from.
	 *
	 * @var string
	 */
	public $from = '';

	/**
	 * Taxonomy to switch to.
	 *
	 * @var string
	 */
	public $to = '';

	/**
	 * Parent term_id to limit by.
	 *
	 * @var int
	 */
	public $parent = 0;

	/**
	 * Array of term IDs to convert.
	 *
	 * @var array
	 */
	public $terms = array();

	/**
	 * Array of term IDs to convert.
	 *
	 * @var array
	 */
	public $term_ids = array();

	/**
	 * Array of notices from conversion.
	 *
	 * @var array
	 */
	public $notices = array();

	/**
	 * Array of error/success messages.
	 *
	 * @var array
	 */
	public $messages = array();

	/**
	 * Setup the object.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Arguments containing from taxonomy, to taxonomy,
	 *                    and additional optional params.
	 */
	public function __construct( $args = array() ) {

		$args = wp_parse_args( $args, array(
			'from_tax' => '',
			'to_tax'   => '',
			'parent'   => '',
			'terms'    => '',
		) );

		if ( ! $args['from_tax'] || ! $args['to_tax'] ) {
			return;
		}

		if ( ! empty( $args['parent'] ) ) {
			$this->parent = absint( $args['parent'] );
		}

		if ( ! empty( $args['terms'] ) ) {
			$this->terms = wp_parse_id_list( $args['terms'] );
		}

		// $this->is_ui = ( isset( $_GET['page'] ) && 'taxonomy-switcher' == $_GET['page'] );
		$this->is_ui = true;

		$this->from = sanitize_text_field( $args['from_tax'] );
		$this->to   = sanitize_text_field( $args['to_tax'] );

	}

	/**
	 * Convert taxonomy of terms from the Admin.
	 *
	 * @since 1.0.0
	 */
	public function admin_convert() {

		$count = $this->count();

		if ( ! $count && $this->is_ui ) {
			return $this->notice( $this->notices( 'no_terms' ) );
		}

		$this->notice( $this->notices( 'switching' ) );

		if ( 0 < $this->parent ) {
			$this->notice( $this->notices( 'limit_by_parent' ) );
		} elseif ( ! empty( $this->terms ) ) {
			$this->notice( $this->notices( 'limit_by_terms' ) );
		}

		set_time_limit( 0 );

		$this->convert();

		$this->notice( $this->notices( 'switched' ) );

		if ( $this->is_ui ) {
			return $this->notices;
		}

		die();
	}

	/**
	 * Stores and (maybe) displays notices.
	 *
	 * @since 1.0.0
	 *
	 * @param string $notice Notice to store and/or display.
	 * @return array
	 */
	public function notice( $notice ) {
		// Add to our notices array.
		$this->notices[] = $notice;
		if ( ! $this->is_ui ) {
			echo $notice;
		}
		return $this->notices;
	}

	/**
	 * Compile our notices.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Array key to retrieve.
	 * @return mixed
	 */
	public function notices( $key ) {
		if ( ! empty( $this->messages ) ) {
			return $this->messages[ $key ];
		}

		$count          = $this->count();
		$count_name     = sprintf( _n( '1 term', '%d terms', $count, 'mediacenter' ), $count );
		$this->messages = array(
			'no_terms'        => __( 'No terms to be switched. Check if the term exists in your "from" taxonomy.', 'mediacenter' ),
			'switching'       => sprintf( __( 'Switching %s with the taxonomy \'%s\' to the taxonomy \'%s\'', 'mediacenter' ), $count_name, $this->from, $this->to ),
			'limit_by_parent' => sprintf( __( 'Limiting the switch by the parent term_id of %d', 'mediacenter' ), $this->parent ),
			'limit_by_terms'  => sprintf( __( 'Limiting the switch to these terms: %s', 'mediacenter' ), implode( ', ', $this->terms ) ),
			'switched'        => sprintf( __( 'Taxonomies switched for %s!', 'mediacenter' ), $count_name ),
		);
		return $this->messages[ $key ];
	}

	/**
	 * Get term ids based on $from and $parent.
	 *
	 * @since 1.0.0
	 *
	 * @return array An array of term ids.
	 */
	public function get_term_ids() {

		$args = array(
			'hide_empty' => false,
			'fields'     => 'ids',
			'child_of'   => $this->parent,
			'include'    => $this->terms,
		);

		$args = apply_filters( 'taxonomy_switcher_get_terms_args', $args, $this->from, $this->to, array( 'parent' => $this->parent, 'terms' => $this->terms ) );

		$terms = get_terms( $this->from, $args );

		$this->term_ids = array();

		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			$this->term_ids = $terms;
		}

		return $this->term_ids;

	}

	/**
	 * Return the total count of terms found.
	 *
	 * @since 1.0.0
	 *
	 * @return int Total count of terms found.
	 */
	public function count() {

		if ( empty( $this->term_ids ) ) {
			$this->get_term_ids();
		}

		return count( $this->term_ids );

	}

	/**
	 * Convert taxonomy of terms.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether the conversion was successful.
	 */
	public function convert() {

		if ( empty( $this->term_ids ) ) {
			$this->get_term_ids();
		}

		if ( empty( $this->term_ids ) ) {
			return false;
		}

		global $wpdb;

		$term_ids = array_map( 'absint', $this->term_ids );
		$term_ids = implode( ', ', $term_ids );

		$wpdb->query( $wpdb->prepare( "
			UPDATE `{$wpdb->term_taxonomy}`
			SET `taxonomy` = %s
			WHERE `taxonomy` = %s AND `term_id` IN ( {$term_ids} )
		", $this->to, $this->from ) );

		if ( 0 < $this->parent ) {
			$wpdb->query( $wpdb->prepare( "
				UPDATE `{$wpdb->term_taxonomy}`
				SET `parent` = 0
				WHERE `parent` = %d AND `term_id` IN ( {$term_ids} )
			", $this->parent ) );
		}

		return true;
	}
}

if( ! function_exists( 'mc_taxonomy_brand_migrate' ) ) {
	function mc_taxonomy_brand_migrate() {

		if( isset( $_GET[ 'do_migrate_mc_brands' ] ) && $_GET[ 'do_migrate_mc_brands' ] ) {
			$is_migrated = 'no';
			$theme = wp_get_theme( get_template() );
			$brand_taxonomy = is_woocommerce_activated() ? mc_get_brands_taxonomy() : '';

			if ( ! empty( $brand_taxonomy ) && version_compare( $theme->get( 'Version' ), '2.5.0', '>=' ) && ! get_option( 'mediacenter_tax_brand_migrated' ) ) {
				$args = array(
					'from_tax' => 'product_brand',
					'to_tax'   => $brand_taxonomy,
					'parent'   => '',
					'terms'    => '',
				);

				$taxonomy_switcher = new MC_Taxonomy_Switcher( $args );
				$success_notices = $taxonomy_switcher->admin_convert();

				add_option( 'mediacenter_tax_brand_migrated', true, '', 'yes' );
				$is_migrated = 'yes';
			}

			// Redirect and strip query string.
			wp_redirect( esc_url_raw( add_query_arg( 'mc_brands_migrated', $is_migrated, admin_url( 'index.php' ) ) ) );
		}
	}
}

if( ! function_exists( 'mc_taxonomy_label_migrate' ) ) {
	function mc_taxonomy_label_migrate() {

		if( isset( $_GET[ 'do_migrate_mc_labels' ] ) && $_GET[ 'do_migrate_mc_labels' ] ) {
			$is_migrated = 'no';
			$theme = wp_get_theme( get_template() );
			$label_taxonomy = is_woocommerce_activated() ? mc_get_labels_taxonomy() : '';

			if ( ! empty( $label_taxonomy ) && version_compare( $theme->get( 'Version' ), '2.5.0', '>=' ) && ! get_option( 'mediacenter_tax_label_migrated' ) ) {
				$args = array(
					'from_tax' => 'product_label',
					'to_tax'   => $label_taxonomy,
					'parent'   => '',
					'terms'    => '',
				);

				$taxonomy_switcher = new MC_Taxonomy_Switcher( $args );
				$success_notices = $taxonomy_switcher->admin_convert();

				add_option( 'mediacenter_tax_label_migrated', true, '', 'yes' );
				$is_migrated = 'yes';
			}

			// Redirect and strip query string.
			wp_redirect( esc_url_raw( add_query_arg( 'mc_labels_migrated', $is_migrated, admin_url( 'index.php' ) ) ) );
		}
	}
}

if( ! function_exists( 'mc_taxonomy_brand_migrate_notices' ) ) {
	function mc_taxonomy_brand_migrate_notices() {
		if( ! get_option( 'mediacenter_tax_brand_migrated' ) ) {
			?>
			<div id="message" class="updated woocommerce-message wc-connect">
				<p><strong><?php _e( 'MediaCenter Brands Taxonomy Migrate', 'mediacenter' ); ?></strong> &#8211; <?php _e( 'We should setup Brand attribute in MC Options > Shop.', 'mediacenter' ); ?></p>
				<p class="submit"><a href="<?php echo esc_url( add_query_arg( 'do_migrate_mc_brands', 'true', admin_url( 'admin.php' ) ) ); ?>" class="mc-brands-migrate-now button-primary"><?php _e( 'Run the updater', 'mediacenter' ); ?></a></p>
			</div>
			<script type="text/javascript">
				jQuery( '.mc-brands-migrate-now' ).click( 'click', function() {
					return window.confirm( '<?php echo esc_js( __( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'mediacenter' ) ); ?>' );
				});
			</script>
			<?php
		}

		if( isset( $_GET[ 'mc_brands_migrated' ] ) && $_GET[ 'mc_brands_migrated' ] == 'yes' ) {
			echo '<div class="updated"><p>' . esc_html__( 'Brands taxonomy migrated.', 'mediacenter' ) . '</p></div>';
		} elseif( isset( $_GET[ 'mc_brands_migrated' ] ) && $_GET[ 'mc_brands_migrated' ] == 'no' ) {
			echo '<div class="error"><p>' . esc_html__( 'Migration failed. Please try again.', 'mediacenter' ) . '</p></div>';
		}
	}
}

if( ! function_exists( 'mc_taxonomy_label_migrate_notices' ) ) {
	function mc_taxonomy_label_migrate_notices() {
		if( ! get_option( 'mediacenter_tax_label_migrated' ) ) {
			?>
			<div id="message" class="updated woocommerce-message wc-connect">
				<p><strong><?php _e( 'MediaCenter Labels Taxonomy Migrate', 'mediacenter' ); ?></strong> &#8211; <?php _e( 'We should setup Label attribute in MC Options > Shop.', 'mediacenter' ); ?></p>
				<p class="submit"><a href="<?php echo esc_url( add_query_arg( 'do_migrate_mc_labels', 'true', admin_url( 'admin.php' ) ) ); ?>" class="mc-labels-migrate-now button-primary"><?php _e( 'Run the updater', 'mediacenter' ); ?></a></p>
			</div>
			<script type="text/javascript">
				jQuery( '.mc-labels-migrate-now' ).click( 'click', function() {
					return window.confirm( '<?php echo esc_js( __( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'mediacenter' ) ); ?>' );
				});
			</script>
			<?php
		}

		if( isset( $_GET[ 'mc_labels_migrated' ] ) && $_GET[ 'mc_labels_migrated' ] == 'yes' ) {
			echo '<div class="updated"><p>' . esc_html__( 'Labels taxonomy migrated.', 'mediacenter' ) . '</p></div>';
		} elseif( isset( $_GET[ 'mc_labels_migrated' ] ) && $_GET[ 'mc_labels_migrated' ] == 'no' ) {
			echo '<div class="error"><p>' . esc_html__( 'Migration failed. Please try again.', 'mediacenter' ) . '</p></div>';
		}
	}
}

if( is_woocommerce_activated() ) {
	add_action( 'admin_init', 'mc_taxonomy_brand_migrate' );
	add_action( 'admin_init', 'mc_taxonomy_label_migrate' );
	add_action( 'admin_notices', 'mc_taxonomy_brand_migrate_notices' );
	add_action( 'admin_notices', 'mc_taxonomy_label_migrate_notices' );
}

if( get_option( 'mediacenter_tax_brand_migrated' ) ) {
	add_filter( 'mc_register_product_taxonomy_brand', '__return_false' );
}

if( get_option( 'mediacenter_tax_label_migrated' ) ) {
	add_filter( 'mc_register_product_taxonomy_label', '__return_false' );
}