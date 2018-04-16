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
})(jQuery, Drupal);