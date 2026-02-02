<?php
/**
 * Security: Anti-Bot Protection
 * 
 * Prevents bots from publishing posts via REST API, XML-RPC, or other methods
 * Only authenticated users with proper capabilities can publish posts
 * 
 * @package KPG_Elementor_Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Disable REST API for creating/updating posts (unless user is authenticated)
 */
function kpg_restrict_post_creation_via_rest_api( $result, $server, $request ) {
	// Only restrict POST/PUT/PATCH requests for posts
	if ( ! in_array( $request->get_method(), [ 'POST', 'PUT', 'PATCH', 'DELETE' ] ) ) {
		return $result;
	}
	
	$route = $request->get_route();
	
	// Check if this is a post creation/update endpoint
	if ( strpos( $route, '/wp/v2/posts' ) !== false ) {
		// Require authentication
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'rest_cannot_create',
				'Sorry, you are not allowed to create posts.',
				[ 'status' => rest_authorization_required_code() ]
			);
		}
		
		// Check if user has publish_posts capability
		if ( ! current_user_can( 'publish_posts' ) ) {
			return new WP_Error(
				'rest_cannot_create',
				'Sorry, you are not allowed to publish posts.',
				[ 'status' => 403 ]
			);
		}
	}
	
	return $result;
}
add_filter( 'rest_pre_dispatch', 'kpg_restrict_post_creation_via_rest_api', 10, 3 );

/**
 * Disable XML-RPC completely (common attack vector)
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Remove XML-RPC from headers
 */
function kpg_remove_xmlrpc_headers() {
	header( 'X-Pingback: ', true );
}
add_action( 'init', 'kpg_remove_xmlrpc_headers' );

/**
 * Block unauthorized post publishing
 */
function kpg_prevent_unauthorized_post_publishing( $post_id ) {
	// Skip for admin/editor actions
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	
	if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
		return;
	}
	
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		// Allow AJAX only for authenticated users
		if ( ! is_user_logged_in() ) {
			wp_die( 'Unauthorized', 'Unauthorized', [ 'response' => 403 ] );
		}
		return;
	}
	
	// Check if user is logged in
	if ( ! is_user_logged_in() ) {
		wp_die( 'You must be logged in to publish posts.', 'Unauthorized', [ 'response' => 403 ] );
	}
	
	// Check if user has publish_posts capability
	if ( ! current_user_can( 'publish_posts' ) ) {
		wp_die( 'You do not have permission to publish posts.', 'Forbidden', [ 'response' => 403 ] );
	}
}
add_action( 'transition_post_status', 'kpg_prevent_unauthorized_post_publishing', 10, 1 );

/**
 * Block post creation via wp_insert_post if not authenticated
 */
function kpg_restrict_wp_insert_post( $postarr, $wp_error = false ) {
	// Skip for admin/editor actions
	if ( is_admin() && current_user_can( 'publish_posts' ) ) {
		return $postarr;
	}
	
	// If post is being published, require authentication
	if ( isset( $postarr['post_status'] ) && $postarr['post_status'] === 'publish' ) {
		if ( ! is_user_logged_in() ) {
			if ( $wp_error ) {
				return new WP_Error( 'unauthorized', 'You must be logged in to publish posts.' );
			}
			wp_die( 'Unauthorized: You must be logged in to publish posts.', 'Unauthorized', [ 'response' => 403 ] );
		}
		
		if ( ! current_user_can( 'publish_posts' ) ) {
			if ( $wp_error ) {
				return new WP_Error( 'forbidden', 'You do not have permission to publish posts.' );
			}
			wp_die( 'Forbidden: You do not have permission to publish posts.', 'Forbidden', [ 'response' => 403 ] );
		}
	}
	
	return $postarr;
}
add_filter( 'wp_insert_post_data', 'kpg_restrict_wp_insert_post', 10, 2 );

/**
 * Disable REST API endpoints for unauthenticated users (optional - can be too restrictive)
 * Uncomment if you want to completely disable REST API for non-logged-in users
 */
/*
function kpg_disable_rest_api_for_guests( $result, $server, $request ) {
	if ( ! is_user_logged_in() ) {
		$route = $request->get_route();
		// Allow only read-only endpoints
		if ( in_array( $request->get_method(), [ 'POST', 'PUT', 'PATCH', 'DELETE' ] ) ) {
			return new WP_Error(
				'rest_forbidden',
				'REST API is disabled for unauthenticated users.',
				[ 'status' => 403 ]
			);
		}
	}
	return $result;
}
add_filter( 'rest_pre_dispatch', 'kpg_disable_rest_api_for_guests', 10, 3 );
*/

/**
 * Add security headers
 */
function kpg_add_security_headers() {
	if ( ! headers_sent() ) {
		header( 'X-Content-Type-Options: nosniff' );
		header( 'X-Frame-Options: SAMEORIGIN' );
		header( 'X-XSS-Protection: 1; mode=block' );
		// Remove server signature
		header_remove( 'X-Powered-By' );
	}
}
add_action( 'send_headers', 'kpg_add_security_headers' );

/**
 * Log suspicious activity (posts created by non-admin users)
 */
function kpg_log_suspicious_post_activity( $post_id ) {
	$post = get_post( $post_id );
	
	if ( ! $post || $post->post_type !== 'post' ) {
		return;
	}
	
	// Log if post was created by non-admin
	$user = wp_get_current_user();
	if ( $user && ! user_can( $user->ID, 'manage_options' ) ) {
		error_log( sprintf(
			'KPG Security: Post "%s" (ID: %d) created by user "%s" (ID: %d)',
			$post->post_title,
			$post_id,
			$user->user_login,
			$user->ID
		) );
	}
}
add_action( 'wp_insert_post', 'kpg_log_suspicious_post_activity', 10, 1 );

/**
 * Check for spam keywords in post content
 */
function kpg_check_for_spam_keywords( $postarr ) {
	// List of spam keywords (casino, gambling, etc.)
	$spam_keywords = [
		'casino',
		'gambling',
		'betting',
		'poker',
		'slot machine',
		'online casino',
		'casino bonus',
	];
	
	$content = strtolower( $postarr['post_content'] . ' ' . $postarr['post_title'] );
	
	foreach ( $spam_keywords as $keyword ) {
		if ( strpos( $content, $keyword ) !== false ) {
			// If user is not admin, block the post
			if ( ! current_user_can( 'manage_options' ) ) {
				if ( isset( $postarr['post_status'] ) && $postarr['post_status'] === 'publish' ) {
					// Change status to draft for review
					$postarr['post_status'] = 'draft';
					
					// Log the attempt
					error_log( sprintf(
						'KPG Security: Post blocked due to spam keyword "%s". User: %s',
						$keyword,
						wp_get_current_user()->user_login ?? 'Unknown'
					) );
				}
			}
		}
	}
	
	return $postarr;
}
add_filter( 'wp_insert_post_data', 'kpg_check_for_spam_keywords', 20, 1 );
