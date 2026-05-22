( function () {
	'use strict';

	function normalizePathname( pathname ) {
		var normalized = pathname.replace( /\/+$/, '' );

		return normalized || '/';
	}

	function isSamePageHashLink( link ) {
		var href = link.getAttribute( 'href' );

		if ( ! href || href.charAt( 0 ) === '#' ) {
			return href && href.length > 1;
		}

		try {
			var targetUrl = new URL( href, window.location.href );

			if ( ! targetUrl.hash ) {
				return false;
			}

			return (
				targetUrl.origin === window.location.origin &&
				normalizePathname( targetUrl.pathname ) === normalizePathname( window.location.pathname ) &&
				targetUrl.search === window.location.search
			);
		} catch ( error ) {
			return false;
		}
	}

	function getHashFromLink( link ) {
		var href = link.getAttribute( 'href' );

		if ( ! href ) {
			return '';
		}

		if ( href.charAt( 0 ) === '#' ) {
			return href;
		}

		try {
			return new URL( href, window.location.href ).hash;
		} catch ( error ) {
			return '';
		}
	}

	function closeSidebar( sidebarGroup ) {
		if ( ! sidebarGroup ) {
			return;
		}

		sidebarGroup.classList.remove( 'ekit_isActive' );

		if ( ! document.querySelector( '.ekit-sidebar-group.ekit_isActive' ) ) {
			document.body.style.overflow = '';
		}
	}

	function scrollToHashTarget( hash ) {
		var targetId;
		var target;

		if ( ! hash || hash.length < 2 ) {
			return;
		}

		targetId = decodeURIComponent( hash.slice( 1 ) );
		target = document.getElementById( targetId );

		if ( ! target && document.getElementsByName( targetId ).length ) {
			target = document.getElementsByName( targetId )[0];
		}

		if ( ! target ) {
			window.location.hash = hash;
			return;
		}

		if ( window.history && typeof window.history.pushState === 'function' ) {
			window.history.pushState( null, '', hash );
		} else {
			window.location.hash = hash;
		}

		window.setTimeout( function () {
			target.scrollIntoView( {
				behavior: 'smooth',
				block: 'start'
			} );
		}, 50 );
	}

	document.addEventListener( 'click', function ( event ) {
		var link = event.target.closest( '.ekit-sidebar-group a[href]' );
		var sidebarGroup;
		var hash;

		if ( ! link || ! isSamePageHashLink( link ) ) {
			return;
		}

		sidebarGroup = link.closest( '.ekit-sidebar-group.ekit_isActive' );

		if ( ! sidebarGroup ) {
			return;
		}

		hash = getHashFromLink( link );

		if ( ! hash ) {
			return;
		}

		event.preventDefault();
		closeSidebar( sidebarGroup );
		scrollToHashTarget( hash );
	}, true );

	document.addEventListener( 'click', function ( event ) {
		var indicator = event.target.closest( '.elementskit-submenu-indicator, .ekit-submenu-indicator-icon' );
		var link = event.target.closest( '.elementskit-navbar-nav li a[href]' );
		var menuWidget;
		var menuContainer;
		var closeButton;

		if ( indicator || ! link || ! isSamePageHashLink( link ) ) {
			return;
		}

		menuWidget = link.closest( '.ekit-wid-con' );
		menuContainer = link.closest( '.elementskit-menu-container' );

		if ( ! menuWidget || ! menuContainer || ! menuWidget.querySelector( '.elementskit-menu-offcanvas-elements.active' ) ) {
			return;
		}

		closeButton = menuWidget.querySelector( '.elementskit-menu-close.elementskit-menu-toggler' );

		window.setTimeout( function () {
			if ( closeButton ) {
				closeButton.click();
				return;
			}

			menuWidget.querySelectorAll( '.elementskit-menu-offcanvas-elements.active' ).forEach( function ( element ) {
				element.classList.remove( 'active' );
			} );
		}, 0 );
	}, true );
}() );
