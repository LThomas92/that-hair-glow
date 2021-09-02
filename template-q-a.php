<?php
/**
 * Template Name: Template Q & A
 * The template for displaying Template Q & A
 *
 * @link
 *
 * @package That Hair Glow
 */ ?>

<?php get_header(); ?>

<main id="primary" class="site-main">

  <h1 class="main-qa-container__title"><?php the_title(); ?></h1>
  <?php while ( have_posts() ) :
    the_post(); ?>

  <?php echo the_content(); ?>

  <?php endwhile; // End of the loop. ?>

</main>

<?php get_footer(); ?>
