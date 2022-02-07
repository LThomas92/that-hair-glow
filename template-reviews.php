<?php
/**
 * Template Name: Template Reviews
 * The template for displaying Reviews
 *
 * @link
 *
 * @package That Hair Glow
 */ ?>

<?php get_header(); ?>

<button class="reviews-button">Leave a Review</button>

<div class="reviews-form">
<?php echo the_content(); ?>
<svg class="reviews-form__close-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M24 20.188l-8.315-8.209 8.2-8.282-3.697-3.697-8.212 8.318-8.31-8.203-3.666 3.666 8.321 8.24-8.206 8.313 3.666 3.666 8.237-8.318 8.285 8.203z"/></svg>
</div>

<?php
    $args = array(  
        'post_type' => 'reviews',
        'post_status' => 'publish',
        'posts_per_page' => -1, 
        'orderby' => 'title', 
        'order' => 'ASC', 
    );

    $reviewsLoop = new WP_Query( $args ); ?>
    <ul class="reviews-slider">
    <?php while ( $reviewsLoop->have_posts() ) : $reviewsLoop->the_post();  ?>
    
    <div class="reviews-slide">
      <div class="reviews-slide__header">
      <h1 class="reviews-slide__title"><?php the_title(); ?></h1> &nbsp; &nbsp; <?php $posttags = get_the_tags();
        if ($posttags) {
          foreach($posttags as $tag) { ?>
           <p class="reviews-slide__service"><?php echo $tag->name ; ?></p>
         <?php  }
        }
      ?>
      </div>

      <?php if(has_post_thumbnail()) { ?>
        <picture class="reviews-slide__image">
        <img src="<?php echo the_post_thumbnail_url(); ?>" alt="">
      </picture>
      <?php }?>
      <p class="reviews-slide__date"><?php echo get_the_date(); ?></p>

      <?php $cats = get_the_category($post_id); ?>
      <?php foreach($cats as $cat){ ?>
        <?php if( $cat->name == "star-rating-5-question-1") { ?>
          <h2 class="reviews-slide__question"><?php echo get_field('question_1', 'option'); ?></h2>
          <div class="stars">
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          </div>
        <?php } else if( $cat->name == "star-rating-4-question-1") { ?>
          <h2 class="reviews-slide__question"><?php echo get_field('question_1', 'option'); ?></h2>
          <div class="stars">
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star "></span>
          </div>
       <?php } else if( $cat->name == "star-rating-3-question-1") { ?>
        <h2 class="reviews-slide__question"><?php echo get_field('question_1', 'option'); ?></h2>
          <div class="stars">
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star"></span>
          <span class="fa fa-star"></span>
          </div>
     <?php } else if( $cat->name == "star-rating-2-question-1") { ?>
      <h2 class="reviews-slide__question"><?php echo get_field('question_1', 'option'); ?></h2>
       <div class="stars">
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star"></span>
          <span class="fa fa-star"></span>
          <span class="fa fa-star"></span>
          </div>
     <?php } else if( $cat->name == "star-rating-1-question-1") { ?>
      <h2 class="reviews-slide__question"><?php echo get_field('question_1', 'option'); ?></h2>
       <div class="stars">
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star"></span>
          <span class="fa fa-star"></span>
          <span class="fa fa-star"></span>
          <span class="fa fa-star"></span>
          </div>
     <?php } ?>

    

<?php if( $cat->name == "star-rating-5-question-2") { ?>
  <h2 class="reviews-slide__question"><?php echo get_field('question_2', 'option'); ?></h2>
  <div class="stars">
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          </div>
<?php } else if( $cat->name == "star-rating-4-question-2") { ?>
  <h2 class="reviews-slide__question"><?php echo get_field('question_2', 'option'); ?></h2>
  <div class="stars">
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star"></span>
          </div>
<?php } else if( $cat->name == "star-rating-3-question-2") { ?>
  <h2 class="reviews-slide__question"><?php echo get_field('question_2', 'option'); ?></h2>
  <div class="stars">
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star "></span>
          <span class="fa fa-star "></span>
          </div>
<?php } else if( $cat->name == "star-rating-2-question-2") { ?>
  <h2 class="reviews-slide__question"><?php echo get_field('question_2', 'option'); ?></h2>
<div class="stars">
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star "></span>
          <span class="fa fa-star "></span>
          <span class="fa fa-star "></span>
          </div>
<?php } else if( $cat->name == "star-rating-1-question-2") { ?>
  <h2 class="reviews-slide__question"><?php echo get_field('question_2', 'option'); ?></h2>
<div class="stars">
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star"></span>
          <span class="fa fa-star"></span>
          <span class="fa fa-star"></span>
          <span class="fa fa-star"></span>
          </div>
<?php } ?>


<?php if( $cat->name == "star-rating-5-question-3") { ?>
  <h2 class="reviews-slide__question"><?php echo get_field('question_3', 'option'); ?></h2>
  <div class="stars">
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          </div>
<?php } else if( $cat->name == "star-rating-4-question-3") { ?>
  <h2 class="reviews-slide__question"><?php echo get_field('question_3', 'option'); ?></h2>
  <div class="stars">
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star"></span>
          </div>
<?php } else if( $cat->name == "star-rating-3-question-3") { ?>
  <h2 class="reviews-slide__question"><?php echo get_field('question_3', 'option'); ?></h2>
  <div class="stars">
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star"></span>
          <span class="fa fa-star "></span>
          </div>
<?php } else if( $cat->name == "star-rating-2-question-3") { ?>
  <h2 class="reviews-slide__question"><?php echo get_field('question_3', 'option'); ?></h2>
<div class="stars">
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star"></span>
          <span class="fa fa-star"></span>
          <span class="fa fa-star"></span>
          </div>
<?php } else if( $cat->name == "star-rating-1-question-3") { ?>
  <h2 class="reviews-slide__question"><?php echo get_field('question_3', 'option'); ?></h2>
<div class="stars">
          <span class="fa fa-star checked"></span>
          <span class="fa fa-star"></span>
          <span class="fa fa-star"></span>
          <span class="fa fa-star"></span>
          <span class="fa fa-star"></span>
          </div>
<?php }
      }
        ?>


  <h2 class="reviews-slide__question"><?php echo get_field('question_4', 'option'); ?></h2>
  <div class="reviews-slide__answer"><?php the_content(); ?></div>

    </div> <!-- reviews slide -->

   <?php endwhile; ?>

   </ul>

   <div class="reviews-slide__arrows">

        <svg class="reviews-slide__prev" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
        viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
      <style type="text/css">
        .st0{fill-rule:evenodd;clip-rule:evenodd;fill:#FFFFFF;}
      </style>
      <path class="st0" d="M21.9,12l-7.5,6.2L15,19l9-7.5L15,4l-0.6,0.8l7.5,6.2H0v1H21.9z"/>
      </svg>

      <svg class="reviews-slide__next" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
        viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
      <style type="text/css">
        .st0{fill-rule:evenodd;clip-rule:evenodd;fill:#FFFFFF;}
      </style>
      <path class="st0" d="M21.9,12l-7.5,6.2L15,19l9-7.5L15,4l-0.6,0.8l7.5,6.2H0v1H21.9z"/>
      </svg>

  </div>

    <?php wp_reset_postdata(); ?>

<?php get_footer(); ?>

