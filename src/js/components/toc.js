/**
 * Table of Contents — toc.js
 * - Scroll spy: highlights the currently visible heading
 * - Smooth scrolls to heading on link click
 * - Auto-scrolls the TOC itself so active item stays visible
 * Included via Vite to dist/js/main.js
 */
(function () {
  "use strict";

  // How many px from the top of viewport to trigger "active"
  // Should roughly match your sticky nav height
  var OFFSET = 100;

  function ready(fn) {
    if (document.readyState !== "loading") fn();
    else document.addEventListener("DOMContentLoaded", fn);
  }

  ready(function () {
    var toc = document.getElementById("ntc-toc");
    var links = toc
      ? Array.prototype.slice.call(toc.querySelectorAll(".ntc-toc__link"))
      : [];

    if (!toc || links.length === 0) return;

    // Build list of target heading elements
    var targets = links
      .map(function (link) {
        var id = link.getAttribute("data-target");
        return id ? document.getElementById(id) : null;
      })
      .filter(Boolean);

    if (targets.length === 0) return;

    /* ── Smooth scroll on click ── */
    links.forEach(function (link) {
      link.addEventListener("click", function (e) {
        e.preventDefault();
        var id = link.getAttribute("data-target");
        var target = document.getElementById(id);
        if (!target) return;

        var top =
          target.getBoundingClientRect().top + window.pageYOffset - OFFSET + 10;
        window.scrollTo({ top: top, behavior: "smooth" });

        // Update URL hash without jumping
        if (history.pushState) {
          history.pushState(null, null, "#" + id);
        }
      });
    });

    /* ── Scroll spy using IntersectionObserver ── */
    var activeIndex = -1;

    function setActive(index) {
      if (index === activeIndex) return;
      activeIndex = index;

      links.forEach(function (l) {
        l.classList.remove("is-active");
      });

      if (index >= 0 && links[index]) {
        links[index].classList.add("is-active");
        scrollTocToActive(links[index]);
      }
    }

    // Scroll the TOC sidebar so the active link is visible
    function scrollTocToActive(activeLink) {
      var tocRect = toc.getBoundingClientRect();
      var linkRect = activeLink.getBoundingClientRect();
      var relativeTop = linkRect.top - tocRect.top + toc.scrollTop;
      var center = relativeTop - toc.clientHeight / 2 + linkRect.height / 2;
      toc.scrollTo({ top: center, behavior: "smooth" });
    }

    // Use IntersectionObserver if available (modern browsers)
    if ("IntersectionObserver" in window) {
      var visibleMap = {};

      var observer = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            visibleMap[entry.target.id] = entry.isIntersecting;
          });

          // Find the first visible heading
          var firstVisible = -1;
          targets.forEach(function (target, i) {
            if (visibleMap[target.id] && firstVisible === -1) {
              firstVisible = i;
            }
          });

          if (firstVisible !== -1) {
            setActive(firstVisible);
          }
        },
        {
          rootMargin: "-" + OFFSET + "px 0px -60% 0px",
          threshold: 0,
        },
      );

      targets.forEach(function (t) {
        observer.observe(t);
      });
    } else {
      // Fallback: scroll event for older browsers
      var ticking = false;

      window.addEventListener("scroll", function () {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(function () {
          ticking = false;

          var scrollTop =
            window.pageYOffset || document.documentElement.scrollTop;
          var current = 0;

          targets.forEach(function (target, i) {
            var top = target.getBoundingClientRect().top + window.pageYOffset;
            if (scrollTop >= top - OFFSET - 10) {
              current = i;
            }
          });

          setActive(current);
        });
      });

      // Trigger once on load
      window.dispatchEvent(new Event("scroll"));
    }
  });
})();
