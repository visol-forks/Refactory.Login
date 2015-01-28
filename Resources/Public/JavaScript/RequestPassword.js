$('.ajax-request').on('submit', function(e) {
	e.preventDefault();
	$(this).triggerRequest($(this));
});