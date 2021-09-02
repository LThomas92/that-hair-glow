<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package That_Hair_Glow
 */

?>

	<footer id="colophon" class="site-footer">
		<div class="site-info">
			<div class="meta-text">
			<p class="that-hair-glow-footer-quote">"Making you feel better one strand at a time"</p>
			<p class="that-hair-glow-footer-txt">| That Hair Glow |</p>
			<p class="address">Jersey City, New Jersey 07304</p>

			<nav class="footer-nav">
			<?php
				wp_nav_menu(
					array(
						'theme_location' => 'menu-2',
						'menu_id'        => 'secondary-menu',
					)
							); ?>
				</nav>

			<div class="sm-links">
			  <a target="_blank" href="mailto:thathairglow@gmail.com">
			    <img class="email-icon" src="<?php echo get_template_directory_uri(); ?>/img/email-icon.png" alt="Email Icon" />
			    </a>
			<a target="_blank" href="https://www.instagram.com/thathairglow/">
			  <img class="instagram-icon" src="<?php echo get_template_directory_uri(); ?>/img/instagram-icon.png" alt="Instagram Icon" />
			  </a>

			  <a target="_blank" href="https://www.facebook.com/thathairglow1/">
			    <img class="facebook-icon" src="<?php echo get_template_directory_uri(); ?>/img/facebook-icon.png" alt="Facebook Icon" />
			    </a>
			  </div> <!-- social media links -->
			  <p class="copyright-text">Copyright<span>&copy</span> 2021 That Hair Glow. All Rights Reserved.</p>
			</div>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
