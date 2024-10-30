(function($) {
  $(document).ready( function() {
  	// variables
    var load_timeout = 3000;
    var error_count = 0;
    var $trigger = $('#laveem-fetch');
    var $input = $('#laveem-key');
    var original_value = $input.val();

	// disable input and show loading icon while attempting to fetch api key
    var disable = function() {
    	$trigger.addClass( 'loading' );
    	$input.attr( 'disabled', true );
    };

	// re-enable input and hide loading icon when not fetching api key
    var enable = function() {
    	$trigger.removeClass( 'loading' );
    	$input.removeAttr( 'disabled' );
    };
  });
})(jQuery);