(function () {
  'use strict';

  function getMobileRoot(widget) {
    if (!widget) {
      return null;
    }
    return widget._kpgMobileRoot || widget.querySelector('.kpg-menu-mobile');
  }

  function hoistMobileRoot(widget) {
    if (!widget) {
      return;
    }
    var mobileRoot = widget.querySelector('.kpg-menu-mobile');
    if (!mobileRoot) {
      return;
    }
    if (mobileRoot.dataset.kpgHoisted === '1') {
      widget._kpgMobileRoot = mobileRoot;
      return;
    }
    mobileRoot.dataset.kpgHoisted = '1';
    mobileRoot.dataset.kpgWidgetId = widget.id || '';
    document.body.appendChild(mobileRoot);
    widget._kpgMobileRoot = mobileRoot;
  }

  function setPanelHeight(element) {
    if (!element) {
      return;
    }
    element.style.setProperty('--kpg-menu-panel-height', element.scrollHeight + 'px');
  }

  function isNestedDesktopDropdown(element) {
    var parentItem = element ? element.parentElement : null;
    var parentList = parentItem ? parentItem.parentElement : null;

    return !!(
      parentList &&
      parentList.classList &&
      parentList.classList.contains('kpg-menu-desktop-submenu')
    );
  }

  function syncDesktopDropdownState(element, open) {
    if (!element || !element.classList || !element.classList.contains('kpg-menu-desktop-dropdown')) {
      return;
    }

    var isNested = isNestedDesktopDropdown(element);

    element.style.display = 'block';
    element.style.opacity = open ? '1' : '0';
    element.style.visibility = open ? 'visible' : 'hidden';
    element.style.pointerEvents = open ? 'auto' : 'none';
    element.style.transform = open ? 'translateY(0)' : 'none';

    if (isNested) {
      element.style.left = '0';
      element.style.marginTop = '0';
      element.style.zIndex = '2147483402';
      if (open) {
        element.style.position = 'relative';
        element.style.top = 'auto';
        element.style.paddingTop = '8px';
      } else {
        element.style.position = 'absolute';
        element.style.top = '100%';
        element.style.paddingTop = '4px';
      }
      return;
    }

    element.style.position = 'absolute';
    element.style.top = '100%';
    element.style.left = '0';
    element.style.right = 'auto';
    element.style.zIndex = '2147483401';
  }

  function getMobilePanel(root) {
    if (!root) {
      return null;
    }
    return root.querySelector('.kpg-menu-mobile-panel');
  }

  function getMobilePanelViewport(panel) {
    if (!panel) {
      return null;
    }

    var panelRect = panel.getBoundingClientRect();
    var viewportTop = panelRect.top;
    var stickyHeader = panel.querySelector('.kpg-menu-mobile-panel-header');

    if (stickyHeader) {
      var stickyHeaderRect = stickyHeader.getBoundingClientRect();
      viewportTop = Math.max(viewportTop, stickyHeaderRect.bottom);
    }

    return {
      top: viewportTop,
      bottom: panelRect.bottom
    };
  }

  function getDirectLevel2List(submenu) {
    if (!submenu) {
      return null;
    }

    var firstChild = submenu.firstElementChild;
    if (
      firstChild &&
      firstChild.classList &&
      firstChild.classList.contains('kpg-menu-mobile-list') &&
      firstChild.classList.contains('level-2')
    ) {
      return firstChild;
    }

    return null;
  }

  function scrollMobilePanel(panel, delta) {
    if (!panel || !delta) {
      return;
    }

    if (typeof panel.scrollBy === 'function') {
      panel.scrollBy({
        top: delta,
        behavior: 'smooth'
      });
      return;
    }

    panel.scrollTop += delta;
  }

  function syncOpenMobileAncestorHeights(element) {
    var current = element ? element.parentElement : null;
    while (current) {
      if (
        current.classList &&
        current.classList.contains('kpg-menu-mobile-submenu') &&
        current.classList.contains('is-open')
      ) {
        setPanelHeight(current);
      }
      current = current.parentElement;
    }
  }

  function syncLevel2ListMaxHeight(root, submenu) {
    var panel = getMobilePanel(root);
    var list = getDirectLevel2List(submenu);
    var toggle = findMobileSubmenuToggle(root, submenu);
    var row = toggle ? toggle.closest('.kpg-menu-mobile-item-row') : null;

    if (!panel || !list) {
      return;
    }

    // Override stale cached UCSS on production until LiteSpeed/QUIC.cloud regenerates.
    list.style.setProperty('height', 'auto', 'important');
    list.style.setProperty('overflow-x', 'hidden', 'important');
    list.style.setProperty('overflow-y', 'auto', 'important');
    list.style.setProperty('-webkit-overflow-scrolling', 'touch', 'important');
    list.style.setProperty('overscroll-behavior', 'auto', 'important');
    list.style.setProperty('touch-action', 'pan-y', 'important');
    list.style.setProperty('padding-top', '6px', 'important');
    list.style.setProperty('padding-right', '0px', 'important');
    list.style.setProperty('padding-bottom', '12px', 'important');
    list.style.setProperty('padding-left', '0px', 'important');
    list.style.setProperty('scroll-padding-top', '6px', 'important');
    list.style.setProperty('scroll-padding-bottom', '12px', 'important');

    var panelViewport = getMobilePanelViewport(panel);
    var listRect = list.getBoundingClientRect();
    var rowRect = row ? row.getBoundingClientRect() : null;
    var bottomPadding = Math.max(56, panel.clientHeight * 0.1);
    var listStart = listRect.top;

    if (rowRect) {
      listStart = Math.max(listStart, rowRect.bottom);
    }

    if (panelViewport && listStart < panelViewport.top) {
      listStart = panelViewport.top;
    }

    var availableHeight = panelViewport
      ? Math.floor(panelViewport.bottom - listStart - bottomPadding)
      : Math.floor(panel.clientHeight - bottomPadding);

    if (availableHeight > 0) {
      list.style.setProperty('--kpg-menu-level-2-max-height', availableHeight + 'px');
      list.style.setProperty('max-height', availableHeight + 'px', 'important');
      setPanelHeight(submenu);
      syncOpenMobileAncestorHeights(submenu);
    }
  }

  function resetLevel2ListScroll(submenu) {
    var list = getDirectLevel2List(submenu);

    if (!list) {
      return;
    }

    list.scrollTop = 0;
  }

  function findMobileSubmenuToggle(root, submenu) {
    if (!root || !submenu || !submenu.id) {
      return null;
    }

    var toggles = root.querySelectorAll('.kpg-menu-mobile-submenu-toggle[aria-controls]');
    for (var i = 0; i < toggles.length; i += 1) {
      if (toggles[i].getAttribute('aria-controls') === submenu.id) {
        return toggles[i];
      }
    }

    return null;
  }

  function closeMobileSubmenuBranch(root, submenu) {
    if (!root || !submenu) {
      return;
    }

    Array.prototype.slice
      .call(submenu.querySelectorAll('.kpg-menu-mobile-submenu.is-open'))
      .reverse()
      .forEach(function (nestedSubmenu) {
        var nestedToggle = findMobileSubmenuToggle(root, nestedSubmenu);
        if (nestedToggle) {
          nestedToggle.setAttribute('aria-expanded', 'false');
        }
        setExpandableState(nestedSubmenu, false);
      });

    var toggle = findMobileSubmenuToggle(root, submenu);
    if (toggle) {
      toggle.setAttribute('aria-expanded', 'false');
    }

    setExpandableState(submenu, false);
  }

  function closeOtherOpenLevel2Submenus(root, activeSubmenu) {
    if (!root || !activeSubmenu || !getDirectLevel2List(activeSubmenu)) {
      return;
    }

    root.querySelectorAll('.kpg-menu-mobile-submenu.is-open').forEach(function (submenu) {
      if (submenu === activeSubmenu || !getDirectLevel2List(submenu)) {
        return;
      }

      closeMobileSubmenuBranch(root, submenu);
    });
  }

  function keepMobileSubmenuReachable(root, toggle, submenu) {
    var panel = getMobilePanel(root);
    var row = toggle ? toggle.closest('.kpg-menu-mobile-item-row') : null;

    if (!panel || !row || !submenu) {
      return;
    }

    window.requestAnimationFrame(function () {
      window.requestAnimationFrame(function () {
        syncLevel2ListMaxHeight(root, submenu);
        syncOpenMobileAncestorHeights(submenu);

        var panelRect = panel.getBoundingClientRect();
        var rowRect = row.getBoundingClientRect();
        var rowTargetTop = panelRect.top + Math.min(160, Math.max(96, panel.clientHeight * 0.24));
        var rowNeedsLift = rowRect.top > rowTargetTop;
        var didScroll = false;

        if (rowNeedsLift) {
          scrollMobilePanel(panel, rowRect.top - rowTargetTop);
          didScroll = true;
        } else {
          var submenuRect = submenu.getBoundingClientRect();
          var bottomPadding = Math.max(40, panel.clientHeight * 0.12);
          var availableBelowRow = panelRect.bottom - rowRect.bottom - bottomPadding;

          if (
            submenu.scrollHeight <= availableBelowRow &&
            submenuRect.bottom > panelRect.bottom - bottomPadding
          ) {
            scrollMobilePanel(panel, submenuRect.bottom - (panelRect.bottom - bottomPadding));
            didScroll = true;
          }
        }

        if (didScroll) {
          window.setTimeout(function () {
            syncLevel2ListMaxHeight(root, submenu);
            syncOpenMobileAncestorHeights(submenu);
          }, 240);
        } else {
          syncLevel2ListMaxHeight(root, submenu);
        }
      });
    });
  }

  function setupMobileLevel2ScrollBridge(root) {
    if (!root || root.dataset.kpgLevel2ScrollBridge === '1') {
      return;
    }

    var lastTouchY = 0;

    root.addEventListener(
      'touchstart',
      function (event) {
        var list = event.target.closest('.kpg-menu-mobile-list.level-2');
        if (!list || !root.contains(list) || !event.touches || event.touches.length !== 1) {
          return;
        }

        lastTouchY = event.touches[0].clientY;
      },
      { passive: true }
    );

    root.addEventListener(
      'touchmove',
      function (event) {
        var list = event.target.closest('.kpg-menu-mobile-list.level-2');
        var panel = getMobilePanel(root);

        if (!list || !root.contains(list) || !panel || !event.touches || event.touches.length !== 1) {
          return;
        }

        var currentTouchY = event.touches[0].clientY;
        var deltaY = lastTouchY - currentTouchY;
        lastTouchY = currentTouchY;

        if (!deltaY) {
          return;
        }

        var maxScrollTop = Math.max(0, list.scrollHeight - list.clientHeight);
        var atTop = list.scrollTop <= 0;
        var atBottom = list.scrollTop >= maxScrollTop - 1;
        var shouldHandOffToPanel = (deltaY < 0 && atTop) || (deltaY > 0 && atBottom) || maxScrollTop === 0;

        if (!shouldHandOffToPanel) {
          return;
        }

        panel.scrollTop += deltaY;
        event.preventDefault();
      },
      { passive: false }
    );

    root.dataset.kpgLevel2ScrollBridge = '1';
  }

  function setExpandableState(element, open) {
    if (!element) {
      return;
    }
    var isMobileRootPanel = element.classList.contains('kpg-menu-mobile-panel');
    var isDesktopDropdown = element.classList.contains('kpg-menu-desktop-dropdown');

    if (isMobileRootPanel) {
      element.style.setProperty('--kpg-menu-panel-height', '100dvh');
      element.style.height = '100dvh';
      element.style.maxHeight = '100dvh';
      element.style.visibility = open ? 'visible' : 'hidden';
      element.style.display = open ? 'block' : 'none';
      element.style.pointerEvents = open ? 'auto' : 'none';
      element.style.transform = open ? 'translateX(0)' : 'translateX(100%)';
      if (open) {
        element.classList.add('is-open');
        element.classList.remove('is-collapsed');
        return;
      }
      element.classList.remove('is-open');
      element.classList.add('is-collapsed');
      return;
    }

    if (open) {
      setPanelHeight(element);
      if (isDesktopDropdown) {
        syncDesktopDropdownState(element, true);
      }
      element.classList.add('is-open');
      element.classList.remove('is-collapsed');
      return;
    }
    if (isDesktopDropdown) {
      syncDesktopDropdownState(element, false);
    }
    element.classList.remove('is-open');
    element.classList.add('is-collapsed');
    element.style.setProperty('--kpg-menu-panel-height', '0px');
  }

  function closeDesktopMenus(widget) {
    widget.querySelectorAll('.kpg-menu-item.is-open').forEach(function (item) {
      closeDesktopItem(widget, item);
    });
  }

  function closeTopLevelDesktopMenus(widget) {
    widget.querySelectorAll('.kpg-menu-desktop-main > .kpg-menu-desktop-list > .kpg-menu-item.is-open').forEach(function (item) {
      closeDesktopItem(widget, item);
    });
  }

	  function setupScrollReveal(widget) {
	    if (!widget || widget.dataset.kpgScrollInit === '1') {
	      return;
	    }
	    widget.dataset.kpgScrollInit = '1';
	    var desktopMq = window.matchMedia('(min-width: 1025px)');
	    widget.classList.add('kpg-scroll-enhanced');
    
	    var getScrollY = function () {
	      return window.scrollY || window.pageYOffset || 0;
	    };
    
	    var DESKTOP_TOP_THRESHOLD = 4;
	    var DESKTOP_HIDE_THRESHOLD = 140;
	    var DESKTOP_DELTA_THRESHOLD = 6;
	    var MOBILE_THRESHOLD = 140;
    
	    var lastY = getScrollY();
	    var ticking = false;
	    var desktopState = 'top';
	    var mobileHidden = false;
	    var spacer = null;
    
	    var ensureSpacer = function () {
	      if (spacer) {
	        return spacer;
	      }
      spacer = document.createElement('div');
      spacer.className = 'kpg-menu-scroll-spacer';
      spacer.setAttribute('aria-hidden', 'true');
      spacer.style.display = 'none';
	      widget.parentNode.insertBefore(spacer, widget.nextSibling);
	      return spacer;
	    };

	    var measureDesktopGeometry = function () {
	      var reference = spacer && spacer.style.display !== 'none' ? spacer : widget;
	      var rect = reference.getBoundingClientRect();
	      widget.style.setProperty('--kpg-menu-fixed-top', Math.max(rect.top, 0).toFixed(3) + 'px');
	      widget.style.setProperty('--kpg-menu-fixed-left', Math.max(rect.left, 0).toFixed(3) + 'px');
	      widget.style.setProperty('--kpg-menu-fixed-width', Math.max(rect.width, 0).toFixed(3) + 'px');
	    };
    
	    var syncSpacer = function (enabled) {
	      var currentSpacer = ensureSpacer();
	      if (!enabled) {
        currentSpacer.style.display = 'none';
        currentSpacer.style.height = '0px';
        return;
      }
	      currentSpacer.style.display = 'block';
	      currentSpacer.style.height = widget.offsetHeight + 'px';
	    };
    
	    var applyDesktop = function (state) {
	      widget.classList.add('is-fixed');
	      widget.classList.toggle('is-scroll-hidden', state === 'hidden');
	      widget.classList.toggle('is-scrolled', state !== 'top');
	      widget.classList.toggle('is-at-top', state === 'top');
	      syncSpacer(true);
	      measureDesktopGeometry();
	      desktopState = state;
	    };

	    var resetDesktop = function () {
	      widget.classList.remove('is-fixed', 'is-scroll-hidden', 'is-scrolled', 'is-at-top');
	      syncSpacer(false);
	      desktopState = 'top';
	    };
    
	    var applyMobile = function (hidden) {
	      if (hidden === mobileHidden) {
	        return;
      }
      var mobileRoot = getMobileRoot(widget);
      if (mobileRoot) {
        mobileRoot.classList.toggle('is-scroll-hidden', hidden);
      }
      mobileHidden = hidden;
    };

    var update = function () {
      ticking = false;
      var currentY = getScrollY();
      var delta = currentY - lastY;
      var isMobileOpen = widget.classList.contains('mobile-open');

      if (isMobileOpen) {
        lastY = currentY;
        return;
	      }
    
	      if (desktopMq.matches) {
	        if (currentY <= DESKTOP_TOP_THRESHOLD) {
	          applyDesktop('top');
	        } else if (currentY <= DESKTOP_HIDE_THRESHOLD) {
	          applyDesktop('visible');
	        } else if (delta > DESKTOP_DELTA_THRESHOLD) {
	          applyDesktop('hidden');
	        } else if (delta < -DESKTOP_DELTA_THRESHOLD) {
	          applyDesktop('visible');
	        } else if (desktopState === 'top') {
	          applyDesktop('visible');
	        }
	      } else {
	        resetDesktop();
	        if (currentY <= MOBILE_THRESHOLD) {
	          applyMobile(false);
	        } else if (delta > 6) {
          applyMobile(true);
        } else if (delta < -6) {
          applyMobile(false);
        }
      }

      lastY = currentY;
    };

    window.addEventListener(
      'scroll',
      function () {
        if (ticking) {
          return;
        }
        ticking = true;
        window.requestAnimationFrame(update);
      },
      { passive: true }
    );

	    window.addEventListener(
	      'resize',
	      function () {
	        lastY = getScrollY();
	        if (desktopMq.matches) {
	          syncSpacer(true);
	          measureDesktopGeometry();
	        }
	        window.requestAnimationFrame(update);
	      },
	      { passive: true }
	    );
    
	    widget.classList.remove('is-fixed', 'is-scroll-hidden', 'is-scrolled', 'is-at-top');
	    var mobileRoot = getMobileRoot(widget);
	    if (mobileRoot) {
	      mobileRoot.classList.remove('is-scroll-hidden');
	    }

	    if (desktopMq.matches) {
	      applyDesktop(lastY <= DESKTOP_TOP_THRESHOLD ? 'top' : 'visible');
	    }
	    
	    var onViewportChange = function () {
	      lastY = getScrollY();
	      resetDesktop();
	      var root = getMobileRoot(widget);
	      if (root) {
	        root.classList.remove('is-scroll-hidden');
	      }
	      mobileHidden = false;
	      if (desktopMq.matches) {
	        syncSpacer(true);
	        measureDesktopGeometry();
	      }
	      window.requestAnimationFrame(update);
	    };

    if (typeof desktopMq.addEventListener === 'function') {
      desktopMq.addEventListener('change', onViewportChange);
    } else if (typeof desktopMq.addListener === 'function') {
      desktopMq.addListener(onViewportChange);
    }

    window.requestAnimationFrame(update);
  }

  function setMobileBodyLock(locked) {
    document.documentElement.classList.toggle('kpg-mobile-menu-open', !!locked);
    document.body.classList.toggle('kpg-mobile-menu-open', !!locked);
  }

  function syncBodyLockFromRoots() {
    var hasOpen = !!document.querySelector('.kpg-menu-mobile.mobile-open');
    setMobileBodyLock(hasOpen);
  }

  function closeOtherMobileRoots(activeRoot) {
    document.querySelectorAll('.kpg-menu-mobile').forEach(function (root) {
      if (!root || root === activeRoot) {
        return;
      }
      root.classList.remove('mobile-open');
      root.classList.remove('is-scroll-hidden');
      var toggle = root.querySelector('.kpg-menu-mobile-toggle');
      if (toggle) {
        toggle.setAttribute('aria-expanded', 'false');
      }
      var panel = root.querySelector('.kpg-menu-mobile-panel');
      setExpandableState(panel, false);
      root.querySelectorAll('.kpg-menu-mobile-submenu-toggle[aria-expanded="true"]').forEach(function (btn) {
        btn.setAttribute('aria-expanded', 'false');
        var controls = btn.getAttribute('aria-controls');
        if (!controls) {
          return;
        }
        var submenu = root.querySelector('#' + CSS.escape(controls));
        setExpandableState(submenu, false);
      });
    });
  }

  function closeMobileMenu(widget) {
    var mobileRoot = getMobileRoot(widget);
    if (!mobileRoot) {
      return;
    }
    var toggle = mobileRoot.querySelector('.kpg-menu-mobile-toggle');
    var panel = mobileRoot.querySelector('.kpg-menu-mobile-panel');
    if (toggle) {
      toggle.setAttribute('aria-expanded', 'false');
    }
    setExpandableState(panel, false);
    widget.classList.remove('mobile-open');
    mobileRoot.classList.remove('mobile-open');
    mobileRoot.classList.remove('is-scroll-hidden');
    syncBodyLockFromRoots();
  }

  function closeMobileSubmenus(widget) {
    var mobileRoot = getMobileRoot(widget);
    if (!mobileRoot) {
      return;
    }
    mobileRoot.querySelectorAll('.kpg-menu-mobile-submenu-toggle[aria-expanded="true"]').forEach(function (btn) {
      btn.setAttribute('aria-expanded', 'false');
      var controls = btn.getAttribute('aria-controls');
      if (!controls) {
        return;
      }
      var panel = mobileRoot.querySelector('#' + CSS.escape(controls));
      setExpandableState(panel, false);
    });
  }

  function closeDesktopItem(widget, item) {
    if (!item) {
      return;
    }
    Array.prototype.slice.call(item.querySelectorAll('.kpg-menu-item.is-open')).reverse().forEach(function (child) {
      closeDesktopItem(widget, child);
    });
    delete item.dataset.kpgHoverLocked;
    item.classList.remove('is-open');
    var trigger = item.querySelector(':scope > .kpg-menu-desktop-item-row > .kpg-menu-desktop-trigger');
    if (!trigger) {
      return;
    }
    var triggerVertical = trigger.querySelector('.kpg-menu-plus-vertical');
    if (triggerVertical) {
      triggerVertical.style.opacity = '1';
    }
    trigger.setAttribute('aria-expanded', 'false');
    var controls = trigger.getAttribute('aria-controls');
    if (!controls) {
      return;
    }
    var panel = widget.querySelector('#' + CSS.escape(controls));
    setExpandableState(panel, false);
  }

  function closeDesktopSiblings(widget, item) {
    Array.prototype.forEach.call(item && item.parentElement ? item.parentElement.children : [], function (sibling) {
      if (sibling === item) {
        return;
      }
      closeDesktopItem(widget, sibling);
    });
  }

  function openDesktopItem(widget, item) {
    if (!item) {
      return;
    }

    var trigger = item.querySelector(':scope > .kpg-menu-desktop-item-row > .kpg-menu-desktop-trigger');
    if (!trigger) {
      return;
    }

    var controls = trigger.getAttribute('aria-controls');
    if (!controls) {
      return;
    }

    closeDesktopSiblings(widget, item);

    var panel = widget.querySelector('#' + CSS.escape(controls));
    trigger.setAttribute('aria-expanded', 'true');
    var triggerVertical = trigger.querySelector('.kpg-menu-plus-vertical');
    if (triggerVertical) {
      triggerVertical.style.opacity = '0';
    }
    setExpandableState(panel, true);
    item.classList.add('is-open');

    // Background should align with the full desktop shell, but content should stay under the trigger.
    // We render background via ::before, positioned using CSS vars.
    if (panel && panel.classList.contains('kpg-menu-desktop-dropdown')) {
      var shell = widget.querySelector('.kpg-menu-desktop-shell');
      if (shell) {
        var shellRect = shell.getBoundingClientRect();
        var panelRect = panel.getBoundingClientRect();
        if (shellRect.width && panelRect.width) {
          panel.style.setProperty('--kpg-shell-bg-left', Math.round(shellRect.left - panelRect.left) + 'px');
          panel.style.setProperty('--kpg-shell-bg-width', Math.round(shellRect.width) + 'px');
        }
      }
    }
  }

  function onDesktopTriggerClick(widget, trigger) {
    var item = trigger.closest('.kpg-menu-item');
    if (!item) {
      return;
    }
    delete item.dataset.kpgHoverLocked;
    var nextState = trigger.getAttribute('aria-expanded') !== 'true';
    if (nextState) {
      openDesktopItem(widget, item);
      return;
    }
    // Desktop dropdowns stay open during interaction; outside click/Escape closes them.
    item.dataset.kpgHoverLocked = '1';
  }

  function initWidget(widget) {
    if (!widget || widget.dataset.kpgMenuInit === '1') {
      return;
    }
    widget.dataset.kpgMenuInit = '1';
    widget.classList.remove('mobile-open');
    setMobileBodyLock(false);
    setupScrollReveal(widget);
    hoistMobileRoot(widget);

    // Used by CSS to keep first-level dropdown equal to shell width.
    var shell = widget.querySelector('.kpg-menu-desktop-shell');
    if (shell) {
      widget.style.setProperty(
        '--kpg-menu-shell-width',
        Math.round(shell.getBoundingClientRect().width) + 'px'
      );
    }

    var mobileRoot = getMobileRoot(widget);
    setupMobileLevel2ScrollBridge(mobileRoot);
    var clickHandler = function (event) {
      var desktopTrigger = event.target.closest('.kpg-menu-desktop-trigger');
      if (desktopTrigger && widget.contains(desktopTrigger)) {
        event.preventDefault();
        onDesktopTriggerClick(widget, desktopTrigger);
        return;
      }

      var mobileToggle = event.target.closest('.kpg-menu-mobile-toggle');
      if (mobileToggle && mobileRoot && mobileRoot.contains(mobileToggle)) {
        event.preventDefault();
        var panel = mobileRoot.querySelector('.kpg-menu-mobile-panel');
        var isOpen = mobileToggle.getAttribute('aria-expanded') === 'true';
        var nextOpen = !isOpen;
        if (nextOpen && mobileRoot) {
          closeOtherMobileRoots(mobileRoot);
        }
        mobileToggle.setAttribute('aria-expanded', nextOpen ? 'true' : 'false');
        setExpandableState(panel, nextOpen);
        widget.classList.toggle('mobile-open', nextOpen);
        if (mobileRoot) {
          mobileRoot.classList.toggle('mobile-open', nextOpen);
        }
        if (nextOpen && mobileRoot) {
          mobileRoot.classList.remove('is-scroll-hidden');
        }
        syncBodyLockFromRoots();
        if (!nextOpen) {
          closeMobileSubmenus(widget);
        }
        return;
      }

      var mobileClose = event.target.closest('.kpg-menu-mobile-close');
      if (mobileClose && mobileRoot && mobileRoot.contains(mobileClose)) {
        event.preventDefault();
        closeMobileSubmenus(widget);
        closeMobileMenu(widget);
        return;
      }

      var mobileSubmenuToggle = event.target.closest('.kpg-menu-mobile-submenu-toggle');
      if (mobileSubmenuToggle && mobileRoot && mobileRoot.contains(mobileSubmenuToggle)) {
        event.preventDefault();
        var controls = mobileSubmenuToggle.getAttribute('aria-controls');
        if (!controls) {
          return;
        }
        var submenu = mobileRoot.querySelector('#' + CSS.escape(controls));
        if (!submenu) {
          return;
        }
        var expanded = mobileSubmenuToggle.getAttribute('aria-expanded') === 'true';
        if (!expanded) {
          closeOtherOpenLevel2Submenus(mobileRoot, submenu);
        }
        mobileSubmenuToggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
        setExpandableState(submenu, !expanded);
        if (!expanded) {
          resetLevel2ListScroll(submenu);
          syncLevel2ListMaxHeight(mobileRoot, submenu);
        }
        syncOpenMobileAncestorHeights(submenu);
        window.requestAnimationFrame(function () {
          if (!expanded) {
            syncLevel2ListMaxHeight(mobileRoot, submenu);
          }
          syncOpenMobileAncestorHeights(submenu);
        });
        if (!expanded) {
          window.setTimeout(function () {
            syncLevel2ListMaxHeight(mobileRoot, submenu);
            syncOpenMobileAncestorHeights(submenu);
          }, 360);
        }
        if (!expanded) {
          keepMobileSubmenuReachable(mobileRoot, mobileSubmenuToggle, submenu);
        }
        return;
      }

      var mobileLink = event.target.closest('.kpg-menu-mobile-link');
      if (mobileLink && mobileRoot && mobileRoot.contains(mobileLink)) {
        closeMobileSubmenus(widget);
        closeMobileMenu(widget);
      }
    };

    widget.addEventListener('click', clickHandler);
    if (mobileRoot && !widget.contains(mobileRoot)) {
      mobileRoot.addEventListener('click', clickHandler);
    }

    widget.querySelectorAll('.kpg-menu-desktop-dropdown').forEach(function (panel) {
      if (panel.classList.contains('is-open')) {
        setPanelHeight(panel);
      } else {
        setExpandableState(panel, false);
      }
    });
    if (mobileRoot) {
      mobileRoot.querySelectorAll('.kpg-menu-mobile-panel, .kpg-menu-mobile-submenu').forEach(function (panel) {
        if (panel.classList.contains('is-open')) {
          setPanelHeight(panel);
        } else {
          setExpandableState(panel, false);
        }
      });
    }

	    if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
	      widget.querySelectorAll('.kpg-menu-desktop-main > .kpg-menu-desktop-list > .kpg-menu-item.has-children').forEach(function (item) {
	        item.addEventListener('mouseenter', function () {
	          if (!window.matchMedia('(min-width: 1025px)').matches) {
	            return;
	          }
	          openDesktopItem(widget, item);
	        });
	      });

	      widget.querySelectorAll('.kpg-menu-desktop-submenu > .kpg-menu-item.has-children').forEach(function (item) {
	        item.addEventListener('mouseenter', function () {
	          if (!window.matchMedia('(min-width: 1025px)').matches) {
	            return;
	          }
	          item.dataset.kpgHoverLocked = '1';
	          openDesktopItem(widget, item);
	        });
	      });

	    }

    document.addEventListener('click', function (event) {
      var target = event.target;
      var isInsideWidget = widget.contains(target);
      var isInsideMobileRoot = mobileRoot && mobileRoot.contains(target);

      if (!isInsideWidget && !isInsideMobileRoot) {
        closeDesktopMenus(widget);
      }
    });

    document.addEventListener('keydown', function (event) {
      if (event.key !== 'Escape') {
        return;
      }
      closeDesktopMenus(widget);
      closeMobileSubmenus(widget);
      closeMobileMenu(widget);
    });

    window.addEventListener('resize', function () {
      if (window.matchMedia('(min-width: 1025px)').matches) {
        closeMobileSubmenus(widget);
        closeMobileMenu(widget);
      }
      if (mobileRoot) {
        mobileRoot.querySelectorAll('.kpg-menu-mobile-panel.is-open, .kpg-menu-mobile-submenu.is-open').forEach(function (panel) {
          setPanelHeight(panel);
          syncOpenMobileAncestorHeights(panel);
          syncLevel2ListMaxHeight(mobileRoot, panel);
        });
      }
      if (window.matchMedia('(min-width: 1025px)').matches && widget.classList.contains('is-fixed')) {
        var currentSpacer = widget.nextElementSibling;
        if (currentSpacer && currentSpacer.classList.contains('kpg-menu-scroll-spacer')) {
          currentSpacer.style.height = widget.offsetHeight + 'px';
        }
      }
    });
  }

  function initAllWidgets() {
    document.querySelectorAll('.kpg-menu-widget').forEach(initWidget);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllWidgets);
  } else {
    initAllWidgets();
  }

  if (
    typeof elementorFrontend !== 'undefined' &&
    elementorFrontend.hooks &&
    typeof elementorFrontend.hooks.addAction === 'function'
  ) {
    elementorFrontend.hooks.addAction('frontend/element_ready/kpg-menu.default', function (scope) {
      scope[0].querySelectorAll('.kpg-menu-widget').forEach(initWidget);
    });
  }
})();
