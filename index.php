<?php
/**
<<<<<<< HEAD
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package That_Hair_Glow
 */

get_header();
?>

<div id="primary" class="content-area">
	<div id="content" class="site-content" role="main">
	<?php if ( have_posts() ) : ?>

		<?php
		// Start the loop.
		while ( have_posts() ) :
			the_post(); ?>
			<?php echo do_shortcode('[salon_booking_calendar]'); ?>

		<?php endwhile; ?>

	<?php else : ?>
		<?php get_template_part( 'content', 'none' ); ?>
	<?php endif; ?>

	</div><!-- #content -->
</div><!-- #primary -->
<?php
get_footer();
=======
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define( 'WP_USE_THEMES', true );

/** Loads the WordPress Environment and Template */
require __DIR__ . '/wp-blog-header.php';
>>>>>>> 0fab2c7fcfbee7049afff7ef01171e51126d4a6e
