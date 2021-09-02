<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package That_Hair_Glow
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function that_hair_glow_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'that_hair_glow_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function that_hair_glow_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'that_hair_glow_pingback_header' );

if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page();
	
}

//Custom ACF Gutenberg Blocks

function about_slideshow() {

	// check function exists
	if( function_exists('acf_register_block') ) {

		// register a custom slideshow
		acf_register_block(array(
			'name'				=> 'about-slideshow',
			'title'				=> __('About Slideshow'),
			'description'		=> __('A custom slideshow for About Page.'),
			'render_template'	=> 'template-parts/blocks/about-slideshow.php',
			'category'			=> 'layout',
			'icon'				=> 'images-alt2',
			'keywords'			=> array( 'about, slideshow', 'block', 'that-hair-glow' ),
		));
	}
}

add_action('acf/init', 'about_slideshow');

function faq_circle() {

	// check function exists
	if( function_exists('acf_register_block') ) {

		// register a portfolio item block
		acf_register_block(array(
			'name'				=> 'faq-circle',
			'title'				=> __('Faq Circles'),
			'description'		=> __('A custom block for the faqs circle.'),
			'render_template'	=> 'template-parts/blocks/faq-circle.php',
			'category'			=> 'layout',
			'icon'				=> 'excerpt-view',
			'keywords'			=> array( 'faq, faqs, circle, question, answer, that hair glow'),
		));
	}
}

add_action('acf/init', 'faq_circle');

function accordion_block() {

// check function exists
if( function_exists('acf_register_block') ) {

	// register a portfolio item block
	acf_register_block(array(
		'name'				=> 'accordion',
		'title'				=> __('Accordion Block'),
		'description'		=> __('A custom block for accoridon items.'),
		'render_template'	=> 'template-parts/blocks/accordion-block.php',
		'category'			=> 'layout',
		'icon'				=> 'excerpt-view',
		'keywords'			=> array( 'accordion, faqs, faq, question, answer, that hair glow'),
	));
}
}

add_action('acf/init', 'accordion_block');

