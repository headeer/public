/**
 * KPG Table of Contents - Smooth Scroll + Scroll Spy
 */

(function($) {
	'use strict';

	function initTOC() {
		var $toc = $('.kpg-toc-container');
		
		if ($toc.length === 0) {
			return;
		}

		$toc.each(function() {
			var $container = $(this);
			var $links = $container.find('.kpg-toc-link');
			var stickyOffset = parseInt($container.data('sticky-offset')) || 40;

			// Smooth scroll
			$links.on('click', function(e) {
				e.preventDefault();
				
				var targetId = $(this).data('target');
				var $target = $('#' + targetId);
				
				if ($target.length) {
					var offsetTop = $target.offset().top - stickyOffset - 20;
					
					$('html, body').animate({
						scrollTop: offsetTop
					}, 500, 'swing');
					
					// Update active immediately
					$links.removeClass('active');
					$(this).addClass('active');
				}
			});

			// Scroll spy
			function updateActiveLink() {
				var scrollPos = $(window).scrollTop() + stickyOffset + 50;
				
				$links.each(function() {
					var targetId = $(this).data('target');
					var $target = $('#' + targetId);
					
					if ($target.length) {
						var targetTop = $target.offset().top;
						var targetBottom = targetTop + $target.outerHeight();
						
						if (scrollPos >= targetTop && scrollPos < targetBottom) {
							$links.removeClass('active');
							$(this).addClass('active');
						}
					}
				});
			}

			// Update on scroll (throttled)
			var scrollTimeout;
			$(window).on('scroll', function() {
				if (scrollTimeout) {
					clearTimeout(scrollTimeout);
				}
				scrollTimeout = setTimeout(updateActiveLink, 50);
			});

			// Initial update
			updateActiveLink();
		});
	}

	$(document).ready(function() {
		initTOC();
	});

	if (typeof elementorFrontend !== 'undefined') {
		elementorFrontend.hooks.addAction('frontend/element_ready/kpg-table-of-contents.default', function() {
			initTOC();
		});
	}

})(jQuery);

