<?php
/**
 * Template Name: Template Hair Care Guide 2
 * The template for displaying Hair Care Guide 2
 *
 * @link
 *
 * @package That Hair Glow
 */ ?>

<?php get_header(); ?>

<main id="primary" class="site-main faqs">

<div class="container-margins">

<h1 class="faqs__title"><?php the_title();?></h1>

  <?php while ( have_posts() ) :
    the_post(); ?>

  <?php echo the_content(); ?>

  <?php endwhile; // End of the loop. ?>

</div> <!-- container margins -->
</main> <!-- faqs -->



<?php get_footer(); ?>
