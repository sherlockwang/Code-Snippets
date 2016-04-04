// Drupal js header explaination

// Closure and map jQuery to $
(function ($) {

	// For Drupal 7
	// Store our function as a property of Drupal.behaviors. 
	/**
	The benefit of having a single place for the behaviors is that 
	they can be applied consistently when the page is first loaded 
	and then when new content is added during AHAH/AJAX requests.
	*/
	Drupal.behaviours.myModuleFunction = {
		attach: function (context, settings) {

		}
	};

	// Another function
	Drupal.behaviours.myModuleAnotherFunction = {
		attach: function (context, settings) {

		}
		detach: function (context, settings) {

		}
	}

// Call this function and use jQuery as parameter	
})(jQuery);


// Same
(function ($){

}(jQuery));

// Do same with document.ready()
jQuery(document).ready(function ($){

});