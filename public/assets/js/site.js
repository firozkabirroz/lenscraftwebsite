(function () {
    'use strict';

    var nav = document.getElementById('siteNav');
    var links = document.getElementById('navLinks');
    var toggle = document.getElementById('navToggle');

    if (toggle && links) {
        toggle.addEventListener('click', function () {
            links.classList.toggle('is-open');
        });
    }

    if (nav) {
        var onScroll = function () {
            nav.classList.toggle('is-stuck', window.scrollY > 20);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    // Showreel modal — supports both embeds and locally uploaded files.
    var modal = document.getElementById('reelModal');
    var frame = document.getElementById('reelFrame');
    var close = document.getElementById('reelClose');

    function openReel(src, type, trackUrl) {
        if (!modal || !frame) {
            return;
        }

        var embedSrc = src + (src.indexOf('?') === -1 ? '?' : '&') + 'autoplay=1';
        frame.innerHTML = type === 'embed'
            ? '<iframe src="' + embedSrc + '" allow="autoplay; fullscreen" allowfullscreen></iframe>'
            : '<video src="' + src + '" controls autoplay playsinline></video>';

        modal.hidden = false;
        document.body.style.overflow = 'hidden';

        if (trackUrl) {
            fetch(trackUrl, { method: 'POST' }).catch(function () { /* view tracking is best effort */ });
        }
    }

    function closeReel() {
        if (!modal || !frame) {
            return;
        }
        frame.innerHTML = '';
        modal.hidden = true;
        document.body.style.overflow = '';
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

    // Preloader — hides once the page has loaded (min display 350ms, max 2.5s).
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

    // Fade the page out before navigating to another internal page.
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
        window.setTimeout(function () { window.location = link.href; }, 190);
    });

    // Section headings reveal from their mask.
    if ('IntersectionObserver' in window) {
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

    // Count-up animation for the studio stats (only values that start with 10+).
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

    if ('IntersectionObserver' in window) {
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

    // Hover video previews on work cards (pointer devices only).
    if (window.matchMedia('(hover: hover)').matches) {
        document.querySelectorAll('.card-work[data-preview]').forEach(function (card) {
            var thumb = card.querySelector('.card-work__thumb');
            if (!thumb) {
                return;
            }

            var player = null;
            var timer = null;

            card.addEventListener('mouseenter', function () {
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
                    } else {
                        player = document.createElement('iframe');
                        player.src = card.dataset.preview;
                        player.setAttribute('allow', 'autoplay');
                        player.setAttribute('tabindex', '-1');
                    }
                    player.className = 'card-work__preview';
                    thumb.appendChild(player);
                    window.setTimeout(function () {
                        if (player) {
                            player.classList.add('is-live');
                        }
                    }, 250);
                }, 160);
            });

            card.addEventListener('mouseleave', function () {
                window.clearTimeout(timer);
                if (player) {
                    var old = player;
                    player = null;
                    old.classList.remove('is-live');
                    window.setTimeout(function () { old.remove(); }, 320);
                }
            });
        });
    }

    // Reveal sections as they enter the viewport.
    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'none';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        document.querySelectorAll('.card-work, .card-discipline, .service, .step').forEach(function (element) {
            element.style.opacity = '0';
            element.style.transform = 'translateY(14px)';
            element.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            observer.observe(element);
        });
    }
})();
