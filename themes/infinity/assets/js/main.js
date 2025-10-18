/**
 * Infinity CMS - Default Theme JavaScript
 */

// HTMX configuration
document.addEventListener('DOMContentLoaded', function() {
    // Add HTMX event listeners
    document.body.addEventListener('htmx:afterSwap', function(event) {
        console.log('HTMX content swapped');
    });

    // Handle HTMX errors
    document.body.addEventListener('htmx:responseError', function(event) {
        console.error('HTMX error:', event.detail);
    });
});

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});
