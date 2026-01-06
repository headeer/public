<?php
/**
 * KPG Blog Content Widget
 *
 * @package KPG_Elementor_Widgets
 * 
 * Wyświetla treść bloga z numeracją sekcji, sekcją "Ważne" i sekcją o autorze
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class KPG_Elementor_Blog_Content_Widget extends Widget_Base {

	public function get_name() {
		return 'kpg-blog-content';
	}

	public function get_title() {
		return esc_html__( 'KPG Blog Content', 'kpg-elementor-widgets' );
	}

	public function get_icon() {
		return 'eicon-post-content';
	}

	public function get_categories() {
		return [ 'kpg-widgets' ];
	}

	public function get_keywords() {
		return [ 'blog', 'content', 'post', 'article', 'kpg' ];
	}

	public function get_style_depends() {
		return [ 'kpg-blog-content-style' ];
	}

	public function get_script_depends() {
		return [ 'kpg-blog-content-script' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content Settings', 'kpg-elementor-widgets' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_important_section',
			[
				'label' => esc_html__( 'Show Important Section', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no', // Changed from 'yes' to 'no' - section should be opt-in, not opt-out
			]
		);

		$this->add_control(
			'important_position',
			[
				'label' => esc_html__( 'Important Section Position', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'after_3',
				'options' => [
					'after_1' => 'After Section 1',
					'after_2' => 'After Section 2',
					'after_3' => 'After Section 3',
					'after_4' => 'After Section 4',
					'after_5' => 'After Section 5',
					'after_6' => 'After Section 6',
					'end' => 'At the End',
				],
				'condition' => [
					'show_important_section' => 'yes',
				],
			]
		);

		$this->add_control(
			'important_text',
			[
				'label' => esc_html__( 'Important Section Text', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => 'Zgłoszenia mogą być anonimowe lub podpisane – w obu przypadkach ustawa wymaga zachowania poufności (art. 8 ustawy). Tożsamość sygnalisty nie może być ujawniona bez jego wyraźnej zgody, chyba że wymagają tego przepisy szczególne.',
				'condition' => [
					'show_important_section' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_author_section',
			[
				'label' => esc_html__( 'Show Author Section', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Parse post content into sections
	 */
	protected function parse_content_into_sections( $content ) {
		// Split content by headings (h2, h3) - more robust pattern
		$pattern = '/(<h[2-3][^>]*>.*?<\/h[2-3]>)/is';
		$parts = preg_split( $pattern, $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );
		
		$sections = [];
		$current_section = [
			'heading' => '',
			'content' => '',
		];
		
		foreach ( $parts as $part ) {
			// Check if this part is a heading
			if ( preg_match( '/<h[2-3][^>]*>(.*?)<\/h[2-3]>/is', $part, $matches ) ) {
				// Save previous section if it has content
				if ( ! empty( trim( $current_section['content'] ) ) || ! empty( $current_section['heading'] ) ) {
					$sections[] = $current_section;
				}
				// Start new section
				$current_section = [
					'heading' => trim( strip_tags( $matches[1] ) ),
					'content' => '',
				];
			} else {
				// Add content to current section (trim whitespace)
				$trimmed = trim( $part );
				if ( ! empty( $trimmed ) ) {
					$current_section['content'] .= $part;
				}
			}
		}
		
		// Add last section if it has content
		if ( ! empty( trim( $current_section['content'] ) ) || ! empty( $current_section['heading'] ) ) {
			$sections[] = $current_section;
		}
		
		// If no sections found, treat entire content as intro (no heading)
		if ( empty( $sections ) ) {
			$sections[] = [
				'heading' => '',
				'content' => $content,
			];
		}
		
		return $sections;
	}

	protected function render() {
		if ( ! is_single() && ! is_page() ) {
			return;
		}

		global $post;
		if ( ! $post ) {
			return;
		}

		$settings = $this->get_settings_for_display();
		
		// Try to get content from Elementor if post is built with Elementor
		$content = '';
		$is_elementor = false;
		
		if ( class_exists( '\Elementor\Plugin' ) ) {
			$document = \Elementor\Plugin::$instance->documents->get( $post->ID );
			if ( $document ) {
				// Check if built with Elementor (method exists check)
				$is_elementor = method_exists( $document, 'is_built_with_elementor' ) ? $document->is_built_with_elementor() : false;
				
				if ( $is_elementor ) {
					// Get Elementor content - but this will render widgets, so we need to parse from DOM
					// Instead, we'll use JavaScript to parse rendered content
					$content = ''; // Empty - will be parsed by JS
				}
			}
		}
		
		// Fallback to post content
		if ( empty( $content ) && ! $is_elementor ) {
			$content = apply_filters( 'the_content', $post->post_content );
		}
		
		$sections = $this->parse_content_into_sections( $content );
		
		// Get post title
		$post_title = get_the_title();
		?>
		<div class="kpg-blog-content-container">
			
			
			<!-- Content Frame -->
			<div class="kpg-blog-content-frame">
				<?php
				// Debug: show content info
				if ( current_user_can( 'edit_posts' ) ) {
					echo '<!-- KPG Blog Content Debug: Sections found: ' . count( $sections ) . ', Content length: ' . strlen( $content ) . ' -->';
				}
				
				// First paragraph (intro) - if exists
				if ( ! empty( $sections ) && empty( $sections[0]['heading'] ) && ! empty( trim( $sections[0]['content'] ) ) ) {
					$intro = array_shift( $sections );
					?>
					<div class="kpg-blog-intro">
						<?php echo wp_kses_post( $intro['content'] ); ?>
					</div>
					<?php
				}
				
				// Render sections with numbering
				$section_number = 1;
				$total_sections = count( $sections );
				$important_rendered = false;
				
				// If no sections found or Elementor content, use JavaScript to parse
				if ( empty( $sections ) || ( count( $sections ) === 1 && empty( $sections[0]['heading'] ) && empty( trim( $sections[0]['content'] ) ) ) || $is_elementor ) {
					// Placeholder for JavaScript parsing
					?>
					<div class="kpg-blog-content-placeholder" data-post-id="<?php echo esc_attr( $post->ID ); ?>" data-show-important="<?php echo esc_attr( $settings['show_important_section'] === 'yes' ? 'yes' : 'no' ); ?>" data-important-position="<?php echo esc_attr( $settings['show_important_section'] === 'yes' ? $settings['important_position'] : '' ); ?>" data-important-text="<?php echo esc_attr( $settings['show_important_section'] === 'yes' ? $settings['important_text'] : '' ); ?>">
						<!-- Content will be parsed from Elementor output by JavaScript -->
					</div>
					<?php
				} else {
					// Render sections normally
					foreach ( $sections as $index => $section ) {
						$section_id = 'section-' . ( $index + 1 );
						if ( ! empty( $section['heading'] ) ) {
							$section_id = 'toc-' . sanitize_title( $section['heading'] );
						}
						
						?>
						<div class="kpg-blog-section" id="<?php echo esc_attr( $section_id ); ?>">
							<div class="kpg-blog-section-row">
								<span class="kpg-blog-section-number">0.<?php echo $section_number; ?></span>
								<div class="kpg-blog-section-content">
									<?php if ( ! empty( $section['heading'] ) ) : ?>
										<h2 class="kpg-blog-section-heading"><?php echo esc_html( $section['heading'] ); ?></h2>
									<?php endif; ?>
									<div class="kpg-blog-section-text">
										<?php echo wp_kses_post( $section['content'] ); ?>
									</div>
								</div>
							</div>
						</div>
						<?php
						
						// Insert Important Section if needed
						if ( $settings['show_important_section'] === 'yes' && ! $important_rendered ) {
							$position = $settings['important_position'];
							$should_insert = false;
							
							// Check if we should insert after this section
							if ( $position === 'after_' . $section_number ) {
								$should_insert = true;
							} elseif ( $position === 'end' && $index === $total_sections - 1 ) {
								$should_insert = true;
							}
							
							if ( $should_insert ) {
								$this->render_important_section( $settings['important_text'] );
								$important_rendered = true;
							}
						}
						
						$section_number++;
					}
				}
				
				// Render Author Section (always at the end)
				if ( $settings['show_author_section'] === 'yes' ) {
					$this->render_author_section();
				}
				?>
			</div>
		</div>
		<?php
		// Don't close article here - let comments widget close it
	}

	/**
	 * Render Important Section
	 */
	protected function render_important_section( $text ) {
		?>
		<div class="kpg-blog-important-section">
			<div class="kpg-blog-important-rectangle">
				<div class="kpg-blog-important-icon"></div>
				<div class="kpg-blog-important-content-wrapper">
					<div class="kpg-blog-important-line"></div>
					<div class="kpg-blog-important-title-wrapper">
						<h2 class="kpg-blog-important-title">Ważne</h2>
					</div>
					<div class="kpg-blog-important-text">
						<?php echo wp_kses_post( wpautop( $text ) ); ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Author Section
	 */
	protected function render_author_section() {
		$author_id = get_the_author_meta( 'ID' );
		$author_name = get_the_author();
		$author_description = get_the_author_meta( 'description' );
		
		// Get author meta fields (if using ACF or similar)
		$author_title = get_user_meta( $author_id, 'author_title', true ) ?: 'RADCA PRAWNY';
		$author_linkedin = get_user_meta( $author_id, 'author_linkedin', true ) ?: '';
		$author_facebook = get_user_meta( $author_id, 'author_facebook', true ) ?: '';
		$author_image = kpg_get_author_avatar_url( $author_id, 251 );
		
		?>
		<div class="kpg-blog-author-section">
			<div class="kpg-blog-author-frame">
				<?php if ( $author_image ) : ?>
					<div class="kpg-blog-author-image" style="background-image: url('<?php echo esc_url( $author_image ); ?>');"></div>
				<?php endif; ?>
				<div class="kpg-blog-author-content">
					<div class="kpg-blog-author-header">
						<div class="kpg-blog-author-info">
							<div class="kpg-blog-author-name-wrapper">
								<span class="kpg-blog-author-name"><?php echo esc_html( $author_name ); ?></span>
								<span class="kpg-blog-author-title"><?php echo esc_html( $author_title ); ?></span>
							</div>
							<?php if ( $author_linkedin || $author_facebook ) : ?>
								<div class="kpg-blog-author-social">
									<?php if ( $author_linkedin ) : ?>
										<a href="<?php echo esc_url( $author_linkedin ); ?>" class="kpg-blog-author-link">LINKEDIN</a>
									<?php endif; ?>
									<?php if ( $author_facebook ) : ?>
										<a href="<?php echo esc_url( $author_facebook ); ?>" class="kpg-blog-author-link">FACEBOOK</a>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
						<?php if ( $author_description ) : ?>
							<div class="kpg-blog-author-description">
								<?php echo wp_kses_post( wpautop( $author_description ) ); ?>
							</div>
						<?php endif; ?>
					</div>
					<div class="kpg-blog-author-button">
						<a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>" class="kpg-blog-author-more">
							<span>ZOBACZ WIĘCEJ artykułów</span>
						</a>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

