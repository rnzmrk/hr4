import './bootstrap';
import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

document.addEventListener('DOMContentLoaded', function() {
    const menuBtn = document.getElementById('menu-btn');
    const desktopToggle = document.getElementById('desktop-toggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getaElementById('overlay');
    const mainContent = document.getElementById('main-content');

    // Mobile sidebar toggle
    if (menuBtn && sidebar && overlay) {
    menuBtn.addEventListener('click', (e) => {
        e.preventDefault();
        sidebar.classList.toggle('active');
        overlay.classList.toggle('show');
        document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
    });
    }

    // Desktop sidebar toggle
    if (desktopToggle && sidebar && mainContent) {
    desktopToggle.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();

        const isCollapsed = sidebar.classList.contains('collapsed');

        // Toggle classes with smooth animation
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');

        // Store state in localStorage for persistence
        localStorage.setItem('sidebarCollapsed', !isCollapsed);

        // Trigger window resize event to help responsive components adjust
        setTimeout(() => {
        window.dispatchEvent(new Event('resize'));
        }, 300);
    });
    }

    // Restore sidebar state from localStorage
    const savedState = localStorage.getItem('sidebarCollapsed');
    if (savedState === 'true' && sidebar && mainContent) {
    sidebar.classList.add('collapsed');
    mainContent.classList.add('expanded');
    }

    // Close mobile sidebar when clicking overlay
    if (overlay) {
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('active');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    });
    }

    // Add smooth hover effects to nav links
    document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        // Remove active class from all links
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        // Add active class to clicked link
        this.classList.add('active');
    });
    });

    // Add loading animation to quick action buttons
    document.querySelectorAll('.btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        if (!this.classList.contains('loading')) {
        this.classList.add('loading');
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Loading...';

        setTimeout(() => {
            this.innerHTML = originalText;
            this.classList.remove('loading');
        }, 1500);
        }
    });
    });

    // Handle window resize for responsive behavior
    window.addEventListener('resize', () => {
    // Reset mobile sidebar state on desktop
    if (window.innerWidth >= 768) {
        sidebar.classList.remove('active');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    }
    });
}); // Close DOMContentLoadeds


