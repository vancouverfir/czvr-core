(() => {
    'use strict';

    const images = Array.from(document.querySelectorAll('.homepage-background'));
    const credit = document.querySelector('.home-inline-credit');
    const data = document.getElementById('homepageBackgrounds');

    const overview = document.querySelector('.home-overview');

    if (overview && 'IntersectionObserver' in window) {
        overview.querySelectorAll('.home-section-heading').forEach((heading, index) => {
            heading.style.setProperty('--wave-index', index);
        });

        const waveObserver = new IntersectionObserver((entries) => {
            if (entries.some((entry) => entry.isIntersecting)) {
                overview.classList.add('is-waving');
                waveObserver.disconnect();
            }
        }, { threshold: 0.25 });

        waveObserver.observe(overview);

        document.querySelector('.home-explore-link')?.addEventListener('click', () => {
            overview.classList.remove('is-waving');
            void overview.offsetWidth;
            overview.classList.add('is-waving');
        });
    }

    if (images.length !== 2 || !credit || !data) {
        return;
    }

    let backgrounds;

    try {
        backgrounds = JSON.parse(data.textContent);
    } catch {
        return;
    }

    if (!Array.isArray(backgrounds) || backgrounds.length < 2) {
        return;
    }

    let activeImage = images.find((image) => image.classList.contains('is-active'));
    let currentIndex = backgrounds.findIndex((background) => background.url === activeImage.getAttribute('src'));
    currentIndex = currentIndex < 0 ? 0 : currentIndex;

    const scheduleNext = () => window.setTimeout(showNext, 7000);

    const showNext = () => {
        if (document.visibilityState === 'hidden') {
            scheduleNext();
            return;
        }

        const nextIndex = (currentIndex + 1) % backgrounds.length;
        const next = backgrounds[nextIndex];
        const preload = new Image();

        preload.addEventListener('load', () => {
            const incomingImage = images.find((image) => image !== activeImage);

            incomingImage.src = next.url;
            incomingImage.classList.add('is-active');
            activeImage.classList.remove('is-active');
            activeImage = incomingImage;
            credit.textContent = `Screenshot by ${next.credit}`;
            currentIndex = nextIndex;
            scheduleNext();
        }, { once: true });
        preload.addEventListener('error', scheduleNext, { once: true });
        preload.src = next.url;
    };

    scheduleNext();
})();
