<?php if( have_rows('accordion_items') ): ?>
  <div id="accordion" class="accordion-container">
    <?php $i = 0;  while( have_rows('accordion_items') ) : the_row();
        $question = get_sub_field('question');
        $answer = get_sub_field('answer'); ?>

        <h4 class="accordion-title accordion-color-<?php echo $i; ?>"><span>Q.</span><?php echo $question; ?><svg class="arrow-color-<?php echo $i; ?>" id="accordion__arrow" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
<style type="text/css">
	.st0{fill-rule:evenodd;clip-rule:evenodd;fill:#FFFFFF;}
</style>
<path class="st0" d="M21.9,12l-7.5,6.2L15,19l9-7.5L15,4l-0.6,0.8l7.5,6.2H0v1H21.9z"/>
</svg>
</h4>

      <div class="accordion-content">
      <p class="accordion-content__letter">A.</p> <p class="accordion-content__answer"><?php echo $answer;?></p>
    </div> <!-- accordion content  -->



  <?php  // End loop.
  $i++;
endwhile; ?>

        </div> <!-- accordion -->
<?php // No value.
else :
    // Do something...
endif; ?>
