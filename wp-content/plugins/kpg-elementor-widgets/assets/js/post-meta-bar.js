/**
 * KPG Post Meta Bar - Share Functionality
 */

(function($) {
	'use strict';

	function initShareButtons() {
		$('.kpg-share-btn').off('click').on('click', function(e) {
			e.preventDefault();
			
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

