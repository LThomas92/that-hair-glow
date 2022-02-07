<?php
/**
 * Template Name: If you're New This is for you
 * The template for displaying If you're New This is for you
 *
 * @link
 *
 * @package That Hair Glow
 */ ?>

<?php get_header(); ?>

<main class="hair-care-guide"> 

<div class="container-margins">
<?php while ( have_posts() ) :
    the_post(); ?>

<h1 class="hair-care-guide__title"><?php the_title(); ?></h1>

<?php if( have_rows('hair_care_guide_content') ): ?>
    <div class="hair-care-guide-container">
    <?php $i = 0; while( have_rows('hair_care_guide_content') ) : the_row();
        $question = get_sub_field('question');
        $answer = get_sub_field('answer'); 
        $icon = get_sub_field('icon'); 
        $arrow = get_sub_field('arrow_icon');
        ?>
    
    <div class="flip-card">
  <div class="flip-card-inner">
    <div class="flip-card-front flip-card-bg-<?php echo $i; ?>">
      <picture class="flip-card__icon">
          <img src="<?php echo $icon['url']; ?>" alt="">
      </picture>
      <p class="flip-card__question"><?php echo $question; ?></p>
      <div class="flip-card__arrow-icon">  
      <?php echo file_get_contents($arrow); ?>
      </div>
    </div>
    <div class="flip-card-back">
    <p class="flip-card__answer"><?php echo $answer;?></p>
    <img class="flip-card__back-arrow" src="<?php echo get_template_directory_uri(); ?>/img/arrow-thin.svg" alt="Arrow" />
    </div>
  </div>
</div> 


    <?php // End loop.
    $i++;
    endwhile; ?>

</div> <!-- hair care guide content container -->

<?php // No value.
else :
    // Do something...
endif; ?>

  <?php endwhile; // End of the loop. ?>


</div> <!-- container margins -->
</main>



<?php get_footer(); ?>