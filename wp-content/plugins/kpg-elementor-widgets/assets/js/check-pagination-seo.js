/**
 * KPG Pagination SEO Checker
 * 
 * Skrypt do konsoli przeglądarki - sprawdza canonical, prev, next linki
 * 
 * Użycie: Skopiuj i wklej do konsoli przeglądarki (F12)
 */

(function() {
	console.log('%c=== KPG PAGINATION SEO CHECKER ===', 'color: #f8ff46; font-size: 16px; font-weight: bold; background: #404848; padding: 5px;');
	console.log('');
	
	// Znajdź wszystkie linki SEO
	var canonical = document.querySelector('link[rel="canonical"]');
	var prev = document.querySelector('link[rel="prev"]');
	var next = document.querySelector('link[rel="next"]');
	
	// Pobierz informacje o paginacji z URL
	var url = new URL(window.location.href);
	var pathname = url.pathname;
	var paged = url.searchParams.get('paged') || (pathname.match(/\/page\/(\d+)/) ? pathname.match(/\/page\/(\d+)/)[1] : '1');
	var currentPage = parseInt(paged) || 1;
	
	// Sprawdź typ strony
	var pageType = 'Unknown';
	if (pathname.match(/^\/blog/)) {
		pageType = 'Blog Archive';
	} else if (pathname.match(/^\/category\//)) {
		pageType = 'Category Archive';
	} else if (pathname.match(/^\/tag\//)) {
		pageType = 'Tag Archive';
	} else if (pathname.match(/^\/autor\//) || pathname.match(/^\/author\//)) {
		pageType = 'Author Archive';
	} else if (url.searchParams.get('s')) {
		pageType = 'Search Results';
	}
	
	// Wyświetl informacje o stronie
	console.log('%c📄 INFORMACJE O STRONIE', 'color: #55a2fb; font-weight: bold;');
	console.log('Typ strony:', pageType);
	console.log('URL:', window.location.href);
	console.log('Aktualna strona:', currentPage);
	console.log('');
	
	// Sprawdź canonical
	console.log('%c🔗 CANONICAL LINK', 'color: #55a2fb; font-weight: bold;');
	if (canonical) {
		var canonicalHref = canonical.getAttribute('href');
		console.log('✅ Znaleziono:', canonicalHref);
		
		// Sprawdź czy canonical wskazuje na aktualną stronę
		var canonicalUrl = new URL(canonicalHref, window.location.origin);
		var currentUrl = new URL(window.location.href);
		
		// Porównaj pathname i query params (bez paged)
		var canonicalPath = canonicalUrl.pathname.replace(/\/page\/\d+\/?$/, '');
		var currentPath = currentUrl.pathname.replace(/\/page\/\d+\/?$/, '');
		
		if (canonicalPath === currentPath) {
			// Sprawdź paginację
			var canonicalPaged = canonicalUrl.pathname.match(/\/page\/(\d+)/) ? canonicalUrl.pathname.match(/\/page\/(\d+)/)[1] : (canonicalUrl.searchParams.get('paged') || '1');
			if (parseInt(canonicalPaged) === currentPage) {
				console.log('✅ Canonical wskazuje na aktualną stronę');
			} else {
				console.warn('⚠️ Canonical wskazuje na inną stronę paginacji:', canonicalPaged, 'vs', currentPage);
			}
		} else {
			console.warn('⚠️ Canonical path różni się:', canonicalPath, 'vs', currentPath);
		}
	} else {
		console.error('❌ BRAK canonical link!');
	}
	console.log('');
	
	// Sprawdź prev
	console.log('%c⬅️ PREV LINK', 'color: #55a2fb; font-weight: bold;');
	if (prev) {
		var prevHref = prev.getAttribute('href');
		console.log('✅ Znaleziono:', prevHref);
		
		if (currentPage > 1) {
			console.log('✅ Prev link jest obecny (strona > 1)');
			
			// Sprawdź czy prev wskazuje na poprzednią stronę
			var prevUrl = new URL(prevHref, window.location.origin);
			var prevPaged = prevUrl.pathname.match(/\/page\/(\d+)/) ? prevUrl.pathname.match(/\/page\/(\d+)/)[1] : (prevUrl.searchParams.get('paged') || '1');
			
			if (parseInt(prevPaged) === currentPage - 1) {
				console.log('✅ Prev wskazuje na poprzednią stronę:', prevPaged);
			} else {
				console.warn('⚠️ Prev wskazuje na niepoprawną stronę:', prevPaged, 'oczekiwano:', currentPage - 1);
			}
		} else {
			console.warn('⚠️ Prev link jest obecny, ale jesteśmy na stronie 1');
		}
	} else {
		if (currentPage > 1) {
			console.error('❌ BRAK prev link (powinien być na stronie > 1)!');
		} else {
			console.log('ℹ️ Brak prev link (OK - jesteśmy na stronie 1)');
		}
	}
	console.log('');
	
	// Sprawdź next
	console.log('%c➡️ NEXT LINK', 'color: #55a2fb; font-weight: bold;');
	if (next) {
		var nextHref = next.getAttribute('href');
		console.log('✅ Znaleziono:', nextHref);
		
		// Sprawdź czy są więcej stron (nie możemy tego sprawdzić bez max_num_pages)
		console.log('ℹ️ Next link jest obecny');
		
		// Sprawdź czy next wskazuje na następną stronę
		var nextUrl = new URL(nextHref, window.location.origin);
		var nextPaged = nextUrl.pathname.match(/\/page\/(\d+)/) ? nextUrl.pathname.match(/\/page\/(\d+)/)[1] : (nextUrl.searchParams.get('paged') || '2');
		
		if (parseInt(nextPaged) === currentPage + 1) {
			console.log('✅ Next wskazuje na następną stronę:', nextPaged);
		} else {
			console.warn('⚠️ Next wskazuje na niepoprawną stronę:', nextPaged, 'oczekiwano:', currentPage + 1);
		}
	} else {
		console.log('ℹ️ Brak next link (możliwe że to ostatnia strona)');
	}
	console.log('');
	
	// Podsumowanie
	console.log('%c📊 PODSUMOWANIE', 'color: #f8ff46; font-weight: bold; background: #404848; padding: 5px;');
	var summary = [];
	if (canonical) summary.push('✅ Canonical');
	else summary.push('❌ Canonical');
	
	if (currentPage > 1) {
		if (prev) summary.push('✅ Prev');
		else summary.push('❌ Prev');
	}
	
	if (next) summary.push('✅ Next');
	else summary.push('ℹ️ Next (może być ostatnia strona)');
	
	console.log(summary.join(' | '));
	console.log('');
	console.log('%c=== KONIEC SPRAWDZENIA ===', 'color: #f8ff46; font-size: 12px;');
	
	// Zwróć obiekt z wynikami (można użyć w kodzie)
	return {
		canonical: canonical ? canonical.getAttribute('href') : null,
		prev: prev ? prev.getAttribute('href') : null,
		next: next ? next.getAttribute('href') : null,
		currentPage: currentPage,
		pageType: pageType
	};
})();

