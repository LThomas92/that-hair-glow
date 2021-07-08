<?php
/**
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
