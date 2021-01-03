$(document).ready(function() {

	$(document).on("scroll", function() {
		scrollValue = $(document).scrollTop();

		$('.top-players').css('opacity', scrollValue/100 - 2);

		if ($('.top-players').css('opacity') == 0) {
			$('.top-players').css('display', 'none');
		} else {
			$('.top-players').css('display', 'table');
		}
	});
});