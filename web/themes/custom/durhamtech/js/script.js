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
})(jQuery, Drupal);