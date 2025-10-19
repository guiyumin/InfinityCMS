/**
 * Admin Dashboard JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Flash message auto-dismiss
    const flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(function(message) {
        setTimeout(function() {
            message.style.opacity = '0';
            setTimeout(function() {
                message.remove();
            }, 300);
        }, 5000);
    });

    // Confirm destructive actions
    const confirmButtons = document.querySelectorAll('[data-confirm]');
    confirmButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm');
            if (!confirm(message)) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
    });

    // Auto-refresh elements with data-refresh attribute
    const refreshElements = document.querySelectorAll('[data-refresh]');
    refreshElements.forEach(function(element) {
        const interval = parseInt(element.getAttribute('data-refresh')) || 30000;
        setInterval(function() {
            if (element.hasAttribute('hx-get')) {
                htmx.trigger(element, 'refresh');
            }
        }, interval);
    });
});

// HTMX configuration
document.addEventListener('htmx:configRequest', function(event) {
    // Add CSRF token to all HTMX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        event.detail.headers['X-CSRF-Token'] = csrfToken.content;
    }
});

// Handle HTMX errors
document.addEventListener('htmx:responseError', function(event) {
    console.error('HTMX Error:', event.detail);
    alert('An error occurred. Please try again.');
});

// Show loading indicator for HTMX requests
document.addEventListener('htmx:beforeRequest', function(event) {
    const target = event.target;
    if (target.classList.contains('htmx-loading')) {
        target.classList.add('loading');
    }
});

document.addEventListener('htmx:afterRequest', function(event) {
    const target = event.target;
    if (target.classList.contains('htmx-loading')) {
        target.classList.remove('loading');
    }
});
