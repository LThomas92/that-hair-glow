<?php
/**
 * Template Name: Template About
 * The template for displaying About Page
 *
 * @link
 *
 * @package That Hair Glow
 */ ?>

<?php get_header(); ?>

<main id="primary" class="site-main">
<div class="container-margins">

<section class="about-page">
<h1 class="about-page__title"><?php the_title();?></h1>

<div class="about-page__content">
<?php while ( have_posts() ) :
  the_post(); ?>

<?php echo the_content(); ?>

</div> <!-- about page content -->

<?php endwhile; // End of the loop. ?>

</main>

</div> <!-- container margins -->

</section> <!-- about page section -->

<?php get_footer(); ?>
