$ = jQuery.noConflict();

$(document).ready(function () {
  $(".booked-calendar-wrap").wrap('<div class="booked-calendar-bg"></div>');

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

});

