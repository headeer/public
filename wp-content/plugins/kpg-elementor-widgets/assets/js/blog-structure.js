/**
 * KPG Blog Structure - Semantic HTML & TOC Fix
 * 
 * Fixes:
 * - Prevents duplicate TOC headers
 * - Wraps content in semantic <article> and <aside> tags
 */

(function($) {
	'use strict';

	/**
	 * Remove duplicate TOC headers (now using nav element, so check for nav)
	 */
	function fixDuplicateTOCHeaders() {
		var $tocContainers = $('.kpg-toc-container');
		
		if ($tocContainers.length > 1) {
			// Keep only the first one, hide the rest
			$tocContainers.slice(1).hide();
			console.log('KPG Blog Structure: Removed', $tocContainers.length - 1, 'duplicate TOC containers');
		}
	}

	/**
	 * Wrap blog content in semantic <article> tag
	 * Includes: title, post meta bar, TOC, blog content, author section, comments
	 */
	function wrapContentInArticle() {
		// Find all blog-related elements
		// 1. Post title - look for common title selectors
		var $title = $('h1.entry-title, h1.post-title, .elementor-heading-title:first, h1:first').first();
		
		// If no title found, try to find it in Elementor
		if (!$title.length) {
			// Look for Elementor heading widget that might be the title
			$title = $('.elementor-widget-heading h1, .elementor-widget-heading h2').first();
		}
		
		// 2. Post meta bar
		var $postMetaBar = $('.kpg-post-meta-bar').first();
		
		// 3. Table of Contents
		var $toc = $('.kpg-toc-container').first();
		
		// 4. Blog content
		var $blogContent = $('.kpg-blog-content-container').first();
		
		// 5. Author section
		var $authorSection = $('.kpg-blog-author-section').first();
		
		// 6. Comments
		var $comments = $('#comments, .comments-area, .wp-block-comments, .kpg-comments-container').first();
		
		// Check if already wrapped
		if ($postMetaBar.length && $postMetaBar.closest('article').length > 0) {
			return;
		}
		if ($blogContent.length && $blogContent.closest('article').length > 0) {
			return;
		}
		
		// Collect all elements that should be in article
		var $elements = $();
		
		// Add title if found and not already in article
		if ($title.length && !$title.closest('article').length) {
			$elements = $elements.add($title);
		}
		
		// Add post meta bar
		if ($postMetaBar.length && !$postMetaBar.closest('article').length) {
			$elements = $elements.add($postMetaBar);
		}
		
		// Add TOC
		if ($toc.length && !$toc.closest('article').length) {
			$elements = $elements.add($toc);
		}
		
		// Add blog content
		if ($blogContent.length && !$blogContent.closest('article').length) {
			$elements = $elements.add($blogContent);
		}
		
		// Add author section (if not already inside blog content)
		if ($authorSection.length && !$authorSection.closest('.kpg-blog-content-container').length && !$authorSection.closest('article').length) {
			$elements = $elements.add($authorSection);
		}
		
		// Add comments
		if ($comments.length && !$comments.closest('article').length) {
			$elements = $elements.add($comments);
		}
		
		// If we have elements to wrap
		if ($elements.length > 0) {
			// Try to find a common parent container first
			var $commonParent = null;
			
			// If all elements share a parent, use that
			if ($elements.length > 1) {
				var parents = [];
				$elements.each(function() {
					parents.push($(this).parent()[0]);
				});
				
				// Check if all have the same parent
				var allSameParent = parents.every(function(val, i, arr) {
					return val === arr[0];
				});
				
				if (allSameParent && parents[0]) {
					$commonParent = $(parents[0]);
				}
			}
			
			// If we found a common parent that's not body or html, wrap it
			if ($commonParent && $commonParent.length && 
				!$commonParent.is('body') && !$commonParent.is('html') && 
				!$commonParent.closest('article').length) {
				$commonParent.wrap('<article class="kpg-blog-article"></article>');
				console.log('KPG Blog Structure: Wrapped common parent container in <article>');
			} else {
				// Otherwise, wrap all elements together
				$elements.wrapAll('<article class="kpg-blog-article"></article>');
				console.log('KPG Blog Structure: Wrapped', $elements.length, 'elements in <article> (title, meta, TOC, content, author, comments)');
			}
		}
	}

	/**
	 * Wrap sidebar content in <aside> tags
	 */
	function wrapSidebarInAside() {
		// Look for "potrzebujesz pomocy prawnej" - this might be in a widget or custom section
		// We'll look for common patterns
		var $helpSection = $('[class*="pomoc"], [class*="help"], [class*="sidebar"], [id*="sidebar"]')
			.filter(function() {
				var text = $(this).text().toLowerCase();
				return text.includes('pomoc') || text.includes('prawn') || text.includes('potrzebujesz');
			}).first();
		
		// Look for "zobacz inne artykuły" or related posts
		var $relatedPosts = $('[class*="related"], [class*="inne"], [id*="related"]')
			.filter(function() {
				var text = $(this).text().toLowerCase();
				return text.includes('inne') || text.includes('related') || text.includes('zobacz');
			}).first();
		
		// Wrap help section
		if ($helpSection.length && !$helpSection.closest('aside').length) {
			$helpSection.wrap('<aside class="kpg-blog-sidebar kpg-blog-sidebar-help" aria-label="Pomoc prawna"></aside>');
			console.log('KPG Blog Structure: Wrapped help section in <aside>');
		}
		
		// Wrap related posts
		if ($relatedPosts.length && !$relatedPosts.closest('aside').length) {
			$relatedPosts.wrap('<aside class="kpg-blog-sidebar kpg-blog-sidebar-related" aria-label="Zobacz inne artykuły"></aside>');
			console.log('KPG Blog Structure: Wrapped related posts in <aside>');
		}
		
		// Also check for Elementor sidebar widgets
		$('.elementor-widget:has([class*="pomoc"]), .elementor-widget:has([class*="help"])').each(function() {
			var $widget = $(this);
			if (!$widget.closest('aside').length && !$widget.closest('article').length) {
				$widget.wrap('<aside class="kpg-blog-sidebar"></aside>');
			}
		});
	}

	/**
	 * Initialize all fixes
	 */
	function initBlogStructure() {
		fixDuplicateTOCHeaders();
		
		// Wait a bit for Elementor to render
		setTimeout(function() {
			wrapContentInArticle();
			wrapSidebarInAside();
		}, 500);
	}

	// Run on document ready
	$(document).ready(function() {
		initBlogStructure();
	});

	// Run after Elementor loads
	if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
		elementorFrontend.hooks.addAction('frontend/element_ready/global', function() {
			setTimeout(initBlogStructure, 300);
		});
	}

	// Run on window load
	$(window).on('load', function() {
		setTimeout(initBlogStructure, 200);
	});

})(jQuery);

