<?php
/**
 * KPG Breadcrumbs Widget
 *
 * @package KPG_Elementor_Widgets
 * 
 * Based on prompts:
 * - Prompt #51 (line 13678): Blog archive breadcrumbs (home / blog-test)
 * - Prompt #52 (line 14997): Author archive breadcrumbs (home / Author Name)
 * 
 * Requirements:
 * - Single post: home / post-title
 * - Blog archive: home / blog-title (from posts page setting)
 * - Author archive: home / First-Name Last-Name (NOT display_name)
 * - Category: home / category-name
 * - Page: home / page-title
 * - Separator: "/" between items
 * - Home always as link
 * - Last element (current) without link
 * - Responsive (mobile + desktop)
 * - Lowercase text-transform
 * - Font: DM Mono
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

/**
 * KPG Breadcrumbs Widget
 */
class KPG_Elementor_Breadcrumbs_Widget extends Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'kpg-breadcrumbs';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return esc_html__( 'KPG Breadcrumbs', 'kpg-elementor-widgets' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'eicon-navigation-horizontal';
	}

	/**
	 * Get widget categories
	 */
	public function get_categories() {
		return [ 'kpg-widgets' ];
	}

	/**
	 * Get widget keywords
	 */
	public function get_keywords() {
		return [ 'breadcrumbs', 'navigation', 'path', 'kpg' ];
	}

	/**
	 * Get style dependencies
	 */
	public function get_style_depends() {
		return [ 'kpg-breadcrumbs-style' ];
	}

	/**
	 * Register widget controls
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'kpg-elementor-widgets' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'home_text',
			[
				'label' => esc_html__( 'Home Text', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'home',
				'label_block' => true,
			]
		);

		$this->add_control(
			'separator',
			[
				'label' => esc_html__( 'Separator', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::TEXT,
				'default' => '/',
				'label_block' => false,
			]
		);

		$this->add_control(
			'show_home',
			[
				'label' => esc_html__( 'Show Home', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'kpg-elementor-widgets' ),
				'label_off' => esc_html__( 'No', 'kpg-elementor-widgets' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Get breadcrumb items
	 */
	protected function get_breadcrumb_items() {
		$items = [];

		// Always start with home (if enabled in settings)
		$items[] = [
			'label' => $this->get_settings_for_display()['home_text'],
			'url'   => home_url( '/' ),
			'type'  => 'home',
		];

		// Single post
		if ( is_single() ) {
			$items[] = [
				'label' => get_the_title(),
				'url'   => '',
				'type'  => 'current',
			];
		}
		// Category archive
		elseif ( is_category() ) {
			$cat = get_queried_object();
			if ( $cat && isset( $cat->name ) ) {
				$items[] = [
					'label' => $cat->name,
					'url'   => '',
					'type'  => 'current',
				];
			}
		}
		// Author archive - IMPORTANT: Use first_name + last_name (Prompt #52)
		elseif ( is_author() ) {
			$author = get_queried_object();
			if ( $author && isset( $author->ID ) ) {
				// Get first name and last name (NOT display_name)
				$first_name = get_the_author_meta( 'first_name', $author->ID );
				$last_name = get_the_author_meta( 'last_name', $author->ID );
				
				// Combine first and last name
				$full_name = trim( $first_name . ' ' . $last_name );
				
				// Fallback to display_name if both are empty
				if ( empty( $full_name ) ) {
					$full_name = $author->display_name;
				}
				
				$items[] = [
					'label' => $full_name,
					'url'   => '',
					'type'  => 'current',
				];
			}
		}
		// Blog archive or posts page - IMPORTANT: Get page title (Prompt #51)
		elseif ( is_home() || is_archive() ) {
			$archive_label = '';
			$queried_object = get_queried_object();
			
			// Check if it's a page set as posts page
			if ( is_home() && ! is_front_page() ) {
				$posts_page_id = get_option( 'page_for_posts' );
				if ( $posts_page_id ) {
					$archive_label = get_the_title( $posts_page_id );
				}
			}
			
			// If it's a page object, get its title
			if ( empty( $archive_label ) && is_a( $queried_object, 'WP_Post' ) ) {
				$archive_label = get_the_title( $queried_object->ID );
			}
			
			// If no label yet, try post type archive title
			if ( empty( $archive_label ) ) {
				$archive_label = post_type_archive_title( '', false );
			}
			
			// If still no label, try to get current page title
			if ( empty( $archive_label ) ) {
				$archive_label = get_the_title();
			}
			
			// Fallback to "Blog" if still empty
			if ( empty( $archive_label ) ) {
				$archive_label = __( 'Blog', 'kpg-elementor-widgets' );
			}
			
			$items[] = [
				'label' => $archive_label,
				'url'   => '',
				'type'  => 'current',
			];
		}
		// Regular page
		elseif ( is_page() ) {
			// Check if this page is set as posts page
			$posts_page_id = get_option( 'page_for_posts' );
			$current_page_id = get_queried_object_id();
			
			if ( $posts_page_id && $current_page_id == $posts_page_id ) {
				// This is the posts page, show its title
				$items[] = [
					'label' => get_the_title( $current_page_id ),
					'url'   => '',
					'type'  => 'current',
				];
			} else {
				// Regular page, show page title
				$items[] = [
					'label' => get_the_title(),
					'url'   => '',
					'type'  => 'current',
				];
			}
		}

		return $items;
	}

	/**
	 * Render widget output on the frontend
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$items = $this->get_breadcrumb_items();

		if ( empty( $items ) ) {
			return;
		}

		// Remove home if disabled
		if ( $settings['show_home'] !== 'yes' && isset( $items[0] ) && $items[0]['type'] === 'home' ) {
			array_shift( $items );
		}

		if ( empty( $items ) ) {
			return;
		}

		?>
		<nav class="kpg-breadcrumbs" aria-label="<?php esc_attr_e( 'breadcrumbs', 'kpg-elementor-widgets' ); ?>">
			<div class="kpg-breadcrumbs-inner">
				<?php foreach ( $items as $index => $item ) : ?>
					<?php if ( $index > 0 ) : ?>
						<span class="kpg-breadcrumbs-separator"><?php echo esc_html( $settings['separator'] ); ?></span>
					<?php endif; ?>
					
					<?php if ( ! empty( $item['url'] ) && $item['type'] !== 'current' ) : ?>
						<a href="<?php echo esc_url( $item['url'] ); ?>" 
						   class="kpg-breadcrumbs-item kpg-breadcrumbs-item--type-<?php echo esc_attr( $item['type'] ); ?>">
							<?php echo esc_html( $item['label'] ); ?>
						</a>
					<?php else : ?>
						<span class="kpg-breadcrumbs-item kpg-breadcrumbs-item--current">
							<?php echo esc_html( $item['label'] ); ?>
						</span>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</nav>
		<?php
	}

	/**
	 * Render widget output in the editor
	 */
	protected function content_template() {
		?>
		<#
		var homeText = settings.home_text || 'home';
		var separator = settings.separator || '/';
		var showHome = settings.show_home === 'yes';
		#>
		<nav class="kpg-breadcrumbs" aria-label="breadcrumbs">
			<div class="kpg-breadcrumbs-inner">
				<# if (showHome) { #>
					<a href="#" class="kpg-breadcrumbs-item kpg-breadcrumbs-item--type-home">
						{{{ homeText }}}
					</a>
					<span class="kpg-breadcrumbs-separator">{{{ separator }}}</span>
				<# } #>
				<span class="kpg-breadcrumbs-item kpg-breadcrumbs-item--current">
					Current Page
				</span>
			</div>
		</nav>
		<?php
	}
}


