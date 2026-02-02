<?php
/**
 * KPG Table of Contents Widget
 * 
 * Auto-generates TOC from H2, H3 headings in post content
 * Features: smooth scroll, scroll spy, sticky on desktop
 * Style: KPG design (similar to Important Section)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class KPG_Elementor_TOC_Widget extends Widget_Base {

	public function get_name() {
		return 'kpg-table-of-contents';
	}

	public function get_title() {
		return esc_html__( 'KPG Table of Contents', 'kpg-elementor-widgets' );
	}

	public function get_icon() {
		return 'eicon-table-of-contents';
	}

	public function get_categories() {
		return [ 'kpg-widgets' ];
	}

	public function get_keywords() {
		return [ 'toc', 'table of contents', 'spis treści', 'navigation', 'kpg' ];
	}

	public function get_style_depends() {
		return [ 'kpg-toc-style' ];
	}

	public function get_script_depends() {
		return [ 'kpg-toc-script' ];
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
				'default' => 'SPIS TREŚCI',
				'label_block' => true,
			]
		);

		$this->add_control(
			'heading_levels',
			[
				'label' => esc_html__( 'Heading Levels', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'default' => [ 'h2', 'h3' ],
				'options' => [
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
				],
			]
		);

		$this->add_control(
			'show_numbers',
			[
				'label' => esc_html__( 'Show Numbers', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'sticky_desktop',
			[
				'label' => esc_html__( 'Sticky on Desktop', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'sticky_offset',
			[
				'label' => esc_html__( 'Sticky Offset (px)', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 40,
				'condition' => [
					'sticky_desktop' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Extract headings from post content
	 */
	protected function extract_headings() {
		global $post;
		
		if ( ! $post ) {
			return [];
		}

		$content = apply_filters( 'the_content', $post->post_content );
		$settings = $this->get_settings_for_display();
		$levels = $settings['heading_levels'];
		
		if ( empty( $levels ) ) {
			$levels = [ 'h2', 'h3' ];
		}

		// Build regex pattern for selected heading levels
		$level_pattern = implode( '|', array_map( 'preg_quote', $levels ) );
		$pattern = '/<(' . $level_pattern . ')[^>]*>(.*?)<\/\1>/is';
		
		preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER );

		$headings = [];
		$counter = 1;

		foreach ( $matches as $match ) {
			$level = $match[1];
			$text = strip_tags( $match[2] );
			$id = 'toc-' . sanitize_title( $text );
			
			$headings[] = [
				'level' => $level,
				'text' => $text,
				'id' => $id,
				'number' => $counter++,
			];
		}

		return $headings;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$headings = $this->extract_headings();

		if ( empty( $headings ) ) {
			if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
				echo '<p style="padding: 20px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px;">' . 
					 esc_html__( 'Spis treści: Brak nagłówków w treści posta.', 'kpg-elementor-widgets' ) . 
					 '</p>';
			}
			return;
		}

		$sticky_class = ( $settings['sticky_desktop'] === 'yes' ) ? 'kpg-toc-sticky' : '';
		$sticky_offset = isset( $settings['sticky_offset'] ) ? intval( $settings['sticky_offset'] ) : 40;
		?>
		<nav class="kpg-toc-container <?php echo esc_attr( $sticky_class ); ?>" 
			 data-sticky-offset="<?php echo esc_attr( $sticky_offset ); ?>"
			 aria-label="<?php esc_attr_e( 'Spis treści', 'kpg-elementor-widgets' ); ?>">
			<div class="kpg-toc-header">
				<div class="kpg-toc-title"><?php echo esc_html( $settings['title'] ); ?></div>
			</div>
			<div class="kpg-toc-nav">
				<ul class="kpg-toc-list">
					<?php foreach ( $headings as $heading ) : ?>
						<li class="kpg-toc-item kpg-toc-item--<?php echo esc_attr( $heading['level'] ); ?>">
							<a href="#<?php echo esc_attr( $heading['id'] ); ?>" 
							   class="kpg-toc-link" 
							   data-target="<?php echo esc_attr( $heading['id'] ); ?>">
								<?php if ( $settings['show_numbers'] === 'yes' ) : ?>
									<span class="kpg-toc-number">0.<?php echo $heading['number']; ?></span>
								<?php endif; ?>
								<span class="kpg-toc-text"><?php echo esc_html( $heading['text'] ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</nav>
		
		<!-- Add IDs to actual headings in content -->
		<script>
		jQuery(document).ready(function($) {
			var headings = <?php echo json_encode( $headings ); ?>;
			
			// Try multiple selectors for content area
			var $contentAreas = $('.entry-content, .kpg-blog-content, .post-content, article, main, .elementor-widget-theme-post-content');
			
			if ($contentAreas.length === 0) {
				// Fallback: search entire body
				$contentAreas = $('body');
			}
			
			// Add IDs to headings
			$contentAreas.each(function() {
				var $content = $(this);
				
				headings.forEach(function(h) {
					// Find all headings of this level
					var $allHeadings = $content.find(h.level);
					
					$allHeadings.each(function() {
						var $heading = $(this);
						var headingText = $heading.text().trim();
						
						// Match by text content
						if (headingText === h.text || headingText.indexOf(h.text) !== -1) {
							if (!$heading.attr('id')) {
								$heading.attr('id', h.id);
							}
						}
					});
				});
			});
		});
		</script>
		<?php
	}

	protected function content_template() {
		?>
		<#
		var title = settings.title || 'SPIS TREŚCI';
		var showNumbers = settings.show_numbers === 'yes';
		var stickyClass = settings.sticky_desktop === 'yes' ? 'kpg-toc-sticky' : '';
		#>
		<nav class="kpg-toc-container {{{ stickyClass }}}" aria-label="Spis treści">
			<div class="kpg-toc-header">
				<div class="kpg-toc-title">{{{ title }}}</div>
			</div>
			<div class="kpg-toc-nav">
				<ul class="kpg-toc-list">
					<li class="kpg-toc-item kpg-toc-item--h2">
						<a href="#section-1" class="kpg-toc-link">
							<# if (showNumbers) { #>
								<span class="kpg-toc-number">0.1</span>
							<# } #>
							<span class="kpg-toc-text">O czym jest ustawa o ochronie sygnalistów?</span>
						</a>
					</li>
					<li class="kpg-toc-item kpg-toc-item--h2">
						<a href="#section-2" class="kpg-toc-link">
							<# if (showNumbers) { #>
								<span class="kpg-toc-number">0.2</span>
							<# } #>
							<span class="kpg-toc-text">Co może być zgłaszane w szkole?</span>
						</a>
					</li>
					<li class="kpg-toc-item kpg-toc-item--h2">
						<a href="#section-3" class="kpg-toc-link">
							<# if (showNumbers) { #>
								<span class="kpg-toc-number">0.3</span>
							<# } #>
							<span class="kpg-toc-text">Kim jest sygnalista?</span>
						</a>
					</li>
				</ul>
			</div>
		</nav>
		<?php
	}
}

