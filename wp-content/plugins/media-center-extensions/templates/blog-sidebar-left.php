<?php
/*
 * The template for displaying Blog Posts with a Left Sidebar
 */

global $media_center_theme_options, $custom_query;

$blog_style = 'normal';

if ( isset( $custom_query->query['blog_style'] ) ) {
	$blog_style = $custom_query->query['blog_style'];
} else if ( isset( $media_center_theme_options['blog_style'] ) ) {
	$blog_style = $media_center_theme_options['blog_style'];
}

get_header(); 
?>
<!-- ============================================================= SECTION - BLOG ============================================================= -->
<section id="blog">
	<div class="container inner-top-xs inner-bottom-xs sidebar-left">
		<div class="row">
			
			<div class="col-md-9 col-md-push-3 <?php if( $blog_style != 'list-view' ) : ?>inner-left-sm<?php endif; ?>">
				
				<?php

					if ( $blog_style == 'grid-view' ) {
						get_template_part( 'templates/blog-style/style-grid' );
					} else if( $blog_style == 'list-view' ) {
						get_template_part( 'templates/blog-style/style-list' );
					} else {
						get_template_part( 'templates/blog-style/style-normal' );
					}
				
					media_center_blog_pagination( 'nav-below' ); 
				?>

			</div><!-- /.col -->

			<div class="col-md-3 col-md-pull-9">
				<aside class="sidebar blog-sidebar">
					<?php get_sidebar();?>
				</aside>
			</div>

		</div><!-- /.row -->
	</div><!-- /.container-class -->
</section><!-- /#blog -->
<!-- ============================================================= SECTION - BLOG : END ============================================================= -->