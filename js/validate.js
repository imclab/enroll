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
});