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

  if (window.matchMedia("(max-width: 768px)").matches) {
    $(".faq-circles-mobile").slick({
      slidesToShow: 1,
      slidesToScroll: 1,
    });
  }

  $(".card").hover(function () {
    $(this).toggleClass("is-flipped");
  });
});
