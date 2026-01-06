/**
 * KPG User Profile Avatar Upload
 * 
 * Handles avatar image upload in WordPress user profile
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		var $uploadButton = $('.kpg-upload-avatar-button');
		var $removeButton = $('.kpg-remove-avatar-button');
		var $avatarInput = $('#author_avatar_image');
		var $preview = $('.kpg-author-avatar-preview');

		// Upload button click
		$uploadButton.on('click', function(e) {
			e.preventDefault();

			var frame = wp.media({
				title: 'Wybierz zdjęcie profilowe',
				button: {
					text: 'Użyj tego zdjęcia'
				},
				multiple: false,
				library: {
					type: 'image'
				}
			});

			frame.on('select', function() {
				var attachment = frame.state().get('selection').first().toJSON();
				
				// Update hidden input
				$avatarInput.val(attachment.id);
				
				// Update preview
				if (attachment.sizes && attachment.sizes.thumbnail) {
					$preview.html('<img src="' + attachment.sizes.thumbnail.url + '" alt="Avatar preview" style="max-width: 150px; height: auto; border-radius: 8px; display: block;" />');
				} else {
					$preview.html('<img src="' + attachment.url + '" alt="Avatar preview" style="max-width: 150px; height: auto; border-radius: 8px; display: block;" />');
				}
				
				// Update button text
				$uploadButton.text('Zmień zdjęcie');
				
				// Show remove button if not already visible
				if ($removeButton.length === 0) {
					$uploadButton.after('<button type="button" class="button kpg-remove-avatar-button" style="margin-left: 10px;">Usuń zdjęcie</button>');
					$removeButton = $('.kpg-remove-avatar-button');
					attachRemoveHandler();
				}
			});

			frame.open();
		});

		// Remove button handler
		function attachRemoveHandler() {
			$removeButton.off('click').on('click', function(e) {
				e.preventDefault();
				
				// Clear input
				$avatarInput.val('');
				
				// Reset preview
				$preview.html('<div style="width: 150px; height: 150px; background: #e3ebec; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #899596; font-size: 14px;">Brak zdjęcia</div>');
				
				// Update button text
				$uploadButton.text('Wybierz zdjęcie');
				
				// Remove remove button
				$removeButton.remove();
				$removeButton = null;
			});
		}

		// Attach remove handler if button exists
		if ($removeButton.length > 0) {
			attachRemoveHandler();
		}
	});

})(jQuery);



