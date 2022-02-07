$ = jQuery.noConflict();

$(document).ready(function () {
  $(".booked-calendar-wrap").wrap('<div class="booked-calendar-bg"></div>');

  if($('body').hasClass('page-template-template-stylist') ){
    $('.site-footer').addClass('white-footer');
  }

  $(".reviews-slider").slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 3000,
    prevArrow: jQuery(".reviews-slide__prev"),
    nextArrow: jQuery(".reviews-slide__next"),
  });


  $(".abt-slideshow-block__slides").slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 5000,
    prevArrow: jQuery(".prev-arrow"),
    nextArrow: jQuery(".next-arrow"),
  });

  $(".flip-card .flip-card-inner").click(function () {
    $(this).toggleClass("flipped");
  });

  $(".accordion-content").css("display", "none");

  $(".accordion-title").click(function () {
    $(".accordion-title").not(this).removeClass("open");
    $(".accordion-title").not(this).next().slideUp(300);
    $(this).toggleClass("open");
    $(this).next().slideToggle(300);
  });

  $('.book-now__info-btn').click(function() {
	 $('.book-now__info-popup').toggleClass('show-popup');
	 $(this).toggleClass('info-btn-margin');
  });

  $('.book-now__info-close').click(function() {
	$('.book-now__info-popup').toggleClass('show-popup');
	$('.book-now__info-btn').toggleClass('info-btn-margin');
  });

	$('.reviews-button').click(function(){
		$('.reviews-form').addClass('reviews-form--active');
	});

	$('.reviews-form__close-icon').click(function() {
		$('.reviews-form').removeClass('reviews-form--active');
	});

  $('#primary-menu .menu-item-has-children').click(function() {
    $('#primary-menu .menu-item-has-children .sub-menu').toggleClass('show-dropdown');
  });

  $(document).on('gform_confirmation_loaded', function(){
    $('.reviews-form').addClass('reviews-form--conf-active');
});

$('.gfield_required').text('*');

$('.book-dropdown').click(function() {
  $(this).toggleClass('active-dropdown');
});

});




// STAR RATINGS

(function($) {

  $(document).ready(function() {
      $('.js-gf-rating-stars').gformsRatings();
  });

  // The meat and potatoes
  $.fn.gformsRatings = function( options ) {

      // Setup options
      var settings = $.extend({
          'labels' : false,
          'activeClass' : 'gf-rating-star-active',
          'iconClass' : 'fa fa-star'
      }, options);

      return this.each(function() {

          // Store the object
          let $this = $(this),
              $container = $('.ginput_container', $this),
              $radioGroup = $('.gfield_radio', $this),
              $radios = $('input[type="radio"]', $radioGroup),
              $stars = null,
              $currentIndex = null;

          // Initialize
          let init = function() {

              $radioGroup.css('display', 'none');

              $wrap = $('<div class="gf-rating-stars"></div>');

              for (let i = 0; i < $radios.length; i++) {
                  let label = $radios.eq(i).siblings('label').text(),
                      value = $radios.eq(i).val(),
                      $star = $('<i class="js-gf-rating-star gf-rating-star"></i>');

                  $star
                      .addClass(settings.iconClass)
                      .data('index', i)
                      .data('value', value)
                      .data('label', label)
                      .attr('title', label)
                      .appendTo($wrap);

              }

              $wrap.appendTo($container);
              
              $stars = $('.js-gf-rating-star', $this); // Fix issue with multiple radio groups

              // Star Interactions
              $stars.on('mouseover', function() { // Mouseover instead of hover
                  handleHover($(this));
              }).on('click', function() {
                  handleClick($(this));
              }).on('mouseout', function() {
                  handleOut($(this));
              });

              // Restore to currently checked next field
              $wrap.on('mouseout', function() {
                  $checked = $radios.filter(':checked');

                  if (!$checked.length) {
                      $stars.removeClass(settings.activeClass);
                  } else {
                      highlight($currentIndex);
                  }
              });
          }

          let highlight = function(index) {
              $stars.filter(':gt(' + index + ')').removeClass(settings.activeClass);
              $stars.filter(':lt(' + index + '), :eq(' + index + ')').addClass(settings.activeClass);
          }

          let handleHover = function(el) {
              let index = el.data('index');

              if (index >= $currentIndex) { // Do not apply to stars lesser than
                  highlight(index);
              }
          }

          let handleOut = function(el) { // Empty selections if not clicked
              if ($currentIndex === null) {
                  $stars.removeClass(settings.activeClass);
              }
          }

          let handleClick = function(el) {
              let index = el.data('index');
              highlight(index); // Highlight immediately after clicking
              $currentIndex = index;
              $radios.eq(index).trigger('click');
          }

          // Kick it off
          init();

      });

  };


})(jQuery);