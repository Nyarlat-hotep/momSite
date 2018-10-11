<?php if(get_option('13floor_featured') == 'on') { ?>
	<?php get_header(); ?>

	<?php include(TEMPLATEPATH . '/includes/featured.php'); ?>

	<?php get_footer(); ?>
<?php } else { ?>
	<?php include(TEMPLATEPATH . '/index.php'); ?>
<?php }; ?>