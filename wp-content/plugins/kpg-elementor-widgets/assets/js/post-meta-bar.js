/**
 * KPG Post Meta Bar - Share Functionality & Email Truncation
 */

(function($) {
	'use strict';

	/**
	 * Initialize share buttons functionality
	 */
	function initShareButtons() {
		// Toggle share buttons on click
		$('.kpg-post-meta-share-section').off('click.share').on('click.share', function(e) {
			e.stopPropagation();
			var $section = $(this);
			var $buttons = $section.find('.kpg-post-meta-share-buttons');
			
			// Toggle visibility
			if ($buttons.hasClass('show')) {
				$buttons.removeClass('show');
			} else {
				// Hide other share sections
				$('.kpg-post-meta-share-buttons').removeClass('show');
				$buttons.addClass('show');
			}
		});
		
		// Close share buttons when clicking outside
		$(document).off('click.share').on('click.share', function(e) {
			if (!$(e.target).closest('.kpg-post-meta-share-section').length) {
				$('.kpg-post-meta-share-buttons').removeClass('show');
			}
		});
		
		// Handle share button clicks
		$('.kpg-share-btn').off('click.share').on('click.share', function(e) {
			e.preventDefault();
			e.stopPropagation();
			
			var platform = $(this).data('platform');
			var url = $(this).data('url') || window.location.href;
			var title = $(this).data('title') || document.title;
			
			var shareUrl = '';
			
			switch(platform) {
				case 'facebook':
					shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url);
					break;
				case 'twitter':
					shareUrl = 'https://twitter.com/intent/tweet?url=' + encodeURIComponent(url) + '&text=' + encodeURIComponent(title);
					break;
				case 'linkedin':
					shareUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(url);
					break;
			}
			
			if (shareUrl) {
				window.open(shareUrl, 'share', 'width=600,height=400');
				// Hide buttons after sharing
				$('.kpg-post-meta-share-buttons').removeClass('show');
			}
		});
	}

	$(document).ready(function() {
		initShareButtons();
	});

	if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
		elementorFrontend.hooks.addAction('frontend/element_ready/kpg-post-meta-bar.default', function() {
			initShareButtons();
		});
	}

})(jQuery);

