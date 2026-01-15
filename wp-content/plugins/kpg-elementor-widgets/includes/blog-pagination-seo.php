<?php
/**
 * Blog Pagination SEO
 * 
 * Adds canonical, prev, and next links for blog pagination pages
 * 
 * @package KPG_Elementor_Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add canonical, prev, and next links for blog pagination
 */
function kpg_add_blog_pagination_seo() {
	// EXCLUDE single posts - our code should only run on archive pages
	// Rank Math SEO handles canonical for single posts
	if ( is_single() || is_singular( 'post' ) ) {
		return;
	}
	
	// Get request URI first (for debugging and fallback)
	$request_uri = $_SERVER['REQUEST_URI'] ?? '';
	
	// Check if we're on a blog-related page
	$is_blog_page = false;
	
	// FIRST: Check if URL contains /blog/ (most reliable for Elementor pages)
	if ( preg_match( '#^/blog(/|/page/|$)#', $request_uri ) ) {
		$is_blog_page = true;
	}
	
	// Check if it's the posts page (page_for_posts)
	$posts_page_id = get_option( 'page_for_posts' );
	if ( $posts_page_id ) {
		$current_page_id = get_queried_object_id();
		if ( $current_page_id == $posts_page_id ) {
			$is_blog_page = true;
		}
	}
	
	// Check standard WordPress conditions
	if ( is_home() || is_archive() || is_search() ) {
		$is_blog_page = true;
	}
	
	// If not a blog page, exit
	if ( ! $is_blog_page ) {
		return;
	}

	// Check if Rank Math SEO is active and handling pagination
	// If Rank Math is generating these links, we might want to disable ours
	// But for now, we'll add ours with higher priority to ensure they're correct
	if ( class_exists( 'RankMath' ) ) {
		// Rank Math might add canonical/prev/next, but we want to ensure ours are correct
		// Our links will be added with priority 1, so they come first
		// Rank Math typically uses priority 10, so ours will take precedence
	}

	global $wp_query;
	
	// Get current page number
	$paged = max( 1, get_query_var( 'paged' ) );
	if ( $paged === 0 ) {
		$paged = 1;
	}

	// Get max pages - try multiple sources
	$max_pages = 0;
	
	// First, try global wp_query
	if ( isset( $wp_query->max_num_pages ) && $wp_query->max_num_pages > 0 ) {
		$max_pages = $wp_query->max_num_pages;
	}
	
	// If wp_query doesn't have it (Elementor pages), try to get from pagination widget data
	// Check if we're on a page with pagination - if paged > 1, there must be at least 2 pages
	if ( $max_pages <= 1 && $paged > 1 ) {
		// If we're on page 2+, assume there are at least 2 pages
		// We'll check if there's a next page by looking at pagination elements
		$max_pages = $paged + 1; // Assume at least one more page
	}
	
	// If still no max_pages and we're on page 1, check if URL suggests pagination exists
	if ( $max_pages <= 1 && $paged === 1 ) {
		// On page 1, we can't know max pages without query
		// But if there's pagination widget, it will show if there are more pages
		// For now, set to 1 and let prev/next be added only if we detect more pages
		$max_pages = 1;
	}
	
	// Fallback: if we're on page 2+ and max_pages is still 0, assume at least 2 pages
	if ( $max_pages <= 1 && $paged > 1 ) {
		$max_pages = max( $paged + 1, 2 );
	}
	
	// Get query parameters (sort, search, etc.) - preserve them
	$query_params = [];
	if ( isset( $_GET['sort'] ) && ! empty( $_GET['sort'] ) ) {
		$query_params['sort'] = sanitize_text_field( $_GET['sort'] );
	}
	if ( isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) {
		$query_params['s'] = sanitize_text_field( $_GET['s'] );
	}
	// Preserve other query params if needed
	if ( isset( $_GET['e_search_props'] ) && ! empty( $_GET['e_search_props'] ) ) {
		$query_params['e_search_props'] = sanitize_text_field( $_GET['e_search_props'] );
	}

	// Build base URL (without pagination)
	$base_url = kpg_get_current_blog_url();

	// Remove existing pagination from URL
	$base_url = preg_replace( '/\/page\/\d+\/?$/', '', $base_url );
	$base_url = preg_replace( '/[?&]paged=\d+/', '', $base_url );
	$base_url = rtrim( $base_url, '/' );

	// For search, base URL might already have query params
	if ( is_search() ) {
		// Remove query params from base URL, we'll add them back
		$parsed = parse_url( $base_url );
		$base_url = $parsed['scheme'] . '://' . $parsed['host'];
		if ( isset( $parsed['path'] ) ) {
			$base_url .= $parsed['path'];
		}
		$base_url = rtrim( $base_url, '/' );
	}

	// Build canonical URL for current page
	$canonical_url = $base_url;
	if ( $paged > 1 ) {
		// Check if base URL already ends with /
		if ( ! is_search() ) {
			$canonical_url .= '/page/' . $paged . '/';
		} else {
			// For search, use query parameter
			$canonical_url = add_query_arg( 'paged', $paged, $base_url );
		}
	} else {
		if ( ! is_search() ) {
			$canonical_url .= '/';
		}
	}
	
	// Add query parameters if any
	if ( ! empty( $query_params ) ) {
		$canonical_url = add_query_arg( $query_params, $canonical_url );
	}

	// Output canonical
	echo '<link rel="canonical" href="' . esc_url( $canonical_url ) . '" />' . "\n";

	// Build prev URL - always add if we're on page > 1
	// (if we're on page 2+, there must be at least 2 pages)
	if ( $paged > 1 ) {
		$prev_url = $base_url;
		if ( ! is_search() ) {
			if ( $paged > 2 ) {
				$prev_url .= '/page/' . ( $paged - 1 ) . '/';
			} else {
				$prev_url .= '/';
			}
		} else {
			// For search, use query parameter
			if ( $paged > 2 ) {
				$prev_url = add_query_arg( 'paged', $paged - 1, $base_url );
			}
		}
		
		// Add query parameters if any
		if ( ! empty( $query_params ) ) {
			$prev_url = add_query_arg( $query_params, $prev_url );
		}
		
		echo '<link rel="prev" href="' . esc_url( $prev_url ) . '" />' . "\n";
	}

	// Build next URL
	// If we know max_pages, only add if there's a next page
	// If we don't know max_pages but we're on page 2+, assume there might be more
	$should_add_next = false;
	if ( $max_pages > 1 && $paged < $max_pages ) {
		$should_add_next = true;
	} elseif ( $max_pages <= 1 && $paged > 1 ) {
		// We're on page 2+ but don't know max - assume there might be more pages
		// This is a safe assumption - if there's page 2, there might be page 3
		$should_add_next = true;
	}
	
	if ( $should_add_next ) {
		$next_url = $base_url;
		if ( ! is_search() ) {
			$next_url .= '/page/' . ( $paged + 1 ) . '/';
		} else {
			// For search, use query parameter
			$next_url = add_query_arg( 'paged', $paged + 1, $base_url );
		}
		
		// Add query parameters if any
		if ( ! empty( $query_params ) ) {
			$next_url = add_query_arg( $query_params, $next_url );
		}
		
		echo '<link rel="next" href="' . esc_url( $next_url ) . '" />' . "\n";
	}
}

/**
 * Get current blog URL based on context
 */
function kpg_get_current_blog_url() {
	// Get blog page ID
	$blog_page_id = get_option( 'page_for_posts' );
	$current_page_id = get_queried_object_id();
	
	// Check if current page is the posts page
	if ( $blog_page_id && $current_page_id == $blog_page_id ) {
		return get_permalink( $blog_page_id );
	}
	
	if ( is_home() ) {
		// Main blog page
		if ( $blog_page_id ) {
			return get_permalink( $blog_page_id );
		}
		return home_url( '/blog/' );
	}
	
	if ( is_category() ) {
		$category = get_queried_object();
		return get_category_link( $category->term_id );
	}
	
	if ( is_tag() ) {
		$tag = get_queried_object();
		return get_tag_link( $tag->term_id );
	}
	
	if ( is_author() ) {
		$author = get_queried_object();
		$author_base = get_option( 'kpg_author_base', 'autor' );
		return home_url( '/' . $author_base . '/' . $author->user_nicename . '/' );
	}
	
	if ( is_search() ) {
		$search_query = get_query_var( 's' );
		return home_url( '/?s=' . urlencode( $search_query ) );
	}
	
	// Fallback: check if URL contains /blog/
	$request_uri = $_SERVER['REQUEST_URI'] ?? '';
	if ( preg_match( '#^/blog(/|/page/|$)#', $request_uri ) ) {
		return home_url( '/blog/' );
	}
	
	// Final fallback to current URL
	return home_url( add_query_arg( null, null ) );
}

// Hook into wp_head
add_action( 'wp_head', 'kpg_add_blog_pagination_seo', 1 );

