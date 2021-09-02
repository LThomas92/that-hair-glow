<?php
/**
 * Template Name: Template Book Now
 * The template for displaying Book Now
 * @link
 *
 * @package That Hair Glow
 */ ?>

<?php get_header(); ?>

<script>
	window.afterBookingSelectService = function(appointment, service, provider, location) { 
		document.querySelector('.book-now__info-btn').classList.add('hide-btn');
		document.querySelector('#amelia-step-booking0').classList.remove('book-now-mobile-height');
	}
	window.beforeBookingLoaded = function(appointment, service, provider, location) { 
		document.querySelector('#amelia-step-booking0').classList.add('book-now-mobile-height');
		var addElement = document.querySelector('.am-add-element');
		var bookNowBtn = document.querySelector('.book-now__info-btn');
		
	}
</script>

<main id="primary" class="site-main book-now">

<?php $moreInfoBtnText = get_field('book_now_btn_text'); ?>

<button class="book-now__info-btn"><?php echo $moreInfoBtnText; ?></button>

<?php $popupText = get_field('book_now_info'); ?>

<div class="book-now__info-popup">
<h4 class="book-now__info-title">Important Info Before Booking</h4>
<?php echo $popupText; ?>
<img class="book-now__info-close" src="<?php echo get_template_directory_uri(); ?>/img/close.svg" alt="Close Icon" />
</div>

<div class="container-margins">

  <?php while ( have_posts() ) :
    the_post(); ?>

  <?php echo the_content(); ?>

  <?php endwhile; // End of the loop. ?>

</div> 
</main> 


<?php get_footer(); ?>
