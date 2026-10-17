(function () {
    'use strict';

    const STORAGE_KEY = 'zaffran_theme_preference';
    const rootElement = document.documentElement;

    function getPreferredTheme() {
        const storedTheme = localStorage.getItem(STORAGE_KEY);
        if (storedTheme === 'dark' || storedTheme === 'light') {
            return storedTheme;
        }
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    function applyTheme(theme) {
        rootElement.setAttribute('data-theme', theme);
        localStorage.setItem(STORAGE_KEY, theme);
    }

    applyTheme(getPreferredTheme());

    document.addEventListener('DOMContentLoaded', () => {
        // 1. Dark / Light Theme Toggle
        const toggleButtons = document.querySelectorAll('[data-theme-toggle]');
        toggleButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                const currentTheme = rootElement.getAttribute('data-theme') || 'light';
                const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
                applyTheme(nextTheme);
            });
        });

        // 2. Responsive Hamburger Menu Drawer
        const navToggleBtn = document.querySelector('[data-nav-toggle]');
        const primaryNav = document.getElementById('primaryNav');

        if (navToggleBtn && primaryNav) {
            // Create a backdrop overlay dynamically for mobile drawer
            const backdrop = document.createElement('div');
            backdrop.className = 'nav-backdrop';
            document.body.appendChild(backdrop);

            function setMenuState(isOpen) {
                navToggleBtn.setAttribute('aria-expanded', String(isOpen));
                primaryNav.classList.toggle('is-open', isOpen);
                backdrop.classList.toggle('is-active', isOpen);
                document.body.classList.toggle('nav-open', isOpen);
            }

            navToggleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isExpanded = navToggleBtn.getAttribute('aria-expanded') === 'true';
                setMenuState(!isExpanded);
            });

            // Close menu when clicking any link inside the drawer
            primaryNav.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => setMenuState(false));
            });

            // Close menu when tapping backdrop
            backdrop.addEventListener('click', () => setMenuState(false));

            // Close menu on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && primaryNav.classList.contains('is-open')) {
                    setMenuState(false);
                    navToggleBtn.focus();
                }
            });

            // Reset mobile drawer state if window is resized above 768px
            window.addEventListener('resize', () => {
                if (window.innerWidth > 768 && primaryNav.classList.contains('is-open')) {
                    setMenuState(false);
                }
            });
        }

        // 3. Confirm Delete Prompts (Menu CRUD & Contact Messages)
        const deleteForms = document.querySelectorAll('[data-confirm-delete]');
        deleteForms.forEach((form) => {
            form.addEventListener('submit', (event) => {
                const itemName = form.getAttribute('data-item-name') || 'this record';
                if (!window.confirm(`Are you sure you want to permanently delete "${itemName}"?`)) {
                    event.preventDefault();
                }
            });
        });

        // 4. Public Menu & Admin Table Live Search + Category + Diet Filtering
        const searchInput = document.getElementById('menuSearchInput');
        const categoryButtons = document.querySelectorAll('[data-filter-category]');
        const dietButtons = document.querySelectorAll('[data-filter-diet]');
        const filterableItems = document.querySelectorAll('[data-menu-item]');
        const emptyStateBox = document.getElementById('menuNoResults');
        const visibleCountEl = document.getElementById('menuVisibleCount');

        if (filterableItems.length > 0) {
            let activeCategory = 'all';
            let activeDiet = 'all';
            let searchQuery = '';

            function filterMenu() {
                let visibleCount = 0;

                filterableItems.forEach((item) => {
                    const name = (item.getAttribute('data-name') || '').toLowerCase();
                    const desc = (item.getAttribute('data-desc') || '').toLowerCase();
                    const category = (item.getAttribute('data-category') || '').toLowerCase();
                    const diet = (item.getAttribute('data-diet') || '').toLowerCase();

                    const matchesSearch =
                        searchQuery === '' ||
                        name.includes(searchQuery) ||
                        desc.includes(searchQuery) ||
                        category.includes(searchQuery);

                    const matchesCategory =
                        activeCategory === 'all' || category === activeCategory.toLowerCase();

                    const matchesDiet =
                        activeDiet === 'all' || diet === activeDiet.toLowerCase();

                    if (matchesSearch && matchesCategory && matchesDiet) {
                        item.style.display = '';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                if (visibleCountEl) {
                    visibleCountEl.textContent = String(visibleCount);
                }

                if (emptyStateBox) {
                    emptyStateBox.style.display = visibleCount === 0 ? 'block' : 'none';
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    searchQuery = e.target.value.trim().toLowerCase();
                    filterMenu();
                });
            }

            categoryButtons.forEach((btn) => {
                btn.addEventListener('click', () => {
                    categoryButtons.forEach((b) => b.classList.remove('active'));
                    btn.classList.add('active');
                    activeCategory = btn.getAttribute('data-filter-category') || 'all';
                    filterMenu();
                });
            });

            dietButtons.forEach((btn) => {
                btn.addEventListener('click', () => {
                    dietButtons.forEach((b) => b.classList.remove('active'));
                    btn.classList.add('active');
                    activeDiet = btn.getAttribute('data-filter-diet') || 'all';
                    filterMenu();
                });
            });
        }
    });
})();