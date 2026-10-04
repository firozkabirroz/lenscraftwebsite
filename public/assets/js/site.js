(function () {
    'use strict';

    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    var hasIO = 'IntersectionObserver' in window;

    /* ---------------------------------------------------------------
       Smooth scrolling (Lenis). Everything downstream reads window.scrollY,
       which Lenis keeps authoritative, so observers and the parallax below
       need no special casing. Skipped entirely for reduced-motion users.
       --------------------------------------------------------------- */
    var lenis = null;

    if (!reduceMotion && typeof window.Lenis === 'function') {
        lenis = new window.Lenis({
            duration: 1.05,
            easing: function (t) { return Math.min(1, 1.001 - Math.pow(2, -10 * t)); },
            smoothWheel: true,
            touchMultiplier: 1.6
        });

        window.requestAnimationFrame(function raf(time) {
            lenis.raf(time);
            window.requestAnimationFrame(raf);
        });

        // Anchor links must go through Lenis or they jump while it animates.
        document.addEventListener('click', function (event) {
            var link = event.target.closest('a[href^="#"]');
            if (!link) {
                return;
            }
            var id = link.getAttribute('href');
            if (id.length < 2) {
                return;
            }
            var target = document.querySelector(id);
            if (target) {
                event.preventDefault();
                lenis.scrollTo(target, { offset: -90 });
            }
        });
    }

    function lockScroll() {
        if (lenis) {
            lenis.stop();
        }
        document.body.style.overflow = 'hidden';
    }

    function unlockScroll() {
        if (lenis) {
            lenis.start();
        }
        document.body.style.overflow = '';
    }

    /* --------------------------------------------------------------- nav */
    var nav = document.getElementById('siteNav');
    var links = document.getElementById('navLinks');
    var toggle = document.getElementById('navToggle');

    if (toggle && links) {
        toggle.addEventListener('click', function () {
            var open = links.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    /* -------------------------------------------------- hero + nav state */
    var hero = document.querySelector('.hero');
    var heroBg = hero ? hero.querySelector('.hero__bg') : null;
    var scrollQueued = false;

    function onScrollFrame() {
        scrollQueued = false;
        var y = window.scrollY;

        if (nav) {
            nav.classList.toggle('is-stuck', y > 20);
            // While the nav sits over the darkened hero it needs cream links.
            if (hero) {
                nav.classList.toggle('is-over-hero', y < hero.offsetHeight - 120);
            }
        }

        // Slow zoom + parallax drift on the hero media as the hero scrolls away.
        if (heroBg && !reduceMotion) {
            var h = hero.offsetHeight || 1;
            var p = Math.min(1, Math.max(0, y / h));
            hero.style.setProperty('--hero-p', p.toFixed(4));
        }
    }

    function requestScrollFrame() {
        if (!scrollQueued) {
            scrollQueued = true;
            window.requestAnimationFrame(onScrollFrame);
        }
    }

    window.addEventListener('scroll', requestScrollFrame, { passive: true });
    window.addEventListener('resize', requestScrollFrame, { passive: true });
    onScrollFrame();

    /* ------------------------------------------------------- reel modal */
    var modal = document.getElementById('reelModal');
    var frame = document.getElementById('reelFrame');
    var close = document.getElementById('reelClose');
    var lastFocused = null;

    function openReel(src, type, trackUrl) {
        if (!modal || !frame || !src) {
            return;
        }

        lastFocused = document.activeElement;

        var embedSrc = src + (src.indexOf('?') === -1 ? '?' : '&') + 'autoplay=1';
        frame.innerHTML = type === 'embed'
            ? '<iframe src="' + embedSrc + '" allow="autoplay; fullscreen" allowfullscreen></iframe>'
            : '<video src="' + src + '" controls autoplay playsinline></video>';

        modal.hidden = false;
        lockScroll();

        if (close) {
            close.focus();
        }

        if (trackUrl) {
            fetch(trackUrl, { method: 'POST' }).catch(function () { /* view tracking is best effort */ });
        }
    }

    function closeReel() {
        if (!modal || !frame || modal.hidden) {
            return;
        }
        frame.innerHTML = '';
        modal.hidden = true;
        unlockScroll();

        if (lastFocused && typeof lastFocused.focus === 'function') {
            lastFocused.focus();
        }
    }

    document.querySelectorAll('[data-reel]').forEach(function (button) {
        button.addEventListener('click', function () {
            openReel(button.dataset.embed, button.dataset.type, button.dataset.track);
        });
    });

    if (close) {
        close.addEventListener('click', closeReel);
    }

    if (modal) {
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeReel();
            }
        });
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeReel();
        }
    });

    /* -------------------------------------------------------- preloader */
    var preloader = document.getElementById('preloader');
    if (preloader) {
        var shownAt = Date.now();
        var hide = function () {
            var wait = Math.max(0, 350 - (Date.now() - shownAt));
            window.setTimeout(function () {
                preloader.classList.add('is-done');
                window.setTimeout(function () { preloader.remove(); }, 500);
            }, wait);
        };
        if (document.readyState === 'complete') {
            hide();
        } else {
            window.addEventListener('load', hide);
            window.setTimeout(hide, 2500);
        }
    }

    /* ------------------------------------------------- page transitions */
    document.addEventListener('click', function (event) {
        var link = event.target.closest('a[href]');
        if (!link || event.defaultPrevented || event.metaKey || event.ctrlKey || event.shiftKey || event.button !== 0) {
            return;
        }
        var href = link.getAttribute('href');
        if (link.target === '_blank' || !href || href.charAt(0) === '#' ||
            href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0 ||
            (link.origin && link.origin !== window.location.origin)) {
            return;
        }
        event.preventDefault();
        document.body.classList.add('is-leaving');
        window.setTimeout(function () { window.location = link.href; }, reduceMotion ? 0 : 190);
    });

    /* ------------------------------------------------ section head mask */
    if (hasIO) {
        var headObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                    headObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.4 });

        document.querySelectorAll('.section__head').forEach(function (head) {
            headObserver.observe(head);
        });
    } else {
        document.querySelectorAll('.section__head').forEach(function (head) {
            head.classList.add('in-view');
        });
    }

    /* ------------------------------------------------------- stat count */
    function countUp(element) {
        var match = element.textContent.match(/^(\d+)(.*)$/);
        if (!match || parseInt(match[1], 10) < 10) {
            return;
        }
        var target = parseInt(match[1], 10);
        var suffix = match[2];
        var start = null;

        function tick(now) {
            if (!start) {
                start = now;
            }
            var progress = Math.min(1, (now - start) / 1100);
            var eased = 1 - Math.pow(1 - progress, 3);
            element.textContent = Math.round(target * eased) + suffix;
            if (progress < 1) {
                window.requestAnimationFrame(tick);
            }
        }

        window.requestAnimationFrame(tick);
    }

    if (hasIO && !reduceMotion) {
        var statObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    countUp(entry.target);
                    statObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.6 });

        document.querySelectorAll('.about-teaser__stats strong').forEach(function (stat) {
            statObserver.observe(stat);
        });
    }

    /* --------------------------------------------- work card previews */
    if (finePointer) {
        document.querySelectorAll('.card-work[data-preview]').forEach(function (card) {
            var thumb = card.querySelector('.card-work__thumb');
            if (!thumb) {
                return;
            }

            var player = null;
            var timer = null;

            function startPreview() {
                timer = window.setTimeout(function () {
                    if (player) {
                        return;
                    }
                    if (card.dataset.previewKind === 'video') {
                        player = document.createElement('video');
                        player.src = card.dataset.preview;
                        player.muted = true;
                        player.loop = true;
                        player.playsInline = true;
                        player.autoplay = true;
                        player.preload = 'none';
                    } else {
                        player = document.createElement('iframe');
                        player.src = card.dataset.preview;
                        player.setAttribute('allow', 'autoplay');
                        player.setAttribute('tabindex', '-1');
                        player.setAttribute('aria-hidden', 'true');
                    }
                    player.className = 'card-work__preview';
                    thumb.appendChild(player);
                    window.requestAnimationFrame(function () {
                        if (player) {
                            player.classList.add('is-live');
                        }
                    });
                }, 160);
            }

            function stopPreview() {
                window.clearTimeout(timer);
                if (player) {
                    var old = player;
                    player = null;
                    old.classList.remove('is-live');
                    window.setTimeout(function () { old.remove(); }, 420);
                }
            }

            card.addEventListener('mouseenter', startPreview);
            card.addEventListener('mouseleave', stopPreview);
            // Keyboard users get the same preview when the card takes focus.
            card.addEventListener('focus', startPreview);
            card.addEventListener('blur', stopPreview);
        });
    }

    /* ------------------------------------------------- scroll reveals */
    var revealSelector = '.card-work, .card-discipline, .service, .step, [data-reveal]';

    if (hasIO && !reduceMotion) {
        var revealObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-revealed');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

        document.querySelectorAll(revealSelector).forEach(function (element, index) {
            // Stagger siblings slightly; capped so a long grid never crawls.
            element.style.setProperty('--reveal-delay', Math.min(index % 6, 5) * 70 + 'ms');
            revealObserver.observe(element);
        });
    } else {
        document.querySelectorAll(revealSelector).forEach(function (element) {
            element.classList.add('is-revealed');
        });
    }

    /* ------------------------------------------------------ custom cursor */
    if (finePointer && !reduceMotion) {
        var dot = document.createElement('div');
        dot.className = 'cursor';
        dot.setAttribute('aria-hidden', 'true');
        document.body.appendChild(dot);
        document.body.classList.add('has-cursor');

        var mx = window.innerWidth / 2;
        var my = window.innerHeight / 2;
        var cx = mx;
        var cy = my;
        var seen = false;

        document.addEventListener('mousemove', function (event) {
            mx = event.clientX;
            my = event.clientY;
            if (!seen) {
                seen = true;
                cx = mx;
                cy = my;
                dot.classList.add('is-ready');
            }
        }, { passive: true });

        document.addEventListener('mouseleave', function () { dot.classList.remove('is-ready'); });
        document.addEventListener('mouseenter', function () { dot.classList.add('is-ready'); });

        (function loop() {
            cx += (mx - cx) * 0.18;
            cy += (my - cy) * 0.18;
            dot.style.transform = 'translate3d(' + cx.toFixed(2) + 'px,' + cy.toFixed(2) + 'px,0) translate(-50%,-50%)';
            window.requestAnimationFrame(loop);
        })();

        var hoverables = 'a, button, [role="button"], input, select, textarea, .card-work, [data-cursor]';
        document.addEventListener('mouseover', function (event) {
            if (event.target.closest(hoverables)) {
                dot.classList.add('is-active');
            }
        });
        document.addEventListener('mouseout', function (event) {
            var to = event.relatedTarget;
            if (event.target.closest(hoverables) && !(to && to.closest && to.closest(hoverables))) {
                dot.classList.remove('is-active');
            }
        });
    }
})();
