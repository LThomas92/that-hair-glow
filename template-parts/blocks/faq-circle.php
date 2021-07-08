<?php if( have_rows('faq_circles') ): ?>
    <div class="faq-circles">
    <?php while( have_rows('faq_circles') ) : the_row();
        $question = get_sub_field('question');
        $answer =  get_sub_field('answer'); ?>

    <div class="scene scene--card">
    <div class="card">
    <div class="card__face card__face--front">
    <p><?php echo $question; ?></p>
    <img title="Next Arrow" class="arrow-thin" src="<?php echo get_template_directory_uri(); ?>/img/arrow-thin.svg" alt="Next Arrow" />
  </div> <!-- faqs circles item front -->

    <div class="card__face card__face--back">
    <p><?php echo $answer; ?></p>
    <img title="Back Arrow" class="arrow-thin arrow-thin__prev" src="<?php echo get_template_directory_uri(); ?>/img/arrow-thin.svg" alt="Back Arrow" />
    </div> <!-- faqs circles item back -->

  </div> <!-- faq circle item inner -->
    </div> <!-- faq circle -->


  <?php // End loop.
endwhile; ?>
</div> <!-- faq circles -->
<?php // No value.
else :
    // Do something...
endif;
