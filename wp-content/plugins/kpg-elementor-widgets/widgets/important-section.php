<?php
/**
 * KPG Important Section Widget
 *
 * @package KPG_Elementor_Widgets
 * 
 * Highlight box dla ważnych informacji w artykule
 * Style dopasowane do KPG design (podobnie jak success box)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class KPG_Elementor_Important_Widget extends Widget_Base {

	public function get_name() {
		return 'kpg-important-section';
	}

	public function get_title() {
		return esc_html__( 'KPG Important Section', 'kpg-elementor-widgets' );
	}

	public function get_icon() {
		return 'eicon-alert';
	}

	public function get_categories() {
		return [ 'kpg-widgets' ];
	}

	public function get_keywords() {
		return [ 'important', 'ważne', 'highlight', 'notice', 'kpg' ];
	}

	public function get_style_depends() {
		return [ 'kpg-important-style' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'kpg-elementor-widgets' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'WAŻNE',
				'label_block' => true,
			]
		);

		$this->add_control(
			'content',
			[
				'label' => esc_html__( 'Content', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => 'Najważniejsze informacje z tego artykułu. Ta sekcja zwraca uwagę czytelnika na kluczowe punkty.',
			]
		);

		$this->add_control(
			'show_icon',
			[
				'label' => esc_html__( 'Show Icon', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'icon_text',
			[
				'label' => esc_html__( 'Icon Text', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::TEXT,
				'default' => '!',
				'condition' => [
					'show_icon' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="kpg-important-container">
			<div class="kpg-important-header">
				<?php if ( $settings['show_icon'] === 'yes' ) : ?>
					<div class="kpg-important-icon">
						<span class="kpg-important-icon-text"><?php echo esc_html( $settings['icon_text'] ); ?></span>
					</div>
				<?php endif; ?>
				<h3 class="kpg-important-title"><?php echo esc_html( $settings['title'] ); ?></h3>
			</div>
			<div class="kpg-important-content">
				<?php echo wp_kses_post( $settings['content'] ); ?>
			</div>
		</div>
		<?php
	}

	protected function content_template() {
		?>
		<#
		var title = settings.title || 'WAŻNE';
		var content = settings.content || 'Treść sekcji ważne...';
		var showIcon = settings.show_icon === 'yes';
		var iconText = settings.icon_text || '!';
		#>
		<div class="kpg-important-container">
			<div class="kpg-important-header">
				<# if (showIcon) { #>
					<div class="kpg-important-icon">
						<span class="kpg-important-icon-text">{{{ iconText }}}</span>
					</div>
				<# } #>
				<h3 class="kpg-important-title">{{{ title }}}</h3>
			</div>
			<div class="kpg-important-content">
				{{{ content }}}
			</div>
		</div>
		<?php
	}
}




