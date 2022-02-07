<?php
/**
 * Template Name: Template About Me
 * The template for displaying About Me
 *
 * @link
 *
 * @package That Hair Glow
 */ ?>

<?php get_header(); ?>

<div class="template-about-me">
	<div class="template-about-me__title-container">
		<div class="template-about-me__top-line"></div>
		<h1 class="template-about-me__title"><?php the_title(); ?></h1>
		<div class="template-about-me__bottom-line"></div>
	</div>
	<div class="template-about-me__main-content">
		<div class="template-about-me__content">
		<?php the_content(); ?>
		</div>
		<picture class="template-about-me__image">
		<?php the_post_thumbnail(); ?>
		</picture>
	</div>
</div>

<?php get_footer(); ?>