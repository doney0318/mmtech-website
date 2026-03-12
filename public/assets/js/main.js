// ========== Navbar Scroll Effect ==========
const navbar = document.getElementById('navbar');
let lastScroll = 0;

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;
    
    if (currentScroll > 100) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
    
    lastScroll = currentScroll;
});

// ========== Mobile Menu ==========
const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const navLinks = document.querySelector('.nav-links');

mobileMenuBtn.addEventListener('click', () => {
    navLinks.style.display = navLinks.style.display === 'flex' ? 'none' : 'flex';
    if (navLinks.style.display === 'flex') {
        navLinks.style.flexDirection = 'column';
        navLinks.style.position = 'absolute';
        navLinks.style.top = '100%';
        navLinks.style.left = '0';
        navLinks.style.right = '0';
        navLinks.style.background = 'white';
        navLinks.style.padding = '20px';
        navLinks.style.boxShadow = 'var(--shadow-lg)';
    }
});

// ========== Scroll Animations ==========
const observerOptions = {
    root: null,
    rootMargin: '0px',
    threshold: 0.1
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            
            // Trigger number animation for stats
            if (entry.target.classList.contains('stat-item')) {
                animateNumbers(entry.target.querySelector('.stat-value'));
            }
        }
    });
}, observerOptions);

document.querySelectorAll('.fade-in').forEach(el => {
    observer.observe(el);
});

document.querySelectorAll('.stat-item').forEach(el => {
    observer.observe(el);
});

// ========== Smooth Scroll for Anchor Links ==========
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// ========== Performance: Debounce Scroll Events ==========
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

// ========== Initialize ==========
document.addEventListener('DOMContentLoaded', () => {
    console.log('MMTech Website Loaded Successfully 🚀');
    
    // Add loaded class to body for CSS animations
    document.body.classList.add('loaded');
});

// ========== Accessibility: Keyboard Navigation ==========
document.querySelectorAll('a, button, input, select, textarea').forEach(el => {
    el.addEventListener('focus', function() {
        this.style.outline = '2px solid var(--primary-blue)';
        this.style.outlineOffset = '2px';
    });
    
    el.addEventListener('blur', function() {
        this.style.outline = '';
        this.style.outlineOffset = '';
    });
});
