$(document).ready(function() {
	// validate the login form
	$("#login").validate({
		rules: {
			username: "required",
			password: "required"
		},
		highlight: function(element) {
			$(element).closest('.control-group').removeClass('success').addClass('error');
		},
		success: function(element) {
			element.text('OK!').addClass('valid').closest('.control-group').removeClass('error').addClass('success');
		}
	});
	// validate the add new XY form
	$("#addXYForm").validate({
		rules: {
			name: "required",
			description: "required",
			category: {
				required: true,
				minlength: 1
			},
			preferred_room: "required",
			preferred_class_size: "required"
		},
		highlight: function(element) {
			$(element).closest('.control-group').removeClass('success').addClass('error');
		},
		success: function(element) {
			element.text('OK!').addClass('valid').closest('.control-group').removeClass('error').addClass('success');
		}
	});
	// validate the update XY form
	$(".updateXYForm").validate({
		rules: {
			name: "required",
			description: "required",
			category: {
				required: true,
				minlength: 1
			},
			preferred_room: "required",
			preferred_class_size: "required"
		},
		highlight: function(element) {
			$(element).closest('.control-group').removeClass('success').addClass('error');
		},
		success: function(element) {
			element.text('OK!').addClass('valid').closest('.control-group').removeClass('error').addClass('success');
		}
	});
});