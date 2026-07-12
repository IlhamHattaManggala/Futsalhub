document.addEventListener("DOMContentLoaded", function() {
    const parallaxLayers = document.querySelectorAll('.parallax-layer');
    
    // Disable parallax on mobile devices & if reduced motion is preferred for accessibility
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isMobile = window.innerWidth < 768;
    
    if (!prefersReducedMotion && !isMobile) {
        let lastScrollY = window.pageYOffset;
        let ticking = false;
        
        function updateParallax() {
            const scrollY = window.pageYOffset;
            
            parallaxLayers.forEach(layer => {
                const speed = parseFloat(layer.getAttribute('data-speed')) || 0.1;
                // Use translate3d to trigger hardware acceleration for 60fps butter-smooth rendering
                const yVal = scrollY * speed;
                layer.style.transform = `translate3d(0, ${yVal}px, 0)`;
            });
            
            ticking = false;
        }
        
        window.addEventListener('scroll', function() {
            lastScrollY = window.pageYOffset;
            
            if (!ticking) {
                window.requestAnimationFrame(updateParallax);
                ticking = true;
            }
        }, { passive: true });
    }
});

document.addEventListener("DOMContentLoaded", function() {
    function fetchLandingSettings() {
        fetch('/api/settings/landing')
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                // 1. Update Features Main Header
                const featTitle = document.getElementById('featMainTitle');
                const featSubtitle = document.getElementById('featMainSubtitle');
                if (featTitle && data.feat_title) featTitle.innerText = data.feat_title;
                if (featSubtitle && data.feat_subtitle) featSubtitle.innerText = data.feat_subtitle;

                // 2. Update Features Cards (feat1 to feat6)
                for (let i = 1; i <= 6; i++) {
                    const iconEl = document.getElementById('icon_feat' + i);
                    const titleEl = document.getElementById('title_feat' + i);
                    const descEl = document.getElementById('desc_feat' + i);

                    if (iconEl && data['feat' + i + '_icon']) {
                        iconEl.className = 'fa-solid ' + data['feat' + i + '_icon'];
                    }
                    if (titleEl && data['feat' + i + '_title']) {
                        titleEl.innerText = data['feat' + i + '_title'];
                    }
                    if (descEl && data['feat' + i + '_desc']) {
                        descEl.innerText = data['feat' + i + '_desc'];
                    }
                }

                // 3. Update Advantages Main Header
                const advTitle = document.getElementById('advMainTitle');
                const advSubtitle = document.getElementById('advMainSubtitle');
                if (advTitle && data.adv_title) advTitle.innerText = data.adv_title;
                if (advSubtitle && data.adv_subtitle) advSubtitle.innerText = data.adv_subtitle;

                // 4. Update Advantages Cards (adv1 to adv3)
                for (let i = 1; i <= 3; i++) {
                    const iconEl = document.getElementById('advIcon_adv' + i);
                    const titleEl = document.getElementById('advTitle_adv' + i);
                    const descEl = document.getElementById('advDesc_adv' + i);

                    if (iconEl && data['adv' + i + '_icon']) {
                        iconEl.className = 'fa-solid ' + data['adv' + i + '_icon'];
                    }
                    if (titleEl && data['adv' + i + '_title']) {
                        titleEl.innerText = data['adv' + i + '_title'];
                    }
                    if (descEl && data['adv' + i + '_desc']) {
                        descEl.innerText = data['adv' + i + '_desc'];
                    }
                }

                // 5. Update Statistics Metrics (stat1 to stat3)
                for (let i = 1; i <= 3; i++) {
                    const valEl = document.getElementById('stat' + i + '_val');
                    const labelEl = document.getElementById('stat' + i + '_label');
                    const descEl = document.getElementById('stat' + i + '_desc');

                    if (valEl && data['stat' + i + '_val']) {
                        valEl.innerText = data['stat' + i + '_val'];
                    }
                    if (labelEl && data['stat' + i + '_label']) {
                        labelEl.innerText = data['stat' + i + '_label'];
                    }
                    if (descEl && data['stat' + i + '_desc']) {
                        descEl.innerText = data['stat' + i + '_desc'];
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching landing settings:', error);
            });
    }

    // Initial fetch
    fetchLandingSettings();
});
