(function () {
    'use strict';

    /* =========================================================
       1. MENÚ MÓVIL
    ========================================================= */
    var btn   = document.getElementById('sitec-menu-btn');
    var menu  = document.getElementById('sitec-mobile-menu');
    var bars  = btn ? btn.querySelectorAll('.sitec-bar') : [];

    if (btn && menu) {
        btn.addEventListener('click', function () {
            var isOpen = menu.classList.contains('sitec-menu-open');
            if (isOpen) {
                menu.classList.remove('sitec-menu-open');
                menu.classList.add('hidden');
                btn.setAttribute('aria-expanded', 'false');
                // Restablecer barras
                if (bars[0]) { bars[0].style.transform = ''; bars[0].style.marginTop = ''; }
                if (bars[1]) { bars[1].style.opacity = '1'; }
                if (bars[2]) { bars[2].style.transform = ''; bars[2].style.marginTop = ''; }
            } else {
                menu.classList.add('sitec-menu-open');
                menu.classList.remove('hidden');
                btn.setAttribute('aria-expanded', 'true');
                // Animar a X
                if (bars[0]) { bars[0].style.transform = 'rotate(45deg)'; bars[0].style.marginTop = '6px'; }
                if (bars[1]) { bars[1].style.opacity = '0'; }
                if (bars[2]) { bars[2].style.transform = 'rotate(-45deg)'; bars[2].style.marginTop = '-6px'; }
            }
        });

        // Cerrar al hacer clic en un enlace del menú móvil
        var mobileLinks = menu.querySelectorAll('a');
        mobileLinks.forEach(function (link) {
            link.addEventListener('click', function () {
                menu.classList.remove('sitec-menu-open');
                menu.classList.add('hidden');
                btn.setAttribute('aria-expanded', 'false');
                if (bars[0]) { bars[0].style.transform = ''; bars[0].style.marginTop = ''; }
                if (bars[1]) { bars[1].style.opacity = '1'; }
                if (bars[2]) { bars[2].style.transform = ''; bars[2].style.marginTop = ''; }
            });
        });
    }

    /* =========================================================
       2. HEADER: sombra al hacer scroll
    ========================================================= */
    var header = document.getElementById('sitec-header');
    if (header) {
        var onScroll = function () {
            if (window.scrollY > 20) {
                header.classList.add('sitec-header-scrolled');
            } else {
                header.classList.remove('sitec-header-scrolled');
            }
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    /* =========================================================
       3. SCROLL REVEAL con IntersectionObserver
    ========================================================= */
    if ('IntersectionObserver' in window) {
        var reveals = document.querySelectorAll('.sitec-reveal');
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('sitec-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

        reveals.forEach(function (el) {
            observer.observe(el);
        });
    } else {
        // Fallback: mostrar todo si no hay soporte
        document.querySelectorAll('.sitec-reveal').forEach(function (el) {
            el.classList.add('sitec-visible');
        });
    }

    /* =========================================================
       4. ANIMACIÓN DE CONTADORES en KPIs del hero
    ========================================================= */
    function animateCounter(el, target, duration) {
        var start = 0;
        var startTime = null;
        var prefix = '';
        var suffix = '';
        var num = target;

        // Extraer prefijo/sufijo (ej: "+500", "99.9%", "60%")
        var match = target.match(/^([+]?)(\d+\.?\d*)(.*)$/);
        if (match) {
            prefix = match[1] || '';
            num    = parseFloat(match[2]);
            suffix = match[3] || '';
        }

        function step(timestamp) {
            if (!startTime) { startTime = timestamp; }
            var progress = Math.min((timestamp - startTime) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
            var current = Math.floor(eased * num);
            el.textContent = prefix + current + suffix;
            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                el.textContent = target; // valor final exacto
            }
        }
        requestAnimationFrame(step);
    }

    if ('IntersectionObserver' in window) {
        var kpiObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    var cards = entry.target.querySelectorAll('.sitec-kpi-card span');
                    cards.forEach(function (span) {
                        var val = span.getAttribute('data-target') || span.textContent;
                        span.setAttribute('data-target', val);
                        animateCounter(span, val, 1200);
                    });
                    kpiObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });

        var kpiGrid = document.querySelector('.sitec-hero .sitec-kpi-card');
        if (kpiGrid && kpiGrid.parentElement) {
            kpiObserver.observe(kpiGrid.parentElement);
        }
    }

    /* =========================================================
       5. PUNTO PULSANTE del badge del hero
    ========================================================= */
    // Se anima solo con CSS en overrides.css

})();
