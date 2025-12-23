<?php
/**
 * KPG Blog Archive Widget - MEGA WIDGET
 *
 * @package KPG_Elementor_Widgets
 * 
 * COMBINES: Featured Post + Post List + Pagination
 * Based on prompts #1-12 (Featured Post + Layout)
 * Based on prompts #18-27 (Pagination)
 * 
 * Features:
 * - Featured post (yellow #F9FF46) with "BLOG" label
 * - Regular post list (white/gray)
 * - Every 3rd post as large format (#e3ebec)
 * - Integrated pagination
 * - 100vw compliant
 * - Placeholder for posts without images
 * - Dynamic query
 * - Respects ?sort= parameter
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class KPG_Elementor_Blog_Archive_Widget extends Widget_Base {

	public function get_name() {
		return 'kpg-blog-archive';
	}

	public function get_title() {
		return esc_html__( 'KPG Blog Archive', 'kpg-elementor-widgets' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return [ 'kpg-widgets' ];
	}

	public function get_keywords() {
		return [ 'blog', 'archive', 'posts', 'featured', 'list', 'kpg' ];
	}

	public function get_style_depends() {
		return [ 'kpg-blog-archive-style', 'kpg-pagination-style' ];
	}

	public function get_script_depends() {
		return [ 'kpg-pagination-script' ];
	}

	protected function register_controls() {
		// Content Section
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'kpg-elementor-widgets' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label' => esc_html__( 'Posts Per Page', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 10,
				'min' => 1,
				'max' => 50,
			]
		);

		$this->add_control(
			'show_featured',
			[
				'label' => esc_html__( 'Show Featured Post', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'large_post_interval',
			[
				'label' => esc_html__( 'Large Post Interval', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 3,
				'min' => 0,
				'max' => 10,
				'description' => esc_html__( 'Every Nth post will be large format (0 = disable)', 'kpg-elementor-widgets' ),
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label' => esc_html__( 'Show Pagination', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_separator',
			[
				'label' => esc_html__( 'Show Separator (Desktop)', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		
		// Get current page
		$paged = max( 1, get_query_var( 'paged' ) );
		if ( $paged === 0 ) {
			$paged = 1;
		}

		// Get sort from URL
		$sort = isset( $_GET['sort'] ) ? sanitize_text_field( $_GET['sort'] ) : 'newest';
		$order = ( $sort === 'oldest' ) ? 'ASC' : 'DESC';

		// Featured post query (only on first page)
		$featured_post = null;
		$featured_id = null;
		
		if ( $paged === 1 && $settings['show_featured'] === 'yes' ) {
			$featured_args = [
				'post_type' => 'post',
				'posts_per_page' => 1,
				'orderby' => 'date',
				'order' => 'DESC',
				'post_status' => 'publish',
			];
			$featured_query = new WP_Query( $featured_args );
			if ( $featured_query->have_posts() ) {
				$featured_query->the_post();
				$featured_post = get_post();
				$featured_id = get_the_ID();
			}
			wp_reset_postdata();
		}

		// Main posts query (exclude featured)
		$args = [
			'post_type' => 'post',
			'posts_per_page' => intval( $settings['posts_per_page'] ),
			'paged' => $paged,
			'orderby' => 'date',
			'order' => $order,
			'post_status' => 'publish',
			'ignore_sticky_posts' => true,
		];

		if ( $featured_id ) {
			$args['post__not_in'] = [ $featured_id ];
		}

		$query = new WP_Query( $args );
		?>
		
		<div class="kpg-blog-archive-container">
			
			<?php if ( $featured_post && $paged === 1 ) : 
				$this->render_featured_post( $featured_post );
			endif; ?>
			
			<?php if ( $query->have_posts() ) : ?>
				<div class="kpg-post-list">
					<?php 
					$index = 0;
					$large_interval = intval( $settings['large_post_interval'] );
					
					while ( $query->have_posts() ) : 
						$query->the_post();
						$index++;
						
						// Add separator before each post (except first)
						if ( $index > 1 ) {
							echo '<div class="kpg-post-separator"></div>';
						}
						
						// Determine if this should be large format
						$is_large = ( $large_interval > 0 && $index % $large_interval === 0 );
						
						if ( $is_large ) {
							$this->render_large_post();
						} else {
							$this->render_regular_post();
						}
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			<?php endif; ?>
			
			<?php if ( $settings['show_pagination'] === 'yes' && $query->max_num_pages > 1 ) : 
				$this->render_pagination( $paged, $query->max_num_pages, $settings );
			endif; ?>
			
		</div>
		<?php
	}

	protected function render_featured_post( $post ) {
		setup_postdata( $post );
		?>
		<!-- Featured Post - Prompt #7, #8 -->
		<div class="kpg-featured-post">
			<div class="kpg-featured-post-label">
				<span class="kpg-featured-post-label-text">BLOG</span>
			</div>
			
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="kpg-featured-post-image">
					<a href="<?php the_permalink(); ?>">
						<?php the_post_thumbnail( 'large' ); ?>
					</a>
				</div>
			<?php endif; ?>
			
			<h2 class="kpg-featured-post-title">
				<a href="<?php the_permalink(); ?>">
					<?php the_title(); ?>
				</a>
			</h2>
			
			<div class="kpg-featured-post-excerpt">
				<?php echo esc_html( wp_trim_words( get_the_excerpt(), 50 ) ); ?>
			</div>
			
			<div class="kpg-featured-post-meta">
				<div class="kpg-featured-post-avatar">
					<?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?>
				</div>
				<span class="kpg-featured-post-author-date">
					<?php 
					$first = get_the_author_meta( 'first_name' );
					$last = get_the_author_meta( 'last_name' );
					$name = trim( $first . ' ' . $last );
					if ( empty( $name ) ) {
						$name = get_the_author();
					}
					echo esc_html( $name . ' • ' . get_the_date( 'd/m/y' ) );
					?>
				</span>
			</div>
			
			<div class="kpg-featured-post-separator"></div>
		</div>
		<?php
		wp_reset_postdata();
	}

	protected function render_regular_post() {
		?>
		<div class="kpg-post-list-item">
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="kpg-post-list-item-image">
					<a href="<?php the_permalink(); ?>">
						<?php the_post_thumbnail( 'medium' ); ?>
					</a>
				</div>
			<?php else : ?>
				<div class="kpg-post-list-item-image">
					<a href="<?php the_permalink(); ?>">
						<div class="kpg-post-list-item-image-placeholder"></div>
					</a>
				</div>
			<?php endif; ?>
			
			<div class="kpg-post-list-item-content">
				<h3 class="kpg-post-list-item-title">
					<a href="<?php the_permalink(); ?>">
						<?php the_title(); ?>
					</a>
				</h3>
				
				<!-- Excerpt for desktop grid -->
				<div class="kpg-post-list-item-excerpt">
					<?php echo esc_html( wp_trim_words( get_the_excerpt(), 15 ) ); ?>
				</div>
				
				<div class="kpg-post-list-item-meta">
					<div class="kpg-post-list-item-avatar">
						<?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?>
					</div>
					<span class="kpg-post-list-item-author-date">
						<?php 
						$first = get_the_author_meta( 'first_name' );
						$last = get_the_author_meta( 'last_name' );
						$name = trim( $first . ' ' . $last );
						if ( empty( $name ) ) {
							$name = get_the_author();
						}
						echo esc_html( $name . ' • ' . get_the_date( 'd/m/y' ) );
						?>
					</span>
				</div>
			</div>
		</div>
		<?php
	}

	protected function render_large_post() {
		?>
		<div class="kpg-post-large">
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="kpg-post-large-image">
					<a href="<?php the_permalink(); ?>">
						<?php the_post_thumbnail( 'large' ); ?>
					</a>
				</div>
			<?php else : ?>
				<div class="kpg-post-large-image">
					<a href="<?php the_permalink(); ?>">
						<div class="kpg-post-large-image-placeholder"></div>
					</a>
				</div>
			<?php endif; ?>
			
			<h2 class="kpg-post-large-title">
				<a href="<?php the_permalink(); ?>">
					<?php the_title(); ?>
				</a>
			</h2>
			
			<div class="kpg-post-large-excerpt-wrapper">
				<div class="kpg-post-large-excerpt-inner">
					<span class="kpg-post-large-excerpt">
						<?php echo esc_html( wp_trim_words( get_the_excerpt(), 50 ) ); ?>
					</span>
				</div>
			</div>
			
			<div class="kpg-post-large-meta">
				<div class="kpg-post-large-avatar">
					<?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?>
				</div>
				<span class="kpg-post-large-author-date">
					<?php 
					$first = get_the_author_meta( 'first_name' );
					$last = get_the_author_meta( 'last_name' );
					$name = trim( $first . ' ' . $last );
					if ( empty( $name ) ) {
						$name = get_the_author();
					}
					echo esc_html( $name . ' • ' . get_the_date( 'd/m/y' ) );
					?>
				</span>
			</div>
		</div>
		<?php
	}

	protected function render_pagination( $current, $max, $settings ) {
		// Calculate pages to show
		$pages = [];

		// Always show first 3
		for ( $i = 1; $i <= min( 3, $max ); $i++ ) {
			$pages[] = [ 'number' => $i, 'is_separator' => false ];
		}

		// If more than 3 pages
		if ( $max > 3 ) {
			$pages[] = [ 'is_separator' => true ];

			// Show current if > 3 and < max
			if ( $current > 3 && $current < $max ) {
				$pages[] = [ 'number' => $current, 'is_separator' => false ];
				
				if ( $current < ( $max - 1 ) ) {
					$pages[] = [ 'is_separator' => true ];
				}
			}

			// Always show max
			$pages[] = [ 'number' => $max, 'is_separator' => false ];
		}
		?>
		
		<?php if ( $settings['show_separator'] === 'yes' ) : ?>
			<div class="kpg-blog-separator"></div>
		<?php endif; ?>
		
		<div class="kpg-blog-pagination">
			<div class="kpg-blog-pagination-numbers">
				<?php foreach ( $pages as $page ) : ?>
					<?php if ( isset( $page['is_separator'] ) && $page['is_separator'] ) : ?>
						<div class="kpg-blog-pagination-separator"></div>
					<?php else : ?>
						<span class="kpg-blog-pagination-item <?php echo ( $page['number'] === $current ) ? 'active' : 'inactive'; ?>" 
							  data-page="<?php echo esc_attr( $page['number'] ); ?>">
							<?php echo str_pad( $page['number'], 2, '0', STR_PAD_LEFT ); ?>
						</span>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			
			<div class="kpg-blog-pagination-arrow <?php echo ( $current >= $max ) ? 'disabled' : ''; ?>" 
				 data-direction="next">
				<svg xmlns="http://www.w3.org/2000/svg" width="40" height="32" viewBox="0 0 40 32" fill="none">
					<path d="M20 24L28 16L20 8" stroke="#252B2B" stroke-width="1.33333" stroke-linecap="square"/>
					<path d="M12 24L20 16L12 8" stroke="#252B2B" stroke-width="1.33333" stroke-linecap="square"/>
				</svg>
			</div>
		</div>
		<?php
	}

	protected function content_template() {
		?>
		<div class="kpg-blog-archive-container">
			<!-- Featured Post Preview -->
			<div class="kpg-featured-post">
				<div class="kpg-featured-post-label">
					<span class="kpg-featured-post-label-text">BLOG</span>
				</div>
				<div class="kpg-featured-post-image" style="background: #e3ebec; height: 257px;"></div>
				<h2 class="kpg-featured-post-title">
					<a href="#">Featured Post Title</a>
				</h2>
				<div class="kpg-featured-post-excerpt">
					Post excerpt text goes here...
				</div>
				<div class="kpg-featured-post-meta">
					<div class="kpg-featured-post-avatar" style="background: #ccc; width: 32px; height: 32px; border-radius: 50%;"></div>
					<span class="kpg-featured-post-author-date">Author • 23/12/25</span>
				</div>
				<div class="kpg-featured-post-separator"></div>
			</div>
			
			<!-- Post List Preview -->
			<div class="kpg-post-list">
				<div class="kpg-post-list-item">
					<div class="kpg-post-list-item-image" style="background: #e3ebec; width: 74px; height: 56px;"></div>
					<div class="kpg-post-list-item-content">
						<h3 class="kpg-post-list-item-title"><a href="#">Post Title</a></h3>
						<div class="kpg-post-list-item-meta">
							<span>Author • 23/12/25</span>
						</div>
					</div>
				</div>
			</div>
			
			<!-- Pagination Preview -->
			<# if (settings.show_separator === 'yes') { #>
				<div class="kpg-blog-separator"></div>
			<# } #>
			<# if (settings.show_pagination === 'yes') { #>
				<div class="kpg-blog-pagination">
					<div class="kpg-blog-pagination-numbers">
						<span class="kpg-blog-pagination-item active">01</span>
						<span class="kpg-blog-pagination-item inactive">02</span>
						<span class="kpg-blog-pagination-item inactive">03</span>
						<div class="kpg-blog-pagination-separator"></div>
						<span class="kpg-blog-pagination-item inactive">10</span>
					</div>
					<div class="kpg-blog-pagination-arrow">
						<svg xmlns="http://www.w3.org/2000/svg" width="40" height="32" viewBox="0 0 40 32" fill="none">
							<path d="M20 24L28 16L20 8" stroke="#252B2B" stroke-width="1.33333" stroke-linecap="square"/>
							<path d="M12 24L20 16L12 8" stroke="#252B2B" stroke-width="1.33333" stroke-linecap="square"/>
						</svg>
					</div>
				</div>
			<# } #>
		</div>
		<?php
	}
}

