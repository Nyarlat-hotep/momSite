<?php 

/* sets predefined Post Thumbnail dimensions */
if ( function_exists( 'add_theme_support' ) ) {
	add_theme_support( 'post-thumbnails' );
	
	//blog page template
	add_image_size( 'ptentry-thumb', 184, 184, true );
	//gallery page template
	add_image_size( 'ptgallery-thumb', 207, 136, true );
	
	//featured image size
	add_image_size( 'featured', 335, 220, true );
	
	//index,category image size
	add_image_size( 'entry', 136, 136, true );
		
};
/* --------------------------------------------- */

?>