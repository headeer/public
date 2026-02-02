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
		}
	}

	/**
	 * Wrap blog content in semantic <article> tag
	 * Includes: title, post meta bar, TOC, blog content, author section, comments
	 */
	function wrapContentInArticle() {
		// Only on single post pages
		if (!$('body.single-post, body.single').length && !$('[data-elementor-type="single-post"]').length) {
			return;
		}
		
		// Remove any incorrectly placed article tags (that only wrap small sections)
		$('article.kpg-blog-article').each(function() {
			var $article = $(this);
			// If article only contains post meta bar or a small section, unwrap it
			if ($article.find('.kpg-post-meta-bar').length && 
				!$article.find('.kpg-blog-content-container').length && 
				!$article.find('.elementor-widget-theme-post-title').length) {
				$article.children().unwrap();
			}
		});
		
		// Check if we already have a proper article tag wrapping all content
		var $existingArticle = $('article.kpg-blog-article').filter(function() {
			var $art = $(this);
			return $art.find('.kpg-post-meta-bar').length > 0 && 
				   ($art.find('.kpg-blog-content-container').length > 0 || $art.find('.elementor-widget-theme-post-title').length > 0);
		}).first();
		
		if ($existingArticle.length) {
			// Already properly wrapped
			return;
		}
		
		// Find all blog-related elements
		// 1. Post title - look for Elementor theme-post-title widget
		var $titleWidget = $('.elementor-widget-theme-post-title').first();
		var $title = $titleWidget.length ? $titleWidget : $('.elementor-widget-heading:has(h1)').first();
		
		// 2. Post meta bar
		var $postMetaBar = $('.kpg-post-meta-bar').first();
		
		// 3. Table of Contents
		var $toc = $('.kpg-toc-container:visible').first();
		if (!$toc.length) {
			$toc = $('.kpg-toc-container').first();
		}
		
		// 4. Blog content
		var $blogContent = $('.kpg-blog-content-container').first();
		
		// 5. Comments
		var $comments = $('.kpg-comments-container, #comments, .comments-area, .wp-block-comments').first();
		
		// Find the Elementor single post container
		var $elementorPost = $('[data-elementor-type="single-post"]').first();
		if (!$elementorPost.length) {
			return;
		}
		
		// Collect all elements that should be in article
		var $elementsToWrap = $();
		if ($titleWidget.length) $elementsToWrap = $elementsToWrap.add($titleWidget);
		if ($postMetaBar.length) $elementsToWrap = $elementsToWrap.add($postMetaBar);
		if ($toc.length) $elementsToWrap = $elementsToWrap.add($toc);
		if ($blogContent.length) $elementsToWrap = $elementsToWrap.add($blogContent);
		if ($comments.length) $elementsToWrap = $elementsToWrap.add($comments);
		
		if ($elementsToWrap.length === 0) {
			return;
		}
		
		// Find the common parent of all these elements
		var $commonParent = null;
		if ($elementsToWrap.length === 1) {
			// Only one element, use its parent
			$commonParent = $elementsToWrap.first().parent();
		} else {
			// Multiple elements - find common ancestor
			var $first = $elementsToWrap.first();
			$commonParent = $first.parent();
			
			// Walk up the tree until we find a parent that contains all elements
			while ($commonParent.length && 
				   !$commonParent.is('body') && 
				   !$commonParent.is('html') &&
				   !$commonParent.is('[data-elementor-type="single-post"]')) {
				var allContained = true;
				$elementsToWrap.each(function() {
					if (!$.contains($commonParent[0], this)) {
						allContained = false;
						return false;
					}
				});
				
				if (allContained) {
					// Check if this parent is reasonable (not too high up)
					// Make sure it's within the Elementor post container
					if ($.contains($elementorPost[0], $commonParent[0])) {
						break;
					}
				}
				
				$commonParent = $commonParent.parent();
			}
		}
		
		// If we found a reasonable common parent, wrap it
		if ($commonParent && $commonParent.length && 
			!$commonParent.is('body') && 
			!$commonParent.is('html') &&
			!$commonParent.is('article') &&
			!$commonParent.closest('article.kpg-blog-article').length &&
			$.contains($elementorPost[0], $commonParent[0])) {
			$commonParent.wrap('<article class="kpg-blog-article"></article>');
		} else {
			// Fallback: wrap all elements together
			$elementsToWrap.wrapAll('<article class="kpg-blog-article"></article>');
		}
	}

	/**
	 * Wrap sidebar content in <aside> tags
	 */
	function wrapSidebarInAside() {
		// Only on single post pages
		if (!$('body.single-post, body.single').length && !$('[data-elementor-type="single-post"]').length) {
			return;
		}
		
		// Look for "zobacz inne artykuły" or related posts section
		// Check for heading with text "Zobacz inne artykuły"
		var $relatedHeading = $('h2, h3').filter(function() {
			return $(this).text().toLowerCase().includes('zobacz inne') || 
				   $(this).text().toLowerCase().includes('inne artykuły');
		}).first();
		
		// Find the container that holds the "Zobacz inne artykuły" section
		var $relatedSection = null;
		if ($relatedHeading.length) {
			// Find the Elementor container that contains this heading
			$relatedSection = $relatedHeading.closest('.e-con, .elementor-element');
		} else {
			// Fallback: look for containers with class patterns
			$relatedSection = $('[class*="related"], [class*="inne"], [id*="related"]')
				.filter(function() {
					var text = $(this).text().toLowerCase();
					return text.includes('inne') || text.includes('related') || text.includes('zobacz');
				}).first();
		}
		
		// Wrap related posts section if not already in aside
		if ($relatedSection && $relatedSection.length && 
			!$relatedSection.closest('aside').length && 
			!$relatedSection.closest('article').length) {
			$relatedSection.wrap('<aside class="kpg-blog-sidebar kpg-blog-sidebar-related" aria-label="Zobacz inne artykuły"></aside>');
		}
		
		// Look for "potrzebujesz pomocy prawnej" section
		var $helpSection = $('[class*="pomoc"], [class*="help"], [class*="sidebar"], [id*="sidebar"]')
			.filter(function() {
				var text = $(this).text().toLowerCase();
				return (text.includes('pomoc') || text.includes('prawn') || text.includes('potrzebujesz')) &&
					   !$(this).closest('aside').length && !$(this).closest('article').length;
			}).first();
		
		// Wrap help section
		if ($helpSection.length) {
			$helpSection.wrap('<aside class="kpg-blog-sidebar kpg-blog-sidebar-help" aria-label="Pomoc prawna"></aside>');
		}
	}

	/**
	 * Initialize all fixes
	 */
	function initBlogStructure() {
		fixDuplicateTOCHeaders();
		
		// Wait for Elementor to fully render
		setTimeout(function() {
			wrapContentInArticle();
			wrapSidebarInAside();
		}, 1000);
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

