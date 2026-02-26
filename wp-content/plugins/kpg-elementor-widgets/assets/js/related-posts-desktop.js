(function($) {
	'use strict';

	function getCurrentPostId() {
		if (typeof kpgRelatedPostsDesktopData !== 'undefined' && kpgRelatedPostsDesktopData.postId) {
			return parseInt(kpgRelatedPostsDesktopData.postId, 10) || 0;
		}

		var bodyClass = $('body').attr('class') || '';
		var match = bodyClass.match(/postid-(\d+)/);
		return match ? parseInt(match[1], 10) : 0;
	}

	function getRestBaseUrl() {
		var base = (typeof kpgRelatedPostsDesktopData !== 'undefined' && kpgRelatedPostsDesktopData.restUrl)
			? kpgRelatedPostsDesktopData.restUrl
			: '/wp-json/wp/v2/';

		if (base.slice(-1) !== '/') {
			base += '/';
		}

		return base;
	}

	function stripHtml(value) {
		return $('<div>').html(value || '').text().replace(/\s+/g, ' ').trim();
	}

	function shortenTeaser(text, maxLength) {
		var clean = (text || '').replace(/\s+/g, ' ').trim();
		if (!clean) {
			return '';
		}

		if (clean.length <= maxLength) {
			return clean;
		}

		var sliced = clean.slice(0, maxLength);
		var lastSpace = sliced.lastIndexOf(' ');
		if (lastSpace > Math.floor(maxLength * 0.6)) {
			sliced = sliced.slice(0, lastSpace);
		}

		return sliced.replace(/[.,;:!?-]+$/, '') + '...';
	}

	function formatPolishDate(dateString) {
		var date = new Date(dateString);
		if (Number.isNaN(date.getTime())) {
			return '';
		}

		var months = [
			'STYCZEŃ', 'LUTY', 'MARZEC', 'KWIECIEŃ', 'MAJ', 'CZERWIEC',
			'LIPIEC', 'SIERPIEŃ', 'WRZESIEŃ', 'PAŹDZIERNIK', 'LISTOPAD', 'GRUDZIEŃ'
		];
		var day = String(date.getDate()).padStart(2, '0');
		var month = months[date.getMonth()] || '';
		var year = date.getFullYear();

		return day + ' ' + month + ' ' + year;
	}

	function findRelatedSection() {
		var $heading = $('h2, h3').filter(function() {
			var text = ($(this).text() || '').toLowerCase();
			return text.includes('zobacz inne') || text.includes('inne artykuły') || text.includes('inne artykuly');
		}).first();

		if (!$heading.length) {
			return $();
		}

		var $section = $heading.closest('.elementor-element[data-element_type="container"], .e-con').first();
		if ($section.length) {
			return $section;
		}

		$section = $heading.closest('.elementor-element').first();
		if ($section.length) {
			return $section;
		}

		return $heading.parent();
	}

	function hasExistingCards($section) {
		return $section.find('.kpg-related-figma-grid').length > 0;
	}

	function getFeaturedImage(post) {
		if (!post || !post._embedded || !post._embedded['wp:featuredmedia'] || !post._embedded['wp:featuredmedia'][0]) {
			return '';
		}
		return post._embedded['wp:featuredmedia'][0].source_url || '';
	}

	function getAuthorData(post) {
		var fallback = { name: '', avatar: '', url: '' };
		if (!post) {
			return fallback;
		}

		if (post.kpg_author_name || post.kpg_author_avatar || post.kpg_author_url) {
			return {
				name: post.kpg_author_name || '',
				avatar: post.kpg_author_avatar || '',
				url: post.kpg_author_url || ''
			};
		}

		if (!post._embedded || !post._embedded.author || !post._embedded.author[0]) {
			return fallback;
		}

		var author = post._embedded.author[0];
		return {
			name: author.name || '',
			avatar: (author.avatar_urls && (author.avatar_urls['48'] || author.avatar_urls['96'])) ? (author.avatar_urls['48'] || author.avatar_urls['96']) : '',
			url: author.link || ''
		};
	}

	function getDisplayDate(post) {
		if (!post) {
			return '';
		}

		if (post.kpg_related_date) {
			return post.kpg_related_date;
		}

		return formatPolishDate(post.date);
	}

	function buildCard(post) {
		var title = stripHtml(post && post.title ? post.title.rendered : '');
		var excerptRaw = stripHtml(post && post.excerpt ? post.excerpt.rendered : '');
		var excerpt = shortenTeaser(excerptRaw, 260);
		var url = (post && post.link) ? post.link : '#';
		var image = getFeaturedImage(post);
		var authorData = getAuthorData(post);
		var date = getDisplayDate(post);

		var $card = $('<article>', { class: 'kpg-related-figma-card' });
		var $thumb = $('<a>', { class: 'kpg-related-figma-thumb', href: url, 'aria-label': title });
		if (image) {
			$thumb.append($('<img>', { src: image, alt: title, loading: 'lazy' }));
		}

		var $body = $('<div>', { class: 'kpg-related-figma-body' });
		var $title = $('<h3>', { class: 'kpg-related-figma-title' }).append($('<a>', { href: url, text: title }));
		var $excerptWrap = $('<div>', { class: 'kpg-related-figma-excerpt-wrap' });
		$excerptWrap.append($('<p>', { class: 'kpg-related-figma-excerpt', text: excerpt }));
		var $meta = $('<div>', { class: 'kpg-related-figma-meta' });
		var $metaText = $('<span>', { class: 'kpg-related-figma-meta-text' });

		if (authorData.avatar) {
			var $avatarNode = $('<img>', {
				class: 'kpg-related-figma-meta-avatar',
				src: authorData.avatar,
				alt: authorData.name || '',
				loading: 'lazy'
			});

			if (authorData.url) {
				$meta.append($('<a>', {
					class: 'kpg-related-figma-meta-avatar-link',
					href: authorData.url,
					'aria-label': authorData.name ? ('Zobacz artykuły autora: ' + authorData.name) : 'Zobacz artykuły autora',
					rel: 'author'
				}).append($avatarNode));
			} else {
				$meta.append($avatarNode);
			}
		} else {
			$meta.append($('<span>', { class: 'kpg-related-figma-meta-avatar kpg-related-figma-meta-avatar--empty', 'aria-hidden': 'true' }));
		}

		if (authorData.name) {
			if (authorData.url) {
				$metaText.append($('<a>', {
					class: 'kpg-related-figma-meta-author',
					href: authorData.url,
					rel: 'author',
					text: authorData.name
				}));
			} else {
				$metaText.append($('<span>', { class: 'kpg-related-figma-meta-author', text: authorData.name }));
			}
		}

		if (authorData.name && date) {
			$metaText.append($('<span>', { class: 'kpg-related-figma-meta-sep', text: ' • ' }));
		}

		if (date) {
			$metaText.append($('<span>', { class: 'kpg-related-figma-meta-date', text: date }));
		}

		$meta.append($metaText);

		$body.append($title, $excerptWrap, $meta);
		$card.append($thumb, $body);

		return $card;
	}

	function fetchPosts(restBase, postId, categories) {
		var params = {
			per_page: 2,
			exclude: postId,
			_embed: 'author,wp:featuredmedia'
		};

		if (Array.isArray(categories) && categories.length) {
			params.categories = categories.join(',');
		}

		return $.getJSON(restBase + 'posts?' + $.param(params));
	}

	function renderDesktopRelatedPosts() {
		if (!$('body.single-post').length) {
			return;
		}

		var $section = findRelatedSection();
		if (!$section.length) {
			return;
		}

		$section.addClass('kpg-related-figma');
		$section.find('.elementor-widget-posts, section.related-posts, .block-rel').addClass('kpg-related-figma-legacy-hidden');

		if (hasExistingCards($section)) {
			return;
		}

		if ($section.data('kpgRelatedDesktopLoaded')) {
			return;
		}
		$section.data('kpgRelatedDesktopLoaded', true);

		var postId = getCurrentPostId();
		if (!postId) {
			return;
		}

		var restBase = getRestBaseUrl();
		$.getJSON(restBase + 'posts/' + postId + '?_fields=id,categories')
			.done(function(currentPost) {
				var categories = (currentPost && Array.isArray(currentPost.categories)) ? currentPost.categories : [];
				fetchPosts(restBase, postId, categories)
					.done(function(posts) {
						var list = Array.isArray(posts) ? posts : [];
						if (!list.length) {
							fetchPosts(restBase, postId, [])
								.done(function(fallback) {
									var fallbackList = Array.isArray(fallback) ? fallback : [];
									if (!fallbackList.length) {
										return;
									}
									var $gridFallback = $('<div>', { class: 'kpg-related-figma-grid' });
									fallbackList.slice(0, 2).forEach(function(post) {
										$gridFallback.append(buildCard(post));
									});
									$section.append($gridFallback);
								});
							return;
						}

						var $grid = $('<div>', { class: 'kpg-related-figma-grid' });
						list.slice(0, 2).forEach(function(post) {
							$grid.append(buildCard(post));
						});

						$section.append($grid);
					});
			});
	}

	$(document).ready(function() {
		setTimeout(renderDesktopRelatedPosts, 250);
	});

	$(window).on('load', function() {
		setTimeout(renderDesktopRelatedPosts, 100);
	});

})(jQuery);
