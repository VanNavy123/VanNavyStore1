<?php global $grid_products, $grid_product_details;?>
<!-- ========================================= <?php echo strtoupper( $grid_product_details['title'] ); ?> ========================================= -->
<section id="<?php echo $grid_product_details['grid_id']; ?>-six-one-product-grid" class="six-one-products-grid <?php echo trim($grid_product_details['el_class']);?> color-bg inner-bottom-sm inner-top-xs">
    <div class="container">
		<h2 class="section-title"><?php echo $grid_product_details['title']; ?></h2>
<?php
$total_grid_products = count( $grid_products->posts );
$counter = 1;

if( $total_grid_products > 7 ){
	$total_grid_products = 7;
}

if ( $grid_products->have_posts() ) :
?>
		<div class="six-products-grid col-xs-12 col-md-7 no-margin">
			<div class="product-grid-holder row no-margin">
<?php
	while ( $grid_products->have_posts() ) : $grid_products->the_post();

		if( $counter < $total_grid_products ) :
			include ( 'six_products_6_1_grid.php' );
		else :
?>
			</div><!-- /.row -->
		</div><!-- /.col -->
		
		<div class="single-product-grid col-xs-12 col-md-5 no-margin">
			<div class="product-grid-holder">
				<?php include ( 'one_product_6_1_grid.php' );?>
			</div><!-- /.product-grid-holder -->
		</div><!-- /.col -->
<?php
		endif;

		if( $counter++ == $total_grid_products ) break;
?>
<?php	endwhile; // end of the loop. ?>
	</div><!-- /.container -->
<?php
endif;
?>
</section><!-- /.six-one-products-grid -->
<!-- ========================================= <?php echo strtoupper( $grid_product_details['title'] ); ?> : END ========================================= -->