/**
 * KPG Comments - AJAX Submission + Nested Replies
 */

(function($) {
	'use strict';

	function initComments() {
		// NAJPIERW usuń wszelkie inline style które ukrywają komentarze (ale NIE z linków odpowiedzi)
		jQuery('.kpg-comments-container .comment').each(function() {
			var $comment = jQuery(this);
			// Usuń style tylko jeśli nie zawiera linku odpowiedzi
			if (!$comment.find('.kpg-comment-reply-link').length) {
				$comment.removeAttr('style');
			}
		});
		
		// Wymuś widoczność linków odpowiedzi
		jQuery('.kpg-comments-container .kpg-comment-reply-link').each(function() {
			var $link = jQuery(this);
			$link.css({
				'display': 'inline-block',
				'visibility': 'visible',
				'opacity': '1'
			});
			$link.find('a').css({
				'display': 'inline',
				'visibility': 'visible',
				'opacity': '1',
				'color': '#55a2fb'
			});
		});

		var $form = $('#commentform, .kpg-comment-form');

		if ($form.length === 0) {
			return;
		}

		// Throttling - zapobieganie zbyt szybkiemu wysyłaniu
		var lastSubmitTime = 0;
		var minTimeBetweenSubmits = 5000; // 5 sekund minimum między wysyłkami
		var isSubmitting = false;

		// AJAX form submission
		$form.on('submit', function(e) {
			e.preventDefault();
			
			// Sprawdź czy już wysyłamy
			if (isSubmitting) {
				console.log('KPG Comments: Already submitting, please wait...');
				return;
			}
			
			// Sprawdź throttling
			var now = Date.now();
			var timeSinceLastSubmit = now - lastSubmitTime;
			if (timeSinceLastSubmit < minTimeBetweenSubmits) {
				var waitTime = Math.ceil((minTimeBetweenSubmits - timeSinceLastSubmit) / 1000);
				var $error = $('<div class="kpg-comment-message kpg-comment-error">Wysyłasz komentarze zbyt szybko. Poczekaj ' + waitTime + ' sekund.</div>');
				$form.before($error);
				setTimeout(function() {
					$error.fadeOut(function() {
						$(this).remove();
					});
				}, 5000);
				return;
			}
			
			isSubmitting = true;
			
			var $submitBtn = $form.find('.kpg-comment-submit, input[type="submit"]');
			var originalText = $submitBtn.text() || $submitBtn.val();
			
			// Disable submit button
			$submitBtn.prop('disabled', true);
			if ($submitBtn.is('button')) {
				$submitBtn.text('Wysyłanie...');
			} else {
				$submitBtn.val('Wysyłanie...');
			}
			
			// Get form data + nonce
			var formData = $form.serialize();
			
			// Sprawdź czy wszystkie wymagane pola są wypełnione
			// Dla zalogowanych użytkowników WordPress nie wymaga imienia i e-maila
			var $authorField = $form.find('#author');
			var $emailField = $form.find('#email');
			var $authorRow = $form.find('.kpg-comment-form-row'); // Sprawdź czy row z author/email istnieje
			var comment = $form.find('#comment').val();
			
			// Sprawdź czy użytkownik jest zalogowany (pola author/email nie istnieją w DOM)
			var isLoggedIn = ($authorField.length === 0 && $emailField.length === 0) || 
			                 ($authorRow.length === 0);
			
			// Walidacja: dla zalogowanych użytkowników wymagamy tylko komentarza
			if (isLoggedIn) {
				if (!comment || comment.trim() === '') {
					var $error = $('<div class="kpg-comment-message kpg-comment-error">Wypełnij pole wiadomość.</div>');
					$form.before($error);
					setTimeout(function() {
						$error.fadeOut(function() {
							$(this).remove();
						});
					}, 5000);
					
					isSubmitting = false;
					$submitBtn.prop('disabled', false);
					if ($submitBtn.is('button')) {
						$submitBtn.text(originalText);
					} else {
						$submitBtn.val(originalText);
					}
					return;
				}
			} else {
				// Dla niezalogowanych użytkowników wymagamy wszystkich pól
				var author = $authorField.length > 0 ? $authorField.val() : '';
				var email = $emailField.length > 0 ? $emailField.val() : '';
				
				if (!author || !email || !comment || comment.trim() === '') {
					var $error = $('<div class="kpg-comment-message kpg-comment-error">Wypełnij wszystkie wymagane pola (imię, e-mail, wiadomość).</div>');
					$form.before($error);
					setTimeout(function() {
						$error.fadeOut(function() {
							$(this).remove();
						});
					}, 5000);
					
					isSubmitting = false;
					$submitBtn.prop('disabled', false);
					if ($submitBtn.is('button')) {
						$submitBtn.text(originalText);
					} else {
						$submitBtn.val(originalText);
					}
					return;
				}
			}
			
			// Get comment_post_ID if not in form
			if (!formData.includes('comment_post_ID')) {
				var postId = $form.find('input[name="comment_post_ID"]').val() || 
				             $('input[name="post_id"]').val() || 
				             $('body').data('post-id') ||
				             window.kpgPostId;
				if (postId) {
					formData += '&comment_post_ID=' + postId;
				} else {
					console.warn('KPG Comments: Nie znaleziono comment_post_ID');
				}
			}
			
			// Upewnij się że mamy nonce (WordPress powinien go dodać automatycznie)
			if (!formData.includes('_wp_unfiltered_html_comment')) {
				var nonce = $form.find('input[name="_wp_unfiltered_html_comment"]').val();
				if (nonce) {
					formData += '&_wp_unfiltered_html_comment=' + encodeURIComponent(nonce);
				}
			}
			
			// AJAX request - użyj fetch API z obsługą redirect
			var formUrl = $form.attr('action') || '/wp-comments-post.php';
			
			// Debug: log form data
			console.log('KPG Comments: Form data:', formData);
			console.log('KPG Comments: Form URL:', formUrl);
			
			fetch(formUrl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
				},
				body: formData,
				redirect: 'manual', // Nie follow redirect automatycznie
				credentials: 'same-origin' // Wysyłaj cookies (ważne dla nonce)
			})
			.then(function(response) {
				console.log('KPG Comments: Response status:', response.status);
				console.log('KPG Comments: Response type:', response.type);
				
				// Sprawdź status
				if (response.status === 302 || response.status === 200 || response.status === 0) {
					// 302 = redirect = sukces! WordPress przekierowuje po dodaniu komentarza
					console.log('KPG Comments: Success (status: ' + response.status + ')');
					
					// Update last submit time
					lastSubmitTime = Date.now();
					isSubmitting = false;
					
					// Sprawdź czy komentarz wymaga zatwierdzenia
					var successMsg = 'Komentarz został dodany!';
					var requiresModeration = false;
					
					if (response.status === 302) {
						// Sprawdź Location header
						var location = response.headers.get('Location');
						console.log('KPG Comments: Location header:', location);
						
						if (location) {
							if (location.indexOf('comment=moderated') !== -1 || 
							    location.indexOf('comment=hold') !== -1 ||
							    location.indexOf('comment-awaiting-moderation') !== -1 ||
							    location.indexOf('awaiting-moderation') !== -1) {
								requiresModeration = true;
								successMsg = 'Komentarz został dodany i oczekuje na zatwierdzenie. Zostanie wyświetlony po zatwierdzeniu przez administratora.';
							}
						}
					}
					
					// Dla statusu 302, WordPress zwykle przekierowuje z informacją o moderacji
					// Domyślnie zakładamy że komentarze od anonimowych wymagają zatwierdzenia
					if (response.status === 302 && !requiresModeration) {
						// Sprawdź czy użytkownik jest zalogowany
						var isLoggedIn = $('body').hasClass('logged-in') || $('.logged-in-as').length > 0;
						if (!isLoggedIn) {
							// Anonimowy użytkownik - prawdopodobnie wymaga zatwierdzenia
							requiresModeration = true;
							successMsg = 'Komentarz został dodany i oczekuje na zatwierdzenie. Zostanie wyświetlony po zatwierdzeniu przez administratora.';
						}
					}
					
					// Pokaż komunikat
					var $success = $('<div class="kpg-comment-message kpg-comment-success">' + successMsg + '</div>');
					$form.before($success);
					
					// Clear form
					$form[0].reset();
					
					// NIE przeładowuj strony jeśli komentarz wymaga zatwierdzenia (nie będzie widoczny)
					if (!requiresModeration) {
						setTimeout(function() {
							window.location.reload();
						}, 2000);
					} else {
						// Usuń komunikat po 7 sekundach (dłużej żeby użytkownik przeczytał)
						setTimeout(function() {
							$success.fadeOut(function() {
								$(this).remove();
							});
						}, 7000);
					}
				} else {
					// Błąd - sprawdź odpowiedź
					return response.text().then(function(text) {
						console.log('KPG Comments: Error response text:', text.substring(0, 500)); // Pierwsze 500 znaków
						
						isSubmitting = false;
						
						var errorMsg = 'Błąd podczas dodawania komentarza.';
						
						// Sprawdź różne typy błędów WordPress
						if (response.status === 409) {
							// 409 Conflict - duplikat lub zbyt szybkie wysyłanie
							if (text.indexOf('duplicate') !== -1 || text.indexOf('Duplicate') !== -1 || text.indexOf('już to powiedziano') !== -1) {
								errorMsg = 'Ten komentarz już istnieje. Nie możesz wysłać tego samego komentarza dwa razy.';
							} else if (text.indexOf('zbyt szybko') !== -1 || text.indexOf('too quickly') !== -1 || text.indexOf('Zwolnij') !== -1) {
								errorMsg = 'Wysyłasz komentarze zbyt szybko. Poczekaj chwilę przed wysłaniem kolejnego komentarza.';
								// Zwiększ czas oczekiwania
								lastSubmitTime = Date.now();
							} else {
								errorMsg = 'Wystąpił konflikt. Spróbuj ponownie za chwilę.';
							}
						} else if (text.indexOf('duplicate') !== -1 || text.indexOf('Duplicate') !== -1 || text.indexOf('już to powiedziano') !== -1) {
							errorMsg = 'Ten komentarz już istnieje. Nie możesz wysłać tego samego komentarza dwa razy.';
						} else if (text.indexOf('zbyt szybko') !== -1 || text.indexOf('too quickly') !== -1 || text.indexOf('Zwolnij') !== -1) {
							errorMsg = 'Wysyłasz komentarze zbyt szybko. Poczekaj chwilę przed wysłaniem kolejnego komentarza.';
							lastSubmitTime = Date.now();
						} else if (text.indexOf('error') !== -1 || text.indexOf('Error') !== -1) {
							// Spróbuj wyciągnąć konkretny komunikat błędu
							var errorMatch = text.match(/<p[^>]*class="[^"]*error[^"]*"[^>]*>([^<]+)<\/p>/i) ||
							                text.match(/<div[^>]*class="[^"]*error[^"]*"[^>]*>([^<]+)<\/div>/i) ||
							                text.match(/Wykryto[^<]+/i);
							if (errorMatch && errorMatch[1]) {
								errorMsg = errorMatch[1].trim();
							} else if (text.match(/Wykryto[^<]+/i)) {
								errorMsg = text.match(/Wykryto[^<]+/i)[0].replace(/<[^>]+>/g, '').trim();
							} else {
								errorMsg = 'Nie można dodać komentarza. Sprawdź czy wszystkie pola są wypełnione poprawnie.';
							}
						} else if (text.indexOf('spam') !== -1 || text.indexOf('Spam') !== -1) {
							errorMsg = 'Komentarz został oznaczony jako spam.';
						} else if (response.status === 403) {
							errorMsg = 'Brak uprawnień. Sprawdź czy jesteś zalogowany lub czy komentarze są włączone.';
						} else if (response.status === 400) {
							errorMsg = 'Nieprawidłowe dane. Sprawdź czy wszystkie wymagane pola są wypełnione.';
						}
						
						var $error = $('<div class="kpg-comment-message kpg-comment-error">' + errorMsg + '</div>');
						$form.before($error);
						
						setTimeout(function() {
							$error.fadeOut(function() {
								$(this).remove();
							});
						}, 7000); // Dłużej pokazuj błąd (7 sekund)
						
						$submitBtn.prop('disabled', false);
						if ($submitBtn.is('button')) {
							$submitBtn.text(originalText);
						} else {
							$submitBtn.val(originalText);
						}
					});
				}
			})
			.catch(function(error) {
				console.error('KPG Comments: Network Error', error);
				isSubmitting = false;
				
				var $error = $('<div class="kpg-comment-message kpg-comment-error">Błąd sieci. Sprawdź połączenie i spróbuj ponownie.</div>');
				$form.before($error);
				
				setTimeout(function() {
					$error.fadeOut(function() {
						$(this).remove();
					});
				}, 5000);
				
				$submitBtn.prop('disabled', false);
				if ($submitBtn.is('button')) {
					$submitBtn.text(originalText);
				} else {
					$submitBtn.val(originalText);
				}
			});
		});

		// Reply button handling - DOKŁADNIE Z FIGMY
		$(document).on('click', '.kpg-comment-reply-link a, .kpg-comments-container .comment-reply-link', function(e) {
			e.preventDefault();
			
			var $link = $(this);
			var commentId = $link.data('commentid');
			var commentAuthor = $link.data('replyto') || $link.attr('aria-label') || '';
			
			// Wyciągnij imię autora z aria-label lub replyto
			var authorName = '';
			if (commentAuthor) {
				var match = commentAuthor.match(/użytkownikowi\s+(.+)/i);
				if (match && match[1]) {
					authorName = match[1].trim();
				}
			}
			
			// Ustaw comment_parent w formularzu
			var $form = $('#commentform');
			if ($form.length) {
				$form.find('#comment_parent').val(commentId);
				
				// Pokaż informację o odpowiedzi
				var $replyInfo = $form.find('.kpg-reply-info');
				if ($replyInfo.length === 0) {
					$replyInfo = $('<div class="kpg-reply-info"></div>');
					$form.prepend($replyInfo);
				}
				
				if (authorName) {
					$replyInfo.html('<span class="kpg-reply-to">Odpowiedź dla: <strong>' + authorName + '</strong></span> <a href="#" class="kpg-cancel-reply">Anuluj</a>');
				} else {
					$replyInfo.html('<span class="kpg-reply-to">Odpowiedź na komentarz</span> <a href="#" class="kpg-cancel-reply">Anuluj</a>');
				}
				
				$replyInfo.show();
				
				// Pokaż przycisk anuluj
				var $cancelLink = $('#cancel-comment-reply-link');
				if ($cancelLink.length) {
					$cancelLink.show();
				}
				
				// Scroll do formularza
				setTimeout(function() {
					var $respond = $('#respond');
					if ($respond.length) {
						$('html, body').animate({
							scrollTop: $respond.offset().top - 100
						}, 500);
						
						// Focus na textarea
						$form.find('#comment').focus();
					}
				}, 100);
			}
		});
		
		// Anuluj odpowiedź
		$(document).on('click', '.kpg-cancel-reply, #cancel-comment-reply-link', function(e) {
			e.preventDefault();
			
			var $form = $('#commentform');
			if ($form.length) {
				$form.find('#comment_parent').val(0);
				
				// Ukryj informację o odpowiedzi
				$form.find('.kpg-reply-info').hide();
				
				// Ukryj przycisk anuluj
				$('#cancel-comment-reply-link').hide();
			}
		});
	}

	// Ukryj wszystkie komentarze poza widgetem KPG
	function hideDefaultComments() {
		console.log('KPG Comments: Running hideDefaultComments');

		// Najpierw ukryj WSZYSTKIE komentarze i formularze
		jQuery('.comment, .comments-area, #comments, .comment-list, .comment-respond, .comment-form, .comments-title').each(function() {
			var $element = jQuery(this);
			// Sprawdź czy element jest w kontenerze KPG
			if (!$element.closest('.kpg-comments-container').length) {
				$element.hide();
			}
		});

		// Zapewnij że WSZYSTKIE komentarze w widgecie są widoczne
		jQuery('.kpg-comments-container .comment').each(function() {
			var $comment = jQuery(this);
			// Usuń wszelkie style inline które ukrywają komentarz
			$comment.removeAttr('style');
			$comment.show();
			$comment.css({
				'display': 'flex',
				'visibility': 'visible',
				'opacity': '1'
			});
		});

		// Zapewnij że formularz w widgecie KPG jest widoczny
		jQuery('.kpg-comments-container .comment-respond, .kpg-comments-container .comment-form, .kpg-comments-container #respond, .kpg-comments-container #commentform').each(function() {
			var $form = jQuery(this);
			$form.show();
			$form.css({
				'display': 'block',
				'visibility': 'visible',
				'opacity': '1'
			});
		});

		console.log('KPG Comments: Finished hiding default comments');
	}

	$(document).ready(function() {
		// Najpierw usuń style inline z komentarzy
		jQuery('.kpg-comments-container .comment').removeAttr('style');

		initComments();
		hideDefaultComments();
	});
	
	// Ukryj komentarze po załadowaniu Elementora
	if (typeof elementorFrontend !== 'undefined') {
		elementorFrontend.hooks.addAction('frontend/element_ready/kpg-comments.default', function() {
			initComments();
			setTimeout(hideDefaultComments, 100);
		});
		
		// Ukryj komentarze po załadowaniu całej strony
		elementorFrontend.hooks.addAction('frontend/init', function() {
			setTimeout(hideDefaultComments, 500);
		});
	}
	
	// Ukryj komentarze po załadowaniu strony
	$(window).on('load', function() {
		setTimeout(hideDefaultComments, 1000);
	});

})(jQuery);

