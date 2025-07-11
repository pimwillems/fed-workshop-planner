// Main application JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializeTheme();
    initializeThemeToggle();
    setupCSRFToken();
});

// Theme management
function initializeTheme() {
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const theme = savedTheme || (prefersDark ? 'dark' : 'light');
    
    setTheme(theme);
}

function initializeThemeToggle() {
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
        updateThemeIcon();
    }
}

function setTheme(theme) {
    if (theme === 'dark') {
        document.documentElement.classList.add('dark-mode');
    } else {
        document.documentElement.classList.remove('dark-mode');
    }
    
    localStorage.setItem('theme', theme);
    updateThemeIcon();
}

function toggleTheme() {
    const isDark = document.documentElement.classList.contains('dark-mode');
    setTheme(isDark ? 'light' : 'dark');
}

function updateThemeIcon() {
    const themeIcon = document.getElementById('theme-icon');
    if (themeIcon) {
        const isDark = document.documentElement.classList.contains('dark-mode');
        themeIcon.textContent = isDark ? '☀️' : '🌙';
    }
}

// CSRF Token setup for AJAX requests
function setupCSRFToken() {
    // Add CSRF token to all AJAX requests
    const originalFetch = window.fetch;
    window.fetch = function(url, options = {}) {
        if (options.method && ['POST', 'PUT', 'DELETE'].includes(options.method.toUpperCase())) {
            options.headers = options.headers || {};
            
            // Add CSRF token if available and not already present
            if (window.csrfToken && !options.headers['X-CSRF-Token']) {
                options.headers['X-CSRF-Token'] = window.csrfToken;
            }
        }
        
        return originalFetch(url, options);
    };
}

// Utility functions
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
        max-width: 400px;
        word-wrap: break-word;
    `;
    
    // Set background color based on type
    const colors = {
        success: 'var(--color-po-dark)',
        error: 'var(--color-portfolio-dark)',
        warning: 'var(--color-research-dark)',
        info: 'var(--color-dev-dark)'
    };
    
    notification.style.backgroundColor = colors[type] || colors.info;
    notification.textContent = message;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after delay
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 4000);
}

// Form validation helpers
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePassword(password) {
    return password.length >= 6;
}

function validateRequired(value) {
    return value && value.trim().length > 0;
}

// API helpers
async function apiRequest(url, options = {}) {
    try {
        const response = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.error || `HTTP error! status: ${response.status}`);
        }
        
        return data;
    } catch (error) {
        console.error('API request failed:', error);
        throw error;
    }
}

// Error handling
window.addEventListener('error', function(e) {
    console.error('JavaScript error:', e.error);
    // Could send error to logging service here
});

window.addEventListener('unhandledrejection', function(e) {
    console.error('Unhandled promise rejection:', e.reason);
    // Could send error to logging service here
});

// Performance monitoring
if ('performance' in window) {
    window.addEventListener('load', function() {
        setTimeout(() => {
            const perfData = performance.getEntriesByType('navigation')[0];
            console.log('Page load time:', perfData.loadEventEnd - perfData.loadEventStart, 'ms');
        }, 0);
    });
}

// Accessibility improvements
document.addEventListener('keydown', function(e) {
    // Add focus visible for keyboard navigation
    if (e.key === 'Tab') {
        document.body.classList.add('using-keyboard');
    }
});

document.addEventListener('mousedown', function() {
    document.body.classList.remove('using-keyboard');
});

// Add keyboard navigation support
document.addEventListener('keydown', function(e) {
    // Close modals with Escape key
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('[id$="-modal"]');
        modals.forEach(modal => {
            if (modal.style.display === 'flex') {
                modal.style.display = 'none';
            }
        });
    }
    
    // Submit forms with Ctrl+Enter
    if (e.ctrlKey && e.key === 'Enter') {
        const activeElement = document.activeElement;
        if (activeElement && (activeElement.tagName === 'TEXTAREA' || activeElement.tagName === 'INPUT')) {
            const form = activeElement.closest('form');
            if (form) {
                form.dispatchEvent(new Event('submit', { cancelable: true }));
            }
        }
    }
});

// Auto-save functionality for forms (optional)
function setupAutoSave(formId, key) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    // Load saved data
    const savedData = localStorage.getItem(`autosave_${key}`);
    if (savedData) {
        try {
            const data = JSON.parse(savedData);
            Object.keys(data).forEach(field => {
                const input = form.querySelector(`[name="${field}"]`);
                if (input && input.type !== 'password') {
                    input.value = data[field];
                }
            });
        } catch (e) {
            console.error('Failed to load auto-saved data:', e);
        }
    }
    
    // Save data on input
    const saveData = debounce(() => {
        const formData = new FormData(form);
        const data = {};
        for (let [key, value] of formData.entries()) {
            if (!key.includes('password') && !key.includes('csrf')) {
                data[key] = value;
            }
        }
        localStorage.setItem(`autosave_${key}`, JSON.stringify(data));
    }, 1000);
    
    form.addEventListener('input', saveData);
    
    // Clear saved data on successful submit
    form.addEventListener('submit', function() {
        setTimeout(() => {
            localStorage.removeItem(`autosave_${key}`);
        }, 1000);
    });
}

// Initialize auto-save for workshop forms
if (document.getElementById('workshop-form')) {
    setupAutoSave('workshop-form', 'workshop');
}

// Service worker registration (for PWA functionality)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js')
            .then(function(registration) {
                console.log('ServiceWorker registration successful');
            })
            .catch(function(err) {
                console.log('ServiceWorker registration failed: ', err);
            });
    });
}