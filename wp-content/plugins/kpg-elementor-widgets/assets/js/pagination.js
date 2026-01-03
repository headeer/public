/**
 * KPG Pagination Widget JavaScript
 * 
 * Simple, clean pagination handler
 */

// IMMEDIATE handler - działa BEZ jQuery, natychmiast po załadowaniu skryptu
(function() {
	'use strict';
	
	console.log('[KPG Pagination] Script loaded, setting up immediate handler');
	
	// Handler który działa NATYCHMIAST, przed jQuery ready
	document.addEventListener('click', function(e) {
		var target = e.target;
		
		// Znajdź najbliższy element paginacji
		var paginationItem = target.closest('.kpg-blog-pagination-item');
		
		if (paginationItem) {
			console.log('[KPG Pagination] IMMEDIATE: Pagination item clicked!', paginationItem);
			console.log('[KPG Pagination] IMMEDIATE: Event target:', target);
			console.log('[KPG Pagination] IMMEDIATE: Event type:', e.type);
			console.log('[KPG Pagination] IMMEDIATE: Event phase:', e.eventPhase);
			
			// BLOKUJ WSZYSTKO
			e.preventDefault();
			e.stopPropagation();
			e.stopImmediatePropagation();
			
			var page = parseInt(paginationItem.getAttribute('data-page'));
			console.log('[KPG Pagination] IMMEDIATE: Page number:', page);
			
			if (page) {
				// Znajdź blog base URL
				var paginationContainer = paginationItem.closest('.kpg-blog-pagination');
				var blogBaseUrl = '';
				
				if (paginationContainer && paginationContainer.dataset.blogBaseUrl) {
					blogBaseUrl = paginationContainer.dataset.blogBaseUrl;
				} else {
					var url = new URL(window.location.href);
					var pathname = url.pathname.replace(/\/page\/\d+\/?$/, '');
					if (!pathname.match(/\/blog/)) {
						pathname = '/blog';
					}
					blogBaseUrl = url.origin + pathname;
				}
				
				blogBaseUrl = blogBaseUrl.replace(/\/$/, '');
				
				// Build URL with query string
				var paginationUrl = blogBaseUrl + '/';
				var params = new URLSearchParams();
				
				if (page > 1) {
					params.set('paged', page);
				}
				
				var currentUrl = new URL(window.location.href);
				var sortParam = currentUrl.searchParams.get('sort');
				if (sortParam) {
					params.set('sort', sortParam);
				}
				
				var queryString = params.toString();
				if (queryString) {
					paginationUrl += '?' + queryString;
				}
				
				console.log('[KPG Pagination] IMMEDIATE: Navigating to:', paginationUrl);
				window.location.href = paginationUrl;
			}
			
			return false;
		}
		
		// Sprawdź strzałkę
		var arrow = target.closest('.kpg-blog-pagination-arrow');
		if (arrow && !arrow.classList.contains('disabled')) {
			console.log('[KPG Pagination] IMMEDIATE: Arrow clicked!', arrow);
			
			e.preventDefault();
			e.stopPropagation();
			e.stopImmediatePropagation();
			
			var paginationContainer = arrow.closest('.kpg-blog-pagination');
			var currentPage = 1;
			var maxPages = 1;
			
			if (paginationContainer) {
				var items = paginationContainer.querySelectorAll('.kpg-blog-pagination-item');
				items.forEach(function(item) {
					var page = parseInt(item.getAttribute('data-page'));
					if (item.classList.contains('active')) {
						currentPage = page;
					}
					if (page > maxPages) {
						maxPages = page;
					}
				});
			}
			
			if (currentPage < maxPages) {
				// Znajdź blog base URL
				var blogBaseUrl = '';
				
				if (paginationContainer && paginationContainer.dataset.blogBaseUrl) {
					blogBaseUrl = paginationContainer.dataset.blogBaseUrl;
				} else {
					var url = new URL(window.location.href);
					var pathname = url.pathname.replace(/\/page\/\d+\/?$/, '');
					if (!pathname.match(/\/blog/)) {
						pathname = '/blog';
					}
					blogBaseUrl = url.origin + pathname;
				}
				
				blogBaseUrl = blogBaseUrl.replace(/\/$/, '');
				
				var nextPage = currentPage + 1;
				var paginationUrl = blogBaseUrl + '/?paged=' + nextPage;
				
				var currentUrl = new URL(window.location.href);
				var sortParam = currentUrl.searchParams.get('sort');
				if (sortParam) {
					paginationUrl += '&sort=' + encodeURIComponent(sortParam);
				}
				
				console.log('[KPG Pagination] IMMEDIATE: Navigating to next page:', paginationUrl);
				window.location.href = paginationUrl;
			}
			
			return false;
		}
	}, true); // capture phase - przechwytuje PRZED wszystkimi innymi handlerami
	
	console.log('[KPG Pagination] Immediate handler registered');
})();

// jQuery-based handler (backup)
(function($) {
	'use strict';
	
	// Global function for navigation
	window.kpgNavigateToPage = function(page) {
		var currentUrl = window.location.href;
		var url = new URL(currentUrl);
		
		// Get blog base URL
		var $pagination = $('.kpg-blog-pagination');
		var blogBaseUrl = '';
		
		if ($pagination.length > 0 && $pagination.data('blog-base-url')) {
			blogBaseUrl = $pagination.data('blog-base-url');
		} else {
			var pathname = url.pathname.replace(/\/page\/\d+\/?$/, '');
			if (!pathname.match(/\/blog/)) {
				pathname = '/blog';
			}
			blogBaseUrl = url.origin + pathname;
		}
		
		blogBaseUrl = blogBaseUrl.replace(/\/$/, '');
		
		// Build URL with query string
		var paginationUrl = blogBaseUrl + '/';
		var params = new URLSearchParams();
		
		if (page > 1) {
			params.set('paged', page);
		}
		
		var sortParam = url.searchParams.get('sort');
		if (sortParam) {
			params.set('sort', sortParam);
		}
		
		var queryString = params.toString();
		if (queryString) {
			paginationUrl += '?' + queryString;
		}
		
		window.location.href = paginationUrl;
	};

	/**
	 * Initialize pagination (jQuery-based, for compatibility)
	 */
	function initPagination() {
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

			// Remove all existing handlers
			$items.off('click.kpg-pagination');
			$arrow.off('click.kpg-pagination');

			// Click on page number (backup handler)
			$items.on('click.kpg-pagination', function(e) {
				e.preventDefault();
				e.stopPropagation();
				e.stopImmediatePropagation();
				
				var $item = $(this);
				var targetPage = parseInt($item.data('page'));
				
				if (targetPage && targetPage !== currentPage) {
					window.kpgNavigateToPage(targetPage);
				}
				
				return false;
			});

			// Click on arrow (next page) - backup handler
			$arrow.on('click.kpg-pagination', function(e) {
				e.preventDefault();
				e.stopPropagation();
				e.stopImmediatePropagation();
				
				var $arrowEl = $(this);
				if ($arrowEl.hasClass('disabled')) {
					return false;
				}
				
				if (currentPage < maxPages) {
					window.kpgNavigateToPage(currentPage + 1);
				}
				
				return false;
			});
		});
	}
	
	// Initialize on page load
	$(document).ready(function() {
		initPagination();
	});
	
	$(window).on('load', function() {
		initPagination();
	});

	// Re-initialize for Elementor editor
	if (typeof elementorFrontend !== 'undefined' && typeof elementorFrontend.hooks !== 'undefined') {
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
