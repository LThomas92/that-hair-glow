<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package That_Hair_Glow
 */

get_header();
?>

	<main id="primary" class="site-main">
		<?php 
			$title = get_field('title', 'option' );
			$text = get_field('content', 'option');
			$link = get_field('link', 'option');
		?>

		<section class="error-404 not-found">
			<div class="error-404__content">
			<h1 class="error-404__title"><?php echo $title; ?></h1>
			<div class="error-404__text">
			<?php echo $text; ?>
			</div>
			<a class="error-404__link" href="<?php echo $link['url']; ?>"><?php echo $link['title']?></a>
			</div><!-- error 404 content -->

			<div class="error-404__question">
			<h2 class="error-404__question-mark">?</h2>
			</div>
		</section><!-- .error-404 -->
	</main><!-- #main -->

<?php
get_footer();
