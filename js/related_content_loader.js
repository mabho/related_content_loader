
(function($) {

  // Equalizes the heights of elements with a given class.
  //equalHeights(jQuery('._equal-heights-services'));

  // Store our function as a property of Drupal.behaviors.
  Drupal.behaviors.pstylnyccode = {
    attach: function (context, settings) {

    }
  };

  /**
  * A jQuery plugin that fades out sibling elements of the main element.
  */
  $.fn.disableSiblings = function(data) {

    // Tests if a target element variable was passed...
    if(typeof data['target'] !== "undefined") {

      // Declares the target object.
      let target = data['target'];

      // Defines speed_transition
      let speed_transition = 250;
      if(typeof data['speed_transition'] !== "undefined") { speed_transition = data['speed_transition']; }

      // Defines partial_opacity
      let partial_opacity = 0.35;
      if(typeof data['partial_opacity'] !== "undefined") { partial_opacity = data['partial_opacity']; }

      // Defines comparison_layer
      let comparison_layer = '.item-list';
      if(typeof data['comparison_layer'] !== "undefined") { comparison_layer = data['comparison_layer']; }

      // Defines opacity of elements not clicked.
      $(target).parents(comparison_layer).stop().animate({backgroundColor: '#53cc98', opacity : 1}, speed_transition, function() {
        $(this).siblings().stop().animate({opacity : partial_opacity}, speed_transition);
      })
    }
  };

  /**
  * A jQuery plugin that scrolls to a given position.
  */
  $.fn.scrollTo = function(data) {

    // Tests if a target element variable was passed...
    if(typeof data['target'] !== "undefined") {

      // Declares the target object.
      let target = data['target'];

      // Tests if a subcategory could be found.
      if(jQuery(target).length > 0) {

        // Defines speed_transition
        let speed_transition = 250;
        if(typeof data['speed_transition'] !== "undefined") { speed_transition = data['speed_transition']; }

        // Grabs offset().top of the target element.
        let destination = jQuery(target).offset().top - 160;

        // Removes toolbar height from calculations, if it exists in the page
        let toolbarHeight = 0;
        let toolbar = jQuery('#toolbar');
        if(toolbar.length > 0) { destination = (destination - toolbar.height()); }

        // Scrolls to the target element position.
        jQuery("html:not(:animated),body:not(:animated)").animate({ scrollTop: destination}, speed_transition);
      }

    }
  };

  /**
  * A jQuery plugin that fades out sibling elements of the main element.
  */
  $.fn.restoreDefaults = function(data) {

    // Tests if a target element variable was passed...
    if(typeof data['target'] !== "undefined") {

      // Declares the target object.
      let target = data['target'];

      // Defines speed_transition
      let speed_transition = 250;
      if(typeof data['speed_transition'] !== "undefined") { speed_transition = data['speed_transition']; }

      // Defines opacity of elements not clicked.
      jQuery(target).stop().animate({opacity : 1}, speed_transition);
    }
  };

  /**
  * A jQuery plugin that reacts on date dropdown change.
  */
  $.fn.formStylistScheduleClearSiblings = function(data) {
    if(typeof data['target_id'] !== "undefined") {

    }
  }

})(jQuery);
