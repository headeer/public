/**
 * KPG Team Slider JavaScript - Thumbs Gallery Layout
 * 
 * Handles Swiper initialization, thumbnail navigation, and arrow navigation
 */

(function($) {
  'use strict';

  function initTeamSlider($container) {
    if (!$container.length) {
      return;
    }

    var $mainSwiper = $container.find('.kpg-team-slider-main-swiper');
    var $thumbnails = $container.find('.kpg-team-slider-thumb');
    var $slideContents = $container.find('.kpg-team-slider-slide-content');
    var $prevArrow = $container.find('.kpg-team-slider-arrow-prev');
    var $nextArrow = $container.find('.kpg-team-slider-arrow-next');

    if (!$mainSwiper.length) {
      return;
    }

    var swiper = null;

    // Set object-position from data attribute on desktop only
    function setImageObjectPosition() {
      var isDesktop = window.matchMedia('(min-width: 1025px)').matches;
      $container.find('.kpg-team-slider-main-image img[data-object-position]').each(function() {
        var $img = $(this);
        var objectPosition = $img.attr('data-object-position');
        if (isDesktop && objectPosition) {
          $img.css('object-position', objectPosition);
          // Set CSS variable for desktop
          $img[0].style.setProperty('--object-position', objectPosition);
        } else {
          $img.css('object-position', 'center center');
        }
      });
    }

    // Set initial object-position
    setImageObjectPosition();

    // Update on window resize
    $(window).on('resize.teamSlider', function() {
      setImageObjectPosition();
      if (swiper) {
        swiper.update();
      }
    });

    // Update active slide content
    function updateActiveSlide(activeIndex) {
      // Update slide contents
      $slideContents.removeClass('active');
      var $activeContent = $slideContents.eq(activeIndex);
      if ($activeContent.length) {
        $activeContent.addClass('active');
      }

      // Update thumbnails (desktop)
      $thumbnails.removeClass('active');
      var $activeThumb = $thumbnails.eq(activeIndex);
      if ($activeThumb.length) {
        $activeThumb.addClass('active');
      }

      // Reset "See More" button state on slide change (mobile)
      var isMobile = window.matchMedia('(max-width: 1024px)').matches;
      if (isMobile && $activeContent.length) {
        // Only reset right column text content
        var $textContent = $activeContent.find('.kpg-team-slider-text-right .kpg-team-slider-text-content');
        var $btn = $activeContent.find('.kpg-team-slider-see-more-btn');
        if ($textContent.length && $btn.length) {
          // Check if text needs collapsing
          $textContent.removeClass('expanded').addClass('collapsed');
          $btn.attr('aria-expanded', 'false');
          $btn.find('.kpg-team-slider-see-more-text').text('zobacz więcej');
        }
      }
    }

    // Update arrow states
    function updateArrowStates(swiperInstance) {
      if (!swiperInstance) return;

      var isBeginning = swiperInstance.isBeginning;
      var isEnd = swiperInstance.isEnd;

      if ($prevArrow.length) {
        $prevArrow.prop('disabled', isBeginning);
      }
      if ($nextArrow.length) {
        $nextArrow.prop('disabled', isEnd);
      }
    }

    // Initialize Swiper
    function initSwiper() {
      // Check if mobile
      var isMobile = window.matchMedia('(max-width: 1024px)').matches;
      // Mobile: 30px spacing between slides (8.3551vw for 383px base)
      var spaceBetween = isMobile ? 8.3551 : 0;

      // Destroy existing swiper if it exists
      if (swiper && swiper.destroy) {
        swiper.destroy(true, true);
      }

      // Initialize Swiper
      swiper = new Swiper($mainSwiper[0], {
        slidesPerView: 1,
        spaceBetween: spaceBetween,
        loop: false,
        speed: 500,
        effect: 'slide',
        allowTouchMove: true,
        touchStartPreventDefault: false,
        touchMoveStopPropagation: false,
        simulateTouch: true,
        touchEventsTarget: 'wrapper',
        navigation: {
          nextEl: $nextArrow.length ? $nextArrow[0] : null,
          prevEl: $prevArrow.length ? $prevArrow[0] : null,
        },
        on: {
          slideChange: function(swiperInstance) {
            updateActiveSlide(swiperInstance.activeIndex);
            updateArrowStates(swiperInstance);
          },
          init: function(swiperInstance) {
            updateActiveSlide(swiperInstance.activeIndex);
            updateArrowStates(swiperInstance);
          },
        },
      });
    }

    // Initialize Swiper
    initSwiper();

    // Enable touch swipe on content section (mobile only)
    function enableContentSwipe() {
      var isMobile = window.matchMedia('(max-width: 1024px)').matches;
      if (!isMobile || !swiper) {
        return;
      }

      var $contentSection = $container.find('.kpg-team-slider-content-section');
      if (!$contentSection.length) {
        return;
      }

      var startX = 0;
      var startY = 0;
      var minSwipeDistance = 50;

      $contentSection.off('touchstart.contentSwipe touchmove.contentSwipe touchend.contentSwipe');

      $contentSection.on('touchstart.contentSwipe', function(e) {
        var touch = e.originalEvent.touches[0];
        startX = touch.clientX;
        startY = touch.clientY;
      });

      $contentSection.on('touchmove.contentSwipe', function(e) {
        // Allow vertical scrolling
        if (!startX || !startY) {
          return;
        }

        var touch = e.originalEvent.touches[0];
        var diffX = Math.abs(startX - touch.clientX);
        var diffY = Math.abs(startY - touch.clientY);

        // If horizontal movement is greater, prevent default to allow swipe
        if (diffX > diffY && diffX > 10) {
          e.preventDefault();
        }
      });

      $contentSection.on('touchend.contentSwipe', function(e) {
        if (!startX || !startY || !swiper) {
          startX = 0;
          startY = 0;
          return;
        }

        var touch = e.originalEvent.changedTouches[0];
        var diffX = startX - touch.clientX;
        var diffY = startY - touch.clientY;
        var absDiffX = Math.abs(diffX);
        var absDiffY = Math.abs(diffY);

        // If horizontal swipe detected and significant enough
        if (absDiffX > absDiffY && absDiffX > minSwipeDistance) {
          if (diffX > 0) {
            // Swipe left - next slide
            swiper.slideNext();
          } else {
            // Swipe right - prev slide
            swiper.slidePrev();
          }
        }

        startX = 0;
        startY = 0;
      });
    }

    // Enable content swipe after Swiper is initialized
    setTimeout(function() {
      if (swiper) {
        enableContentSwipe();
      }
    }, 100);

    // Update spaceBetween on window resize
    $(window).on('resize.teamSliderSpace', function() {
      var isMobileNow = window.matchMedia('(max-width: 1024px)').matches;
      var newSpaceBetween = isMobileNow ? 8.3551 : 0;
      if (swiper && swiper.params.spaceBetween !== newSpaceBetween) {
        swiper.params.spaceBetween = newSpaceBetween;
        swiper.update();
      }
    });

    // Thumbnail click handler (desktop)
    $thumbnails.on('click', function() {
      var slideIndex = $(this).data('slide-index');
      if (swiper && typeof slideIndex !== 'undefined') {
        swiper.slideTo(slideIndex);
      }
    });

    // Initialize first slide as active
    if ($slideContents.length > 0) {
      updateActiveSlide(0);
    }

    // Initialize "See More" buttons (mobile only)
    function initSeeMoreButtons() {
      var isMobile = window.matchMedia('(max-width: 1024px)').matches;
      if (!isMobile) {
        // On desktop, remove collapsed class
        $container.find('.kpg-team-slider-text-content').removeClass('collapsed expanded');
        $container.find('.kpg-team-slider-see-more-wrapper').hide();
        return;
      }

      $container.find('.kpg-team-slider-slide-content').each(function() {
        var $slideContent = $(this);
        // Only work with right column text content
        var $textContent = $slideContent.find('.kpg-team-slider-text-right .kpg-team-slider-text-content');
        var $btn = $slideContent.find('.kpg-team-slider-see-more-btn');
        var $wrapper = $slideContent.find('.kpg-team-slider-see-more-wrapper');
        
        if ($textContent.length) {
          // Temporarily remove classes to measure full height
          $textContent.removeClass('collapsed expanded');
          var fullHeight = $textContent[0].scrollHeight;
          
          // Check if text needs collapsing (if it's taller than 155px)
          if (fullHeight > 155) {
            // Text is long, show button and collapse
            $textContent.addClass('collapsed');
            $btn.attr('aria-expanded', 'false');
            $btn.find('.kpg-team-slider-see-more-text').text('zobacz więcej');
            $wrapper.show();
          } else {
            // Text is short, hide button and show full text
            $textContent.removeClass('collapsed expanded');
            $wrapper.hide();
          }
        }
      });
    }

    // Handle "See More" button clicks
    $container.on('click', '.kpg-team-slider-see-more-btn', function(e) {
      e.preventDefault();
      e.stopPropagation();
      var $btn = $(this);
      var $slideContent = $btn.closest('.kpg-team-slider-slide-content');
      // Only work with right column text content
      var $textContent = $slideContent.find('.kpg-team-slider-text-right .kpg-team-slider-text-content');
      var $text = $btn.find('.kpg-team-slider-see-more-text');
      var isExpanded = $btn.attr('aria-expanded') === 'true';

      if (isExpanded) {
        // Collapse
        $textContent.removeClass('expanded').addClass('collapsed');
        $btn.attr('aria-expanded', 'false');
        $text.text('zobacz więcej');
      } else {
        // Expand
        $textContent.removeClass('collapsed').addClass('expanded');
        $btn.attr('aria-expanded', 'true');
        $text.text('zobacz mniej');
      }
    });

    // Initialize see more buttons
    initSeeMoreButtons();

    // Re-initialize on slide change
    if (swiper) {
      swiper.on('slideChange', function() {
        setTimeout(function() {
          initSeeMoreButtons();
        }, 100);
      });
    }

    // Re-initialize on window resize
    $(window).on('resize.teamSliderSeeMore', function() {
      initSeeMoreButtons();
    });
  }

  // Initialize on document ready
  $(document).ready(function() {
    $('.kpg-team-slider-container').each(function() {
      initTeamSlider($(this));
    });
  });

  // Re-initialize on Elementor frontend
  if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
    elementorFrontend.hooks.addAction('frontend/element_ready/kpg-team-slider.default', function($scope) {
      var $container = $scope.find('.kpg-team-slider-container');
      if ($container.length) {
        initTeamSlider($container);
      }
    });
  }

})(jQuery);
