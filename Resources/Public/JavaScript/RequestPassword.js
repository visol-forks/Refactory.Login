$('#reset-form').on('submit', function(e) {
	e.preventDefault();
	$(this).triggerRequest($(this));
});