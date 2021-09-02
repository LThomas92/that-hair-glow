<?php if( have_rows('about_slideshow') ): ?>
    <div class="abt-slideshow-block__slides">
    <?php while( have_rows('about_slideshow') ) : the_row();
        $text = get_sub_field('text'); 
		$image = get_sub_field('image');
		?>
        <div class="abt-slideshow-block__slide">
        <p><?php echo $text; ?></p>
        <div class="abt-slideshow-block__controls">
        <div title="Prev Slide"><img class="prev-arrow arrow" src="<?php echo get_template_directory_uri(); ?>/img/arrow.svg" alt="Prev Arrow" /></div>
        <div title="Next Slide"><img class="next-arrow arrow" src="<?php echo get_template_directory_uri(); ?>/img/arrow.svg" alt="Next Arrow" /></div>
        </div> <!-- abt slideshow block controls -->
        </div> <!-- abt slideshow block slide -->

  <?php endwhile; ?>

</div> <!-- about slideshow block slides -->

<?php // No value.
else :
    // Do something...
endif; ?>
