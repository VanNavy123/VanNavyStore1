<?php
/**
 * Top Cart
 *
 * @author      Ibrahim Ibn Dawood
 * @package     Templates/Header
 * @version     2.0.0
 */
if ( ! defined( 'ABSPATH' ) ){
    exit;
}

?>
<div class="top-cart-row-container">
    <div class="wishlist-compare-holder">
    </div><!-- /.wishlist-compare-holder -->

    <?php
        if( class_exists('Ecwid_Widget_Minicart_Miniview') ) {
            the_widget( 'Ecwid_Widget_Minicart_Miniview' );
        }
    ?>
    
</div><!-- /.top-cart-row-container -->