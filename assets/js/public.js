/**
 * SIPUPUK Public JS
 */

// Navbar scroll
window.addEventListener('scroll', () => {
    const nav = document.getElementById('navbar');
    if (nav) nav.classList.toggle('scrolled', window.scrollY > 50);
});

// Mobile menu toggle
function toggleMenu() {
    document.getElementById('navMenu').classList.toggle('open');
}
