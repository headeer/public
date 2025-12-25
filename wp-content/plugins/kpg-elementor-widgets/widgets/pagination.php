<?php
/**
 * KPG Pagination Widget
 *
 * @package KPG_Elementor_Widgets
 * 
 * Based on prompts #18-27 (12 prompts about pagination!)
 * 
 * Design spec from prompts:
 * - Format: 01 02 03 ... current ... max
 * - Arrow SVG with two paths (40x32px viewBox)
 * - Arrow container: 60px x 32px, border-radius: 8px, bg: #E3EBEC
 * - Active: #404848, Inactive: #a3afb0
 * - Font: DM Mono, 16px (mobile), uppercase
 * - Line-height: 160% (25.6px)
 * - Desktop separator: width: 1240px, height: 0.5px, bg: #A3AFB0
 * - MUST be dynamic - show current page even if > 3
 * 
 * Mobile base: 375px
 * Desktop base: 1696px
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

/**
 * KPG Pagination Widget
 */
class KPG_Elementor_Pagination_Widget extends Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'kpg-pagination';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return esc_html__( 'KPG Pagination', 'kpg-elementor-widgets' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'eicon-post-navigation';
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
		return [ 'pagination', 'paginacja', 'pages', 'navigation', 'kpg' ];
	}

	/**
	 * Get style dependencies
	 */
	public function get_style_depends() {
		return [ 'kpg-pagination-style' ];
	}

	/**
	 * Get script dependencies
	 */
	public function get_script_depends() {
		return [ 'kpg-pagination-script' ];
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
			'show_separator',
			[
				'label' => esc_html__( 'Show Separator (Desktop)', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'kpg-elementor-widgets' ),
				'label_off' => esc_html__( 'No', 'kpg-elementor-widgets' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'description' => esc_html__( 'Show separator line above pagination on desktop', 'kpg-elementor-widgets' ),
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Get pagination data
	 */
	protected function get_pagination_data() {
		global $wp_query;

		// Get current page
		$current_page = max( 1, get_query_var( 'paged' ) );
		if ( $current_page === 0 ) {
			$current_page = 1;
		}

		// Get max pages
		$max_pages = $wp_query->max_num_pages;
		if ( $max_pages === 0 ) {
			$max_pages = 1;
		}

		return [
			'current' => $current_page,
			'max' => $max_pages,
		];
	}

	/**
	 * Render page numbers
	 * Logic: Always show 01 02 03, then current (if > 3), then max
	 */
	protected function render_page_numbers( $current, $max ) {
		$pages = [];

		// Always show first 3 pages
		for ( $i = 1; $i <= min( 3, $max ); $i++ ) {
			$pages[] = [
				'number' => $i,
				'is_current' => ( $i === $current ),
				'is_separator' => false,
			];
		}

		// If we have more than 3 pages
		if ( $max > 3 ) {
			// Add separator
			$pages[] = [
				'is_separator' => true,
			];

			// If current page is beyond first 3 and not the last, show it
			if ( $current > 3 && $current < $max ) {
				$pages[] = [
					'number' => $current,
					'is_current' => true,
					'is_separator' => false,
				];

				// If current is not adjacent to max, add another separator
				if ( $current < ( $max - 1 ) ) {
					$pages[] = [
						'is_separator' => true,
					];
				}
			}

			// Always show max page
			$pages[] = [
				'number' => $max,
				'is_current' => ( $current === $max ),
				'is_separator' => false,
			];
		}

		return $pages;
	}

	/**
	 * Render widget output on the frontend
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$data = $this->get_pagination_data();

		// Don't show pagination if only 1 page
		if ( $data['max'] <= 1 ) {
			return;
		}

		$pages = $this->render_page_numbers( $data['current'], $data['max'] );
		?>
		
		<?php if ( $settings['show_separator'] === 'yes' ) : ?>
			<!-- Separator line (desktop only) -->
			<div class="kpg-blog-separator"></div>
		<?php endif; ?>
		
		<div class="kpg-blog-pagination">
			<div class="kpg-blog-pagination-numbers">
				<?php foreach ( $pages as $page ) : ?>
					<?php if ( isset( $page['is_separator'] ) && $page['is_separator'] ) : ?>
						<div class="kpg-blog-pagination-separator"></div>
					<?php else : ?>
						<span class="kpg-blog-pagination-item <?php echo $page['is_current'] ? 'active' : 'inactive'; ?>" 
							  data-page="<?php echo esc_attr( $page['number'] ); ?>">
							<?php echo str_pad( $page['number'], 2, '0', STR_PAD_LEFT ); ?>
						</span>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			
			<div class="kpg-blog-pagination-arrow" data-direction="next">
				<svg xmlns="http://www.w3.org/2000/svg" width="40" height="32" viewBox="0 0 40 32" fill="none">
					<path d="M20 24L28 16L20 8" stroke="#252B2B" stroke-width="1.33333" stroke-linecap="square"/>
					<path d="M12 24L20 16L12 8" stroke="#252B2B" stroke-width="1.33333" stroke-linecap="square"/>
				</svg>
			</div>
		</div>
		<?php
	}

	/**
	 * Render widget output in the editor
	 */
	protected function content_template() {
		?>
		<#
		var showSeparator = settings.show_separator === 'yes';
		#>
		
		<# if (showSeparator) { #>
			<div class="kpg-blog-separator"></div>
		<# } #>
		
		<div class="kpg-blog-pagination">
			<div class="kpg-blog-pagination-numbers">
				<span class="kpg-blog-pagination-item active" data-page="1">01</span>
				<span class="kpg-blog-pagination-item inactive" data-page="2">02</span>
				<span class="kpg-blog-pagination-item inactive" data-page="3">03</span>
				<div class="kpg-blog-pagination-separator"></div>
				<span class="kpg-blog-pagination-item inactive" data-page="10">10</span>
			</div>
			
			<div class="kpg-blog-pagination-arrow" data-direction="next">
				<svg xmlns="http://www.w3.org/2000/svg" width="40" height="32" viewBox="0 0 40 32" fill="none">
					<path d="M20 24L28 16L20 8" stroke="#252B2B" stroke-width="1.33333" stroke-linecap="square"/>
					<path d="M12 24L20 16L12 8" stroke="#252B2B" stroke-width="1.33333" stroke-linecap="square"/>
				</svg>
			</div>
		</div>
		<?php
	}
}


