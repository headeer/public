/**
 * KPG Pagination Widget JavaScript
 * 
 * Based on prompts #18-27
 * 
 * Functionality:
 * - Click on page number = navigate to that page
 * - Click on arrow = next page
 * - Preserve ?sort= parameter if present
 * - Update URL and reload
 * - Disable arrow on last page
 */

(function($) {
	'use strict';

	/**
	 * Initialize pagination
	 */
	var initPagination = function() {
		var $pagination = $('.kpg-blog-pagination');
		
		if ($pagination.length === 0) {
			return;
		}

		$pagination.each(function() {
			var $this = $(this);
			var $items = $this.find('.kpg-blog-pagination-item');
			var $arrow = $this.find('.kpg-blog-pagination-arrow');
			
			// Get current page and max pages
			var currentPage = 1;
			var maxPages = 1;
			
			$items.each(function() {
				var page = parseInt($(this).data('page'));
				if ($(this).hasClass('active')) {
					currentPage = page;
				}
				if (page > maxPages) {
					maxPages = page;
				}
			});

			// Disable arrow if on last page
			if (currentPage >= maxPages) {
				$arrow.addClass('disabled');
			}

			// Click on page number
			$items.on('click', function(e) {
				e.preventDefault();
				
				var targetPage = parseInt($(this).data('page'));
				if (!targetPage || targetPage === currentPage) {
					return;
				}
				
				navigateToPage(targetPage);
			});

			// Click on arrow (next page)
			$arrow.on('click', function(e) {
				e.preventDefault();
				
				if ($(this).hasClass('disabled')) {
					return;
				}
				
				if (currentPage < maxPages) {
					navigateToPage(currentPage + 1);
				}
			});

			// Keyboard navigation
			$items.on('keydown', function(e) {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					$(this).trigger('click');
				}
			});

			$arrow.on('keydown', function(e) {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					$(this).trigger('click');
				}
			});
		});
	};

	/**
	 * Navigate to specific page
	 * Preserves ?sort= parameter if present
	 */
	function navigateToPage(page) {
		var url = new URL(window.location.href);
		
		// Set page parameter
		if (page === 1) {
			url.searchParams.delete('paged');
		} else {
			url.searchParams.set('paged', page);
		}
		
		// Preserve sort parameter
		// (already in URL, no need to add)
		
		// Navigate
		window.location.href = url.toString();
	}

	// Initialize on page load
	$(document).ready(function() {
		initPagination();
	});

	// Re-initialize for Elementor editor
	if (typeof elementorFrontend !== 'undefined') {
		elementorFrontend.hooks.addAction('frontend/element_ready/kpg-pagination.default', function($scope) {
			initPagination();
		});
		
		elementorFrontend.hooks.addAction('frontend/element_ready/widget', function($scope) {
			if ($scope.find('.kpg-blog-pagination').length) {
				initPagination();
			}
		});
	}

})(jQuery);


