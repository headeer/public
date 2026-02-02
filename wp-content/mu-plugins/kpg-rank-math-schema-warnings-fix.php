<?php
/**
 * Plugin Name: KPG – ukrycie ostrzeżeń Rank Math schema @type
 * Description: Tłumi ostrzeżenia PHP "Undefined array key '@type'" w pluginie Rank Math (bez modyfikacji plików pluginu).
 * Author: KPG
 * Version: 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rank_math/head', function () {
	$prev_handler = set_error_handler( function ( $errno, $errstr, $errfile, $errline ) {
		if ( $errno !== E_WARNING ) {
			return false;
		}
		if ( strpos( $errstr, "@type" ) !== false && strpos( $errfile, 'seo-by-rank-math' ) !== false ) {
			return true;
		}
		return false;
	}, E_WARNING );
	// Przywróć poprzedni handler po zakończeniu rank_math/head (ostatni priorytet).
	add_action( 'rank_math/head', function () use ( $prev_handler ) {
		restore_error_handler();
	}, 9999 );
}, 1 );
