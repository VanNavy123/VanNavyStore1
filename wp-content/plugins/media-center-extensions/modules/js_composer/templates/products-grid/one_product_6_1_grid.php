<?php global $grid_product_details, $product, $media_center_theme_options; ?>
<div class="size-big single-product-gallery small-gallery">

    <?php 
        woocommerce_show_product_loop_sale_flash();
        mc_template_loop_product_labels();
    ?>

    <div class="slider-container">
        <div id="<?php echo $grid_product_details['grid_id'];?>-single-product-slider" class="single-product-slider owl-carousel">
        <?php

            $product_id = mc_wc_get_product_id( $product );

            if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7', '<' ) ) {
                $attachment_ids = $product->get_gallery_attachment_ids();
            } else {
                $attachment_ids = $product->get_gallery_image_ids();
            }
            if( empty( $attachment_ids ) ) {
                $attachment_ids = array( get_post_thumbnail_id( $product_id ) );
            }
            $counters = 1;

            if ( $attachment_ids ) :
                foreach ( $attachment_ids as $attachment_id ) :
                    $image_link = wp_get_attachment_url( $attachment_id );
                    $image_src  = wp_get_attachment_image_src( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
        ?>
                <div class="single-product-gallery-item" id="slide-<?php echo $attachment_id;?>">
                    <a data-rel="prettyphoto" href="<?php echo get_permalink( $product_id );?>">
                        <img alt="" src="<?php echo $image_src[0];?>">
                    </a>
                </div><!-- /.single-product-gallery-item -->
        <?php
                if( $counter++ == 5 ) break;
                endforeach;
        ?>
            </div><!-- /.single-product-slider -->
            <div class="gallery-thumbs clearfix">
                <ul class="list-unstyled">
        <?php
                $counter = 1;
                foreach ( $attachment_ids as $attachment_id ) :
                    $image_src  = wp_get_attachment_image_src( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
        ?>
                    <li>
                        <a class="horizontal-thumb" data-target="#<?php echo $grid_product_details['grid_id'];?>-single-product-slider" data-slide="<?php echo $counter - 1;?>" href="#slide-<?php echo $attachment_id;?>">
                            <?php if( $media_center_theme_options['lazy_loading'] ) : ?>
                            <img class="echo-lazy-loading" src="<?php echo get_template_directory_uri();?>/assets/images/blank.gif" alt="" data-echo="<?php echo $image_src[0];?>">
                            <?php else : ?>
                            <img alt="" src="<?php echo $image_src[0];?>">
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php if( $counter++ == 6 ) break; ?>
            <?php endforeach; ?>
                </ul>
            </div><!-- /.gallery-thumbs -->
        <?php
            endif; 
        ?>
    </div><!-- /.slider-container -->

    <div class="product-body">
        <h4 class="product-title"><a href="<?php the_permalink(); ?>"><?php echo apply_filters( 'product_title_one_product_6_1', get_the_title() ); ?></a></h4>
        <?php 
            if( function_exists( 'woocommerce_show_brand' ) ) {
                woocommerce_show_brand();
            }
        ?>
    </div>
    <div class="product-prices-container text-right">
        <div class="inline prices">
        <?php 
            if ( $price_html = $product->get_price_html() ){
                echo $price_html;
            }
        ?>
        </div>
    <?php 
        $product_type = mc_wc_get_product_type( $product );
        echo apply_filters( 'woocommerce_loop_add_to_cart_link',
            sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="le-button big inline %s product_type_%s">%s</a>',
                esc_url( $product->add_to_cart_url() ),
                esc_attr( $product_id ),
                esc_attr( $product->get_sku() ),
                $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
                esc_attr( $product_type ),
                esc_html( $product->add_to_cart_text() )
            ),
        $product );
    ?>
    </div>
</div><!-- /.product-item-holder -->