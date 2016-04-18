/**
*	Set $ to jQuery
*/
jQuery(document).ready(function($) {
});

/**
*	Owl Slider 2.X
*/
var owl = $(".feature-slider");
owl.owlCarousel({
	items: 2,
	nav: true,
	center: true,
	loop: true,
	margin: 30
});

/**
*	Get url Query in JS
*/
function urlQuery (data) {
	var qd = {};
	// Put url Query in qd object
	location.search.substr(1).split("&").forEach(function(item) {
		(item.split("=")[0] in qd) ? qd[item.split("=")[0]].push(item.split("=")[1]) : qd[item.split("=")[0]] = [item.split("=")[1]]
	});
}

/**
*	Checkbox input value to array, delete value when unchecked
*/
$('#checkbox-type input[type="checkbox"]').change(function(){
	// Get Checkbox Value
	var temp_input = $(this).val();
	// If "checked"
	if ($(this).prop("checked")) {
		//	Check if value is not existed in array
		if ($.inArray(temp_input, typeID) == -1) {
			// Push value in
			typeID.push(temp_input);
		}
	}
	// If "unchecked"
	else {
		// Check if value already existed in array 
		if ($.inArray(temp_input, typeID) != -1) {
			// Delete this element
			typeID.splice($.inArray(temp_input, typeID), 1);
		}
	}
});

/**
*	Check Checkbox if value is already in array
*/
// Iterate each value in array
$.each(data["TopicCodeIDs"], function(index, value) {
	// Iterate each checkbox
	$.each($('#checkbox-track input'), function() {
		// Get checkbox value
		var checkboxValue = $(this).val();
		if (checkboxValue == value) {
			// Check this checkbox
			$(this).prop('checked', true);
		}
	});
	// Push this value to another temp array
	topic.push(value);
});

/**
*	Change list to dropdown and append up and down arrow
*/
// append arrow element
$('#categories-3 .widgettitle').append('<div class="arrow-down"></div>');
// hide list
$('#categories-3 ul').hide();
// set mouse to pointer
$('#categories-3 h4').css("cursor", "pointer");
// add click event to list title
$('#categories-3 h4').click(function() {
	// target follow list
	var dropdown = $(this).next();
	// function execute after slide animation finished
	dropdown.slideToggle(function() {
		if (dropdown.is(':visible')) {
			// remove current arrow
			$('#categories-3 .arrow-down').remove();
			// add new arrow
			$('#categories-3 .widgettitle').append('<div class="arrow-up"></div>');
		} else {
			$('#categories-3 .arrow-up').remove();
			$('#categories-3 .widgettitle').append('<div class="arrow-down"></div>');
		}
	});
});