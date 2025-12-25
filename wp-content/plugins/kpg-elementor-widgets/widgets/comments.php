<?php
/**
 * KPG Comments Widget
 * 
 * System komentarzy z:
 * - Nested replies (odpowiedzi na komentarze)
 * - AJAX submission (bez przeładowania)
 * - Custom styling (dark bg jak contact form)
 * - Avatars (auutor.png fallback)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class KPG_Elementor_Comments_Widget extends Widget_Base {

	public function get_name() {
		return 'kpg-comments';
	}

	public function get_title() {
		return esc_html__( 'KPG Comments', 'kpg-elementor-widgets' );
	}

	public function get_icon() {
		return 'eicon-comments';
	}

	public function get_categories() {
		return [ 'kpg-widgets' ];
	}

	public function get_style_depends() {
		return [ 'kpg-comments-style' ];
	}

	public function get_script_depends() {
		return [ 'kpg-comments-script' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Settings', 'kpg-elementor-widgets' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'KOMENTARZE',
			]
		);

		$this->add_control(
			'show_count',
			[
				'label' => esc_html__( 'Show Count', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'allow_replies',
			[
				'label' => esc_html__( 'Allow Replies', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'max_depth',
			[
				'label' => esc_html__( 'Max Nesting Level', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 4,
				'min' => 1,
				'max' => 6,
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		if ( ! is_single() && ! is_page() ) {
			return;
		}

		$settings = $this->get_settings_for_display();
		$post_id = get_the_ID();

		// Licz tylko widoczne komentarze
		$comment_status = 'approve';
		if ( current_user_can( 'moderate_comments' ) ) {
			$comment_status = ['approve', 'hold']; // Administrator widzi wszystkie oprócz spamu
		}

		$approved_comments = get_comments( [
			'post_id' => $post_id,
			'status' => $comment_status,
			'count' => true, // Tylko liczenie, nie pobieranie
		] );

		$comments_count = $approved_comments;
		?>
		<div class="kpg-comments-container">
			<!-- Header -->
			<div class="kpg-comments-header">
				<h3 class="kpg-comments-title"><?php echo esc_html( $settings['title'] ); ?></h3>
				<?php if ( $settings['show_count'] === 'yes' ) : ?>
					<span class="kpg-comments-count">(<?php echo $comments_count; ?>)</span>
				<?php endif; ?>
			</div>
			
			<!-- Comment Form - NAJPIERW formularz -->
			<?php if ( comments_open() ) : ?>
				<div class="kpg-comment-form-container">
					<?php
					// Dla zalogowanych użytkowników WordPress nie wymaga imienia i e-maila
					$is_logged_in = is_user_logged_in();
					$post_id = get_the_ID();
					$commenter = wp_get_current_commenter();
					$req = get_option( 'require_name_email' );
					$aria_req = ( $req ? " aria-required='true'" : '' );
					
					// Ręcznie renderuj formularz w odpowiedniej kolejności
					?>
					<form action="<?php echo esc_url( site_url( '/wp-comments-post.php' ) ); ?>" method="post" id="commentform" class="comment-form kpg-comment-form">
						<?php
						// 1. PIERWSZY RZĄD: Imię i Email (tylko dla niezalogowanych)
						if ( ! $is_logged_in ) :
						?>
							<div class="kpg-comment-form-row">
								<div class="kpg-comment-form-field">
									<span class="kpg-comment-form-label">imię</span>
									<div class="kpg-comment-form-field-wrapper">
										<input id="author" name="author" type="text" value="<?php echo esc_attr( $commenter['comment_author'] ); ?>" placeholder="Wpisz swoje imię" <?php echo $aria_req; ?> required />
									</div>
								</div>
								<div class="kpg-comment-form-field">
									<span class="kpg-comment-form-label">E-MAIL</span>
									<div class="kpg-comment-form-field-wrapper">
										<input id="email" name="email" type="email" value="<?php echo esc_attr( $commenter['comment_author_email'] ); ?>" placeholder="Wpisz swój adres e-mail" <?php echo $aria_req; ?> required />
									</div>
								</div>
							</div>
						<?php endif; ?>
						
						<?php
						// 2. DRUGI RZĄD: Textarea (wiadomość)
						?>
						<div class="kpg-comment-form-row">
							<div class="kpg-comment-form-field" style="width: 100%;">
								<span class="kpg-comment-form-message-label">WIADOMOŚĆ</span>
								<div class="kpg-comment-form-field-wrapper">
									<textarea id="comment" name="comment" placeholder="Wpisz swoją wiadomosć" rows="4" aria-required="true" required></textarea>
								</div>
							</div>
						</div>
						
						<?php
						// 3. Checkbox cookies (tylko dla niezalogowanych)
						if ( ! $is_logged_in ) :
						?>
							<div class="kpg-comment-form-cookies">
								<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes" />
								<label for="wp-comment-cookies-consent">Zapamiętaj moje dane w tej przeglądarce podczas pisania kolejnych komentarzy.</label>
							</div>
						<?php endif; ?>
						
						<?php
						// 4. Przycisk submit + hidden fields
						?>
						<div class="kpg-comment-form-submit">
							<button type="submit" class="kpg-comment-submit">SKOMENTUJ</button>
							<?php comment_id_fields( $post_id ); ?>
							<?php do_action( 'comment_form', $post_id ); ?>
						</div>
					</form>
					<?php
					?>
				</div>
			<?php endif; ?>
			
			<!-- Comments List - POTEM komentarze -->
			<?php
			// Pobierz WSZYSTKIE zatwierdzone komentarze
			$comments = get_comments( [
				'post_id' => $post_id,
				'status' => 'approve', // Tylko zatwierdzone
				'orderby' => 'comment_date',
				'order' => 'DESC',
			] );

			// Debug: sprawdź ile komentarzy zostało pobranych
			echo '<!-- KPG Comments Debug: Found ' . count( $comments ) . ' comments for post ' . $post_id . ' -->';
			foreach ( $comments as $comment ) {
				echo '<!-- Comment ID: ' . $comment->comment_ID . ', Status: ' . $comment->comment_approved . ', Type: ' . $comment->comment_type . ', Author: ' . esc_html($comment->comment_author) . ', Parent: ' . $comment->comment_parent . ' -->';
			}
			
			if ( ! empty( $comments ) ) : ?>
				<div class="kpg-comments-list">
					<?php
					// Renderuj komentarze ręcznie - pełna kontrola
					$this->render_comments_list( $comments, $settings );
					?>
				</div>
				
				<?php if ( get_comment_pages_count() > 1 ) : ?>
					<div class="kpg-comments-pagination">
						<?php paginate_comments_links(); ?>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<p class="kpg-comments-none"><?php esc_html_e( 'Brak komentarzy. Dodaj pierwszy!', 'kpg-elementor-widgets' ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Renderuj komentarze ręcznie - pełna kontrola bez wp_list_comments
	 */
	private function render_comments_list( $comments, $settings ) {
		$max_depth = intval( $settings['max_depth'] );
		
		// Sortuj komentarze - główne najpierw, potem odpowiedzi
		$top_level = [];
		$children = [];
		
		foreach ( $comments as $comment ) {
			if ( $comment->comment_parent == 0 ) {
				$top_level[] = $comment;
			} else {
				if ( ! isset( $children[ $comment->comment_parent ] ) ) {
					$children[ $comment->comment_parent ] = [];
				}
				$children[ $comment->comment_parent ][] = $comment;
			}
		}
		
		// Sortuj główne komentarze po dacie (najnowsze najpierw)
		usort( $top_level, function( $a, $b ) {
			return strtotime( $b->comment_date ) - strtotime( $a->comment_date );
		} );
		
		// Sortuj odpowiedzi po dacie (najstarsze najpierw)
		foreach ( $children as $parent_id => $replies ) {
			usort( $children[ $parent_id ], function( $a, $b ) {
				return strtotime( $a->comment_date ) - strtotime( $b->comment_date );
			} );
		}
		
		// Renderuj główne komentarze
		foreach ( $top_level as $comment ) {
			$this->render_single_comment( $comment, $settings, 1, $children, $max_depth );
		}
	}
	
	/**
	 * Renderuj pojedynczy komentarz
	 */
	private function render_single_comment( $comment, $settings, $depth, $children, $max_depth ) {
		// Format daty
		$comment_timestamp = strtotime( $comment->comment_date );
		$time_diff = current_time( 'timestamp' ) - $comment_timestamp;
		
		if ( $time_diff < 86400 ) {
			$comment_date = human_time_diff( $comment_timestamp, current_time( 'timestamp' ) ) . ' temu';
		} else {
			$comment_date = date( 'd.m.Y', $comment_timestamp );
		}
		
		// Określ czy to odpowiedź (depth > 1)
		$is_reply = ( $depth > 1 );
		$has_replies = isset( $children[ $comment->comment_ID ] ) && ! empty( $children[ $comment->comment_ID ] );
		
		// Renderuj komentarz - BEZ żadnych inline style!
		?>
		<div class="comment kpg-comment kpg-comment-main" id="comment-<?php echo $comment->comment_ID; ?>">
			<div class="kpg-comment-meta">
				<span class="kpg-comment-author">
					<span class="kpg-comment-author-name"><?php echo esc_html( $comment->comment_author ); ?></span>
				</span>
				<span class="kpg-comment-separator">·</span>
				<span class="kpg-comment-date"><?php echo esc_html( $comment_date ); ?></span>
			</div>
			<div class="kpg-comment-text">
				<?php echo wp_kses_post( $comment->comment_content ); ?>
			</div>
			<?php 
			// Zawsze pokazuj link "odpowiedz" jeśli allow_replies jest włączone
			$show_reply = true;
			if ( isset( $settings['allow_replies'] ) && $settings['allow_replies'] !== 'yes' ) {
				$show_reply = false;
			}
			if ( $depth >= $max_depth ) {
				$show_reply = false;
			}
			
			// Debug
			echo '<!-- Debug: show_reply=' . ( $show_reply ? 'true' : 'false' ) . ', allow_replies=' . ( isset( $settings['allow_replies'] ) ? $settings['allow_replies'] : 'not set' ) . ', depth=' . $depth . ', max_depth=' . $max_depth . ' -->';
			
			if ( $show_reply ) : ?>
				<span class="kpg-comment-reply-link" style="display: inline-block !important; visibility: visible !important; opacity: 1 !important;">
					<a rel="nofollow" class="comment-reply-link" href="#comment-<?php echo $comment->comment_ID; ?>" data-commentid="<?php echo $comment->comment_ID; ?>" data-postid="<?php echo $comment->comment_post_ID; ?>" data-belowelement="comment-<?php echo $comment->comment_ID; ?>" data-respondelement="respond" data-replyto="Odpowiedz użytkownikowi <?php echo esc_attr( $comment->comment_author ); ?>" aria-label="Odpowiedz użytkownikowi <?php echo esc_attr( $comment->comment_author ); ?>" style="display: inline !important; visibility: visible !important; opacity: 1 !important; color: #55a2fb !important;">odpowiedz</a>
				</span>
			<?php endif; ?>
		</div>
		
		<?php
		// Renderuj odpowiedzi jeśli istnieją - z linią po lewej
		if ( $has_replies ) {
			?>
			<div class="children">
				<?php
				foreach ( $children[ $comment->comment_ID ] as $child ) {
					$this->render_single_comment( $child, $settings, $depth + 1, $children, $max_depth );
				}
				?>
			</div>
			<?php
		}
	}

	/**
	 * Custom comment HTML callback (nieużywane, ale zostawiam dla kompatybilności)
	 */
	public function custom_comment_html( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		$settings = $this->get_settings_for_display();
		
		// Format daty - "3h temu" lub data
		$comment_date = human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) ) . ' temu';
		if ( ( current_time( 'timestamp' ) - get_comment_time( 'U' ) ) > 86400 ) {
			$comment_date = get_comment_date( 'd.m.Y' );
		}
		
		// Główne komentarze (depth 0) - struktura z Figmy
		?>
		<div <?php comment_class( 'kpg-comment kpg-comment-main' ); ?> id="comment-<?php comment_ID(); ?>">
			<div class="kpg-comment-meta">
				<span class="kpg-comment-author">
					<span class="kpg-comment-author-name"><?php comment_author(); ?></span>
				</span>
				<span class="kpg-comment-separator">·</span>
				<span class="kpg-comment-date"><?php echo esc_html( $comment_date ); ?></span>
			</div>
			<div class="kpg-comment-text">
				<?php comment_text(); ?>
			</div>
			<?php if ( $settings['allow_replies'] === 'yes' && $depth < $args['max_depth'] ) : ?>
				<span class="kpg-comment-reply-link">
					<?php
					comment_reply_link( [
						'depth' => $depth,
						'max_depth' => $args['max_depth'],
						'reply_text' => esc_html__( 'odpowiedz', 'kpg-elementor-widgets' ),
					] );
					?>
				</span>
			<?php endif; ?>
		</div>
		<?php
	}

	protected function content_template() {
		?>
		<div class="kpg-comments-container">
			<div class="kpg-comments-header">
				<h3 class="kpg-comments-title">KOMENTARZE</h3>
				<span class="kpg-comments-count">(3)</span>
			</div>
			<div class="kpg-comments-list">
				<div class="kpg-comment">
					<div class="kpg-comment-avatar" style="background: #ccc; width: 32px; height: 32px; border-radius: 50%;"></div>
					<div class="kpg-comment-body">
						<div class="kpg-comment-meta">
							<span class="kpg-comment-author">Jan Kowalski</span>
							<span class="kpg-comment-date">23.12.2025</span>
						</div>
						<div class="kpg-comment-text">Przykładowy komentarz...</div>
						<div class="kpg-comment-reply"><a href="#">Odpowiedz</a></div>
					</div>
				</div>
			</div>
			<div class="kpg-comment-form-container">
				<h4 class="kpg-comment-form-title">DODAJ KOMENTARZ</h4>
				<form class="kpg-comment-form">
					<div class="kpg-comment-form-row">
						<input type="text" placeholder="IMIĘ">
						<input type="email" placeholder="EMAIL">
					</div>
					<textarea placeholder="WIADOMOŚĆ"></textarea>
					<button class="kpg-comment-submit">WYŚLIJ</button>
				</form>
			</div>
		</div>
		<?php
	}
}

