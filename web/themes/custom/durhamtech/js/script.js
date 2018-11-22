(function ($, Drupal) {
  Drupal.behaviors.durhamtech = {
    attach: function (context, settings) {
    }
  };

  Drupal.behaviors.header = {
    attach: function (context, settings) {
      // Open/close search
      $('.navbar-collapse .js-search-trigger').once().click(function () {
        $('#searchbar').slideToggle('fast');
      });
    }
  };

  Drupal.behaviors.alerts = {
    attach: function (context, settings) {
      $('.region-alerts .js-close-alert').once().click(function () {
        $('.region-alerts').empty();
      });
    }
  };

  Drupal.behaviors.zendesk = {
    attach: function () {
      $('a[href^="#javascript"]').each(function () {
        var oldUrl = $(this).attr("href"); // Get current url
        var newUrl = oldUrl.replace("#javascript", "javascript:$zopim.livechat.window.show()"); // Create new url
        $(this).attr("href", newUrl); // Set herf value
      });
    }
  };

  Drupal.behaviors.durham_slick = {
    attach: function () {
      // $('.slick').each(function() {
      //   var slickIndividual = $(this);
      //
      //   slickIndividual.slick({
      //       infinite: true,
      //       centerMode: true,
      //       speed: 500,
      //       autoplay: true,
      //       autoplaySpeed: 5000,
      //       slidesToShow: 3,
      //       slidesToScroll: 1,
      //       arrows: true,
      //       nextArrow: '<button class="slick-next slick-arrow" aria-label="Next" type="button" style=""><i class="fa fa-arrow-right"></i></button>',
      //       prevArrow: '<button class="slick-prev slick-arrow" aria-label="Previous" type="button" style=""><i class="fa fa-arrow-left"></i></button>',
      //   })
      // });

      $(".slick-social").not('.slick-initialized').slick({
        infinite: true,
        centerMode: true,
        speed: 500,
        autoplay: true,
        autoplaySpeed: 5000,
        //slidesToShow: 1,
        //slidesToScroll: 1,
        arrows: true,
        nextArrow: '<button class="slick-next slick-arrow" aria-label="Next" type="button" style=""><i class="fa fa-arrow-right"></i></button>',
        prevArrow: '<button class="slick-prev slick-arrow" aria-label="Previous" type="button" style=""><i class="fa fa-arrow-left"></i></button>',

        responsive: [
      {
        breakpoint: 693,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1
        }
      },
      {
        breakpoint: 99999,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1
        }
      },
    ]
      });

      //$('.slick').slick('reInit', function(slick){
      //    console.log('slicked');
      //});
    }
  };

  Drupal.behaviors.academicPrograms = {
    attach: function () {

      $( ".apply-tab" ).click(function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        $( ".apply-pane" ).hide();
        $(target).show();
      });

    }
  };

  Drupal.behaviors.searchNewsText = {
    attach: function () {
      $('#views-exposed-form-news-search-block-1 input').once().each(function() {
        $(this).attr('placeholder', 'Search News')
      });
    }
  };

 Drupal.behaviors.selectDateText = {
   attach: function () {
     // run function only one time
     //$('#edit-field-event-date').once().each(function() {
     //   $(this).attr('placeholder', 'Date (mm/dd/yyyy)')
             //$(this).val("Select Date");
     //});
   }
 };

})(jQuery, Drupal);
