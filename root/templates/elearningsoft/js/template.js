/* Copyright (C) YOOtheme GmbH, http://www.gnu.org/licenses/gpl.html GNU/GPL */

(function($){

	$(document).ready(function() {

		var config = $('body').data('config') || {};

		// Smoothscroller
		$('.on-page-nav .nav a').smoothScroller({ duration: 500 });


		/* all site menu button */
		var $menuButton = $('.sitemap-button');
		var $menuDropdown = $('#js-sitemap');
		$menuButton.click(function() {
			$menuButton.toggleClass('js-pressed');
			$menuDropdown.removeClass('no-animation').toggleClass('js-shown');
		});

		//tooltips on clients page
		$('.clients-wall .client-logo').tooltip();
	});


})(jQuery);