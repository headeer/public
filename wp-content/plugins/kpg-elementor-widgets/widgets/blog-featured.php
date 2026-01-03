<?php
/**
 * KPG Blog Featured Widget
 * 
 * Displays the first blog post based on sort order (oldest/newest)
 * Desktop: Yellow background (#f8ff46) with image on right
 * Mobile: Gradient background with image on top
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class KPG_Elementor_Blog_Featured_Widget extends Widget_Base {

	public function get_name() {
		return 'kpg-blog-featured';
	}

	public function get_title() {
		return esc_html__( 'KPG Blog Featured', 'kpg-elementor-widgets' );
	}

	public function get_icon() {
		return 'eicon-post';
	}

	public function get_categories() {
		return [ 'kpg-widgets' ];
	}

	public function get_keywords() {
		return [ 'blog', 'featured', 'post', 'kpg' ];
	}

	public function get_style_depends() {
		return [ 'kpg-blog-featured-desktop-style', 'kpg-blog-featured-mobile-style' ];
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
			'blog_label',
			[
				'label' => esc_html__( 'Blog Label', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'BLOG',
				'label_block' => true,
			]
		);

		$this->add_control(
			'post_source',
			[
				'label' => esc_html__( 'Post Source', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'auto',
				'options' => [
					'auto' => esc_html__( 'Automatic (First from sort order)', 'kpg-elementor-widgets' ),
					'manual' => esc_html__( 'Manual Selection', 'kpg-elementor-widgets' ),
				],
			]
		);

		// Get posts list for manual selection
		$posts = get_posts( [
			'post_type' => 'post',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'orderby' => 'date',
			'order' => 'DESC',
		] );

		$post_options = [ '' => esc_html__( '— Select Post —', 'kpg-elementor-widgets' ) ];
		foreach ( $posts as $post ) {
			$post_options[ $post->ID ] = $post->post_title;
		}

		$this->add_control(
			'selected_post_id',
			[
				'label' => esc_html__( 'Select Post', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SELECT,
				'options' => $post_options,
				'default' => '',
				'condition' => [
					'post_source' => 'manual',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		
		$post_id = null;
		$query = null;
		
		// Check if manual post is selected
		if ( $settings['post_source'] === 'manual' && ! empty( $settings['selected_post_id'] ) ) {
			$post_id = intval( $settings['selected_post_id'] );
			$post = get_post( $post_id );
			if ( ! $post || $post->post_status !== 'publish' ) {
				return;
			}
		} else {
			// Get sort from URL
			$sort = isset( $_GET['sort'] ) ? sanitize_text_field( $_GET['sort'] ) : 'newest';
			$order = ( $sort === 'oldest' ) ? 'ASC' : 'DESC';

			// Get first post based on sort order
			$args = [
				'post_type' => 'post',
				'posts_per_page' => 1,
				'orderby' => 'date',
				'order' => $order,
				'post_status' => 'publish',
				'ignore_sticky_posts' => true,
			];

			$query = new WP_Query( $args );
			
			if ( ! $query->have_posts() ) {
				return;
			}

			$query->the_post();
			$post_id = get_the_ID();
		}
		
		// Get post data
		$title = get_the_title( $post_id );
		$excerpt = get_the_excerpt( $post_id );
		$permalink = get_permalink( $post_id );
		
		// Get featured image
		$image_id = get_post_thumbnail_id( $post_id );
		$image_url = '';
		if ( $image_id ) {
			$image_url = wp_get_attachment_image_url( $image_id, 'large' );
		}
		
		// Get author data
		$author_id = get_post_field( 'post_author', $post_id );
		$first = get_user_meta( $author_id, 'first_name', true );
		$last = get_user_meta( $author_id, 'last_name', true );
		$name = trim( $first . ' ' . $last );
		if ( empty( $name ) ) {
			$author = get_userdata( $author_id );
			$name = $author ? $author->display_name : '';
		}
		
		// Get author avatar
		$avatar_url = '';
		if ( function_exists( 'kpg_get_author_avatar_url' ) ) {
			$avatar_url = kpg_get_author_avatar_url( $author_id, 32 );
		} else {
			$avatar_url = get_avatar_url( $author_id, [ 'size' => 32 ] );
		}
		
		// Format date: "07 lipiec 2025"
		$date = get_the_date( 'd F Y', $post_id );
		
		// Get author archive URL
		$author_archive_url = get_author_posts_url( $author_id );
		
		// Reset post data if we used query
		if ( $query ) {
			wp_reset_postdata();
		}
		?>
		
		<!-- Desktop Version -->
		<div class="kpg-blog-featured-desktop">
			<div class="kpg-blog-featured-frame">
				<span class="kpg-blog-featured-label"><?php echo esc_html( $settings['blog_label'] ); ?></span>
			</div>
			<div class="kpg-blog-featured-content">
				<div class="kpg-blog-featured-text-section">
					<div class="kpg-blog-featured-text-wrapper">
						<h2 class="kpg-blog-featured-title">
							<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
						</h2>
						<div class="kpg-blog-featured-excerpt-wrapper">
							<div class="kpg-blog-featured-excerpt-border">
								<p class="kpg-blog-featured-excerpt"><?php echo esc_html( $excerpt ); ?></p>
							</div>
						</div>
						<div class="kpg-blog-featured-author">
							<a href="<?php echo esc_url( $author_archive_url ); ?>" class="kpg-blog-featured-author-link">
								<?php if ( $avatar_url ) : ?>
									<div class="kpg-blog-featured-avatar" style="background-image: url('<?php echo esc_url( $avatar_url ); ?>');"></div>
								<?php endif; ?>
								<span class="kpg-blog-featured-author-info"><?php echo esc_html( $name ); ?> • <?php echo esc_html( $date ); ?></span>
							</a>
						</div>
					</div>
				</div>
				<?php if ( $image_url ) : ?>
					<a href="<?php echo esc_url( $permalink ); ?>" class="kpg-blog-featured-image-link">
						<div class="kpg-blog-featured-image" style="background-image: url('<?php echo esc_url( $image_url ); ?>');"></div>
					</a>
				<?php endif; ?>
			</div>
			<div class="kpg-blog-featured-background"></div>
		</div>

		<!-- Mobile Version -->
		<div class="kpg-blog-featured-mobile">
			<div class="kpg-blog-featured-mobile-frame">
				<span class="kpg-blog-featured-mobile-label"><?php echo esc_html( $settings['blog_label'] ); ?></span>
			</div>
			<?php if ( $image_url ) : ?>
				<a href="<?php echo esc_url( $permalink ); ?>" class="kpg-blog-featured-mobile-image-link">
					<div class="kpg-blog-featured-mobile-image" style="background-image: url('<?php echo esc_url( $image_url ); ?>');"></div>
				</a>
			<?php endif; ?>
			<h2 class="kpg-blog-featured-mobile-title">
				<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
			</h2>
			<div class="kpg-blog-featured-mobile-excerpt-wrapper">
				<p class="kpg-blog-featured-mobile-excerpt"><?php echo esc_html( $excerpt ); ?></p>
			</div>
			<div class="kpg-blog-featured-mobile-author">
				<a href="<?php echo esc_url( $author_archive_url ); ?>" class="kpg-blog-featured-mobile-author-link">
					<?php if ( $avatar_url ) : ?>
						<div class="kpg-blog-featured-mobile-avatar" style="background-image: url('<?php echo esc_url( $avatar_url ); ?>');"></div>
					<?php endif; ?>
					<span class="kpg-blog-featured-mobile-author-info"><?php echo esc_html( $name ); ?> • <?php echo esc_html( $date ); ?></span>
				</a>
			</div>
			<div class="kpg-blog-featured-mobile-background"></div>
		</div>
		
		<?php
	}

	protected function content_template() {
		?>
		<#
		// This is for Elementor editor preview
		// In real usage, data comes from PHP render() method
		#>
		<div class="kpg-blog-featured-desktop">
			<div class="kpg-blog-featured-frame">
				<span class="kpg-blog-featured-label">{{{ settings.blog_label }}}</span>
			</div>
			<div class="kpg-blog-featured-content">
				<div class="kpg-blog-featured-text-section">
					<div class="kpg-blog-featured-text-wrapper">
						<h2 class="kpg-blog-featured-title">Sample Blog Post Title</h2>
						<div class="kpg-blog-featured-excerpt-wrapper">
							<div class="kpg-blog-featured-excerpt-border">
								<p class="kpg-blog-featured-excerpt">Sample excerpt text that describes the blog post content...</p>
							</div>
						</div>
						<div class="kpg-blog-featured-author">
							<div class="kpg-blog-featured-avatar"></div>
							<span class="kpg-blog-featured-author-info">Author Name • 07 lipiec 2025</span>
						</div>
					</div>
				</div>
				<div class="kpg-blog-featured-image"></div>
			</div>
			<div class="kpg-blog-featured-background"></div>
		</div>

		<div class="kpg-blog-featured-mobile">
			<div class="kpg-blog-featured-mobile-frame">
				<span class="kpg-blog-featured-mobile-label">{{{ settings.blog_label }}}</span>
			</div>
			<div class="kpg-blog-featured-mobile-image"></div>
			<h2 class="kpg-blog-featured-mobile-title">Sample Blog Post Title</h2>
			<div class="kpg-blog-featured-mobile-excerpt-wrapper">
				<p class="kpg-blog-featured-mobile-excerpt">Sample excerpt text that describes the blog post content...</p>
			</div>
			<div class="kpg-blog-featured-mobile-author">
				<div class="kpg-blog-featured-mobile-avatar"></div>
				<span class="kpg-blog-featured-mobile-author-info">Author Name • 07 lipiec 2025</span>
			</div>
			<div class="kpg-blog-featured-mobile-background"></div>
		</div>
		<?php
	}
}

