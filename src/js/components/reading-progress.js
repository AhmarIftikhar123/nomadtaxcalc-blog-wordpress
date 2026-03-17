// src/js/components/reading-progress.js

/**
 * Reading Progress Bar Logic
 */
const initReadingProgress = () => {
    const progressBar = document.getElementById('reading-progress-bar');
    if (!progressBar) return;

    let ticking = false;

    const updateProgress = () => {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrollPercent = (scrollTop / scrollHeight) * 100;

        progressBar.style.width = scrollPercent + '%';
        ticking = false;
    };

    const onScroll = () => {
        if (!ticking) {
            window.requestAnimationFrame(updateProgress);
            ticking = true;
        }
    };

    window.addEventListener('scroll', onScroll, { passive: true });
};

document.addEventListener('DOMContentLoaded', initReadingProgress);

export default initReadingProgress;
