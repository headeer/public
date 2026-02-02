<?php
/**
 * Rank Math SEO Migration Tool
 * 
 * Migrates Rank Math SEO meta tags from old WordPress site to new site
 * 
 * Usage:
 * 1. Configure old database connection in wp-config.php or via constants
 * 2. Visit: /wp-admin/admin.php?page=kpg-rank-math-migration
 * 3. Or use WP-CLI: wp kpg-migrate-rankmath
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add admin menu for Rank Math migration
 */
function kpg_add_rankmath_migration_menu() {
	add_management_page(
		'Rank Math SEO Migration',
		'Rank Math Migration',
		'manage_options',
		'kpg-rank-math-migration',
		'kpg_rankmath_migration_page'
	);
}
add_action( 'admin_menu', 'kpg_add_rankmath_migration_menu' );

/**
 * Migration page HTML
 */
function kpg_rankmath_migration_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'You do not have sufficient permissions to access this page.' );
	}

	// Handle cleanup request
	if ( isset( $_POST['kpg_replace_canonical_urls'] ) && check_admin_referer( 'kpg_replace_canonical_urls_action' ) ) {
		$old_url = esc_url_raw( $_POST['old_canonical_url'] ?? '' );
		if ( empty( $old_url ) ) {
			echo '<div class="notice notice-error"><p>Musisz podać stary URL do zamiany.</p></div>';
		} else {
			$result = kpg_replace_canonical_urls( $old_url );
			echo '<div class="notice notice-' . ( $result['success'] ? 'success' : 'error' ) . '"><p>' . esc_html( $result['message'] ) . '</p></div>';
			if ( ! empty( $result['details'] ) ) {
				echo '<div class="notice notice-info"><p><strong>Szczegóły:</strong></p><ul>';
				foreach ( $result['details'] as $detail ) {
					echo '<li>' . esc_html( $detail ) . '</li>';
				}
				echo '</ul></div>';
			}
		}
	}

	if ( isset( $_POST['kpg_fill_canonical_from_permalink'] ) && check_admin_referer( 'kpg_fill_canonical_action' ) ) {
		$result = kpg_fill_canonical_from_permalink();
		echo '<div class="notice notice-' . ( $result['success'] ? 'success' : 'error' ) . '"><p>' . esc_html( $result['message'] ) . '</p></div>';
		if ( ! empty( $result['details'] ) ) {
			echo '<div class="notice notice-info"><p><strong>Szczegóły:</strong></p><ul>';
			foreach ( array_slice( $result['details'], 0, 20 ) as $detail ) {
				echo '<li>' . esc_html( $detail ) . '</li>';
			}
			if ( count( $result['details'] ) > 20 ) {
				echo '<li><em>... i ' . ( count( $result['details'] ) - 20 ) . ' więcej</em></li>';
			}
			echo '</ul></div>';
		}
	}
	
	if ( isset( $_POST['kpg_cleanup_rankmath'] ) && check_admin_referer( 'kpg_cleanup_rankmath_action' ) ) {
		$result = kpg_cleanup_rankmath_meta();
		echo '<div class="notice notice-' . ( $result['success'] ? 'success' : 'error' ) . '"><p>' . esc_html( $result['message'] ) . '</p></div>';
		if ( ! empty( $result['details'] ) ) {
			echo '<div class="notice notice-info"><p><strong>Details:</strong></p><ul>';
			foreach ( $result['details'] as $detail ) {
				echo '<li>' . esc_html( $detail ) . '</li>';
			}
			echo '</ul></div>';
		}
	}

	// Handle verification request
	if ( isset( $_POST['kpg_verify_rankmath'] ) && check_admin_referer( 'kpg_verify_rankmath_action' ) ) {
		$result = kpg_verify_rankmath_import();
		echo '<div class="notice notice-info"><p><strong>Weryfikacja importu Rank Math SEO:</strong></p>';
		echo '<ul>';
		foreach ( $result as $item ) {
			echo '<li>' . esc_html( $item ) . '</li>';
		}
		echo '</ul></div>';
	}
	
	if ( isset( $_POST['kpg_verify_rankmath_detailed'] ) && check_admin_referer( 'kpg_verify_rankmath_action' ) ) {
		$result = kpg_verify_rankmath_posts_detailed();
		echo '<div class="notice notice-info">';
		echo wp_kses_post( nl2br( $result ) );
		echo '</div>';
	}

	// Handle CSV import
	if ( isset( $_POST['kpg_import_csv'] ) && check_admin_referer( 'kpg_import_csv_action' ) ) {
		if ( isset( $_FILES['csv_file'] ) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK ) {
			$old_site_url      = esc_url_raw( $_POST['old_site_url_csv'] ?? '' );
			$dry_run           = isset( $_POST['dry_run'] ) && $_POST['dry_run'] === '1';
			$skip_attachments  = isset( $_POST['skip_attachments'] ) && $_POST['skip_attachments'] === '1';
			$fill_missing_only = isset( $_POST['fill_missing_only'] ) && $_POST['fill_missing_only'] === '1';
			
			if ( $dry_run ) {
				$result = kpg_import_rankmath_from_csv( $_FILES['csv_file']['tmp_name'], $old_site_url, true, $skip_attachments, $fill_missing_only );
				echo '<div class="notice notice-warning"><p><strong>TRYB TESTOWY (DRY RUN) - żadne dane nie zostały zapisane!</strong></p></div>';
			} else {
				$result = kpg_import_rankmath_from_csv( $_FILES['csv_file']['tmp_name'], $old_site_url, false, $skip_attachments, $fill_missing_only );
			}
			
			echo '<div class="notice notice-' . ( $result['success'] ? 'success' : 'error' ) . '"><p>' . esc_html( $result['message'] ) . '</p></div>';
			
			if ( ! empty( $result['details'] ) ) {
				$details_count = count( $result['details'] );
				$show_limit = 50; // Show first 50 details
				echo '<div class="notice notice-info"><p><strong>Szczegóły:</strong> ' . ( $details_count > $show_limit ? "(pokazuję pierwsze {$show_limit} z {$details_count})" : '' ) . '</p><ul>';
				foreach ( array_slice( $result['details'], 0, $show_limit ) as $detail ) {
					echo '<li>' . esc_html( $detail ) . '</li>';
				}
				if ( $details_count > $show_limit ) {
					echo '<li><em>... i ' . ( $details_count - $show_limit ) . ' więcej</em></li>';
				}
				echo '</ul></div>';
			}
		} else {
			echo '<div class="notice notice-error"><p>Please select a valid CSV file to upload.</p></div>';
		}
	}

	// Handle form submission
	if ( isset( $_POST['kpg_migrate_rankmath'] ) && check_admin_referer( 'kpg_migrate_rankmath_action' ) ) {
		$old_db_host = sanitize_text_field( $_POST['old_db_host'] ?? '' );
		$old_db_name = sanitize_text_field( $_POST['old_db_name'] ?? '' );
		$old_db_user = sanitize_text_field( $_POST['old_db_user'] ?? '' );
		$old_db_pass = sanitize_text_field( $_POST['old_db_pass'] ?? '' );
		$old_db_prefix = sanitize_text_field( $_POST['old_db_prefix'] ?? 'wp_' );
		$old_site_url = esc_url_raw( $_POST['old_site_url'] ?? '' );

		$result = kpg_migrate_rankmath_meta( $old_db_host, $old_db_name, $old_db_user, $old_db_pass, $old_db_prefix, $old_site_url );
		
		echo '<div class="notice notice-' . ( $result['success'] ? 'success' : 'error' ) . '"><p>' . esc_html( $result['message'] ) . '</p></div>';
		
		if ( ! empty( $result['details'] ) ) {
			echo '<div class="notice notice-info"><p><strong>Details:</strong></p><ul>';
			foreach ( $result['details'] as $detail ) {
				echo '<li>' . esc_html( $detail ) . '</li>';
			}
			echo '</ul></div>';
		}
	}

	// Check if Rank Math is installed
	$rankmath_active = defined( 'RANK_MATH_VERSION' ) || class_exists( 'RankMath' );
	
	?>
	<div class="wrap">
		<h1>Rank Math SEO Migration</h1>
		<p>Migrate Rank Math SEO meta tags from old WordPress site to this site.</p>
		
		<?php if ( ! $rankmath_active ) : ?>
			<div class="notice notice-warning">
				<p><strong>Warning:</strong> Rank Math SEO plugin is not active. Please install and activate Rank Math SEO before running migration.</p>
			</div>
		<?php endif; ?>
		
		<h2>Method 1: Import from CSV File</h2>
		<p>Upload a CSV file exported from Rank Math SEO (Settings → Import/Export → Export). Obsługiwane są pliki z separatorem <strong>przecinek</strong> (,) oraz <strong>średnik</strong> (;) – np. eksport z Excel/Numbers.</p>
		<div class="notice notice-info inline">
			<p><strong>Uwaga:</strong> Import <strong>nadpisuje</strong> istniejące dane Rank Math SEO dla postów/kategorii/autorów, które są w CSV. Nie tworzy duplikatów.</p>
			<p><strong>💡 Wskazówka:</strong> W Rank Math SEO (Settings → Import/Export → Export) możesz wybrać tylko "Posts" podczas eksportu, aby uniknąć importowania attachmentów (obrazów/media).</p>
			<p><strong>Typy postów:</strong> Import obsługuje zarówno posty blogowe, strony, jak i attachmenty (obrazy/media). Attachmenty, które nie istnieją w nowej bazie, zostaną automatycznie pominięte.</p>
			<p>Jeśli masz już dane Rank Math i chcesz mieć pewność, że importujesz tylko dane z CSV, najpierw użyj funkcji "Wyczyść wszystkie dane Rank Math" poniżej.</p>
			<p>Zalecamy najpierw sprawdzić "Weryfikacja importu", aby zobaczyć, jakie dane już masz.</p>
		</div>
		
		<form method="post" action="" enctype="multipart/form-data">
			<?php wp_nonce_field( 'kpg_import_csv_action' ); ?>
			
			<table class="form-table">
				<tr>
					<th scope="row"><label for="csv_file">CSV File</label></th>
					<td>
						<input type="file" name="csv_file" id="csv_file" accept=".csv" required>
						<p class="description">Select the Rank Math SEO export CSV file</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="old_site_url_csv">Old Site URL (optional)</label></th>
					<td>
						<input type="url" name="old_site_url_csv" id="old_site_url_csv" value="<?php echo esc_attr( defined( 'KPG_OLD_SITE_URL' ) ? KPG_OLD_SITE_URL : '' ); ?>" class="regular-text">
						<p class="description">Full URL of the old site (e.g., https://www.kpgio.pl) - used to replace URLs in meta values</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="skip_attachments">Pomiń attachmenty</label></th>
					<td>
						<label>
							<input type="checkbox" name="skip_attachments" id="skip_attachments" value="1" checked>
							<strong>Automatycznie pomiń attachmenty (obrazy/media)</strong>
						</label>
						<p class="description">Zaznacz, aby automatycznie pomijać attachmenty podczas importu. Zalecane, jeśli chcesz importować tylko posty blogowe i strony.</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="fill_missing_only">Tylko brakujące pola</label></th>
					<td>
						<label>
							<input type="checkbox" name="fill_missing_only" id="fill_missing_only" value="1">
							<strong>Uzupełnij tylko brakujące pola (nie nadpisuj istniejących)</strong>
						</label>
						<p class="description">Dla istniejących postów: ustawia tylko te pola Rank Math, które są puste. Nie zmienia już zapisanych title/description/canonical. Przydatne przy testach i uzupełnianiu canonical bez nadpisywania.</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="dry_run">Tryb testowy</label></th>
					<td>
						<label>
							<input type="checkbox" name="dry_run" id="dry_run" value="1">
							<strong>Dry Run (test bez zapisu)</strong> - sprawdź co zostanie zaimportowane bez zapisywania danych
						</label>
						<p class="description">Zaznacz, aby przetestować import bez zapisywania danych do bazy</p>
					</td>
				</tr>
			</table>
			
			<p class="submit">
				<input type="submit" name="kpg_import_csv" class="button button-primary" value="Import from CSV">
			</p>
		</form>
		
		<h3>Jak przetestować import (istniejące posty)</h3>
		<ol style="list-style: decimal; margin-left: 20px;">
			<li><strong>Weryfikacja:</strong> Kliknij „Zweryfikuj import” – zobaczysz, ile postów ma już dane Rank Math i ile ma canonical.</li>
			<li><strong>Dry Run (bez zapisu):</strong> Zaznacz <strong>„Dry Run (test bez zapisu)”</strong>, wybierz plik CSV, wpisz stary URL (np. <code>https://www.kpgio.pl</code>), kliknij „Import from CSV”. Sprawdź komunikat: ile postów zostanie dopasowanych (znaleziono po slug), ile pominięto.</li>
			<li><strong>Tylko brakujące:</strong> Zaznacz <strong>„Uzupełnij tylko brakujące pola”</strong> – import uzupełni canonical/title/description tylko tam, gdzie są puste. Istniejące wartości nie zostaną nadpisane.</li>
			<li><strong>Import na żywo:</strong> Odznacz Dry Run, ewentualnie zostaw „Tylko brakujące”, kliknij „Import from CSV”. Potem ponownie „Zweryfikuj import”, żeby zobaczyć liczbę canonical.</li>
		</ol>
		
		<hr>
		
		<h2>Narzędzia pomocnicze</h2>
		
		<h3>Weryfikacja importu</h3>
		<p>Sprawdź, ile postów, kategorii i autorów ma wypełnione dane Rank Math SEO.</p>
		<form method="post" action="">
			<?php wp_nonce_field( 'kpg_verify_rankmath_action' ); ?>
			<p class="submit">
				<input type="submit" name="kpg_verify_rankmath" class="button" value="Zweryfikuj import">
				<input type="submit" name="kpg_verify_rankmath_detailed" class="button button-secondary" value="Szczegółowa weryfikacja postów">
			</p>
		</form>
		
		<h3>Canonical URL</h3>
		<p><strong>Import:</strong> Canonical jest importowany z CSV (kolumny: <code>canonical_url</code>, <code>Canonical URL</code>, <code>Canonical</code> lub <code>canonical</code>) oraz z migracji z bazy. Jeśli w CSV brakowało canonical, uruchom ponownie import po upewnieniu się, że eksport Rank Math zawiera tę kolumnę, lub użyj „Uzupełnij canonical z permalinków”.</p>
		<p><strong>Uzupełnij canonical z permalinków:</strong> Ustawia <code>rank_math_canonical_url</code> na permalink dla postów/stron, które mają SEO title lub description, ale nie mają zapisanego canonical (np. gdy w CSV brakowało kolumny canonical).</p>
		<form method="post" action="" style="margin-bottom: 20px;">
			<?php wp_nonce_field( 'kpg_fill_canonical_action' ); ?>
			<p class="submit">
				<input type="submit" name="kpg_fill_canonical_from_permalink" class="button button-secondary" value="Uzupełnij canonical z permalinków">
			</p>
		</form>
		<h3>Zamiana URL-i w Canonical URLs</h3>
		<p>Zamień stare URL-e (np. z kpgio.pl) na nowe URL-e w canonical URLs wszystkich postów.</p>
		<form method="post" action="">
			<?php wp_nonce_field( 'kpg_replace_canonical_urls_action' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="old_canonical_url">Stary URL (do zamiany)</label></th>
					<td>
						<input type="url" name="old_canonical_url" id="old_canonical_url" value="https://www.kpgio.pl" class="regular-text" placeholder="https://www.kpgio.pl">
						<p class="description">Pełny URL starej domeny (np. https://www.kpgio.pl)</p>
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" name="kpg_replace_canonical_urls" class="button button-secondary" value="Zamień URL-e w Canonical URLs">
			</p>
		</form>
		
		<h3>Czyszczenie danych Rank Math</h3>
		<p><strong>UWAGA:</strong> Ta operacja usunie wszystkie meta tagi Rank Math SEO z postów, kategorii i autorów.</p>
		<p><strong>Kiedy użyć:</strong></p>
		<ul>
			<li>Przed pierwszym importem, jeśli chcesz mieć pewność, że importujesz "czyste" dane</li>
			<li>Jeśli import nie przebiegł pomyślnie i chcesz zacząć od nowa</li>
			<li>Jeśli masz stare/nieprawidłowe dane i chcesz je zastąpić nowymi z CSV</li>
		</ul>
		<p><strong>Nie musisz używać</strong> jeśli chcesz tylko zaktualizować istniejące dane - import automatycznie nadpisze istniejące wartości.</p>
		<form method="post" action="" onsubmit="return confirm('Czy na pewno chcesz usunąć wszystkie dane Rank Math SEO? Ta operacja jest nieodwracalna!');">
			<?php wp_nonce_field( 'kpg_cleanup_rankmath_action' ); ?>
			<p class="submit">
				<input type="submit" name="kpg_cleanup_rankmath" class="button button-secondary" value="Wyczyść wszystkie dane Rank Math" style="color: #dc3232;">
			</p>
		</form>
		
		<hr>
		
		<h2>Method 2: Import from Database</h2>
		<p>Connect directly to the old WordPress database to import Rank Math SEO data.</p>
		
		<form method="post" action="">
			<?php wp_nonce_field( 'kpg_migrate_rankmath_action' ); ?>
			
			<table class="form-table">
				<tr>
					<th scope="row"><label for="old_db_host">Old Database Host</label></th>
					<td>
						<input type="text" name="old_db_host" id="old_db_host" value="<?php echo esc_attr( defined( 'KPG_OLD_DB_HOST' ) ? KPG_OLD_DB_HOST : 'localhost' ); ?>" class="regular-text" required>
						<p class="description">Database host of the old WordPress site</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="old_db_name">Old Database Name</label></th>
					<td>
						<input type="text" name="old_db_name" id="old_db_name" value="<?php echo esc_attr( defined( 'KPG_OLD_DB_NAME' ) ? KPG_OLD_DB_NAME : '' ); ?>" class="regular-text" required>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="old_db_user">Old Database User</label></th>
					<td>
						<input type="text" name="old_db_user" id="old_db_user" value="<?php echo esc_attr( defined( 'KPG_OLD_DB_USER' ) ? KPG_OLD_DB_USER : '' ); ?>" class="regular-text" required>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="old_db_pass">Old Database Password</label></th>
					<td>
						<input type="password" name="old_db_pass" id="old_db_pass" value="<?php echo esc_attr( defined( 'KPG_OLD_DB_PASS' ) ? KPG_OLD_DB_PASS : '' ); ?>" class="regular-text" required>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="old_db_prefix">Old Database Prefix</label></th>
					<td>
						<input type="text" name="old_db_prefix" id="old_db_prefix" value="<?php echo esc_attr( defined( 'KPG_OLD_DB_PREFIX' ) ? KPG_OLD_DB_PREFIX : 'wp_' ); ?>" class="regular-text">
						<p class="description">Default: wp_</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="old_site_url">Old Site URL</label></th>
					<td>
						<input type="url" name="old_site_url" id="old_site_url" value="<?php echo esc_attr( defined( 'KPG_OLD_SITE_URL' ) ? KPG_OLD_SITE_URL : '' ); ?>" class="regular-text" required>
						<p class="description">Full URL of the old site (e.g., https://old-site.com)</p>
					</td>
				</tr>
			</table>
			
			<p class="submit">
				<input type="submit" name="kpg_migrate_rankmath" class="button button-primary" value="Start Migration">
			</p>
		</form>
		
		<hr>
		
		<h2>Method 3: Use WP-CLI</h2>
		<p>You can also run migration via WP-CLI:</p>
		<pre>wp kpg-migrate-rankmath --old-db-host=HOST --old-db-name=DB --old-db-user=USER --old-db-pass=PASS --old-db-prefix=wp_ --old-site-url=URL</pre>
		<p>Or import from CSV:</p>
		<pre>wp kpg-import-rankmath-csv /path/to/file.csv --old-site-url=https://old-site.com</pre>
	</div>
	<?php
}

/**
 * Detect CSV delimiter from first line (comma or semicolon).
 * Many European exports (Excel, Numbers) use semicolon.
 *
 * @param string $first_line First line of the CSV file.
 * @return string Delimiter ',' or ';'
 */
function kpg_detect_csv_delimiter( $first_line ) {
	$first_line = trim( $first_line );
	// Remove BOM if present
	if ( substr( $first_line, 0, 3 ) === "\xEF\xBB\xBF" ) {
		$first_line = substr( $first_line, 3 );
	}
	$try_semicolon = str_getcsv( $first_line, ';', '"' );
	$try_comma     = str_getcsv( $first_line, ',', '"' );
	$count_semi    = count( $try_semicolon );
	$count_comma   = count( $try_comma );
	$has_expected  = static function ( $headers ) {
		$trimmed = array_map( 'trim', $headers );
		return in_array( 'object_type', $trimmed, true ) && in_array( 'slug', $trimmed, true );
	};
	// Prefer semicolon if it gives more columns and expected headers
	if ( $count_semi >= 3 && $has_expected( $try_semicolon ) ) {
		return ';';
	}
	if ( $count_comma >= 3 && $has_expected( $try_comma ) ) {
		return ',';
	}
	// Fallback: more columns wins
	return $count_semi >= $count_comma ? ';' : ',';
}

/**
 * Import Rank Math SEO data from CSV file
 *
 * @param string $csv_file_path    Path to CSV file.
 * @param string $old_site_url     Old site URL to replace with home_url().
 * @param bool   $dry_run          If true, no data is saved.
 * @param bool   $skip_attachments If true, skip attachment rows.
 * @param bool   $fill_missing_only If true, only set meta when current value is empty (do not overwrite).
 */
function kpg_import_rankmath_from_csv( $csv_file_path, $old_site_url = '', $dry_run = false, $skip_attachments = true, $fill_missing_only = false ) {
	$results = [
		'success' => false,
		'message' => '',
		'details' => [],
	];

	// Check if Rank Math is installed
	if ( ! defined( 'RANK_MATH_VERSION' ) && ! class_exists( 'RankMath' ) ) {
		$results['message'] = 'Rank Math SEO plugin is not active. Please install and activate Rank Math SEO before running import.';
		return $results;
	}

	if ( ! file_exists( $csv_file_path ) ) {
		$results['message'] = 'CSV file not found.';
		return $results;
	}

	$stats = [
		'posts' => 0,
		'terms' => 0,
		'users' => 0,
		'skipped' => 0,
		'errors' => 0,
		'posts_in_csv' => 0, // Total posts in CSV
		'attachments_in_csv' => 0, // Total attachments in CSV
	];

	// Open CSV file
	$handle = fopen( $csv_file_path, 'r' );
	if ( ! $handle ) {
		$results['message'] = 'Could not open CSV file.';
		return $results;
	}

	// Read first line and detect delimiter (comma vs semicolon – Excel/Numbers often use ;)
	$first_line = fgets( $handle );
	if ( $first_line === false ) {
		fclose( $handle );
		$results['message'] = 'CSV file is empty or invalid.';
		return $results;
	}
	$delimiter = kpg_detect_csv_delimiter( $first_line );
	$first_line = trim( $first_line );
	if ( substr( $first_line, 0, 3 ) === "\xEF\xBB\xBF" ) {
		$first_line = substr( $first_line, 3 );
	}
	$headers = str_getcsv( $first_line, $delimiter, '"' );
	if ( empty( $headers ) || ! in_array( 'slug', array_map( 'trim', $headers ), true ) ) {
		fclose( $handle );
		$results['message'] = 'CSV file has invalid headers (expected at least: object_type, slug).';
		return $results;
	}

	// Map CSV columns to array indices
	$column_map = [];
	foreach ( $headers as $index => $header ) {
		$column_map[ trim( $header ) ] = $index;
	}

	// Process each row (use same delimiter)
	$row_num = 1;
	while ( ( $row = fgetcsv( $handle, 0, $delimiter ) ) !== false ) {
		$row_num++;
		
		if ( count( $row ) < 3 ) {
			continue; // Skip invalid rows
		}

		// Get object type and slug
		$object_type = isset( $column_map['object_type'] ) ? trim( $row[ $column_map['object_type'] ] ) : '';
		$slug = isset( $column_map['slug'] ) ? trim( $row[ $column_map['slug'] ] ) : '';

		if ( empty( $object_type ) || empty( $slug ) ) {
			$stats['skipped']++;
			continue;
		}

		// Process based on object type
		switch ( $object_type ) {
			case 'post':
				// First, check if it exists as post, page, or attachment
				$post_check = get_page_by_path( $slug, OBJECT, 'post' );
				$page_check = false;
				$attachment_check = false;
				$is_attachment = false;
				
				if ( ! $post_check ) {
					$page_check = get_page_by_path( $slug, OBJECT, 'page' );
					if ( ! $page_check ) {
						$attachment_check = get_page_by_path( $slug, OBJECT, 'attachment' );
					}
				}
				
				// Determine if it's likely an attachment based on slug patterns
				// Only if it doesn't exist in database
				if ( ! $post_check && ! $page_check && ! $attachment_check ) {
					$attachment_patterns = ['icon-', 'demo-photo', 'logo', 'favicon', 'blank', 'img-', 'zdj', 'pexels-', 'nz8_', 'dsc_', '-solid', '-regular'];
					$is_likely_attachment = false;
					foreach ( $attachment_patterns as $pattern ) {
						if ( strpos( $slug, $pattern ) !== false ) {
							$is_likely_attachment = true;
							break;
						}
					}
					
					// Also check if slug is very short (likely attachment)
					if ( ! $is_likely_attachment && strlen( $slug ) <= 3 && is_numeric( $slug ) ) {
						$is_likely_attachment = true;
					}
					
					if ( $is_likely_attachment ) {
						$is_attachment = true;
						$stats['attachments_in_csv']++;
					} else {
						$stats['posts_in_csv']++;
					}
				} else {
					// It exists - determine type
					if ( $attachment_check ) {
						$is_attachment = true;
						$stats['attachments_in_csv']++;
					} else {
						$stats['posts_in_csv']++;
					}
				}
				
				// Skip attachments if option is enabled
				if ( $skip_attachments && $is_attachment ) {
					$stats['skipped']++;
					continue 2; // Skip this row entirely (continue 2 to continue the while loop, not just the switch)
				}
				
				$result = kpg_import_post_rankmath_from_csv_row( $row, $column_map, $slug, $old_site_url, $dry_run, $fill_missing_only );
				if ( $result['success'] ) {
					$stats['posts']++;
				} else {
					$stats['skipped']++;
					// Only show details for actual posts/pages, not missing attachments
					if ( ! empty( $result['message'] ) && ! $is_attachment ) {
						$results['details'][] = "Post '{$slug}': " . $result['message'];
					}
				}
				break;

			case 'term':
				$result = kpg_import_term_rankmath_from_csv_row( $row, $column_map, $slug, $old_site_url, $dry_run, $fill_missing_only );
				if ( $result['success'] ) {
					$stats['terms']++;
				} else {
					$stats['skipped']++;
				}
				break;

			case 'user':
				$result = kpg_import_user_rankmath_from_csv_row( $row, $column_map, $slug, $old_site_url, $dry_run, $fill_missing_only );
				if ( $result['success'] ) {
					$stats['users']++;
				} else {
					$stats['skipped']++;
				}
				break;

			default:
				$stats['skipped']++;
				break;
		}
	}

	fclose( $handle );

	// Build result message with detailed stats
	$results['success'] = true;
	$message = sprintf(
		'CSV import completed! Posts: %d, Terms: %d, Users: %d, Skipped: %d',
		$stats['posts'],
		$stats['terms'],
		$stats['users'],
		$stats['skipped']
	);
	
	// Add detailed breakdown if we have stats
	if ( $stats['posts_in_csv'] > 0 || $stats['attachments_in_csv'] > 0 ) {
		$message .= sprintf(
			' | W CSV: %d postów blogowych, %d attachmentów (obrazy/media)',
			$stats['posts_in_csv'],
			$stats['attachments_in_csv']
		);
	}
	
	$results['message'] = $message;

	return $results;
}

/**
 * Import Rank Math data for a post from CSV row
 *
 * @param bool $fill_missing_only If true, only set meta when current value is empty (do not overwrite).
 */
function kpg_import_post_rankmath_from_csv_row( $row, $column_map, $slug, $old_site_url = '', $dry_run = false, $fill_missing_only = false ) {
	// Find post by slug - try 'post' first, then 'page', then 'attachment' (images/media)
	$post = get_page_by_path( $slug, OBJECT, 'post' );
	
	if ( ! $post ) {
		// Try to find as page
		$post = get_page_by_path( $slug, OBJECT, 'page' );
	}
	
	if ( ! $post ) {
		// Try to find as attachment (image/media)
		$post = get_page_by_path( $slug, OBJECT, 'attachment' );
	}
	
	if ( ! $post ) {
		// Last attempt: use WP_Query to search by post_name in all post types
		$query = new WP_Query( [
			'name' => $slug,
			'post_type' => 'any',
			'post_status' => 'any',
			'posts_per_page' => 1,
		] );
		
		if ( $query->have_posts() ) {
			$post = $query->posts[0];
		}
		
		wp_reset_postdata();
	}
	
	if ( ! $post ) {
		return [ 'success' => false, 'message' => 'Post not found' ];
	}

	// Map CSV columns to Rank Math meta keys.
	// canonical_url: Rank Math export may use "canonical_url", "Canonical URL", "Canonical" or "canonical" – all are accepted.
	$meta_mapping = [
		'seo_title' => 'rank_math_title',
		'seo_description' => 'rank_math_description',
		'focus_keyword' => 'rank_math_focus_keyword',
		'robots' => 'rank_math_robots',
		'advanced_robots' => 'rank_math_advanced_robots',
		'canonical_url' => 'rank_math_canonical_url',
		'seo_score' => 'rank_math_seo_score',
		'social_facebook_title' => 'rank_math_facebook_title',
		'social_facebook_description' => 'rank_math_facebook_description',
		'social_facebook_thumbnail' => 'rank_math_facebook_image',
		'social_twitter_title' => 'rank_math_twitter_title',
		'social_twitter_description' => 'rank_math_twitter_description',
		'social_twitter_thumbnail' => 'rank_math_twitter_image',
		'schema_data' => 'rank_math_schema_data',
		'primary_term' => 'rank_math_primary_category',
	];

	// Alternative CSV column names for canonical (Rank Math export may use different headers).
	$canonical_csv_columns = [ 'canonical_url', 'Canonical URL', 'Canonical', 'canonical' ];

	// Import each meta field
	foreach ( $meta_mapping as $csv_column => $meta_key ) {
		// For canonical, accept any of the alternative column names.
		if ( $csv_column === 'canonical_url' ) {
			$value = '';
			foreach ( $canonical_csv_columns as $col ) {
				if ( isset( $column_map[ $col ] ) ) {
					$val = trim( $row[ $column_map[ $col ] ] ?? '' );
					if ( $val !== '' && $val !== 'n/a' ) {
						$value = $val;
						break;
					}
				}
			}
			if ( $value === '' ) {
				continue;
			}
		} elseif ( ! isset( $column_map[ $csv_column ] ) ) {
			continue;
		} else {
			$value = trim( $row[ $column_map[ $csv_column ] ] ?? '' );
		}
		
		if ( empty( $value ) || $value === 'n/a' ) {
			continue;
		}

		// Handle special cases
		if ( $csv_column === 'schema_data' ) {
			// Schema data might be JSON
			$decoded = json_decode( $value, true );
			if ( json_last_error() === JSON_ERROR_NONE ) {
				$value = $decoded;
			}
		} elseif ( $csv_column === 'primary_term' ) {
			// Primary term is stored as slug in CSV, but Rank Math needs term ID
			// Try to find term by slug (check category first, then post_tag)
			$term = get_term_by( 'slug', $value, 'category' );
			if ( ! $term ) {
				$term = get_term_by( 'slug', $value, 'post_tag' );
			}
			if ( $term && ! is_wp_error( $term ) ) {
				$value = $term->term_id;
			} else {
				// If term not found, skip this field
				continue;
			}
		}

		// Only fill if current value is empty (when fill_missing_only is set)
		if ( $fill_missing_only ) {
			$current = get_post_meta( $post->ID, $meta_key, true );
			if ( $current !== '' && $current !== [] && $current !== null ) {
				continue;
			}
		}

		// Replace URLs if old site URL is provided
		if ( ! empty( $old_site_url ) && is_string( $value ) ) {
			$value = str_replace( $old_site_url, home_url(), $value );
		} elseif ( is_array( $value ) ) {
			$value = kpg_replace_urls_in_array( $value, $old_site_url, home_url() );
		}

		if ( ! $dry_run ) {
			update_post_meta( $post->ID, $meta_key, $value );
		}
	}

	// Handle is_pillar_content
	if ( isset( $column_map['is_pillar_content'] ) ) {
		$is_pillar = trim( $row[ $column_map['is_pillar_content'] ] ?? '' );
		if ( $is_pillar === 'yes' ) {
			if ( ! $dry_run ) {
				update_post_meta( $post->ID, 'rank_math_pillar_content', true );
			}
		}
	}

	// Handle redirects (if Rank Math Redirections plugin is active)
	if ( isset( $column_map['redirect_to'] ) && isset( $column_map['redirect_type'] ) ) {
		$redirect_to = trim( $row[ $column_map['redirect_to'] ] ?? '' );
		$redirect_type = trim( $row[ $column_map['redirect_type'] ] ?? '' );
		
		if ( ! empty( $redirect_to ) && ! empty( $redirect_type ) ) {
			// Replace old URL with new URL in redirect target
			if ( ! empty( $old_site_url ) ) {
				$redirect_to = str_replace( $old_site_url, home_url(), $redirect_to );
			}
			
			if ( ! $dry_run ) {
				// Store redirect data (Rank Math uses rank_math_redirection meta)
				update_post_meta( $post->ID, 'rank_math_redirection', [
					'url_to' => $redirect_to,
					'header_code' => intval( $redirect_type ),
				] );
			}
		}
	}

	return [ 'success' => true ];
}

/**
 * Import Rank Math data for a term from CSV row
 */
function kpg_import_term_rankmath_from_csv_row( $row, $column_map, $slug, $old_site_url = '', $dry_run = false, $fill_missing_only = false ) {
	// Try to find term by slug (check all taxonomies)
	$term = get_term_by( 'slug', $slug, 'category' );
	if ( ! $term ) {
		$term = get_term_by( 'slug', $slug, 'post_tag' );
	}
	
	if ( ! $term || is_wp_error( $term ) ) {
		return [ 'success' => false ];
	}

	$meta_mapping = [
		'seo_title' => 'rank_math_title',
		'seo_description' => 'rank_math_description',
		'robots' => 'rank_math_robots',
		'canonical_url' => 'rank_math_canonical_url',
	];

	$canonical_csv_columns = [ 'canonical_url', 'Canonical URL', 'Canonical', 'canonical' ];

	foreach ( $meta_mapping as $csv_column => $meta_key ) {
		if ( $csv_column === 'canonical_url' ) {
			$value = '';
			foreach ( $canonical_csv_columns as $col ) {
				if ( isset( $column_map[ $col ] ) ) {
					$val = trim( $row[ $column_map[ $col ] ] ?? '' );
					if ( $val !== '' && $val !== 'n/a' ) {
						$value = $val;
						break;
					}
				}
			}
			if ( $value === '' ) {
				continue;
			}
		} elseif ( ! isset( $column_map[ $csv_column ] ) ) {
			continue;
		} else {
			$value = trim( $row[ $column_map[ $csv_column ] ] ?? '' );
		}

		if ( empty( $value ) || $value === 'n/a' ) {
			continue;
		}

		if ( $fill_missing_only ) {
			$current = get_term_meta( $term->term_id, $meta_key, true );
			if ( $current !== '' && $current !== [] && $current !== null ) {
				continue;
			}
		}

		if ( ! empty( $old_site_url ) && is_string( $value ) ) {
			$value = str_replace( $old_site_url, home_url(), $value );
		}

		if ( ! $dry_run ) {
			update_term_meta( $term->term_id, $meta_key, $value );
		}
	}

	return [ 'success' => true ];
}

/**
 * Import Rank Math data for a user from CSV row
 */
function kpg_import_user_rankmath_from_csv_row( $row, $column_map, $slug, $old_site_url = '', $dry_run = false, $fill_missing_only = false ) {
	// Find user by nicename (slug)
	$user = get_user_by( 'slug', $slug );
	
	if ( ! $user ) {
		return [ 'success' => false ];
	}

	$meta_mapping = [
		'seo_title' => 'rank_math_title',
		'seo_description' => 'rank_math_description',
		'robots' => 'rank_math_robots',
		'canonical_url' => 'rank_math_canonical_url',
	];

	$canonical_csv_columns = [ 'canonical_url', 'Canonical URL', 'Canonical', 'canonical' ];

	foreach ( $meta_mapping as $csv_column => $meta_key ) {
		if ( $csv_column === 'canonical_url' ) {
			$value = '';
			foreach ( $canonical_csv_columns as $col ) {
				if ( isset( $column_map[ $col ] ) ) {
					$val = trim( $row[ $column_map[ $col ] ] ?? '' );
					if ( $val !== '' && $val !== 'n/a' ) {
						$value = $val;
						break;
					}
				}
			}
			if ( $value === '' ) {
				continue;
			}
		} elseif ( ! isset( $column_map[ $csv_column ] ) ) {
			continue;
		} else {
			$value = trim( $row[ $column_map[ $csv_column ] ] ?? '' );
		}

		if ( empty( $value ) || $value === 'n/a' ) {
			continue;
		}

		if ( $fill_missing_only ) {
			$current = get_user_meta( $user->ID, $meta_key, true );
			if ( $current !== '' && $current !== [] && $current !== null ) {
				continue;
			}
		}

		if ( ! empty( $old_site_url ) && is_string( $value ) ) {
			$value = str_replace( $old_site_url, home_url(), $value );
		}

		if ( ! $dry_run ) {
			update_user_meta( $user->ID, $meta_key, $value );
		}
	}

	return [ 'success' => true ];
}

/**
 * Main migration function
 */
function kpg_migrate_rankmath_meta( $old_db_host, $old_db_name, $old_db_user, $old_db_pass, $old_db_prefix = 'wp_', $old_site_url = '' ) {
	global $wpdb;

	$results = [
		'success' => false,
		'message' => '',
		'details' => [],
	];

	// Check if Rank Math is installed
	if ( ! defined( 'RANK_MATH_VERSION' ) && ! class_exists( 'RankMath' ) ) {
		$results['message'] = 'Rank Math SEO plugin is not active. Please install and activate Rank Math SEO before running migration.';
		return $results;
	}

	// Validate inputs
	if ( empty( $old_db_host ) || empty( $old_db_name ) || empty( $old_db_user ) ) {
		$results['message'] = 'Missing required database connection parameters.';
		return $results;
	}

	// Connect to old database
	$old_db = new wpdb( $old_db_user, $old_db_pass, $old_db_name, $old_db_host );
	
	if ( $old_db->last_error ) {
		$results['message'] = 'Failed to connect to old database: ' . $old_db->last_error;
		return $results;
	}

	// Test connection
	$test_query = $old_db->get_var( "SELECT 1" );
	if ( $test_query !== '1' ) {
		$results['message'] = 'Database connection test failed. Please check your credentials.';
		return $results;
	}

	// Set table prefix
	$old_db->set_prefix( $old_db_prefix );

	$stats = [
		'posts' => 0,
		'terms' => 0,
		'users' => 0,
		'skipped' => 0,
		'errors' => 0,
	];

	// List of Rank Math meta keys to migrate
	$rankmath_meta_keys = [
		'rank_math_title',
		'rank_math_description',
		'rank_math_focus_keyword',
		'rank_math_robots',
		'rank_math_canonical_url',
		'rank_math_facebook_title',
		'rank_math_facebook_description',
		'rank_math_facebook_image',
		'rank_math_facebook_image_id',
		'rank_math_twitter_title',
		'rank_math_twitter_description',
		'rank_math_twitter_image',
		'rank_math_twitter_image_id',
		'rank_math_twitter_card_type',
		'rank_math_snippet_data',
		'rank_math_primary_category',
		'rank_math_internal_links_processed',
		'rank_math_content_score',
		'rank_math_seo_score',
		'rank_math_schema',
		'rank_math_schema_data',
		'rank_math_breadcrumb_title',
		'rank_math_robots_global',
		'rank_math_advanced_robots',
	];

	// 1. Migrate post meta
	$old_posts = $old_db->get_results( 
		"SELECT ID, post_name, post_type FROM {$old_db->prefix}posts WHERE post_type = 'post' AND post_status = 'publish'"
	);

	foreach ( $old_posts as $old_post ) {
		// Find matching post in new database by slug
		$new_post = get_page_by_path( $old_post->post_name, OBJECT, 'post' );
		
		if ( ! $new_post ) {
			$stats['skipped']++;
			$results['details'][] = "Post not found (skipped): {$old_post->post_name}";
			continue;
		}

		// Get all Rank Math meta for this post from old database
		$old_meta = $old_db->get_results( 
			$old_db->prepare(
				"SELECT meta_key, meta_value FROM {$old_db->prefix}postmeta WHERE post_id = %d AND meta_key LIKE %s",
				$old_post->ID,
				'rank_math_%'
			)
		);

		if ( empty( $old_meta ) ) {
			continue;
		}

		// Migrate each meta key
		foreach ( $old_meta as $meta ) {
			if ( in_array( $meta->meta_key, $rankmath_meta_keys, true ) ) {
				// Handle serialized data
				$meta_value = maybe_unserialize( $meta->meta_value );
				
				// Update URLs in meta values if old site URL is provided
				if ( ! empty( $old_site_url ) && is_string( $meta_value ) ) {
					$meta_value = str_replace( $old_site_url, home_url(), $meta_value );
				} elseif ( is_array( $meta_value ) || is_object( $meta_value ) ) {
					$meta_value = kpg_replace_urls_in_array( $meta_value, $old_site_url, home_url() );
				}

				update_post_meta( $new_post->ID, $meta->meta_key, $meta_value );
			}
		}

		$stats['posts']++;
	}

	// 2. Migrate term meta (categories, tags)
	$old_terms = $old_db->get_results( 
		"SELECT term_id, slug, taxonomy FROM {$old_db->prefix}term_taxonomy tt
		 INNER JOIN {$old_db->prefix}terms t ON tt.term_id = t.term_id
		 WHERE taxonomy IN ('category', 'post_tag')"
	);

	foreach ( $old_terms as $old_term ) {
		// Find matching term in new database by slug
		$new_term = get_term_by( 'slug', $old_term->slug, $old_term->taxonomy );
		
		if ( ! $new_term || is_wp_error( $new_term ) ) {
			$stats['skipped']++;
			continue;
		}

		// Get Rank Math meta for this term from old database
		$old_meta = $old_db->get_results( 
			$old_db->prepare(
				"SELECT meta_key, meta_value FROM {$old_db->prefix}termmeta WHERE term_id = %d AND meta_key LIKE %s",
				$old_term->term_id,
				'rank_math_%'
			)
		);

		if ( empty( $old_meta ) ) {
			continue;
		}

		// Migrate each meta key
		foreach ( $old_meta as $meta ) {
			if ( in_array( $meta->meta_key, $rankmath_meta_keys, true ) ) {
				$meta_value = maybe_unserialize( $meta->meta_value );
				
				// Update URLs
				if ( ! empty( $old_site_url ) && is_string( $meta_value ) ) {
					$meta_value = str_replace( $old_site_url, home_url(), $meta_value );
				} elseif ( is_array( $meta_value ) || is_object( $meta_value ) ) {
					$meta_value = kpg_replace_urls_in_array( $meta_value, $old_site_url, home_url() );
				}

				update_term_meta( $new_term->term_id, $meta->meta_key, $meta_value );
			}
		}

		$stats['terms']++;
	}

	// 3. Migrate user meta (authors)
	$old_users = $old_db->get_results( 
		"SELECT ID, user_nicename FROM {$old_db->prefix}users"
	);

	foreach ( $old_users as $old_user ) {
		// Find matching user in new database by nicename
		$new_user = get_user_by( 'slug', $old_user->user_nicename );
		
		if ( ! $new_user ) {
			$stats['skipped']++;
			continue;
		}

		// Get Rank Math meta for this user from old database
		$old_meta = $old_db->get_results( 
			$old_db->prepare(
				"SELECT meta_key, meta_value FROM {$old_db->prefix}usermeta WHERE user_id = %d AND meta_key LIKE %s",
				$old_user->ID,
				'rank_math_%'
			)
		);

		if ( empty( $old_meta ) ) {
			continue;
		}

		// Migrate each meta key
		foreach ( $old_meta as $meta ) {
			if ( in_array( $meta->meta_key, $rankmath_meta_keys, true ) ) {
				$meta_value = maybe_unserialize( $meta->meta_value );
				
				// Update URLs
				if ( ! empty( $old_site_url ) && is_string( $meta_value ) ) {
					$meta_value = str_replace( $old_site_url, home_url(), $meta_value );
				} elseif ( is_array( $meta_value ) || is_object( $meta_value ) ) {
					$meta_value = kpg_replace_urls_in_array( $meta_value, $old_site_url, home_url() );
				}

				update_user_meta( $new_user->ID, $meta->meta_key, $meta_value );
			}
		}

		$stats['users']++;
	}

	// Build result message
	$results['success'] = true;
	$results['message'] = sprintf(
		'Migration completed! Posts: %d, Terms: %d, Users: %d, Skipped: %d',
		$stats['posts'],
		$stats['terms'],
		$stats['users'],
		$stats['skipped']
	);

	return $results;
}

/**
 * Recursively replace URLs in array/object
 */
function kpg_replace_urls_in_array( $data, $old_url, $new_url ) {
	if ( is_array( $data ) ) {
		foreach ( $data as $key => $value ) {
			$data[ $key ] = kpg_replace_urls_in_array( $value, $old_url, $new_url );
		}
	} elseif ( is_object( $data ) ) {
		foreach ( $data as $key => $value ) {
			$data->$key = kpg_replace_urls_in_array( $value, $old_url, $new_url );
		}
	} elseif ( is_string( $data ) && ! empty( $old_url ) ) {
		$data = str_replace( $old_url, $new_url, $data );
	}
	
	return $data;
}

/**
 * WP-CLI command for migration
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command( 'kpg-migrate-rankmath', 'kpg_wpcli_migrate_rankmath' );
	WP_CLI::add_command( 'kpg-import-rankmath-csv', 'kpg_wpcli_import_rankmath_csv' );
}

/**
 * WP-CLI migration command handler
 */
function kpg_wpcli_migrate_rankmath( $args, $assoc_args ) {
	$old_db_host = $assoc_args['old-db-host'] ?? '';
	$old_db_name = $assoc_args['old-db-name'] ?? '';
	$old_db_user = $assoc_args['old-db-user'] ?? '';
	$old_db_pass = $assoc_args['old-db-pass'] ?? '';
	$old_db_prefix = $assoc_args['old-db-prefix'] ?? 'wp_';
	$old_site_url = $assoc_args['old-site-url'] ?? '';

	if ( empty( $old_db_host ) || empty( $old_db_name ) || empty( $old_db_user ) || empty( $old_db_pass ) ) {
		WP_CLI::error( 'Missing required database connection parameters.' );
		return;
	}

	WP_CLI::line( 'Starting Rank Math SEO migration...' );

	$result = kpg_migrate_rankmath_meta( $old_db_host, $old_db_name, $old_db_user, $old_db_pass, $old_db_prefix, $old_site_url );

	if ( $result['success'] ) {
		WP_CLI::success( $result['message'] );
		if ( ! empty( $result['details'] ) ) {
			foreach ( $result['details'] as $detail ) {
				WP_CLI::line( '  - ' . $detail );
			}
		}
	} else {
		WP_CLI::error( $result['message'] );
	}
}

/**
 * WP-CLI CSV import command handler
 */
function kpg_wpcli_import_rankmath_csv( $args, $assoc_args ) {
	$csv_file = $args[0] ?? '';
	$old_site_url = $assoc_args['old-site-url'] ?? '';

	if ( empty( $csv_file ) ) {
		WP_CLI::error( 'Please provide path to CSV file: wp kpg-import-rankmath-csv /path/to/file.csv' );
		return;
	}

	if ( ! file_exists( $csv_file ) ) {
		WP_CLI::error( 'CSV file not found: ' . $csv_file );
		return;
	}

	WP_CLI::line( 'Starting Rank Math SEO CSV import...' );

	$result = kpg_import_rankmath_from_csv( $csv_file, $old_site_url );

	if ( $result['success'] ) {
		WP_CLI::success( $result['message'] );
		if ( ! empty( $result['details'] ) ) {
			foreach ( $result['details'] as $detail ) {
				WP_CLI::line( '  - ' . $detail );
			}
		}
	} else {
		WP_CLI::error( $result['message'] );
	}
}

/**
 * Replace URLs in canonical URLs
 */
function kpg_replace_canonical_urls( $old_url ) {
	global $wpdb;
	
	$results = [
		'success' => false,
		'message' => '',
		'details' => [],
	];
	
	if ( empty( $old_url ) ) {
		$results['message'] = 'Musisz podać stary URL do zamiany.';
		return $results;
	}
	
	$new_url = home_url();
	$old_url = rtrim( $old_url, '/' );
	$new_url = rtrim( $new_url, '/' );
	
	// Get all posts with canonical URLs containing old URL
	$canonical_meta = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT post_id, meta_value 
			 FROM {$wpdb->postmeta} 
			 WHERE meta_key = 'rank_math_canonical_url' 
			 AND meta_value LIKE %s",
			'%' . $wpdb->esc_like( $old_url ) . '%'
		)
	);
	
	if ( empty( $canonical_meta ) ) {
		$results['success'] = true;
		$results['message'] = 'Nie znaleziono canonical URLs ze starym URL-em.';
		return $results;
	}
	
	$updated = 0;
	$skipped = 0;
	
	foreach ( $canonical_meta as $meta ) {
		$old_canonical = $meta->meta_value;
		$new_canonical = str_replace( $old_url, $new_url, $old_canonical );
		
		if ( $old_canonical !== $new_canonical ) {
			update_post_meta( $meta->post_id, 'rank_math_canonical_url', $new_canonical );
			$updated++;
			
			// Get post title for details
			$post = get_post( $meta->post_id );
			if ( $post ) {
				$results['details'][] = sprintf( 
					'Post "%s" (ID: %d): %s → %s', 
					$post->post_title,
					$meta->post_id,
					$old_canonical,
					$new_canonical
				);
			}
		} else {
			$skipped++;
		}
	}
	
	$results['success'] = true;
	$results['message'] = sprintf( 
		'Zamieniono URL-e w %d canonical URLs. Pominięto: %d.', 
		$updated, 
		$skipped 
	);
	
	// Limit details to first 20
	if ( count( $results['details'] ) > 20 ) {
		$results['details'] = array_slice( $results['details'], 0, 20 );
		$results['details'][] = sprintf( '... i %d więcej', count( $canonical_meta ) - 20 );
	}
	
	return $results;
}

/**
 * Fill rank_math_canonical_url from permalink for posts/pages that have SEO data but no canonical.
 */
function kpg_fill_canonical_from_permalink() {
	global $wpdb;

	$results = [
		'success' => false,
		'message' => '',
		'details' => [],
	];

	// Posts that have rank_math_title or rank_math_description but empty rank_math_canonical_url.
	$post_ids = $wpdb->get_col(
		"SELECT DISTINCT p.ID
		 FROM {$wpdb->posts} p
		 INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key IN ('rank_math_title', 'rank_math_description') AND pm.meta_value != ''
		 LEFT JOIN {$wpdb->postmeta} pm_canon ON p.ID = pm_canon.post_id AND pm_canon.meta_key = 'rank_math_canonical_url'
		 WHERE p.post_status = 'publish' AND p.post_type IN ('post', 'page')
		 AND (pm_canon.meta_id IS NULL OR pm_canon.meta_value = '')
		 LIMIT 2000"
	);

	if ( empty( $post_ids ) ) {
		$results['success'] = true;
		$results['message'] = 'Nie znaleziono postów/stron z danymi SEO bez canonical – nic do zrobienia.';
		return $results;
	}

	$updated = 0;
	foreach ( $post_ids as $post_id ) {
		$permalink = get_permalink( (int) $post_id );
		if ( $permalink ) {
			update_post_meta( (int) $post_id, 'rank_math_canonical_url', $permalink );
			$updated++;
			$post = get_post( $post_id );
			if ( $post ) {
				$results['details'][] = sprintf( 'Uzupełniono: "%s" (ID: %d) → %s', $post->post_title, $post_id, $permalink );
			}
		}
	}

	$results['success'] = true;
	$results['message'] = sprintf( 'Uzupełniono canonical z permalinków dla %d postów/stron.', $updated );
	return $results;
}

/**
 * Cleanup all Rank Math SEO meta data
 */
function kpg_cleanup_rankmath_meta() {
	global $wpdb;

	$results = [
		'success' => false,
		'message' => '',
		'details' => [],
	];

	$stats = [
		'posts' => 0,
		'terms' => 0,
		'users' => 0,
	];

	// 1. Delete post meta
	$deleted_posts = $wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s",
			'rank_math_%'
		)
	);
	$stats['posts'] = $deleted_posts;

	// 2. Delete term meta
	$deleted_terms = $wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->termmeta} WHERE meta_key LIKE %s",
			'rank_math_%'
		)
	);
	$stats['terms'] = $deleted_terms;

	// 3. Delete user meta
	$deleted_users = $wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE %s",
			'rank_math_%'
		)
	);
	$stats['users'] = $deleted_users;

	$results['success'] = true;
	$results['message'] = sprintf(
		'Czyszczenie zakończone! Usunięto: %d wpisów postmeta, %d wpisów termmeta, %d wpisów usermeta',
		$stats['posts'],
		$stats['terms'],
		$stats['users']
	);

	return $results;
}

/**
 * Check if canonical URLs point to old domain
 */
function kpg_check_canonical_urls_old_domain( $old_url = 'https://www.kpgio.pl' ) {
	global $wpdb;
	
	$results = [
		'count' => 0,
		'examples' => [],
		'old_url' => $old_url,
	];
	
	if ( empty( $old_url ) ) {
		$old_url = 'https://www.kpgio.pl';
	}
	
	$old_url = rtrim( $old_url, '/' );
	
	// Count posts with canonical URLs containing old URL
	$canonical_meta = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT pm.post_id, pm.meta_value, p.post_title, p.post_name
			 FROM {$wpdb->postmeta} pm
			 INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
			 WHERE pm.meta_key = 'rank_math_canonical_url' 
			 AND pm.meta_value LIKE %s
			 AND p.post_status = 'publish'
			 LIMIT 10",
			'%' . $wpdb->esc_like( $old_url ) . '%'
		)
	);
	
	$results['count'] = count( $canonical_meta );
	
	foreach ( $canonical_meta as $meta ) {
		$results['examples'][] = [
			'post_id' => $meta->post_id,
			'post_title' => $meta->post_title,
			'post_slug' => $meta->post_name,
			'canonical_url' => $meta->meta_value,
		];
	}
	
	// Get total count
	$total_count = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(DISTINCT pm.post_id)
			 FROM {$wpdb->postmeta} pm
			 INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
			 WHERE pm.meta_key = 'rank_math_canonical_url' 
			 AND pm.meta_value LIKE %s
			 AND p.post_status = 'publish'",
			'%' . $wpdb->esc_like( $old_url ) . '%'
		)
	);
	
	$results['total_count'] = $total_count;
	
	return $results;
}

/**
 * Verify Rank Math SEO import
 */
function kpg_verify_rankmath_import() {
	global $wpdb;

	$verification = [];

	// Count posts with Rank Math meta
	$posts_with_meta = $wpdb->get_var(
		"SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key LIKE 'rank_math_%'"
	);
	$verification[] = sprintf( 'Posty z danymi Rank Math: %d', $posts_with_meta );

	// Count posts with title
	$posts_with_title = $wpdb->get_var(
		"SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key = 'rank_math_title' AND meta_value != ''"
	);
	$verification[] = sprintf( 'Posty z SEO Title: %d', $posts_with_title );

	// Count posts with description
	$posts_with_desc = $wpdb->get_var(
		"SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key = 'rank_math_description' AND meta_value != ''"
	);
	$verification[] = sprintf( 'Posty z SEO Description: %d', $posts_with_desc );

	// Count posts with focus keyword
	$posts_with_keyword = $wpdb->get_var(
		"SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key = 'rank_math_focus_keyword' AND meta_value != ''"
	);
	$verification[] = sprintf( 'Posty z Focus Keyword: %d', $posts_with_keyword );

	// Count posts with canonical
	$posts_with_canonical = $wpdb->get_var(
		"SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key = 'rank_math_canonical_url' AND meta_value != ''"
	);
	$verification[] = sprintf( 'Posty z Canonical URL: %d', $posts_with_canonical );
	
	// Check if canonical URLs point to old domain
	$canonical_check = kpg_check_canonical_urls_old_domain();
	if ( $canonical_check['total_count'] > 0 ) {
		$verification[] = '';
		$verification[] = sprintf( '⚠️ UWAGA: Znaleziono %d postów z canonical URLs wskazującymi na stary URL (%s)', $canonical_check['total_count'], $canonical_check['old_url'] );
		$verification[] = 'Użyj funkcji "Zamień URL-e w Canonical URLs" poniżej, aby to naprawić.';
	} else {
		$verification[] = '';
		$verification[] = '✅ Wszystkie canonical URLs wskazują na poprawny URL.';
	}

	// Count posts with schema
	$posts_with_schema = $wpdb->get_var(
		"SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} WHERE meta_key = 'rank_math_schema_data' AND meta_value != ''"
	);
	$verification[] = sprintf( 'Posty z Schema Data: %d', $posts_with_schema );

	// Count terms with Rank Math meta
	$terms_with_meta = $wpdb->get_var(
		"SELECT COUNT(DISTINCT term_id) FROM {$wpdb->termmeta} WHERE meta_key LIKE 'rank_math_%'"
	);
	$verification[] = sprintf( 'Kategorie/Tagi z danymi Rank Math: %d', $terms_with_meta );

	// Count users with Rank Math meta
	$users_with_meta = $wpdb->get_var(
		"SELECT COUNT(DISTINCT user_id) FROM {$wpdb->usermeta} WHERE meta_key LIKE 'rank_math_%'"
	);
	$verification[] = sprintf( 'Autorzy z danymi Rank Math: %d', $users_with_meta );

	// Sample posts with data
	$sample_posts = $wpdb->get_results(
		"SELECT p.ID, p.post_title, p.post_name, pm.meta_value as seo_title
		 FROM {$wpdb->posts} p
		 INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
		 WHERE p.post_type = 'post' AND p.post_status = 'publish'
		 AND pm.meta_key = 'rank_math_title' AND pm.meta_value != ''
		 LIMIT 5"
	);

	if ( ! empty( $sample_posts ) ) {
		$verification[] = '';
		$verification[] = 'Przykładowe posty z danymi SEO:';
		foreach ( $sample_posts as $post ) {
			$verification[] = sprintf( '  - %s (slug: %s) - Title: %s', $post->post_title, $post->post_name, substr( $post->seo_title, 0, 50 ) . '...' );
		}
	}

	return $verification;
}

/**
 * Detailed verification of specific posts
 */
function kpg_verify_rankmath_posts_detailed() {
	global $wpdb;
	
	// List of post slugs to check (from verification results)
	$post_slugs_to_check = [
		'nowelizacja-przepisow-kodeksu-postepowania-cywilnego-w-zakresie-postepowania-nakazowego',
		'preferencyjna-stawka-podatku-dochodowego-dla-osob-prawnych-cit-dla-malego-podatnika',
		'program-maluch-2020-wskazowki-dla-podmiotow-ubiegajacych-sie-o-dotacje',
		'odroczenie-w-edukacji-domowej',
		'czy-na-egzamin-kwalifikacyjny-dla-dziecka-uczacego-sie-w-edukacji-domowej-mozna-powolac-nauczyciela-z-innej-szkoly',
	];
	
	$results = [];
	$results[] = '<h3>Szczegółowa weryfikacja postów:</h3>';
	$results[] = '<div style="background: #f0f0f1; padding: 15px; margin: 10px 0; border-left: 4px solid #2271b1;">';
	
	foreach ( $post_slugs_to_check as $slug ) {
		$post = get_page_by_path( $slug, OBJECT, 'post' );
		
		if ( ! $post ) {
			$results[] = sprintf( '<p style="margin: 10px 0;"><strong style="color: #d63638;">❌ Post "%s"</strong> - nie znaleziono</p>', esc_html( $slug ) );
			continue;
		}
		
		$results[] = sprintf( '<div style="margin: 15px 0; padding: 10px; background: white; border: 1px solid #ddd;">' );
		$results[] = sprintf( '<h4 style="margin-top: 0;">📄 %s (ID: %d)</h4>', esc_html( $post->post_title ), $post->ID );
		$results[] = sprintf( '<p><strong>Slug:</strong> <code>%s</code></p>', esc_html( $slug ) );
		$results[] = sprintf( '<p><strong>URL:</strong> <a href="%s" target="_blank">%s</a></p>', esc_url( get_permalink( $post->ID ) ), esc_url( get_permalink( $post->ID ) ) );
		
		// Get all Rank Math meta
		$meta_keys = [
			'rank_math_title' => 'SEO Title',
			'rank_math_description' => 'SEO Description',
			'rank_math_focus_keyword' => 'Focus Keyword',
			'rank_math_canonical_url' => 'Canonical URL',
			'rank_math_robots' => 'Robots',
			'rank_math_seo_score' => 'SEO Score',
			'rank_math_schema_data' => 'Schema Data',
			'rank_math_facebook_title' => 'Facebook Title',
			'rank_math_facebook_description' => 'Facebook Description',
			'rank_math_twitter_title' => 'Twitter Title',
			'rank_math_twitter_description' => 'Twitter Description',
		];
		
		$has_data = false;
		$meta_list = [];
		foreach ( $meta_keys as $meta_key => $label ) {
			$meta_value = get_post_meta( $post->ID, $meta_key, true );
			if ( ! empty( $meta_value ) ) {
				$has_data = true;
				if ( $meta_key === 'rank_math_schema_data' ) {
					$schema_preview = is_string( $meta_value ) ? substr( $meta_value, 0, 150 ) : 'Array/Object';
					$meta_list[] = sprintf( '<li><strong style="color: #00a32a;">✅ %s:</strong> <code style="font-size: 11px;">%s%s</code></li>', esc_html( $label ), esc_html( $schema_preview ), ( is_string( $meta_value ) && strlen( $meta_value ) > 150 ? '...' : '' ) );
				} elseif ( strlen( $meta_value ) > 100 ) {
					$meta_list[] = sprintf( '<li><strong style="color: #00a32a;">✅ %s:</strong> %s...</li>', esc_html( $label ), esc_html( substr( $meta_value, 0, 100 ) ) );
				} else {
					$meta_list[] = sprintf( '<li><strong style="color: #00a32a;">✅ %s:</strong> %s</li>', esc_html( $label ), esc_html( $meta_value ) );
				}
			}
		}
		
		if ( $has_data ) {
			$results[] = '<ul style="margin: 10px 0; padding-left: 20px;">' . implode( '', $meta_list ) . '</ul>';
		} else {
			$results[] = '<p style="color: #d63638;">⚠️ Brak danych Rank Math SEO</p>';
		}
		
		$results[] = '</div>';
	}
	
	$results[] = '</div>';
	
	// Also check random posts with SEO data
	$random_posts = $wpdb->get_results(
		"SELECT p.ID, p.post_title, p.post_name
		 FROM {$wpdb->posts} p
		 INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
		 WHERE p.post_type = 'post' AND p.post_status = 'publish'
		 AND pm.meta_key = 'rank_math_title' AND pm.meta_value != ''
		 ORDER BY RAND()
		 LIMIT 3"
	);
	
	if ( ! empty( $random_posts ) ) {
		$results[] = '<h3>Losowe posty z danymi SEO:</h3>';
		$results[] = '<div style="background: #f0f0f1; padding: 15px; margin: 10px 0; border-left: 4px solid #2271b1;">';
		
		foreach ( $random_posts as $post ) {
			$title = get_post_meta( $post->ID, 'rank_math_title', true );
			$desc = get_post_meta( $post->ID, 'rank_math_description', true );
			$keyword = get_post_meta( $post->ID, 'rank_math_focus_keyword', true );
			
			$results[] = '<div style="margin: 10px 0; padding: 10px; background: white; border: 1px solid #ddd;">';
			$results[] = sprintf( '<h4 style="margin-top: 0;">📄 %s</h4>', esc_html( $post->post_title ) );
			$results[] = sprintf( '<p><strong>Slug:</strong> <code>%s</code></p>', esc_html( $post->post_name ) );
			$meta_items = [];
			if ( $title ) {
				$meta_items[] = sprintf( '<li><strong style="color: #00a32a;">✅ Title:</strong> %s</li>', esc_html( substr( $title, 0, 80 ) . ( strlen( $title ) > 80 ? '...' : '' ) ) );
			}
			if ( $desc ) {
				$meta_items[] = sprintf( '<li><strong style="color: #00a32a;">✅ Description:</strong> %s</li>', esc_html( substr( $desc, 0, 80 ) . ( strlen( $desc ) > 80 ? '...' : '' ) ) );
			}
			if ( $keyword ) {
				$meta_items[] = sprintf( '<li><strong style="color: #00a32a;">✅ Keyword:</strong> %s</li>', esc_html( $keyword ) );
			}
			if ( ! empty( $meta_items ) ) {
				$results[] = '<ul style="margin: 10px 0; padding-left: 20px;">' . implode( '', $meta_items ) . '</ul>';
			}
			$results[] = '</div>';
		}
		
		$results[] = '</div>';
	}
	
	return implode( "\n", $results );
}
