		<?php if(!is_home() || get_option('13floor_featured') == 'false') { ?>
				</div> <!-- end #content -->	
			</div> <!-- end #contentwrap -->
			
			<div id="content-bottom"></div>
			
			<div id="footer-top"></div>
			<div id="footer" class="clearfix">
				<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer') ) : ?> 
				<?php endif; ?>

			</div> <!-- end #footer -->
		<?php }; ?>
			
			
	</div> <!-- end #wrap -->
	
	<?php include(TEMPLATEPATH . '/includes/scripts.php'); ?>
	<?php wp_footer(); ?>	
</div>
</div>
</body>
</html>