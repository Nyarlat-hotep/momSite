<?php $post_number = get_option('13floor_searchnum_posts'); ?>
<?php get_header(); ?>
	
	<?php 
		global $query_string; 

		parse_str($query_string, $qstring_array);
					
		$args = array('showposts' => $post_number,'paged'=>$paged);
		
		if ( isset($_GET['et_searchform_submit']) ) {			
			$postTypes = array();
			if ( !isset($_GET['et-inc-posts']) && !isset($_GET['et-inc-pages']) ) $postTypes = array('post');
			if ( isset($_GET['et-inc-pages']) ) $postTypes = array('page');
			if ( isset($_GET['et-inc-posts']) ) $postTypes[] = 'post';
			$args['post_type'] = $postTypes;
			
			if ( $_GET['et-month-choice'] != 'no-choice' ) {
				$et_year = substr($_GET['et-month-choice'],0,4);
				$et_month = substr($_GET['et-month-choice'], 4, strlen($_GET['et-month-choice'])-4);
				$args['year'] = $et_year;
				$args['monthnum'] = $et_month;
			}
			
			if ( $_GET['et-cat'] != 0 )
				$args['cat'] = $_GET['et-cat'];
		}	
		
		$args = array_merge($args,$qstring_array);
					
		query_posts($args);
	?>
	
	<div id="content-top"></div>
		
	<div id="contentwrap">
		<div id="content" class="clearfix">
		
			<div id="content-area">
				
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
					<?php $thumb = ''; 	  

						  $width = 136;
						  $height = 136;
						  $classtext = 'thumbnail alignleft';
						  $titletext = get_the_title();
						
					   	  $thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext);
						  $thumb = $thumbnail["thumb"]; ?>
						  
					<?php global $post;
						  $page_result = is_search() && ($post->post_type == 'page') ? true : false; ?>
					
					<div class="entry clearfix<?php if ($page_result) echo(' page_result'); ?>">
					
						<h2 class="title"><a href="<?php the_permalink() ?>" title="<?php printf(__('Permanent Link to %s', '13floor'), $titletext) ?>"><?php the_title(); ?></a></h2>
						
						<?php if ((get_option('13floor_postinfo1') <> '') && !($page_result)) include(TEMPLATEPATH . '/includes/postinfo.php'); ?>
						
						<?php if($thumb <> '' && get_option('13floor_thumbnails_index') == 'on') { ?>						
							<a href="<?php the_permalink() ?>" title="<?php printf(__('Permanent Link to %s', '13floor'), $titletext) ?>">
								<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext); ?>
							</a>
						<?php }; ?>	
						
						<?php if (get_option('13floor_blog_style') == 'on') the_content(""); else { ?>
							<p><?php truncate_post(365); ?></p>
						<?php }; ?>
												
						<a class="readmore" href="<?php the_permalink() ?>" title="<?php printf(__('Permanent Link to %s', '13floor'), $titletext) ?>"><span><?php _e('Read More','13floor'); ?></span></a>
						
					</div> <!-- end .entry -->

				<?php endwhile; ?>

					<?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); }
						else { ?>
							<?php include(TEMPLATEPATH . '/includes/navigation.php'); ?>
					<?php } ?>

				<?php else : ?>
					<?php include(TEMPLATEPATH . '/includes/no-results.php'); ?>
				<?php endif; wp_reset_query(); ?>		
				
			</div> <!-- end #content-area -->
			
			<?php get_sidebar(); ?>
			
			<?php get_footer(); ?>