(function($){
	 $.fn.triggerRequest = function($this) {
		$this.ajaxSubmit({
			type: $this.attr('method'),
			url: $this.attr('action'),
			dataType: 'json',
			beforeSubmit: function() {
				$this.find('.tooltip').remove();
				$('input[type=submit]').attr('disabled', 'disabled');
				$('input[type=submit]').addClass('promise');
			},
			error: function(jqXHR, status, error) {
				$('input[type=submit]').removeAttr('disabled');
				$('.feedback').append('<div class="tooltip bottom in tooltip-warning"><div class="tooltip-arrow"></div><div class="tooltip-inner">This service is unavailable due to an error.</div></div>');
			},
			success: function(callback, status, jqXHR) {
				$('input[type=submit]').removeAttr("disabled");
				$('input[type=submit]').removeClass('promise');

				if (null !== callback) {
					if (jqXHR.status === 200 && typeof callback === 'object') {
						$('fieldset').effect('shake', {times: 1}, 400);
						$('.feedback').append('<div class="tooltip bottom in tooltip-'+ callback.type +'"><div class="tooltip-arrow"></div><div class="tooltip-inner">' + callback.message + '</div></div>');
					} else {
						document.location = callback;
					}
				}
			}
		});
	 };
 })(jQuery);

var notificationOptions = {
	tapToDismiss: false,
	toastClass: 'notification',
	containerId: 'notification-container',
	iconClasses: {
		error: 'notification-error',
		info: 'notification-info',
		success: 'notification-success',
		warning: 'notification-warning'
	},
	titleClass: 'title',
	messageClass: 'message',
	closeHtml: '<i class="icon-remove"></i>',
	positionClass: 'notification-top',
	showMethod: 'slideDown',
	hideMethod: 'slideUp',
	hideDuration: 500,
	showEasing: 'easeOutBounce',
	hideEasing: 'easeInCubic',
	timeOut: 0,
	extendedTimeOut: 0,
	closeButton: true
};

$('[data-loading-text]').each(function(i, el) {
	var $this = $(el);
	$this.on('click', function(ev) {
		$this.button('loading');
		setTimeout(function(){ $this.button('reset'); }, 1000);
	});
});