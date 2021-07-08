<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package That_Hair_Glow
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>

<!-- <div class="addtional-info-text">
	<p>Standard Length: Waist</p>
	<p>Standard Braid Size: Small/Medium</p>
</div> <!-- add't info text -->

	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--  Essential META Tags -->

<meta property="og:title" content="That Hair Glow">
<meta property="og:description" content="Making you feel better one strand at a time">
<meta property="og:image" content="https://i.imgur.com/P7Iid8b.jpg" />
<meta property="og:image:secure_url" content="https://i.imgur.com/P7Iid8b.jpg">
<meta property="og:type" content="image/jpg">
<meta property="og:image:width" content="300">
<meta property="og:image:height" content="300">
<meta property="og:url" content="https://thathairglow.com/">
<meta name="twitter:card" content="summary_large_image">

<!--  Non-Essential, But Recommended -->

<meta property="og:site_name" content="That Hair Glow">
<meta name="twitter:image:alt" content="That Hair Glow Quote">


<!--  Non-Essential, But Required for Analytics -->
<meta property="fb:app_id" content="your_app_id" />
<meta name="twitter:site" content="@thathairglow">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<header id="masthead" class="site-header">
		<div class="site-branding">

		<nav class="header-nav">
			<?php
				wp_nav_menu(
					array(
						'theme_location' => 'menu-1',
						'menu_id'        => 'primary-menu',
					)
							); ?>
		</nav>


		</div><!-- .site-branding -->


	</header><!-- #masthead -->
