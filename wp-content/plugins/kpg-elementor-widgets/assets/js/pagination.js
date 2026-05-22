/**
 * KPG Pagination Widget JavaScript
 * 
 * Simple, clean pagination handler
 */

function kpgGetContextBaseUrl(currentUrl) {
	var pathname = currentUrl.pathname.replace(/\/page\/\d+\/?$/, '');
	if (!pathname) {
		pathname = '/';
	}
	return currentUrl.origin + pathname;
}

// IMMEDIATE handler - działa BEZ jQuery, natychmiast po załadowaniu skryptu
(function() {
	'use strict';
	
	// Handler który działa NATYCHMIAST, przed jQuery ready
	document.addEventListener('click', function(e) {
		var target = e.target;
		
		// Znajdź najbliższy element paginacji
		var paginationItem = target.closest('.kpg-blog-pagination-item');
		
		if (paginationItem) {
			// BLOKUJ WSZYSTKO
			e.preventDefault();
			e.stopPropagation();
			e.stopImmediatePropagation();

			if (
				paginationItem.disabled ||
				paginationItem.classList.contains('active') ||
				paginationItem.getAttribute('aria-current') === 'page'
			) {
				return false;
			}
			
			var page = parseInt(paginationItem.getAttribute('data-page'));
			
			if (page) {
				// Znajdź blog base URL
				var paginationContainer = paginationItem.closest('.kpg-blog-pagination');
				var blogBaseUrl = '';
				
				if (paginationContainer && paginationContainer.dataset.blogBaseUrl) {
					blogBaseUrl = paginationContainer.dataset.blogBaseUrl;
				} else {
					var url = new URL(window.location.href);
					blogBaseUrl = kpgGetContextBaseUrl(url);
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
				
				// Preserve search query parameter
				var searchQuery = '';
				if (paginationContainer && paginationContainer.dataset.searchQuery) {
					searchQuery = paginationContainer.dataset.searchQuery;
				} else {
					searchQuery = currentUrl.searchParams.get('s');
				}
				if (searchQuery) {
					params.set('s', searchQuery);
				}
				
				var queryString = params.toString();
				if (queryString) {
					paginationUrl += '?' + queryString;
				}
				
				window.location.href = paginationUrl;
			}
			
			return false;
		}
		
		// Sprawdź strzałkę
		var arrow = target.closest('.kpg-blog-pagination-arrow');
		if (arrow && !arrow.classList.contains('disabled') && !arrow.disabled) {
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
					blogBaseUrl = kpgGetContextBaseUrl(url);
				}
				
				blogBaseUrl = blogBaseUrl.replace(/\/$/, '');
				
				var nextPage = currentPage + 1;
				var paginationUrl = blogBaseUrl + '/?paged=' + nextPage;
				
				var currentUrl = new URL(window.location.href);
				var sortParam = currentUrl.searchParams.get('sort');
				if (sortParam) {
					paginationUrl += '&sort=' + encodeURIComponent(sortParam);
				}
				
				// Preserve search query parameter
				var searchQuery = '';
				if (paginationContainer && paginationContainer.dataset.searchQuery) {
					searchQuery = paginationContainer.dataset.searchQuery;
				} else {
					searchQuery = currentUrl.searchParams.get('s');
				}
				if (searchQuery) {
					paginationUrl += '&s=' + encodeURIComponent(searchQuery);
				}
				
				window.location.href = paginationUrl;
			}
			
			return false;
		}
	}, true); // capture phase - przechwytuje PRZED wszystkimi innymi handlerami
	
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
			blogBaseUrl = kpgGetContextBaseUrl(url);
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
		
		// Preserve search query parameter
		var searchQuery = '';
		var $pagination = $('.kpg-blog-pagination');
		if ($pagination.length > 0 && $pagination.data('search-query')) {
			searchQuery = $pagination.data('search-query');
		} else {
			searchQuery = url.searchParams.get('s');
		}
		if (searchQuery) {
			params.set('s', searchQuery);
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
				$arrow.addClass('disabled').attr({
					'aria-disabled': 'true',
					disabled: true
				});
			} else {
				$arrow.removeClass('disabled').attr('aria-disabled', 'false').prop('disabled', false);
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
