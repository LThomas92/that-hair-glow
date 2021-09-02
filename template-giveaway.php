<?php
/**
 * Template Name: Giveaway
 * The template for displaying Giveaway
 *
 * @link
 *
 * @package That Hair Glow
 */ ?>

 <?php get_header(); ?>

 <?php while ( have_posts() ) : the_post(); ?>

 <?php echo the_content(); ?>


  <?php endwhile; // End of the loop. ?>

 <?php get_footer(); ?>