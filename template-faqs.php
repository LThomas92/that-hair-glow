<?php
/**
 * Template Name: Template Faqs
 * The template for displaying Template Faqs
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
