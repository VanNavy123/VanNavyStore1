<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) die( '-1' );

define( 'MC_VC_PLUGIN_FILE_PATH', __FILE__ );

// Shortcodes
require_once ( 'include/shortcodes/shortcode_mc_banner.php' );
require_once ( 'include/shortcodes/shortcode_mc_gmap.php' );
require_once ( 'include/shortcodes/shortcode_mc_service_icon.php' );
require_once ( 'include/shortcodes/shortcode_mc_team_member.php' );
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    require_once ( 'include/shortcodes/shortcode_mc_vertical_menu.php' );
    require_once ( 'include/shortcodes/shortcode_mc_6_1_products_grid.php' );
    require_once ( 'include/shortcodes/shortcode_mc_brands_carousel.php' );
    require_once ( 'include/shortcodes/shortcode_mc_home_tabs.php' );
    require_once ( 'include/shortcodes/shortcode_mc_products_carousel.php' );
}
if( defined( 'ECWID_DEMO_STORE_ID' ) ) {
    require_once ( 'include/shortcodes/shortcode_mc_ecwid_products_carousel.php' );
    require_once ( 'include/shortcodes/shortcode_mc_ecwid_home_tabs.php' );
    require_once ( 'include/shortcodes/shortcode_mc_ecwid_vertical_categories.php' );
}

class VCExtendAddonClass {

    /**
     * List of paths.
     *
     * @var array
     */
    private $paths = array();

    function __construct() {

        $dir = dirname( __FILE__ );

        $this->setPaths( Array(
            'APP_ROOT' => $dir,
            'WP_ROOT' => preg_replace( '/$\//', '', ABSPATH ),
            'APP_DIR' => basename( $dir ),
            'CONFIG_DIR' => $dir . '/config',
            'ASSETS_DIR' => $dir . '/assets',
            'ASSETS_DIR_NAME' => 'assets',
        ) );

        // We safely integrate with VC with this hook
        add_action( 'init', array( $this, 'integrateWithVC' ) );
    }

    public function integrateWithVC() {

        // Check if Visual Composer is installed
        if ( ! defined( 'WPB_VC_VERSION' ) ) {
            // Display notice that Visual Compser is required
            add_action('admin_notices', array( $this, 'showVcVersionNotice' ));
            return;
        }

        require_once  $this->path( 'CONFIG_DIR', 'map.php');
    }

    /**
     * Setter for paths
     *
     * @since  4.2
     * @access protected
     * @param $paths
     */
    protected function setPaths( $paths ) {
        $this->paths = $paths;
    }

    /**
     * Gets absolute path for file/directory in filesystem.
     *
     * @since  4.2
     * @access public
     * @param $name        - name of path dir
     * @param string $file - file name or directory inside path
     * @return string
     */
    public function path( $name, $file = '' ) {
        return $this->paths[$name] . ( strlen( $file ) > 0 ? '/' . preg_replace( '/^\//', '', $file ) : '' );
    }

    /*
    Show notice if your plugin is activated but Visual Composer is not
    */
    public function showVcVersionNotice() {
        $plugin_data = get_plugin_data(__FILE__);
        echo '
        <div class="updated">
        <p>'.sprintf(__('<strong>%s</strong> requires <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', 'mc-ext'), $plugin_data['Name']).'</p>
        </div>';
    }
}

// Finally initialize code
new VCExtendAddonClass();