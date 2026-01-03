/**
 * KPG Pagination Debug Script
 * 
 * Wklej ten skrypt do konsoli przeglądarki (F12) na stronie blogowej
 * aby zdiagnozować problemy z paginacją
 */

(function() {
	console.log('========================================');
	console.log('KPG PAGINATION DEBUG SCRIPT');
	console.log('========================================\n');
	
	var issues = [];
	var info = [];
	
	// 1. Sprawdź czy jQuery jest dostępne
	console.log('1. Sprawdzanie jQuery...');
	if (typeof jQuery === 'undefined') {
		issues.push('❌ jQuery nie jest załadowane!');
		console.log('   ❌ jQuery nie jest dostępne');
	} else {
		info.push('✅ jQuery jest dostępne (wersja: ' + jQuery.fn.jquery + ')');
		console.log('   ✅ jQuery jest dostępne (wersja: ' + jQuery.fn.jquery + ')');
	}
	
	// 2. Sprawdź czy skrypt paginacji jest załadowany
	console.log('\n2. Sprawdzanie skryptu paginacji...');
	var paginationScriptLoaded = false;
	var scripts = document.getElementsByTagName('script');
	for (var i = 0; i < scripts.length; i++) {
		if (scripts[i].src && scripts[i].src.indexOf('pagination.js') !== -1) {
			paginationScriptLoaded = true;
			info.push('✅ Skrypt paginacji jest załadowany: ' + scripts[i].src);
			console.log('   ✅ Skrypt paginacji znaleziony: ' + scripts[i].src);
			break;
		}
	}
	if (!paginationScriptLoaded) {
		issues.push('❌ Skrypt paginacji (pagination.js) nie jest załadowany!');
		console.log('   ❌ Skrypt paginacji nie został znaleziony');
	}
	
	// 3. Sprawdź elementy paginacji
	console.log('\n3. Sprawdzanie elementów paginacji...');
	var $pagination = jQuery('.kpg-blog-pagination');
	if ($pagination.length === 0) {
		issues.push('❌ Nie znaleziono elementów .kpg-blog-pagination na stronie!');
		console.log('   ❌ Nie znaleziono elementów .kpg-blog-pagination');
	} else {
		info.push('✅ Znaleziono ' + $pagination.length + ' kontener(y) paginacji');
		console.log('   ✅ Znaleziono ' + $pagination.length + ' kontener(y) paginacji');
		
		$pagination.each(function(index) {
			var $this = jQuery(this);
			var $items = $this.find('.kpg-blog-pagination-item');
			var $arrow = $this.find('.kpg-blog-pagination-arrow');
			var blogBaseUrl = $this.data('blog-base-url');
			
			console.log('\n   Kontener #' + (index + 1) + ':');
			console.log('   - Elementy paginacji: ' + $items.length);
			console.log('   - Strzałka: ' + ($arrow.length > 0 ? 'TAK' : 'NIE'));
			console.log('   - data-blog-base-url: ' + (blogBaseUrl || 'BRAK'));
			
			if ($items.length === 0) {
				issues.push('❌ Kontener #' + (index + 1) + ': Brak elementów .kpg-blog-pagination-item');
			} else {
				$items.each(function() {
					var page = jQuery(this).data('page');
					var isActive = jQuery(this).hasClass('active');
					console.log('     - Strona: ' + page + (isActive ? ' (AKTYWNA)' : ''));
				});
			}
			
			if (!blogBaseUrl) {
				issues.push('⚠️ Kontener #' + (index + 1) + ': Brak atrybutu data-blog-base-url');
			}
		});
	}
	
	// 4. Sprawdź event listenery
	console.log('\n4. Sprawdzanie event listenerów...');
	if ($pagination.length > 0) {
		var $firstItem = $pagination.first().find('.kpg-blog-pagination-item').first();
		if ($firstItem.length > 0) {
			var events = jQuery._data($firstItem[0], 'events');
			if (events && events.click) {
				info.push('✅ Event listenery są przypisane (' + events.click.length + ' handlerów)');
				console.log('   ✅ Event listenery są przypisane');
			} else {
				issues.push('❌ Event listenery NIE są przypisane do elementów paginacji!');
				console.log('   ❌ Event listenery NIE są przypisane');
			}
		}
	}
	
	// 5. Sprawdź aktualny URL
	console.log('\n5. Sprawdzanie aktualnego URL...');
	var currentUrl = window.location.href;
	var url = new URL(currentUrl);
	info.push('Aktualny URL: ' + currentUrl);
	info.push('Pathname: ' + url.pathname);
	info.push('Search: ' + url.search);
	console.log('   URL: ' + currentUrl);
	console.log('   Pathname: ' + url.pathname);
	console.log('   Search: ' + url.search);
	
	// 6. Test funkcji navigateToPage (jeśli istnieje)
	console.log('\n6. Test funkcji navigateToPage...');
	if (typeof navigateToPage === 'function') {
		info.push('✅ Funkcja navigateToPage jest dostępna globalnie');
		console.log('   ✅ Funkcja navigateToPage jest dostępna');
	} else {
		issues.push('⚠️ Funkcja navigateToPage nie jest dostępna globalnie (może być w closure)');
		console.log('   ⚠️ Funkcja navigateToPage nie jest dostępna globalnie');
	}
	
	// 7. Symulacja kliknięcia
	console.log('\n7. Test symulacji kliknięcia...');
	if ($pagination.length > 0) {
		var $testItem = $pagination.first().find('.kpg-blog-pagination-item').not('.active').first();
		if ($testItem.length > 0) {
			var testPage = $testItem.data('page');
			console.log('   Znaleziono element testowy - strona: ' + testPage);
			console.log('   Aby przetestować kliknięcie, wykonaj:');
			console.log('   jQuery(".kpg-blog-pagination-item").first().trigger("click")');
		} else {
			console.log('   ⚠️ Brak elementów do testowania (wszystkie są aktywne?)');
		}
	}
	
	// 8. Sprawdź czy są błędy JavaScript
	console.log('\n8. Sprawdzanie błędów JavaScript...');
	if (window.onerror) {
		info.push('✅ Window.onerror jest ustawione');
	} else {
		console.log('   ⚠️ Window.onerror nie jest ustawione');
	}
	
	// Podsumowanie
	console.log('\n========================================');
	console.log('PODSUMOWANIE');
	console.log('========================================\n');
	
	if (issues.length === 0) {
		console.log('✅ Nie znaleziono problemów!');
		console.log('\nInformacje:');
		info.forEach(function(item) {
			console.log('  ' + item);
		});
	} else {
		console.log('❌ Znaleziono ' + issues.length + ' problem(ów):\n');
		issues.forEach(function(issue) {
			console.log('  ' + issue);
		});
		
		if (info.length > 0) {
			console.log('\nInformacje:');
			info.forEach(function(item) {
				console.log('  ' + item);
			});
		}
	}
	
	console.log('\n========================================');
	console.log('SUGEROWANE ROZWIĄZANIA:');
	console.log('========================================\n');
	
	if (issues.some(function(i) { return i.indexOf('jQuery') !== -1; })) {
		console.log('1. jQuery nie jest załadowane:');
		console.log('   - Sprawdź czy jQuery jest załadowane przed skryptem paginacji');
		console.log('   - Sprawdź kolejność ładowania skryptów w WordPress\n');
	}
	
	if (issues.some(function(i) { return i.indexOf('pagination.js') !== -1; })) {
		console.log('2. Skrypt paginacji nie jest załadowany:');
		console.log('   - Sprawdź czy plik pagination.js istnieje');
		console.log('   - Sprawdź czy jest zarejestrowany w WordPress');
		console.log('   - Sprawdź czy jest dodany do widgetu\n');
	}
	
	if (issues.some(function(i) { return i.indexOf('.kpg-blog-pagination') !== -1; })) {
		console.log('3. Elementy paginacji nie są znalezione:');
		console.log('   - Sprawdź czy widget paginacji jest dodany do strony');
		console.log('   - Sprawdź czy klasa .kpg-blog-pagination jest w HTML\n');
	}
	
	if (issues.some(function(i) { return i.indexOf('event listenery') !== -1; })) {
		console.log('4. Event listenery nie są przypisane:');
		console.log('   - Sprawdź czy skrypt paginacji się wykonał');
		console.log('   - Sprawdź czy nie ma błędów JavaScript w konsoli');
		console.log('   - Spróbuj odświeżyć stronę (Ctrl+F5)\n');
	}
	
	console.log('========================================\n');
	
	// Zwróć obiekt z wynikami dla dalszej analizy
	return {
		issues: issues,
		info: info,
		paginationElements: $pagination.length,
		jQueryAvailable: typeof jQuery !== 'undefined',
		scriptLoaded: paginationScriptLoaded
	};
})();


