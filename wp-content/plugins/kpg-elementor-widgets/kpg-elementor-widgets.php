<?php
/**
 * Plugin Name: KPG Elementor Widgets
 * Description: Custom Elementor widgets for KPG project - blog, team, navigation
 * Version: 1.1.74
 * Author: KPG Development Team
 * Text Domain: kpg-elementor-widgets
 * Requires PHP: 7.4
 * Requires at least: 5.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Main KPG Elementor Widgets Class
 */
final class KPG_Elementor_Widgets {

	const VERSION = '1.1.74';
	const MINIMUM_ELEMENTOR_VERSION = '3.0.0';
	const MINIMUM_PHP_VERSION = '7.4';

	private static $_instance = null;

	/**
	 * Singleton instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
		add_action( 'admin_print_scripts', [ $this, 'dequeue_problematic_admin_scripts' ], 999 );
		add_action( 'wp_print_scripts', [ $this, 'dequeue_problematic_admin_scripts' ], 999 );
	}

	/**
	 * Initialize the plugin
	 */
	public function init() {
		// Check if Elementor is installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_elementor' ] );
			return;
		}

		// Check Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return;
		}

		// Check PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return;
		}

		// Load Elementor loop integration
		require_once( __DIR__ . '/includes/elementor-loop-integration.php' );
		
		// Load blog permalink fix - DISABLED due to Rank Math SEO conflicts
		// require_once( __DIR__ . '/includes/blog-permalink-fix.php' );
		
		// Load user profile fields
		require_once( __DIR__ . '/includes/user-profile-fields.php' );

		// Load REST fields for related posts cards (author avatar/name/date)
		require_once( __DIR__ . '/includes/related-posts-rest-fields.php' );
		
		// Load author permalink settings
		require_once( __DIR__ . '/includes/author-permalink-settings.php' );
		
		// Load blog pagination SEO (canonical, prev, next)
		require_once( __DIR__ . '/includes/blog-pagination-seo.php' );
		
		// Load security anti-bot protection
		require_once( __DIR__ . '/includes/security-anti-bot.php' );
		
		// Load Rank Math migration (Tools → Rank Math Migration)
		require_once( __DIR__ . '/includes/rank-math-migration.php' );
		
		// Tymczasowa kolumna Canonical URL na liście postów (wyłącz: define('KPG_SHOW_CANONICAL_COLUMN', false) w wp-config.php)
		require_once( __DIR__ . '/includes/admin-canonical-column.php' );
		
		// Register widget styles and scripts
		add_action( 'elementor/frontend/after_register_styles', [ $this, 'register_styles' ] );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_scripts' ] );

		// Enqueue global styles (spis responsive)
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_global_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'dequeue_frontend_legacy_icon_assets' ], 999 );
		add_action( 'wp_head', [ $this, 'print_primary_font_faces' ], 100 );
		add_filter( 'elementor_pro/custom_fonts/font_display', [ $this, 'force_custom_font_display_swap' ], 10, 3 );
		add_filter( 'wp_resource_hints', [ $this, 'add_frontend_resource_hints' ], 10, 2 );
		add_filter( 'elementor/widget/render_content', [ $this, 'rename_related_posts_heading_server_side' ], 10, 2 );
		
		// Enqueue blog structure script on single posts
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_blog_structure_script' ] );

		// Register widgets
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );

		// Register widget categories
		add_action( 'elementor/elements/categories_registered', [ $this, 'register_widget_categories' ] );
		
		// Wyłącz domyślne komentarze WordPress jeśli widget jest użyty
		add_filter( 'comments_template', [ $this, 'disable_default_comments_template' ], 99 );
		add_action( 'wp_head', [ $this, 'hide_default_comments_css' ] );
		add_action( 'comments_template', [ $this, 'force_hide_comments_template' ], 999 );
	}
	
	/**
	 * Wyłącz domyślny template komentarzy jeśli widget KPG jest użyty
	 */
	public function disable_default_comments_template( $template ) {
		// Sprawdź czy na stronie jest widget KPG Comments
		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return $template;
		}
		
		// Sprawdź czy Elementor jest dostępny
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return $template;
		}
		
		// Sprawdź czy strona jest zbudowana z Elementorem
		$document = \Elementor\Plugin::$instance->documents->get( $post_id );
		if ( ! $document ) {
			return $template;
		}
		
		// Sprawdź czy widget jest użyty na stronie
		$elements = $document->get_elements_data();
		if ( $elements && $this->has_kpg_comments_widget( $elements ) ) {
			// Zwróć pusty template
			$empty_template = __DIR__ . '/includes/empty-comments-template.php';
			if ( file_exists( $empty_template ) ) {
				return $empty_template;
			}
		}
		
		return $template;
	}
	
	/**
	 * Sprawdź czy w elementach jest widget KPG Comments
	 */
	private function has_kpg_comments_widget( $elements ) {
		if ( ! is_array( $elements ) ) {
			return false;
		}
		
		foreach ( $elements as $element ) {
			if ( isset( $element['widgetType'] ) && $element['widgetType'] === 'kpg-comments' ) {
				return true;
			}
			if ( isset( $element['elements'] ) && $this->has_kpg_comments_widget( $element['elements'] ) ) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Force hide comments template - ostateczny fallback
	 */
	public function force_hide_comments_template( $template ) {
		// Jeśli to Elementor i jest widget komentarzy - użyj pustego template
		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return $template;
		}
		
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return $template;
		}
		
		// Sprawdź czy dokument Elementora istnieje
		$document = \Elementor\Plugin::$instance->documents->get( $post_id );
		if ( $document ) {
			$elements = $document->get_elements_data();
			if ( $elements && $this->has_kpg_comments_widget( $elements ) ) {
				$empty_template = __DIR__ . '/includes/empty-comments-template.php';
				if ( file_exists( $empty_template ) ) {
					return $empty_template;
				}
			}
		}
		
		return $template;
	}

	/**
	 * CSS do ukrycia domyślnych komentarzy WordPress poza widgetem
	 */
	public function hide_default_comments_css() {
		?>
		<style>
			/* Ukryj WSZYSTKIE komentarze poza widgetem KPG */
			.comments-area,
			#comments,
			.comment-list,
			.comment-respond,
			.comment-form,
			.comments-title,
			.comment,
			.comment-body,
			.comment-author,
			.comment-date,
			.comment-text,
			/* Ukryj tylko linki odpowiedzi poza kontenerem KPG */
			.comment-reply-link:not(.kpg-comment-reply-link) {
				display: none !important;
			}
			
			/* Pokaż linki odpowiedzi w kontenerze KPG */
			.kpg-comments-container .kpg-comment-reply-link,
			.kpg-comments-container .kpg-comment-reply-link a,
			.kpg-comments-container .comment-reply-link {
				display: inline-block !important;
				visibility: visible !important;
				opacity: 1 !important;
				height: auto !important;
				overflow: visible !important;
			}


			/* Pokaż komentarze w widgetcie KPG */
			.kpg-comments-container .kpg-comment-main,
			.kpg-comments-container .kpg-comment,
			.kpg-comments-container .comment-respond,
			.kpg-comments-container .comment-form,
			.kpg-comments-container #respond,
			.kpg-comments-container #commentform,
			.kpg-comments-container form#commentform,
			.kpg-comments-container .kpg-comment-reply-link,
			.kpg-comments-container .kpg-comment-reply-link a,
			.kpg-comments-container .comment-reply-link,
			.kpg-comments-container .comment-reply-link a,
			.kpg-comments-container .children .kpg-comment-reply-link,
			.kpg-comments-container .children .kpg-comment-reply-link a,
			.kpg-comments-container .children .comment-reply-link,
			.kpg-comments-container .children .comment-reply-link a {
				display: block !important;
				visibility: visible !important;
				opacity: 1 !important;
			}

			.kpg-comments-container .kpg-comment-main {
				display: flex !important;
			}

			.kpg-comments-container .kpg-comment-reply-link,
			.kpg-comments-container .children .kpg-comment-reply-link {
				display: inline-block !important;
			}


			/* Pokaż TYLKO komentarze w widgecie KPG - bardzo specyficzne selektory */
			.kpg-comments-container .kpg-comment-main,
			.kpg-comments-container .kpg-comment,
			.kpg-comments-container .comment.kpg-comment-main,
			.kpg-comments-container .comment.kpg-comment,
			.kpg-comments-container .kpg-comments-list .comment,
			.kpg-comments-container .comment-respond,
			.kpg-comments-container .comment-form,
			.kpg-comments-container #respond,
			.kpg-comments-container #commentform,
			.kpg-comments-container .kpg-comment-form-container,
			.kpg-comments-container .kpg-comment-form {
				display: block !important;
				visibility: visible !important;
				opacity: 1 !important;
				height: auto !important;
				overflow: visible !important;
			}

			.kpg-comments-container .kpg-comment-main {
				display: flex !important;
			}
		</style>
		<?php
	}

	/**
	 * Replace legacy related-posts heading in server-rendered Elementor HTML
	 * so users do not see the old title before frontend JS runs.
	 */
	public function rename_related_posts_heading_server_side( $content, $widget ) {
		if ( ! is_string( $content ) || '' === $content ) {
			return $content;
		}

		if ( ! is_singular( 'post' ) ) {
			return $content;
		}

		if ( ! is_object( $widget ) || ! method_exists( $widget, 'get_name' ) ) {
			return $content;
		}

		$widget_name = (string) $widget->get_name();
		if ( 'heading' !== $widget_name && 'theme-post-title' !== $widget_name ) {
			return $content;
		}

		$replacements = [
			'Zobacz inne artykuły' => 'Powiązane wpisy',
			'Zobacz inne artykuly' => 'Powiązane wpisy',
			'Inne artykuły'        => 'Powiązane wpisy',
			'Inne artykuly'        => 'Powiązane wpisy',
		];

		foreach ( $replacements as $from => $to ) {
			if ( false !== strpos( $content, $from ) ) {
				$content = str_replace( $from, $to, $content );
			}
		}

		return $content;
	}

	/**
	 * Admin notice for missing Elementor
	 */
	public function admin_notice_missing_elementor() {
		$message = sprintf(
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'kpg-elementor-widgets' ),
			'<strong>' . esc_html__( 'KPG Elementor Widgets', 'kpg-elementor-widgets' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'kpg-elementor-widgets' ) . '</strong>'
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice for minimum Elementor version
	 */
	public function admin_notice_minimum_elementor_version() {
		$message = sprintf(
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'kpg-elementor-widgets' ),
			'<strong>' . esc_html__( 'KPG Elementor Widgets', 'kpg-elementor-widgets' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'kpg-elementor-widgets' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice for minimum PHP version
	 */
	public function admin_notice_minimum_php_version() {
		$message = sprintf(
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'kpg-elementor-widgets' ),
			'<strong>' . esc_html__( 'KPG Elementor Widgets', 'kpg-elementor-widgets' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'kpg-elementor-widgets' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Register widget categories
	 */
	public function register_widget_categories( $elements_manager ) {
		$elements_manager->add_category(
			'kpg-widgets',
			[
				'title' => esc_html__( 'KPG Widgets', 'kpg-elementor-widgets' ),
				'icon' => 'fa fa-plug',
			]
		);
	}

	/**
	 * Register widget styles
	 */
	public function register_styles() {
		// Blog Sorting
		wp_register_style(
			'kpg-blog-sorting-style',
			plugins_url( 'assets/css/blog-sorting.css', __FILE__ ),
			[],
			self::VERSION
		);

		// Breadcrumbs
		wp_register_style(
			'kpg-breadcrumbs-style',
			plugins_url( 'assets/css/breadcrumbs.css', __FILE__ ),
			[],
			self::VERSION
		);

		// Pagination
		wp_register_style(
			'kpg-pagination-style',
			plugins_url( 'assets/css/pagination.css', __FILE__ ),
			[],
			self::VERSION
		);

		// Blog Archive Desktop
		wp_register_style(
			'kpg-blog-archive-desktop-style',
			plugins_url( 'assets/css/blog-archive-desktop.css', __FILE__ ),
			[],
			self::VERSION,
			'screen and (min-width: 768px)'
		);

		// Blog Archive Mobile
		wp_register_style(
			'kpg-blog-archive-mobile-style',
			plugins_url( 'assets/css/blog-archive-mobile.css', __FILE__ ),
			[],
			self::VERSION,
			'screen and (max-width: 767px)'
		);

		// Blog Archive (old - keep for compatibility)
		wp_register_style(
			'kpg-blog-archive-style',
			plugins_url( 'assets/css/blog-archive.css', __FILE__ ),
			[],
			self::VERSION
		);

		// Team Slider (with Swiper)
		wp_register_style(
			'swiper',
			'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
			[],
			'11.0.0'
		);

		wp_register_style(
			'kpg-team-slider-style',
			plugins_url( 'assets/css/team-slider.css', __FILE__ ),
			[ 'swiper' ],
			self::VERSION
		);

		// Table of Contents
		wp_register_style(
			'kpg-toc-style',
			plugins_url( 'assets/css/table-of-contents.css', __FILE__ ),
			[],
			self::VERSION
		);

		// Comments
		wp_register_style(
			'kpg-comments-style',
			plugins_url( 'assets/css/comments.css', __FILE__ ),
			[],
			self::VERSION
		);

		// Important Section
		wp_register_style(
			'kpg-important-style',
			plugins_url( 'assets/css/important-section.css', __FILE__ ),
			[],
			self::VERSION
		);

		// Articles From (O Autorze)
		wp_register_style(
			'kpg-articles-from-style',
			plugins_url( 'assets/css/articles-from.css', __FILE__ ),
			[],
			self::VERSION
		);

		// Post Meta Bar
		wp_register_style(
			'kpg-post-meta-bar-style',
			plugins_url( 'assets/css/post-meta-bar.css', __FILE__ ),
			[],
			self::VERSION
		);

		// O Nas
		wp_register_style(
			'kpg-onas-style',
			plugins_url( 'assets/css/onas.css', __FILE__ ),
			[],
			self::VERSION
		);

		// Menu
		wp_register_style(
			'kpg-menu-style',
			plugins_url( 'assets/css/menu.css', __FILE__ ),
			[],
			self::VERSION
		);

		// Blog Content (global)
		wp_register_style(
			'kpg-blog-content-style',
			plugins_url( 'assets/css/blog-content.css', __FILE__ ),
			[],
			self::VERSION
		);

		// Blog Featured Desktop
		wp_register_style(
			'kpg-blog-featured-desktop-style',
			plugins_url( 'assets/css/blog-featured-desktop.css', __FILE__ ),
			[],
			self::VERSION,
			'screen and (min-width: 768px)'
		);

		// Blog Featured Mobile
		wp_register_style(
			'kpg-blog-featured-mobile-style',
			plugins_url( 'assets/css/blog-featured-mobile.css', __FILE__ ),
			[],
			self::VERSION,
			'screen and (max-width: 767px)'
		);

		// Spis Responsive (spis_mobile / spis_desktop)
		wp_register_style(
			'kpg-spis-responsive-style',
			plugins_url( 'assets/css/spis-responsive.css', __FILE__ ),
			[],
			self::VERSION
		);

		// Blog Semantic HTML (article/aside styles)
		wp_register_style(
			'kpg-blog-semantic-style',
			plugins_url( 'assets/css/blog-semantic.css', __FILE__ ),
			[],
			self::VERSION
		);

		// Frontend optimizations (inline only)
		wp_register_style(
			'kpg-frontend-optimizations-style',
			false,
			[],
			self::VERSION
		);
	}

	/**
	 * Register widget scripts
	 */
	public function register_scripts() {
		// Swiper
		wp_register_script(
			'swiper',
			'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
			[],
			'11.0.0',
			true
		);

		// Blog Sorting
		wp_register_script(
			'kpg-blog-sorting-script',
			plugins_url( 'assets/js/blog-sorting.js', __FILE__ ),
			[ 'jquery' ],
			self::VERSION,
			true
		);

		// Pagination
		wp_register_script(
			'kpg-pagination-script',
			plugins_url( 'assets/js/pagination.js', __FILE__ ),
			[ 'jquery' ],
			self::VERSION,
			true
		);

		// Team Slider
		wp_register_script(
			'kpg-team-slider-script',
			plugins_url( 'assets/js/team-slider.js', __FILE__ ),
			[ 'jquery', 'swiper' ],
			self::VERSION,
			true
		);

		// Table of Contents
		wp_register_script(
			'kpg-toc-script',
			plugins_url( 'assets/js/table-of-contents.js', __FILE__ ),
			[ 'jquery' ],
			self::VERSION,
			true
		);

		// Comments
		wp_register_script(
			'kpg-comments-script',
			plugins_url( 'assets/js/comments.js', __FILE__ ),
			[ 'jquery' ],
			self::VERSION,
			true
		);

		// Post Meta Bar
		wp_register_script(
			'kpg-post-meta-bar-script',
			plugins_url( 'assets/js/post-meta-bar.js', __FILE__ ),
			[ 'jquery' ],
			self::VERSION,
			true
		);

		// Blog Content
		wp_register_script(
			'kpg-blog-content-script',
			plugins_url( 'assets/js/blog-content.js', __FILE__ ),
			[ 'jquery' ],
			self::VERSION,
			true
		);

		// Blog Search - Fill search field from URL
		wp_register_script(
			'kpg-blog-search-script',
			plugins_url( 'assets/js/blog-search.js', __FILE__ ),
			[],
			self::VERSION,
			true
		);

		// Related Posts Desktop (single post - "Zobacz inne artykuły")
		wp_register_script(
			'kpg-related-posts-desktop-script',
			plugins_url( 'assets/js/related-posts-desktop.js', __FILE__ ),
			[ 'jquery' ],
			self::VERSION,
			true
		);

		// Mobile offcanvas menu: close on same-page anchor navigation.
		wp_register_script(
			'kpg-mobile-menu-anchor-close-script',
			plugins_url( 'assets/js/mobile-menu-anchor-close.js', __FILE__ ),
			[],
			self::VERSION,
			true
		);

		// Floating UI (dropdown positioning)
		wp_register_script(
			'floating-ui-dom',
			plugins_url( 'assets/vendor/floating-ui.dom.umd.min.js', __FILE__ ),
			[],
			self::VERSION,
			true
		);

		// KPG Menu
		wp_register_script(
			'kpg-menu-script',
			plugins_url( 'assets/js/menu.js', __FILE__ ),
			[],
			self::VERSION,
			true
		);

		// Elementor nested accordion fallback
		wp_register_script(
			'kpg-elementor-nested-accordion-fallback-script',
			plugins_url( 'assets/js/elementor-nested-accordion-fallback.js', __FILE__ ),
			[],
			self::VERSION,
			true
		);
	}

	/**
	 * Enqueue global styles
	 */
	public function enqueue_global_styles() {
		wp_enqueue_style( 'kpg-frontend-optimizations-style' );
		$inline_css = trim( $this->get_shared_widget_tokens_css() . "\n" . $this->get_frontend_font_optimization_css() );
		if ( '' !== $inline_css ) {
			wp_add_inline_style( 'kpg-frontend-optimizations-style', $inline_css );
		}
		wp_enqueue_script( 'kpg-elementor-nested-accordion-fallback-script' );
	}

	/**
	 * Inline shared widget tokens so widget styles can avoid extra @import requests.
	 */
	private function get_shared_widget_tokens_css() {
		$file_path = __DIR__ . '/assets/css/_kpg-colors.css';
		if ( ! is_readable( $file_path ) ) {
			return '';
		}

		$css = file_get_contents( $file_path );
		return is_string( $css ) ? trim( $css ) : '';
	}

	/**
	 * Detect blog listing-like views that benefit from leaner font declarations.
	 */
	private function is_blog_listing_view() {
		if ( is_admin() ) {
			return false;
		}

		if ( is_home() || is_search() || is_category() || is_tag() || is_author() || is_date() ) {
			return true;
		}

		if ( is_page( 'blog' ) ) {
			return true;
		}

		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( (string) $_SERVER['REQUEST_URI'] ) : '';
		return '' !== $request_uri && (bool) preg_match( '#(?:^|/)blog(?:/|$|\?)#i', $request_uri );
	}

	/**
	 * Remove legacy Font Awesome 4 shim assets on frontend.
	 * Keep regular Font Awesome files so current design stays intact.
	 */
	public function dequeue_frontend_legacy_icon_assets() {
		if ( is_admin() ) {
			return;
		}

		wp_dequeue_style( 'font-awesome-4-shim' );
		wp_deregister_style( 'font-awesome-4-shim' );

		wp_dequeue_script( 'font-awesome-4-shim' );
		wp_deregister_script( 'font-awesome-4-shim' );
	}

	/**
	 * Use swap for Elementor Pro custom fonts to avoid text invisibility on mobile.
	 */
	public function force_custom_font_display_swap( $display, $font_family, $data ) {
		return 'swap';
	}

	/**
	 * Print primary font-face rules after Elementor custom-font CSS.
	 */
	public function print_primary_font_faces() {
		if ( is_admin() ) {
			return;
		}

		$css = trim( $this->get_primary_font_face_css() . "\n" . $this->get_primary_font_override_css() );
		if ( '' === $css ) {
			return;
		}

		printf( "<style id=\"kpg-primary-fonts\">\n%s\n</style>\n", $css );
	}

	/**
	 * Inline font-face hints for above-the-fold assets.
	 *
	 * Elementor stores some legacy settings as Nohemi families, so the primary
	 * font-face block exposes those aliases while rendering the new Zalando font.
	 */
	private function get_frontend_font_optimization_css() {
		$elementor_font_base = content_url( 'plugins/elementor/assets/lib/font-awesome/webfonts' );

	return "
	:root {
		--kpg-font-primary: 'Zalando Sans SemiExpanded', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
		--e-global-typography-primary-font-family: 'Zalando Sans SemiExpanded';
		--e-global-typography-primary-font-weight: 300;
		--e-global-typography-text-font-family: 'Zalando Sans SemiExpanded';
		--e-global-typography-text-font-weight: 300;
		--e-global-typography-accent-font-family: 'Zalando Sans SemiExpanded';
		--e-global-typography-accent-font-weight: 300;
	}
	body[class*='elementor-kit-'] {
		--kpg-font-primary: 'Zalando Sans SemiExpanded', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
		--e-global-typography-primary-font-family: 'Zalando Sans SemiExpanded';
		--e-global-typography-primary-font-weight: 300;
		--e-global-typography-text-font-family: 'Zalando Sans SemiExpanded';
		--e-global-typography-text-font-weight: 300;
		--e-global-typography-accent-font-family: 'Zalando Sans SemiExpanded';
		--e-global-typography-accent-font-weight: 300;
	}
	body {
		font-family: var(--kpg-font-primary);
		font-weight: 300;
		-webkit-font-smoothing: antialiased;
		-moz-osx-font-smoothing: grayscale;
	}
	button,
	input,
	select,
	textarea {
		font-family: var(--kpg-font-primary);
	}
	@font-face {
		font-family: 'DM Mono';
		font-style: normal;
		font-weight: 300;
		font-display: swap;
		src: url('https://fonts.gstatic.com/s/dmmono/v16/aFTR7PB1QTsUX8KYvrGyEY2tbZX9.woff2') format('woff2');
		unicode-range: U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, U+2C60-2C7F, U+A720-A7FF;
	}
	@font-face {
		font-family: 'DM Mono';
		font-style: normal;
		font-weight: 300;
		font-display: swap;
		src: url('https://fonts.gstatic.com/s/dmmono/v16/aFTR7PB1QTsUX8KYvrGyEYOtbQ.woff2') format('woff2');
		unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
	}
	@font-face {
		font-family: 'DM Mono';
		font-style: normal;
		font-weight: 400;
		font-display: swap;
		src: url('https://fonts.gstatic.com/s/dmmono/v16/aFTU7PB1QTsUX8KYthSQBLyM.woff2') format('woff2');
		unicode-range: U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, U+2C60-2C7F, U+A720-A7FF;
	}
	@font-face {
		font-family: 'DM Mono';
		font-style: normal;
		font-weight: 400;
		font-display: swap;
		src: url('https://fonts.gstatic.com/s/dmmono/v16/aFTU7PB1QTsUX8KYthqQBA.woff2') format('woff2');
		unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
	}
	@font-face {
		font-family: 'DM Mono';
		font-style: normal;
		font-weight: 500;
		font-display: swap;
		src: url('https://fonts.gstatic.com/s/dmmono/v16/aFTR7PB1QTsUX8KYvumzEY2tbZX9.woff2') format('woff2');
		unicode-range: U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, U+2C60-2C7F, U+A720-A7FF;
	}
	@font-face {
		font-family: 'DM Mono';
		font-style: normal;
		font-weight: 500;
		font-display: swap;
		src: url('https://fonts.gstatic.com/s/dmmono/v16/aFTR7PB1QTsUX8KYvumzEYOtbQ.woff2') format('woff2');
		unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
	}
	body .kpgio-lista .tresc,
	body .kpgio-lista li > span.tresc {
		font-weight: 300 !important;
	}
			@font-face {
		font-family: 'Font Awesome 5 Free';
		font-style: normal;
		font-weight: 400;
	font-display: swap;
	src: url('{$elementor_font_base}/fa-regular-400.woff2') format('woff2'),
		url('{$elementor_font_base}/fa-regular-400.woff') format('woff');
}
@font-face {
	font-family: 'Font Awesome 5 Free';
	font-style: normal;
	font-weight: 900;
	font-display: swap;
	src: url('{$elementor_font_base}/fa-solid-900.woff2') format('woff2'),
		url('{$elementor_font_base}/fa-solid-900.woff') format('woff');
}
@font-face {
	font-family: 'Font Awesome 5 Brands';
	font-style: normal;
	font-weight: 400;
	font-display: swap;
	src: url('{$elementor_font_base}/fa-brands-400.woff2') format('woff2'),
		url('{$elementor_font_base}/fa-brands-400.woff') format('woff');
}";
	}

	/**
	 * Build primary font-face rules and legacy Nohemi aliases.
	 */
	private function get_primary_font_face_css() {
		$src      = "url('https://fonts.gstatic.com/s/zalandosanssemiexpanded/v3/6qLhKYcHuh3msE9OaXROVVclRRa-ClZSEipa2hrEzR2jhk_n3T6ACkCFEnP9.ttf') format('truetype')";
		$weights  = $this->is_blog_listing_view() ? [ 300 ] : [ 100, 200, 300, 400, 500, 600, 700, 800, 900 ];
		$families = [
			'Zalando Sans SemiExpanded',
			'Nohemi',
			'Nohemi Light',
			'Nohemi Thin',
			'Nohemi ExtraLight',
			'Nohemi Regular',
			'Nohemi Medium',
			'Nohemi SemiBold',
			'Nohemi Bold',
			'Nohemi ExtraBold',
			'Nohemi Black',
		];

		$rules = [];
		foreach ( $families as $family ) {
			foreach ( $weights as $weight ) {
				$rules[] = $this->build_font_face_rule( $family, $weight, $src );
			}
		}

		return implode( "\n", $rules );
	}

	/**
	 * Print after theme styles so the base document font survives Hello reset CSS.
	 */
	private function get_primary_font_override_css() {
		return "
html body {
	font-family: var(--kpg-font-primary);
	font-weight: 300;
}
html body button,
html body input,
html body select,
html body textarea {
	font-family: var(--kpg-font-primary);
}";
	}

	/**
	 * Build one font-face rule.
	 */
	private function build_font_face_rule( $family, $weight, $src ) {
		return sprintf(
			"@font-face {\n\tfont-family: '%s';\n\tfont-style: normal;\n\tfont-weight: %s;\n\tfont-display: swap;\n\tsrc: %s;\n}",
			esc_attr( $family ),
			esc_attr( (string) $weight ),
			$src
		);
	}

	/**
	 * Add safe frontend resource hints for third-party assets used above the fold.
	 */
	public function add_frontend_resource_hints( $urls, $relation_type ) {
		if ( is_admin() ) {
			return $urls;
		}

		$needs_jsdelivr = wp_style_is( 'swiper', 'enqueued' ) || wp_script_is( 'swiper', 'enqueued' );

		if ( 'preconnect' === $relation_type ) {
			$urls[] = [
				'href'        => 'https://fonts.gstatic.com',
				'crossorigin' => 'anonymous',
			];
			if ( $needs_jsdelivr ) {
				$urls[] = [
					'href'        => 'https://cdn.jsdelivr.net',
					'crossorigin' => '',
				];
			}
		}

		return array_unique( $urls, SORT_REGULAR );
	}

	/**
	 * Enqueue blog structure script on single posts
	 */
	public function enqueue_blog_structure_script() {
		if ( is_single() && get_post_type() === 'post' ) {
			wp_localize_script(
				'kpg-related-posts-desktop-script',
				'kpgRelatedPostsDesktopData',
				[
					'restUrl' => esc_url_raw( rest_url( 'wp/v2/' ) ),
					'postId'  => (int) get_the_ID(),
				]
			);

			wp_enqueue_script( 'kpg-related-posts-desktop-script' );
			wp_enqueue_script( 'kpg-blog-structure-script' );
			wp_enqueue_style( 'kpg-blog-semantic-style' );
		}
		
		// Enqueue search script on blog pages (archive, search results)
		if ( is_home() || is_archive() || is_search() || ( get_option( 'page_for_posts' ) && is_page( get_option( 'page_for_posts' ) ) ) ) {
			wp_enqueue_script( 'kpg-blog-search-script' );
		}
	}

	/**
	 * Register widgets
	 */
	public function register_widgets( $widgets_manager ) {
		// Widget 1: Blog Sorting ✅ READY
		if ( file_exists( __DIR__ . '/widgets/blog-sorting.php' ) ) {
			require_once( __DIR__ . '/widgets/blog-sorting.php' );
			$widgets_manager->register( new \KPG_Elementor_Blog_Sorting_Widget() );
		}

		// Widget 2: Breadcrumbs ✅ READY
		if ( file_exists( __DIR__ . '/widgets/breadcrumbs.php' ) ) {
			require_once( __DIR__ . '/widgets/breadcrumbs.php' );
			$widgets_manager->register( new \KPG_Elementor_Breadcrumbs_Widget() );
		}

		// Widget 3: Blog Archive Desktop ✅ READY (3 column grid)
		if ( file_exists( __DIR__ . '/widgets/blog-archive-desktop.php' ) ) {
			require_once( __DIR__ . '/widgets/blog-archive-desktop.php' );
			$widgets_manager->register( new \KPG_Elementor_Blog_Archive_Desktop_Widget() );
		}

		// Widget 4: Blog Archive Mobile ✅ READY (vertical list)
		if ( file_exists( __DIR__ . '/widgets/blog-archive-mobile.php' ) ) {
			require_once( __DIR__ . '/widgets/blog-archive-mobile.php' );
			$widgets_manager->register( new \KPG_Elementor_Blog_Archive_Mobile_Widget() );
		}

		// Widget 3+4+5: Blog Archive (combined - OLD, keep for compatibility)
		if ( file_exists( __DIR__ . '/widgets/blog-archive.php' ) ) {
			require_once( __DIR__ . '/widgets/blog-archive.php' );
			$widgets_manager->register( new \KPG_Elementor_Blog_Archive_Widget() );
		}

		// NOTE: Pagination widget exists but is included in Blog Archive
		// Can be used standalone if needed
		if ( file_exists( __DIR__ . '/widgets/pagination.php' ) ) {
			require_once( __DIR__ . '/widgets/pagination.php' );
			$widgets_manager->register( new \KPG_Elementor_Pagination_Widget() );
		}

		// Widget 6: Team Slider ⏳ TODO
		if ( file_exists( __DIR__ . '/widgets/team-slider.php' ) ) {
			require_once( __DIR__ . '/widgets/team-slider.php' );
			$widgets_manager->register( new \KPG_Elementor_Team_Slider_Widget() );
		}

		// Widget 7: Table of Contents ✅ READY
		if ( file_exists( __DIR__ . '/widgets/table-of-contents.php' ) ) {
			require_once( __DIR__ . '/widgets/table-of-contents.php' );
			$widgets_manager->register( new \KPG_Elementor_TOC_Widget() );
		}

		// NOTE: Widget 6 (Team Slider) and Widget 8 (Comments) are optional
		// Can be added later if needed

		// Widget 8: Comments ✅ READY
		if ( file_exists( __DIR__ . '/widgets/comments.php' ) ) {
			require_once( __DIR__ . '/widgets/comments.php' );
			$widgets_manager->register( new \KPG_Elementor_Comments_Widget() );
		}

		// Widget 9: Important Section ⏳ TODO
		if ( file_exists( __DIR__ . '/widgets/important-section.php' ) ) {
			require_once( __DIR__ . '/widgets/important-section.php' );
			$widgets_manager->register( new \KPG_Elementor_Important_Widget() );
		}

		// Widget 9: Important Section ✅ READY
		if ( file_exists( __DIR__ . '/widgets/important-section.php' ) ) {
			require_once( __DIR__ . '/widgets/important-section.php' );
			$widgets_manager->register( new \KPG_Elementor_Important_Widget() );
		}

		// Blog Content Widget
		if ( file_exists( __DIR__ . '/widgets/blog-content.php' ) ) {
			require_once( __DIR__ . '/widgets/blog-content.php' );
			$widgets_manager->register( new \KPG_Elementor_Blog_Content_Widget() );
		}

		// Widget 10: Articles From (O Autorze) ✅ READY
		if ( file_exists( __DIR__ . '/widgets/articles-from.php' ) ) {
			require_once( __DIR__ . '/widgets/articles-from.php' );
			$widgets_manager->register( new \KPG_Elementor_Articles_From_Widget() );
		}

		// Widget 11: Post Meta Bar ✅ READY
		if ( file_exists( __DIR__ . '/widgets/post-meta-bar.php' ) ) {
			require_once( __DIR__ . '/widgets/post-meta-bar.php' );
			$widgets_manager->register( new \KPG_Elementor_Post_Meta_Bar_Widget() );
		}

		// Widget 12: Blog Featured ✅ READY
		if ( file_exists( __DIR__ . '/widgets/blog-featured.php' ) ) {
			require_once( __DIR__ . '/widgets/blog-featured.php' );
			$widgets_manager->register( new \KPG_Elementor_Blog_Featured_Widget() );
		}

		// Bonus: O Nas ⏳ TODO
		if ( file_exists( __DIR__ . '/widgets/onas.php' ) ) {
			require_once( __DIR__ . '/widgets/onas.php' );
			$widgets_manager->register( new \KPG_Elementor_Onas_Widget() );
		}

		// Widget 13: Menu
		if ( file_exists( __DIR__ . '/widgets/menu.php' ) ) {
			require_once( __DIR__ . '/widgets/menu.php' );
			$widgets_manager->register( new \KPG_Elementor_Menu_Widget() );
		}
	}

	/**
	 * Enqueue admin scripts for user profile avatar upload
	 */
	public function enqueue_admin_scripts( $hook ) {
		// Only load on user profile/edit user pages
		if ( $hook !== 'user-edit.php' && $hook !== 'profile.php' ) {
			return;
		}

		// Enqueue WordPress media uploader
		wp_enqueue_media();

		// Enqueue custom script
		wp_enqueue_script(
			'kpg-user-profile-avatar',
			plugins_url( 'assets/js/user-profile-avatar.js', __FILE__ ),
			[ 'jquery' ],
			self::VERSION,
			true
		);
	}

	/**
	 * Remove frontend-only custom scripts that break Elementor/Rank Math in admin.
	 */
	public function dequeue_problematic_admin_scripts() {
		global $wp_scripts;

		if ( ! $wp_scripts instanceof \WP_Scripts ) {
			return;
		}

		foreach ( (array) $wp_scripts->queue as $handle ) {
			if ( empty( $wp_scripts->registered[ $handle ] ) ) {
				continue;
			}

			$script = $wp_scripts->registered[ $handle ];
			$src    = (string) $script->src;

			if ( strpos( $src, 'elementor-accordion-fix.js' ) === false ) {
				continue;
			}

			wp_dequeue_script( $handle );
			wp_deregister_script( $handle );
		}
	}
}

// Initialize the plugin
KPG_Elementor_Widgets::instance();
