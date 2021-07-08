<?php
/**
 * Template Name: Template Terms & Conditions
 * The template for displaying Template Terms & Conditions
 *
 * @link
 *
 * @package That Hair Glow
 */ ?>

<?php get_header(); ?>

<main id="primary" class="site-main terms-conditions">

<div class="terms-conditions__wrapper">
<h1 class="terms-conditions__title"><?php the_title(); ?></h1>
<?php while ( have_posts() ) :
  the_post(); ?>

<?php echo the_content(); ?>

</div> <!-- terms & conditions wrapper -->
<?php endwhile; // End of the loop. ?>
</main><!-- #main -->

<?php get_footer(); ?>
