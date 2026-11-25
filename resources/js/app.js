const mobileMenuToggle = document.querySelector('[data-mobile-menu-toggle]');
const mobileMenu = document.getElementById('mobile-menu');

if (mobileMenuToggle && mobileMenu) {
    const openIcon = mobileMenuToggle.querySelector('[data-menu-icon="open"]');
    const closeIcon = mobileMenuToggle.querySelector('[data-menu-icon="close"]');

    mobileMenuToggle.addEventListener('click', () => {
        const isExpanded = mobileMenuToggle.getAttribute('aria-expanded') === 'true';
        const shouldExpand = !isExpanded;

        mobileMenuToggle.setAttribute('aria-expanded', String(shouldExpand));
        mobileMenu.classList.toggle('hidden', !shouldExpand);
        mobileMenu.classList.toggle('block', shouldExpand);
        openIcon?.classList.toggle('hidden', shouldExpand);
        closeIcon?.classList.toggle('hidden', !shouldExpand);
    });
}
