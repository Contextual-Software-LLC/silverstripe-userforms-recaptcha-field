(function($) {
	$(document).ready(function() {
		var form = $('.g-recaptcha').closest('form');
		var submitButton = form.find('input[type="submit"]');

		submitButton.prop('disabled',true);

		window.verifyRecaptcha = function(userResponse) {
			$.post("$callbackRoute", { userResponse: userResponse }, function(response){
				if (response.success) {
					submitButton.prop('disabled',false);
				}
			}, "json");
		};

		window.recaptchaExpired = function() {
			submitButton.prop('disabled',true);
		};
	});
}(jQuery));