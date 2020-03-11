<?php
global $grid_products; ?>
<div class="col-xs-12 col-sm-4 no-margin product">
    <div class="product-item-wrap">
        <div class="product-item">
            <div class="product-item-inner">
                <div class="product-image">
                    <?php 
                        woocommerce_show_product_loop_sale_flash();
                        mc_template_loop_product_labels();
                    ?>
                    <a href="<?php the_permalink(); ?>">
                        <?php woocommerce_template_loop_product_thumbnail(); ?>
                    </a>
                </div>
                <div class="product-body">
                    <h4 class="product-title"><a href="<?php the_permalink(); ?>"><?php echo apply_filters( 'product_title_one_product_6_1', get_the_title() ); ?></a></h4>
                    <?php 
                        if( function_exists( 'woocommerce_show_brand' ) ) {
                            woocommerce_show_brand();    
                        }
                     ?>
                </div>
                <div class="product-price-container prices clearfix">
                    <?php wc_get_template( 'loop/price.php' ); ?>
                </div>
                <?php 
                    if( function_exists( 'mc_shop_loop_hover_area' ) ) {
                        mc_shop_loop_hover_area();    
                    }
                 ?>
            </div><!-- /.product-item-inner -->
        </div><!-- /.product-item -->
    </div><!-- /.product-item-wrap -->
</div><!-- /.product-item-holder -->