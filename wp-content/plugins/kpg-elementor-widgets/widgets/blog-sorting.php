<?php
/**
 * KPG Blog Sorting Widget
 *
 * @package KPG_Elementor_Widgets
 * 
 * Based on prompts:
 * - Prompt #4 (line 3983): margin-bottom 32px
 * - Prompt #13 (line 6278): Complete HTML structure and JavaScript
 * 
 * Design spec:
 * - Label: "SORTOWANIE:" (DM Mono, #6f7b7c)
 * - Button: "OD NAJNOWSZYCH" / "OD NAJSTARSZYCH" (DM Mono, #404848, font-weight: 500)
 * - Arrow SVG: 14x8px, stroke: #404848, stroke-width: 1.8
 * - Margin-bottom: 32px (8.5333vw mobile)
 * - Dropdown menu with accessibility (role="menu", aria-expanded)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

/**
 * KPG Blog Sorting Widget
 */
class KPG_Elementor_Blog_Sorting_Widget extends Widget_Base {

	/**
	 * Get widget name
	 */
	public function get_name() {
		return 'kpg-blog-sorting';
	}

	/**
	 * Get widget title
	 */
	public function get_title() {
		return esc_html__( 'KPG Blog Sorting', 'kpg-elementor-widgets' );
	}

	/**
	 * Get widget icon
	 */
	public function get_icon() {
		return 'eicon-sort-amount-desc';
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
		return [ 'sorting', 'sortowanie', 'blog', 'order', 'kpg' ];
	}

	/**
	 * Get style dependencies
	 */
	public function get_style_depends() {
		return [ 'kpg-blog-sorting-style' ];
	}

	/**
	 * Get script dependencies
	 */
	public function get_script_depends() {
		return [ 'kpg-blog-sorting-script' ];
	}

	/**
	 * Register widget controls
	 */
	protected function register_controls() {

		// ============================================
		// CONTENT SECTION
		// ============================================
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'kpg-elementor-widgets' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'label_text',
			[
				'label' => esc_html__( 'Label Text', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'SORTOWANIE:',
				'label_block' => true,
			]
		);

		$this->add_control(
			'option_newest',
			[
				'label' => esc_html__( 'Newest Text', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'OD NAJNOWSZYCH',
				'label_block' => true,
			]
		);

		$this->add_control(
			'option_oldest',
			[
				'label' => esc_html__( 'Oldest Text', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'OD NAJSTARSZYCH',
				'label_block' => true,
			]
		);

		$this->add_control(
			'default_sort',
			[
				'label' => esc_html__( 'Default Sort', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'newest',
				'options' => [
					'newest' => esc_html__( 'Newest First', 'kpg-elementor-widgets' ),
					'oldest' => esc_html__( 'Oldest First', 'kpg-elementor-widgets' ),
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		
		// Get current sort from URL
		$current_sort = isset( $_GET['sort'] ) ? sanitize_text_field( $_GET['sort'] ) : $settings['default_sort'];
		$selected_text = ( $current_sort === 'oldest' ) ? $settings['option_oldest'] : $settings['option_newest'];
		?>
		<div class="kpg_sorting-container">
			<div class="kpg_sorting-container-inner">
				<div class="kpg_sorting-label-wrapper">
					<span class="kpg_sorting-label"><?php echo esc_html( $settings['label_text'] ); ?></span>
				</div>
				<div class="kpg_sorting-dropdown" aria-expanded="false">
					<button class="kpg_sorting-button" type="button" aria-expanded="false" aria-haspopup="true">
						<span class="kpg_sorting-selected"><?php echo esc_html( $selected_text ); ?></span>
						<svg class="kpg_sorting-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="8" viewBox="0 0 14 8" fill="none" aria-hidden="true">
							<path d="M0.63623 0.636719L6.63623 6.63672L12.6362 0.636719" stroke="#404848" stroke-width="1.8"/>
						</svg>
					</button>
					<ul class="kpg_sorting-menu" role="menu" aria-label="<?php esc_attr_e( 'Sortowanie postów', 'kpg-elementor-widgets' ); ?>">
						<li role="none">
							<button class="kpg_sorting-option <?php echo ( $current_sort === 'newest' ) ? 'kpg_sorting-active' : ''; ?>" 
									type="button" 
									role="menuitem" 
									data-sort="newest">
								<?php echo esc_html( $settings['option_newest'] ); ?>
							</button>
						</li>
						<li role="none">
							<button class="kpg_sorting-option <?php echo ( $current_sort === 'oldest' ) ? 'kpg_sorting-active' : ''; ?>" 
									type="button" 
									role="menuitem" 
									data-sort="oldest">
								<?php echo esc_html( $settings['option_oldest'] ); ?>
							</button>
						</li>
					</ul>
				</div>
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
		var labelText = settings.label_text || 'SORTOWANIE:';
		var optionNewest = settings.option_newest || 'OD NAJNOWSZYCH';
		var optionOldest = settings.option_oldest || 'OD NAJSTARSZYCH';
		var defaultSort = settings.default_sort || 'newest';
		var selectedText = (defaultSort === 'oldest') ? optionOldest : optionNewest;
		#>
		<div class="kpg_sorting-container">
			<div class="kpg_sorting-container-inner">
				<div class="kpg_sorting-label-wrapper">
					<span class="kpg_sorting-label">{{{ labelText }}}</span>
				</div>
				<div class="kpg_sorting-dropdown" aria-expanded="false">
					<button class="kpg_sorting-button" type="button" aria-expanded="false" aria-haspopup="true">
						<span class="kpg_sorting-selected">{{{ selectedText }}}</span>
						<svg class="kpg_sorting-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="8" viewBox="0 0 14 8" fill="none" aria-hidden="true">
							<path d="M0.63623 0.636719L6.63623 6.63672L12.6362 0.636719" stroke="#404848" stroke-width="1.8"/>
						</svg>
					</button>
					<ul class="kpg_sorting-menu" role="menu">
						<li role="none">
							<button class="kpg_sorting-option kpg_sorting-active" type="button" role="menuitem" data-sort="newest">
								{{{ optionNewest }}}
							</button>
						</li>
						<li role="none">
							<button class="kpg_sorting-option" type="button" role="menuitem" data-sort="oldest">
								{{{ optionOldest }}}
							</button>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<?php
	}
}


