<?php get_header(); ?>

	<?php the_post(); ?>
	<?php if (get_option('13floor_integration_single_top') <> '' && get_option('13floor_integrate_singletop_enable') == 'on') echo(get_option('13floor_integration_single_top')); ?>	
	
	<div id="content-top"></div>
		
	<div id="contentwrap">
		<div id="content" class="clearfix">
		
			<div id="content-area">
	
				<div class="entry post clearfix">
					
					<h1 class="title"><?php the_title(); ?></h1>
						
					<?php include(TEMPLATEPATH . '/includes/postinfo.php'); ?>
					
					<?php if (get_option('13floor_thumbnails') == 'on') { ?>
						
						<?php $width = 250;
							  $height = 500;
							  $classtext = 'thumbnail alignleft';
							  $titletext = get_the_title();
						
							  $thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext);
							  $thumb = $thumbnail["thumb"]; ?>
						
						<?php if($thumb <> '') print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext , $width, $height, $classtext); ?>
							
					<?php }; ?>
					
					<?php the_content(); ?>
					<?php wp_link_pages(array('before' => '<p><strong>'.__('Pages','13floor').':</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
					<?php edit_post_link(__('Edit this page','13floor')); ?>
					
				</div> <!-- end .post -->
				
				<?php if (get_option('13floor_integration_single_bottom') <> '' && get_option('13floor_integrate_singlebottom_enable') == 'on') echo(get_option('13floor_integration_single_bottom')); ?>		
				<?php if (get_option('13floor_468_enable') == 'on') { ?>
					<?php if(get_option('13floor_468_adsense') <> '') echo(get_option('13floor_468_adsense'));
					else { ?>
						<a href="<?php echo(get_option('13floor_468_url')); ?>"><img src="<?php echo(get_option('13floor_468_image')); ?>" alt="468 ad" class="foursixeight" /></a>
					<?php } ?>	
				<?php } ?>
				
				<?php if (get_option('13floor_show_postcomments') == 'on') comments_template('', true); ?>
		
			</div> <!-- end #content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>