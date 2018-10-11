<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php elegant_titles(); ?></title>
<?php elegant_description(); ?>
<?php elegant_keywords(); ?>
<?php elegant_canonical(); ?>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-23090508-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/style.css" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<!--[if lt IE 7]>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/css/ie6style.css" />
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/DD_belatedPNG_0.0.8a-min.js"></script>
	<script type="text/javascript">DD_belatedPNG.fix('img#logo,img.featured_image,span.current_arrow, div#search-form, #featured-area, #controls, #feat-content a.readmore, #feat-content a.readmore span, .entry a.readmore, .entry a.readmore span, ul.nav li ul, ul.nav li ul a, div#content-top, #sidebar .widget h3.title, #sidebar .widget .widget_content, #sidebar .widget .wrapper, #sidebar .widget .widget_content ul li a, #sidebar .widget, div#footer-top, div#footer .widget h3.title, #footer .widget .widget_content ul li, #footer .widget .widget_content ul li a, #footer .widget .widget_content ul ul');</script>
<![endif]-->
<!--[if IE 7]>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/css/ie7style.css" />
<![endif]-->

<script type="text/javascript">
	document.documentElement.className = 'js';
</script>

<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php wp_head(); ?>

</head>
<body>
<div id="bg1">
<div id="bg2" <?php if (is_home() && get_option('13floor_featured') == 'on') echo(' id="home"'); ?>>
	<div id="wrap">
	
		<div id="header" class="clearfix">
			
			<!-- LOGO -->
			<a href="<?php bloginfo('url'); ?>"><?php $logo = (get_option('13floor_logo') <> '') ? get_option('13floor_logo') : get_bloginfo('template_directory').'/images/logo.png'; ?>
				<img src="<?php echo $logo; ?>" alt="Logo" id="logo"/></a>
			
			<!-- TOP MENU -->
			<?php $menuClass = 'superfish nav clearfix';
			$primaryNav = '';
			
			if (function_exists('wp_nav_menu')) {
				$primaryNav = wp_nav_menu( array( 'theme_location' => 'primary-menu', 'container' => '', 'fallback_cb' => '', 'menu_class' => $menuClass, 'echo' => false ) );
			};
			if ($primaryNav == '') { ?>
				<ul class="<?php echo $menuClass; ?>">
					<?php if (get_option('13floor_home_link') == 'on') { ?>
						<li <?php if (is_front_page()) echo('class="current_page_item"') ?>><a href="<?php bloginfo('url'); ?>"><?php _e('Home','13floor'); ?></a></li>
					<?php }; ?>

					<?php show_categories_menu($menuClass,false); ?>
					
					<?php show_page_menu($menuClass,false,false); ?>
				</ul> <!-- end ul.nav -->
			<?php }
			else echo($primaryNav); ?>
			
			<!-- SEARCH FORM -->
			<div id="search-form">
				<form method="get" id="searchform1" action="<?php bloginfo('url'); ?>/">
					<input type="text" value="<?php _e('search this site...','13floor'); ?>" name="s" id="searchinput" />
					<input type="submit" id="searchsubmit" value="<?php _e('Search','13floor'); ?>" />
				</form>
			</div> <!-- end searchform -->
			
		</div> <!-- end #header -->