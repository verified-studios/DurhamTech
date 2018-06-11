(function ($, Drupal) {
  Drupal.behaviors.durhamtech = {
    attach: function (context, settings) {

    }
  };

  Drupal.behaviors.header = {
    attach: function (context, settings) {
      // Open/close search
      $('.navbar-collapse .js-search-trigger').once().click(function() {
        $('.block-views-exposed-filter-blocksearch-page-1').toggle();
      });
    }
  };

  Drupal.behaviors.alerts = {
    attach: function (context, settings) {
      $('.region-alerts .js-close-alert').once().click(function() {
        $('.region-alerts').empty();
      });
    }
  };

  Drupal.behaviors.zendesk = {
    attach: function () {
      $('a[href^="#javascript"]').each(function(){
          var oldUrl = $(this).attr("href"); // Get current url
          var newUrl = oldUrl.replace("#javascript", "javascript:$zopim.livechat.window.show()"); // Create new url
          $(this).attr("href", newUrl); // Set herf value
      });
    }
  };

  Drupal.behaviors.slick = {
    attach: function() {
        $('.slick').slick({
            infinite: true,
            centerMode: true,
            speed: 500,
            autoplay: true,
            autoplaySpeed: 5000,
            slidesToShow: 3,
            slidesToScroll: 1,
            arrows: true,
        });
    }
  }
})(jQuery, Drupal);