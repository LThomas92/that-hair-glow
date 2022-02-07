<?php
/**
 * Template Name: Template Stylist 
 * The template for displaying Template Stylist
 * 
 *
 *
 * @link
 *
 * @package That Hair Glow
 */ ?>

<?php get_header(); ?>

<?php $content = get_field( 'content' );
			$leftImage = get_field( 'left_image' );
			$middleImage = get_field( 'middle_image' );
			$rightImage = get_field( 'right_image' );
?>


<div class="template-stylist__content">
	<div class="template-stylist__text">
	<div class="template-stylist__title-container">
	<h1 class="template-stylist__title"><?php the_title(); ?></h1>
	</div>	
	<?php echo $content; ?>
	</div>

	<div class="template-stylist__images">
		<picture class="template-stylist__left-image">
			<img src="<?php echo $leftImage['url']; ?>"  alt="<?php echo $leftImage['alt'] ?>" />
		</picture>
		<picture class="template-stylist__middle-image">
			<img src="<?php echo $middleImage['url']; ?>" alt="<?php echo $middleImage['alt'] ?>" />
		</picture>
		<picture class="template-stylist__right-image">
			<img src="<?php echo $rightImage['url']; ?>" alt="<?php echo $rightImage['alt'] ?>" />
		</picture>
	</div>

</div>

<?php get_footer(); ?>