<?php
/**
 * Related Posts REST Fields
 *
 * Exposes author/avatar/date fields for frontend related-posts cards.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register custom REST fields for posts used by related posts cards.
 */
function kpg_register_related_posts_rest_fields() {
	$get_post_id = static function( $post_obj ) {
		if ( is_array( $post_obj ) ) {
			if ( isset( $post_obj['id'] ) ) {
				return (int) $post_obj['id'];
			}
			if ( isset( $post_obj['ID'] ) ) {
				return (int) $post_obj['ID'];
			}
		}

		if ( is_object( $post_obj ) ) {
			if ( isset( $post_obj->id ) ) {
				return (int) $post_obj->id;
			}
			if ( isset( $post_obj->ID ) ) {
				return (int) $post_obj->ID;
			}
		}

		return 0;
	};

	register_rest_field(
		'post',
		'kpg_author_avatar',
		[
			'get_callback' => function( $post_arr ) use ( $get_post_id ) {
				$post_id = $get_post_id( $post_arr );
				if ( ! $post_id ) {
					return '';
				}

				$author_id = (int) get_post_field( 'post_author', $post_id );
				if ( ! $author_id ) {
					return '';
				}

				if ( function_exists( 'kpg_get_author_avatar_url' ) ) {
					return (string) kpg_get_author_avatar_url( $author_id, 48 );
				}

				return (string) get_avatar_url( $author_id, [ 'size' => 48 ] );
			},
			'schema'       => [
				'description' => 'Author avatar URL for related posts card.',
				'type'        => 'string',
				'context'     => [ 'view', 'embed' ],
			],
		]
	);

	register_rest_field(
		'post',
		'kpg_author_name',
		[
			'get_callback' => function( $post_arr ) use ( $get_post_id ) {
				$post_id = $get_post_id( $post_arr );
				if ( ! $post_id ) {
					return '';
				}

				$author_id = (int) get_post_field( 'post_author', $post_id );
				if ( ! $author_id ) {
					return '';
				}

				$first = (string) get_the_author_meta( 'first_name', $author_id );
				$last  = (string) get_the_author_meta( 'last_name', $author_id );
				$name  = trim( $first . ' ' . $last );

				if ( '' === $name ) {
					$name = (string) get_the_author_meta( 'display_name', $author_id );
				}

				return $name;
			},
			'schema'       => [
				'description' => 'Author full name for related posts card.',
				'type'        => 'string',
				'context'     => [ 'view', 'embed' ],
			],
		]
		);

	register_rest_field(
		'post',
		'kpg_author_url',
		[
			'get_callback' => function( $post_arr ) use ( $get_post_id ) {
				$post_id = $get_post_id( $post_arr );
				if ( ! $post_id ) {
					return '';
				}

				$author_id = (int) get_post_field( 'post_author', $post_id );
				if ( ! $author_id ) {
					return '';
				}

				return (string) get_author_posts_url( $author_id );
			},
			'schema'       => [
				'description' => 'Author archive URL for related posts card.',
				'type'        => 'string',
				'context'     => [ 'view', 'embed' ],
			],
		]
	);

	register_rest_field(
		'post',
		'kpg_related_date',
		[
			'get_callback' => function( $post_arr ) use ( $get_post_id ) {
				$post_id = $get_post_id( $post_arr );
				if ( ! $post_id ) {
					return '';
				}

				$month_index = (int) get_the_date( 'n', $post_id );
				$months      = [
					1  => 'styczeń',
					2  => 'luty',
					3  => 'marzec',
					4  => 'kwiecień',
					5  => 'maj',
					6  => 'czerwiec',
					7  => 'lipiec',
					8  => 'sierpień',
					9  => 'wrzesień',
					10 => 'październik',
					11 => 'listopad',
					12 => 'grudzień',
				];

				$month = isset( $months[ $month_index ] ) ? $months[ $month_index ] : '';
				$day   = (string) get_the_date( 'd', $post_id );
				$year  = (string) get_the_date( 'Y', $post_id );

				return trim( $day . ' ' . $month . ' ' . $year );
			},
			'schema'       => [
				'description' => 'Polish publication date formatted for related posts card.',
				'type'        => 'string',
				'context'     => [ 'view', 'embed' ],
			],
		]
	);
}
add_action( 'rest_api_init', 'kpg_register_related_posts_rest_fields' );
