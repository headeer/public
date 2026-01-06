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
		// Remove any existing article tags that were incorrectly placed
		$('article.kpg-blog-article').each(function() {
			var $article = $(this);
			var $children = $article.children();
			$children.unwrap();
		});
		
		// Find all blog-related elements (only on single post pages)
		if (!$('body.single-post, body.single').length && !$('[data-elementor-type="single-post"]').length) {
			return;
		}
		
		// 1. Post title - look for Elementor theme-post-title widget
		var $title = $('.elementor-widget-theme-post-title h1, .elementor-widget-theme-post-title .elementor-heading-title').first();
		if (!$title.length) {
			$title = $('h1.entry-title, h1.post-title, .elementor-heading-title:first').first();
		}
		if (!$title.length) {
			$title = $('.elementor-widget-heading h1').first();
		}
		
		// 2. Post meta bar
		var $postMetaBar = $('.kpg-post-meta-bar').first();
		
		// 3. Table of Contents (take first visible one)
		var $toc = $('.kpg-toc-container:visible').first();
		if (!$toc.length) {
			$toc = $('.kpg-toc-container').first();
		}
		
		// 4. Blog content
		var $blogContent = $('.kpg-blog-content-container').first();
		
		// 5. Author section (already inside blog-content, but we'll include the whole container)
		var $authorSection = $('.kpg-blog-author-section').first();
		
		// 6. Comments
		var $comments = $('.kpg-comments-container').first();
		if (!$comments.length) {
			$comments = $('#comments, .comments-area, .wp-block-comments').first();
		}
		
		// Check if already wrapped in article
		if ($postMetaBar.length && $postMetaBar.closest('article.kpg-blog-article').length > 0) {
			return;
		}
		
		// Collect all elements that should be in article (in order)
		var $elements = $();
		
		// Add title if found and not already in article
		if ($title.length && !$title.closest('article.kpg-blog-article').length) {
			$elements = $elements.add($title);
		}
		
		// Add post meta bar
		if ($postMetaBar.length && !$postMetaBar.closest('article.kpg-blog-article').length) {
			$elements = $elements.add($postMetaBar);
		}
		
		// Add TOC
		if ($toc.length && !$toc.closest('article.kpg-blog-article').length) {
			$elements = $elements.add($toc);
		}
		
		// Add blog content (includes author section)
		if ($blogContent.length && !$blogContent.closest('article.kpg-blog-article').length) {
			$elements = $elements.add($blogContent);
		}
		
		// Add comments
		if ($comments.length && !$comments.closest('article.kpg-blog-article').length) {
			$elements = $elements.add($comments);
		}
		
		// If we have elements to wrap
		if ($elements.length > 0) {
			// Find the first element's parent container that contains all elements
			var $firstElement = $elements.first();
			var $container = $firstElement.parent();
			
			// Try to find Elementor container that wraps all these elements
			var $elementorContainer = $firstElement.closest('[data-elementor-type="single-post"]');
			if ($elementorContainer.length) {
				// Check if all elements are within this container
				var allInContainer = true;
				$elements.each(function() {
					if (!$.contains($elementorContainer[0], this)) {
						allInContainer = false;
						return false;
					}
				});
				
				if (allInContainer) {
					// Find the section that contains title, meta, TOC, content, comments
					// Look for the container that wraps these widgets
					var $widgetContainer = $firstElement.closest('.e-con, .elementor-element');
					
					// Try to find common parent of all elements
					var $commonParent = $firstElement.parent();
					$elements.each(function() {
						var $el = $(this);
						var $parent = $el.parent();
						// Find common ancestor
						while ($commonParent.length && !$.contains($commonParent[0], this)) {
							$commonParent = $commonParent.parent();
						}
					});
					
					// If we found a reasonable common parent (not body/html)
					if ($commonParent.length && 
						!$commonParent.is('body') && 
						!$commonParent.is('html') && 
						!$commonParent.closest('article.kpg-blog-article').length &&
						$commonParent.find($elements).length === $elements.length) {
						$commonParent.wrap('<article class="kpg-blog-article"></article>');
						console.log('KPG Blog Structure: Wrapped common parent container in <article>');
						return;
					}
				}
			}
			
			// Fallback: wrap all elements together
			$elements.wrapAll('<article class="kpg-blog-article"></article>');
			console.log('KPG Blog Structure: Wrapped', $elements.length, 'elements in <article> (title, meta, TOC, content, author, comments)');
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

