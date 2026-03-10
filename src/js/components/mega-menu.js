jQuery(function ($) {
  const MOBILE_BP = 900;
  const CLOSE_DELAY = 250;

  const $nav = $("#smh-nav");
  const $menu = $nav.find(".smh-nav__menu");
  const $burger = $("#smh-burger");
  const $overlay = $("#smh-overlay");
  const $megaItems = $nav.find(".smh-nav__item.has-mega");

  const closeTimers = new Map();

  function openMega($item) {
    $megaItems.not($item).each(function () {
      closeMegaNow($(this));
    });
    clearTimer($item);
    $item.addClass("is-open");
    $item.find(".smh-nav__link").attr("aria-expanded", "true");
  }

  function closeMegaLater($item) {
    clearTimer($item);
    const t = setTimeout(function () {
      closeMegaNow($item);
    }, CLOSE_DELAY);
    closeTimers.set($item[0], t);
  }

  function closeMegaNow($item) {
    clearTimer($item);
    $item.removeClass("is-open");
    $item.find(".smh-nav__link").attr("aria-expanded", "false");
  }

  function clearTimer($item) {
    if (closeTimers.has($item[0])) {
      clearTimeout(closeTimers.get($item[0]));
      closeTimers.delete($item[0]);
    }
  }

  function isMobile() {
    return $(window).width() <= MOBILE_BP;
  }

  $megaItems.each(function () {
    const $item = $(this);
    const $link = $item.find(".smh-nav__link");
    const $mega = $item.find(".smh-mega");

    $item
      .on("mouseenter", function () {
        if (isMobile()) return;
        openMega($item);
      })
      .on("mouseleave", function () {
        if (isMobile()) return;
        closeMegaLater($item);
      });

    if ($mega.length) {
      $mega
        .on("mouseenter", function () {
          if (isMobile()) return;
          clearTimer($item);
        })
        .on("mouseleave", function () {
          if (isMobile()) return;
          closeMegaLater($item);
        });
    }

    if ($link.length) {
      $link.on("click", function (e) {
        if (!isMobile()) return;
        e.preventDefault();
        if ($item.hasClass("is-open")) {
          closeMegaNow($item);
        } else {
          openMega($item);
        }
      });

      $link.on("keydown", function (e) {
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          $item.hasClass("is-open") ? closeMegaNow($item) : openMega($item);
        }
        if (e.key === "Escape") {
          closeMegaNow($item);
          $link.focus();
        }
      });
    }
  });

  $(document).on("keydown", function (e) {
    if (e.key === "Escape") {
      $megaItems.each(function () {
        closeMegaNow($(this));
      });
    }
  });

  $(document).on("click", function (e) {
    $megaItems.each(function () {
      if (!$.contains(this, e.target) && this !== e.target) {
        closeMegaNow($(this));
      }
    });
  });

  function openMobileMenu() {
    $menu.addClass("is-open");
    $burger.addClass("is-open").attr("aria-expanded", "true");
    if ($overlay.length) $overlay.addClass("is-visible");
    $("body").css("overflow", "hidden");
  }

  function closeMobileMenu() {
    $menu.removeClass("is-open");
    $burger.removeClass("is-open").attr("aria-expanded", "false");
    if ($overlay.length) $overlay.removeClass("is-visible");
    $("body").css("overflow", "");
    $megaItems.each(function () {
      closeMegaNow($(this));
    });
  }

  if ($burger.length) {
    $burger.on("click", function () {
      $menu.hasClass("is-open") ? closeMobileMenu() : openMobileMenu();
    });
  }

  if ($overlay.length) {
    $overlay.on("click", closeMobileMenu);
  }

  $(window).on("resize", function () {
    if (!isMobile()) closeMobileMenu();
  });
});
