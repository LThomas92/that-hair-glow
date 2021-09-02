<?php
/**
 * Template Name: Template Hair Care Guide
 * The template for displaying Template Hair Care Guide
 *
 * @link
 *
 * @package That Hair Glow
 */ ?>

<?php get_header(); ?>

<main id="primary" class="site-main hair-care-guide">

<div class="container-margins">

  <?php while ( have_posts() ) :
    the_post(); ?>

  <?php if(has_post_thumbnail()) { ?>
    <header style="background-image: url(<?php echo the_post_thumbnail_url(); ?>)" class="hair-care-guide__header"></header> <!-- hair care guide header -->
  <?php }; ?>
  

<div class="hair-care-guide__content">
  <h1 class="hair-care-guide__title"><?php the_title();?></h1>
  <p class="hair-care-guide__author">by That Hair Glow</p>
<?php echo the_content(); ?>
</div> <!-- hair care guide content -->

  <?php endwhile; // End of the loop. ?>

</div> <!-- container margins -->
</main> <!-- hair care guide -->

<?php get_footer(); ?>