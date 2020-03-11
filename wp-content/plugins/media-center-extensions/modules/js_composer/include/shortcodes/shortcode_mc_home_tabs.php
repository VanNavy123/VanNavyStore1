<?php

if ( !function_exists( 'shortcode_mc_home_tabs' ) ):

function shortcode_mc_home_tabs( $atts, $content = null ){

	global $woocommerce_loop;

	$woocommerce_loop['is_carousel'] = false;
	$woocommerce_loop['screen_width'] = 100;
	$woocommerce_loop['product_item_size'] = 'size-medium';

	extract(shortcode_atts(array(
		'title_tab_1'		=> '',
		'content_sc_tab_1'	=> '',
		'title_tab_2'		=> '',
		'content_sc_tab_2'	=> '',
		'title_tab_3'		=> '',
		'content_sc_tab_3'	=> '',
		'number'            => '4',
    ), $atts));

    $per_page = absint( $number );

    $content_tab_1 = do_shortcode( '['. $content_sc_tab_1 . ' product_item_size="size-medium" animation="none" screen_width="100" per_page="' . $per_page . '" carousel="false"]' );
	$content_tab_2 = do_shortcode( '['. $content_sc_tab_2 . ' product_item_size="size-medium" animation="none" screen_width="100" per_page="' . $per_page . '" carousel="false"]' );
	$content_tab_3 = do_shortcode( '['. $content_sc_tab_3 . ' product_item_size="size-medium" animation="none" screen_width="100" per_page="' . $per_page . '" carousel="false"]' );

    ob_start();
?>
<div class="inner-top-xs inner-bottom-sm home-page-tabs">
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
			<div id="home-page-tab-1" class="tab-pane active"><?php echo $content_tab_1;?></div><!-- /.tab-pane -->
			<div id="home-page-tab-2" class="tab-pane"><?php echo $content_tab_2;?></div><!-- /.tab-pane -->
			<div id="home-page-tab-3" class="tab-pane"><?php echo $content_tab_3;?></div><!-- /.tab-pane -->
		</div><!-- /.tab-content -->
	</div><!-- /.tab-holder -->
</div><!-- /.home-page-tabs -->
<?php

    $home_tabs = ob_get_clean();

    woocommerce_reset_loop();
    
    $woocommerce_loop['is_carousel'] = $woocommerce_loop['screen_width'] = $woocommerce_loop['product_item_size'] = '';

    return $home_tabs;
}

add_shortcode( 'mc_home_page_tabs' , 'shortcode_mc_home_tabs' );
endif;