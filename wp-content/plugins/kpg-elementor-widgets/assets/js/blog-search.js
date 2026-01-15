/**
 * KPG Blog Search - Fill search field from URL parameter
 * 
 * Fills Elementor search widget input with value from ?s= parameter
 */

(function() {
	'use strict';
	
	/**
	 * Fill search input with value from URL
	 */
	function fillSearchFromURL() {
		// Get search parameter from URL
		var urlParams = new URLSearchParams(window.location.search);
		var searchQuery = urlParams.get('s');
		
		if (!searchQuery) {
			return; // No search parameter
		}
		
		// Decode the search query (handles + as space and URL encoding)
		searchQuery = decodeURIComponent(searchQuery.replace(/\+/g, ' '));
		
		// Find Elementor search input fields
		var searchInputs = document.querySelectorAll('.e-search input[type="search"], .e-search input[type="text"], .e-search-form input[type="search"], .e-search-form input[type="text"]');
		
		if (searchInputs.length === 0) {
			// Try alternative selectors
			searchInputs = document.querySelectorAll('input[type="search"], input[name="s"]');
		}
		
		// Fill all found search inputs
		searchInputs.forEach(function(input) {
			if (input && input.value === '') {
				input.value = searchQuery;
				
				// Trigger input event to notify Elementor
				var inputEvent = new Event('input', { bubbles: true });
				input.dispatchEvent(inputEvent);
				
				// Trigger change event
				var changeEvent = new Event('change', { bubbles: true });
				input.dispatchEvent(changeEvent);
			}
		});
	}
	
	// Run on DOM ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', fillSearchFromURL);
	} else {
		fillSearchFromURL();
	}
	
	// Also run after Elementor frontend is ready (for dynamic content)
	if (typeof elementorFrontend !== 'undefined') {
		elementorFrontend.hooks.addAction('frontend/element_ready/global', function($scope) {
			fillSearchFromURL();
		});
	}
	
	// Run after a short delay to ensure Elementor widgets are rendered
	setTimeout(fillSearchFromURL, 500);
	setTimeout(fillSearchFromURL, 1000);
	setTimeout(fillSearchFromURL, 2000);
	
})();


