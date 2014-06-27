$(document).ready(function() {
	var options = {};
	options.ui = {
		container: ".login-body",
		showVerdictsInsideProgressBar: true,
		viewports: {
			progress: ".pwstrength_viewport_progress"
		}
	};
	options.common = {
		debug: true,
		onLoad: function () {
			$('#messages').text('Start typing password');
		}
	};

	$('#password').pwstrength(options);

	$('#register-form').bootstrapValidator({
		message: 'This value is not valid',
		feedbackIcons: {
			valid: 'icon-check',
			invalid: 'icon-remove',
			validating: 'icon-refresh'
		},
		fields: {
			identifier: {
				message: 'The username is not valid',
				validators: {
					notEmpty: {
						message: 'The username is required and cannot be empty'
					},
					stringLength: {
						min: 6,
						max: 30,
						message: 'The username must be more than 6 and less than 30 characters long'
					},
					regexp: {
						regexp: /^[a-zA-Z0-9_]+$/,
						message: 'The username can only consist of alphabetical, number and underscore'
					},
					remote: {
						message: 'The username is not available',
						url: '/register/verifyaccountexists'
					}
				}
			},
			email: {
				validators: {
					notEmpty: {
						message: 'The email is required and cannot be empty'
					},
					emailAddress: {
						message: 'The input is not a valid email address'
					}
				}
			},
			password: {
				validators: {
					identical: {
						field: 'confirmPassword',
						message: 'The password and its confirm are not the same'
					}
				}
			},
			confirmPassword: {
				validators: {
					identical: {
						field: 'password',
						message: 'The password and its confirm are not the same'
					}
				}
			}
		}
	});
});

$('#register-form').on('submit', function(e) {
	e.preventDefault();
	$(this).triggerRequest($(this));
});