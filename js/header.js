$(document).ready(function() {
	var scrollValue = $(document).scrollTop();
	if (scrollValue == 0) {
		$('header').css('height', '220px');
		$('#gang').css('display', 'inline-block');
	}
	$(document).on("scroll", function() {
		scrollValue = $(document).scrollTop();
		$('header').css('height', (-scrollValue/2 + 220)+'px');
		$('#gang').css('opacity', 1 - scrollValue/100);
		$('#arrow-begin').css('opacity', 1 - scrollValue/100);
		$('#gang').css('top', -scrollValue/2 + 98+'px');

		if ($('#gang').css('opacity') == 0) {
			$('#gang').css('display', 'none');
			$('#arrow-begin').css('display', 'none');
		} else {
			$('#gang').css('display', 'inline-block');
			$('#arrow-begin').css('display', 'inline-block');
		}
	});
});