/**
 * KPG Blog Sorting Widget JavaScript
 * 
 * Based on Prompt #13 (line 6278) from cursor_padding_po_rozwini_ciu.md
 * 
 * Functionality:
 * - Toggle dropdown on button click
 * - Close dropdown when clicking outside
 * - Change URL parameter on option select
 * - Update selected text
 * - Set initial selected option based on URL
 * - Accessibility support
 */

(function($) {
	'use strict';

	/**
	 * Initialize sorting component
	 */
	var initSorting = function() {
		var $dropdown = $('.kpg_sorting-dropdown');
		
		if ($dropdown.length === 0) {
			return;
		}

		$dropdown.each(function() {
			var $this = $(this);
			var $button = $this.find('.kpg_sorting-button');
			var $menu = $this.find('.kpg_sorting-menu');
			var $selectedText = $this.find('.kpg_sorting-selected');
			var $options = $menu.find('.kpg_sorting-option');

			// Set initial selected option based on URL
			var urlParams = new URLSearchParams(window.location.search);
			var currentSort = urlParams.get('sort') || 'newest';

			$options.removeClass('kpg_sorting-active');
			var $activeOption = $options.filter('[data-sort="' + currentSort + '"]');
			if ($activeOption.length) {
				$activeOption.addClass('kpg_sorting-active');
				$selectedText.text($activeOption.text().trim());
			} else {
				// Default to newest
				$options.filter('[data-sort="newest"]').addClass('kpg_sorting-active');
				$selectedText.text($options.filter('[data-sort="newest"]').text().trim());
			}

			// Toggle dropdown on button click
			$button.on('click', function(e) {
				e.stopPropagation();
				
				// Close all other dropdowns
				$('.kpg_sorting-dropdown').not($this).removeClass('active');
				
				// Toggle this dropdown
				$this.toggleClass('active');
				
				// Update aria-expanded
				var isExpanded = $this.hasClass('active');
				$button.attr('aria-expanded', isExpanded);
				$this.attr('aria-expanded', isExpanded);
			});

			// Handle option selection
			$options.on('click', function(e) {
				e.stopPropagation();
				
				var sortValue = $(this).data('sort');
				var optionText = $(this).text().trim();
				
				// Check if there's an Elementor loop-grid on the page
				var $loopGrid = $('.elementor-loop-container, .elementor-widget-loop-grid');
				
				if ($loopGrid.length > 0) {
					// Elementor loop detected - use AJAX to refresh
					console.log('KPG Sorting: Elementor loop detected, sorting:', sortValue);
					
					// Update URL without reload
					var url = new URL(window.location.href);
					url.searchParams.set('sort', sortValue);
					url.searchParams.delete('paged');
					window.history.pushState({}, '', url.toString());
					
					// Update selected text immediately
					$selectedText.text(optionText);
					$options.removeClass('kpg_sorting-active');
					$(this).addClass('kpg_sorting-active');
					
					// Close dropdown
					$this.removeClass('active');
					$button.attr('aria-expanded', 'false');
					
					// Trigger page refresh to apply sorting
					// (Elementor loop-grid will read ?sort= parameter on server side)
					window.location.reload();
				} else {
					// No Elementor loop - standard URL redirect
					var url = new URL(window.location.href);
					url.searchParams.set('sort', sortValue);
					url.searchParams.delete('paged');
					window.location.href = url.toString();
				}
			});

			// Close dropdown when clicking outside
			$(document).on('click', function(event) {
				if (!$this.is(event.target) && $this.has(event.target).length === 0) {
					$this.removeClass('active');
					$button.attr('aria-expanded', 'false');
					$this.attr('aria-expanded', 'false');
				}
			});

			// Keyboard navigation
			$button.on('keydown', function(e) {
				// Enter or Space to toggle
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					$button.trigger('click');
				}
				// Escape to close
				else if (e.key === 'Escape') {
					$this.removeClass('active');
					$button.attr('aria-expanded', 'false');
					$this.attr('aria-expanded', 'false');
				}
			});

			$options.on('keydown', function(e) {
				// Enter or Space to select
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					$(this).trigger('click');
				}
				// Escape to close
				else if (e.key === 'Escape') {
					$this.removeClass('active');
					$button.attr('aria-expanded', 'false');
					$this.attr('aria-expanded', 'false');
					$button.focus();
				}
				// Arrow keys for navigation
				else if (e.key === 'ArrowDown') {
					e.preventDefault();
					var $next = $(this).parent().next().find('.kpg_sorting-option');
					if ($next.length) {
						$next.focus();
					}
				}
				else if (e.key === 'ArrowUp') {
					e.preventDefault();
					var $prev = $(this).parent().prev().find('.kpg_sorting-option');
					if ($prev.length) {
						$prev.focus();
					}
				}
			});
		});
	};

	// Initialize on page load
	$(document).ready(function() {
		initSorting();
	});

	// Re-initialize for Elementor editor
	if (typeof elementorFrontend !== 'undefined') {
		elementorFrontend.hooks.addAction('frontend/element_ready/kpg-blog-sorting.default', function($scope) {
			initSorting();
		});
		
		// Also trigger on global widget ready
		elementorFrontend.hooks.addAction('frontend/element_ready/widget', function($scope) {
			if ($scope.find('.kpg_sorting-container').length) {
				initSorting();
			}
		});
	}

})(jQuery);

