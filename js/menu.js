$(document).ready(function() {
	var sectionRulesOffsetTop = $('#rules').offset().top;
	var sectionAboutOffsetTop = $('#about').offset().top;
	var sectionContactOffsetTop = $('#contact').offset().top;

	$(document).on("scroll", function() {
		var scrollTop = $(document).scrollTop();
		var activeListItem;

		if (sectionRulesOffsetTop-130 < scrollTop && scrollTop < sectionAboutOffsetTop-130 ) {
			activeListItem = $('.navbar>ul>li:nth-child(2)');
		} else if (sectionAboutOffsetTop-130 < scrollTop && scrollTop < sectionContactOffsetTop-130) {
			activeListItem = $('.navbar>ul>li:nth-child(3)');
		} else if (scrollTop > sectionContactOffsetTop-130) {
			activeListItem = $('.navbar>ul>li:nth-child(4)');
		} else {
			activeListItem = $('.navbar>ul>li:nth-child(5)');
		}
		activeListItem.addClass('active');
		$('.navbar>ul>li').not(activeListItem).removeClass('active');
	});
});