/**
 * KPG Blog Content - Parse Elementor Content
 * 
 * Parsuje treść z Elementora i reorganizuje ją w sekcje z numeracją
 */

(function($) {
	'use strict';

	function parseBlogContent() {
		var $container = $('.kpg-blog-content-container');
		if ($container.length === 0) {
			return;
		}

		var $placeholder = $container.find('.kpg-blog-content-placeholder');
		if ($placeholder.length === 0) {
			return;
		}

		// Get settings from placeholder
		var showImportant = $placeholder.data('show-important') === 'yes';
		var importantPosition = $placeholder.data('important-position') || '';
		var importantText = $placeholder.data('important-text') || '';

		// Find Elementor content - try multiple selectors
		var $elementorContent = $('.elementor-element-44c7ac50 .e-con-inner, .elementor-post__content, .elementor-widget-theme-post-content .elementor-widget-container, .entry-content, .post-content, article .elementor, .elementor-12620 .e-con-inner');
		
		if ($elementorContent.length === 0) {
			// Try to find content in the page
			$elementorContent = $('body').find('h2, h3, p').first().closest('.elementor, .entry-content, article');
			if ($elementorContent.length === 0) {
				return;
			}
		}

		// Get all content elements in order - look for headings and paragraphs
		var $allElements = $elementorContent.find('h2, h3, p, ul, ol').filter(function() {
			return $(this).text().trim().length > 0;
		});

		if ($allElements.length === 0) {
			return;
		}

		// Parse into sections
		var sections = [];
		var currentSection = { heading: '', headingLevel: 'h2', content: '' };
		var intro = '';
		var hasFirstHeading = false;
		
		$allElements.each(function() {
			var $el = $(this);
			var tag = $el.prop('tagName').toLowerCase();
			var text = $el.text().trim();
			
			if (!text) {
				return;
			}
			
			// Check if it's a heading - preserve original level (h2, h3, h4)
			if (tag === 'h2' || tag === 'h3' || tag === 'h4') {
				hasFirstHeading = true;
				
				// Save previous section if it has content
				if (currentSection.heading || currentSection.content.trim()) {
					sections.push(currentSection);
				}
				
				// Start new section - preserve original heading level
				currentSection = {
					heading: text,
					headingLevel: tag, // h2, h3, or h4
					content: ''
				};
			} else {
				// Add content to current section or intro
				var html = $el.prop('outerHTML');
				if (html && html.trim()) {
					if (!hasFirstHeading) {
						// This is intro content (before first heading)
						intro += html;
					} else {
						// This is section content
						currentSection.content += html;
					}
				}
			}
		});
		
		// Add last section
		if (currentSection.heading || currentSection.content.trim()) {
			sections.push(currentSection);
		}
		
		// Render content
		var html = '';
		
		// Render intro if exists
		if (intro.trim()) {
			html += '<div class="kpg-blog-intro">' + intro + '</div>';
		}
		
		// Render sections
		if (sections.length > 0) {
			var sectionNumber = 1;
			var totalSections = sections.length;
			var importantRendered = false;
			
			sections.forEach(function(section, index) {
				var sectionId = section.heading ? 'toc-' + section.heading.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '') : 'section-' + (index + 1);
				
				// Get original heading level (h2, h3, h4) - default to h2 if not set
				var headingLevel = section.headingLevel || 'h2';
				
				html += '<div class="kpg-blog-section" id="' + sectionId + '">';
				html += '<div class="kpg-blog-section-row">';
				html += '<span class="kpg-blog-section-number">0.' + sectionNumber + '</span>';
				html += '<div class="kpg-blog-section-content">';
				if (section.heading) {
					html += '<' + headingLevel + ' class="kpg-blog-section-heading">' + section.heading + '</' + headingLevel + '>';
				}
				html += '<div class="kpg-blog-section-text">' + section.content + '</div>';
				html += '</div></div></div>';
				
				// Insert Important Section if needed
				if (showImportant && importantPosition && importantText && !importantRendered) {
					var shouldInsert = false;
					
					if (importantPosition === 'after_' + sectionNumber) {
						shouldInsert = true;
					} else if (importantPosition === 'end' && index === totalSections - 1) {
						shouldInsert = true;
					}
					
					if (shouldInsert) {
						html += '<div class="kpg-blog-important-section">';
						html += '<div class="kpg-blog-important-rectangle">';
						html += '<div class="kpg-blog-important-icon"></div>';
						html += '<div class="kpg-blog-important-content-wrapper">';
						html += '<div class="kpg-blog-important-line"></div>';
						html += '<div class="kpg-blog-important-title-wrapper">';
						html += '<h2 class="kpg-blog-important-title">Ważne</h2>';
						html += '</div>';
						html += '<div class="kpg-blog-important-text">' + importantText + '</div>';
						html += '</div></div></div>';
						importantRendered = true;
					}
				}
				
				sectionNumber++;
			});
		}
		
		if (html) {
			$placeholder.replaceWith(html);
		}
	}

	// Run on document ready and after Elementor loads
	$(document).ready(function() {
		// Wait for Elementor to fully render
		setTimeout(function() {
			parseBlogContent();
		}, 1000);
		
		// Also try after Elementor frontend init
		if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
			elementorFrontend.hooks.addAction('frontend/element_ready/global', function() {
				setTimeout(parseBlogContent, 500);
			});
		}
		
		// Try on window load
		$(window).on('load', function() {
			setTimeout(parseBlogContent, 500);
		});
	});

})(jQuery);

