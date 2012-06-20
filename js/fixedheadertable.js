(function($) {
  $(document).ready(function() { $(window).bind('scroll', function() { $('#header').fixedNav({ $window: $(this) }); }); });
  $.fn.extend({
    fixedNav: function( options ) {
      var $self          = $(this),
          self           = this,
          $window        = options.$window,
          offset         = $self.outerHeight() - $self.find('a').outerHeight() - 52,
          $fixedWrapper  = $('#fixedNav');

      if ( $window.scrollTop() >= offset && !$('#fixedNav').length ) {
        $fixedWrapper = $('<div id="fixedNav"></div>').prependTo('body');
        $fixedWrapper.append($self.clone()).find('header').removeAttr('id');
        setTimeout(function() {$fixedWrapper.find('#header').addClass('fixed');}, 0);
      } 
      else if ( $fixedWrapper.length && $window.scrollTop() <= offset ) {
        $fixedWrapper.remove();
      }
      return self;
    }
  });
})(jQuery);
