// Sticky Header Implementation
document.addEventListener("DOMContentLoaded", () => {
    const header = document.querySelector('.sticky-header');

    if (header) {
        const stickyClass = header.getAttribute('data-sticky-class');
        const stickyClasses = stickyClass ? stickyClass.split(' ') : [];
        const stickyOffset = parseInt(header.getAttribute('data-sticky-offset') || '0');

        const handleScroll = () => {
            if (window.scrollY > stickyOffset) {
                header.classList.add(...stickyClasses, 'is-sticky');
            } else {
                header.classList.remove(...stickyClasses, 'is-sticky');
            }
        };

        window.addEventListener('scroll', handleScroll);

        // Initial check
        handleScroll();

        // Also check on resize
        window.addEventListener('resize', handleScroll);
    }
});

// Sidebar Bottom Navigation
document.addEventListener("DOMContentLoaded", () => {
    const sidebarContent = document.getElementById('sidebar_content');
    const bottomNav = sidebarContent?.querySelector('.border-t.border-gray-700');

    if (sidebarContent && bottomNav) {
        // Ensure bottom nav is visible when page loads
        bottomNav.style.display = 'block';

        // Calculate height of sidebar and bottom nav
        const updateBottomNavPosition = () => {
            const sidebarHeight = sidebarContent.offsetHeight;
            const bottomNavHeight = bottomNav.offsetHeight;

            // Ensure there's enough space for the bottom nav
            const scrollableArea = sidebarContent.querySelector('[data-scrollable="true"]');
            if (scrollableArea) {
                scrollableArea.style.maxHeight = `calc(100% - ${bottomNavHeight}px)`;
            }
        };

        // Update on load, resize, and content change
        updateBottomNavPosition();
        window.addEventListener('resize', updateBottomNavPosition);

        // Also check periodically (for dynamic content changes)
        setInterval(updateBottomNavPosition, 1000);
    }
});

// Sidebar Header Dropdown Toggle
document.addEventListener("DOMContentLoaded", () => {
    const dropdownToggle = document.querySelector('[data-dropdown-toggle="header-dropdown"]');
    const dropdownMenu = document.getElementById('header-dropdown');

    if (dropdownToggle && dropdownMenu) {
        dropdownToggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            // Toggle the dropdown visibility
            dropdownMenu.classList.toggle('hidden');

            // Position the dropdown relative to the toggle button
            const toggleRect = dropdownToggle.getBoundingClientRect();
            dropdownMenu.style.position = 'absolute';
            dropdownMenu.style.top = `${toggleRect.bottom + 5}px`;
            dropdownMenu.style.right = '10px';
            dropdownMenu.style.zIndex = '100';
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });
    }
});
