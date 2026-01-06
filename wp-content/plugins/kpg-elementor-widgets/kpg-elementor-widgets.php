<?php
/**
 * Plugin Name: KPG Elementor Widgets
 * Description: Custom Elementor widgets for KPG project - blog, team, navigation
 * Version: 1.0.0
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

	const VERSION = '1.0.5';
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
		
		// Load author permalink settings
		require_once( __DIR__ . '/includes/author-permalink-settings.php' );
		
		// Register widget styles and scripts
		add_action( 'elementor/frontend/after_register_styles', [ $this, 'register_styles' ] );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_scripts' ] );

		// Enqueue global styles (spis responsive)
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_global_styles' ] );
		
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
			self::VERSION
		);

		// Blog Archive Mobile
		wp_register_style(
			'kpg-blog-archive-mobile-style',
			plugins_url( 'assets/css/blog-archive-mobile.css', __FILE__ ),
			[],
			self::VERSION
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
			self::VERSION
		);

		// Blog Featured Mobile
		wp_register_style(
			'kpg-blog-featured-mobile-style',
			plugins_url( 'assets/css/blog-featured-mobile.css', __FILE__ ),
			[],
			self::VERSION
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
	}

	/**
	 * Enqueue global styles
	 */
	public function enqueue_global_styles() {
		wp_enqueue_style( 'kpg-spis-responsive-style' );
	}

	/**
	 * Enqueue blog structure script on single posts
	 */
	public function enqueue_blog_structure_script() {
		if ( is_single() && get_post_type() === 'post' ) {
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
}

// Initialize the plugin
KPG_Elementor_Widgets::instance();

