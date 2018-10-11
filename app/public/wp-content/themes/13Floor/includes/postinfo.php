<?php if (!is_single()) { ?>
	<p class="postinfo">
		<span class="info"><?php _e('Posted','13floor'); ?> <?php if (in_array('author', get_option('13floor_postinfo1'))) { ?> <?php _e('by','13floor'); ?> <?php the_author_posts_link(); ?><?php }; ?><?php if (in_array('date', get_option('13floor_postinfo1'))) { ?> <?php _e('on','13floor'); ?> <?php the_time(get_option('13floor_date_format')) ?><?php }; ?><?php if (in_array('categories', get_option('13floor_postinfo1'))) { ?> <?php _e('in','13floor'); ?> <?php the_category(', ') ?><?php }; ?><?php if (in_array('comments', get_option('13floor_postinfo1'))) { ?> | <?php comments_popup_link(__('0 comments','13floor'), __('1 comment','13floor'), '% '.__('comments','13floor')); ?><?php }; ?></span>
	</p>
<?php } elseif (is_single() && get_option('13floor_postinfo2') <> '') { ?>
	<p class="postinfo">
		<span class="info"><?php _e('Posted','13floor'); ?> <?php if (in_array('author', get_option('13floor_postinfo2'))) { ?> <?php _e('by','13floor'); ?> <?php the_author_posts_link(); ?><?php }; ?><?php if (in_array('date', get_option('13floor_postinfo2'))) { ?> <?php _e('on','13floor'); ?> <?php the_time(get_option('13floor_date_format')) ?><?php }; ?><?php if (in_array('categories', get_option('13floor_postinfo2'))) { ?> <?php _e('in','13floor'); ?> <?php the_category(', ') ?><?php }; ?><?php if (in_array('comments', get_option('13floor_postinfo2'))) { ?> | <?php comments_popup_link(__('0 comments','13floor'), __('1 comment','13floor'), '% '.__('comments','13floor')); ?><?php }; ?></span>
	</p>
<?php }; ?>