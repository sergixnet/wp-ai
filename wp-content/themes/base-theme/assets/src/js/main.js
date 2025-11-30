/**
 * Main JavaScript Entry Point
 * 
 * @package Base_Theme
 */

import Navigation from './modules/navigation.js';
import LazyLoading from './modules/lazy-loading.js';
import AjaxForms from './modules/ajax-forms.js';

/**
 * Initialize theme when DOM is ready
 */
document.addEventListener('DOMContentLoaded', () => {
  // Initialize navigation
  const navigation = new Navigation();
  navigation.init();

  // Initialize lazy loading for images
  const lazyLoading = new LazyLoading();
  lazyLoading.init();

  // Initialize AJAX forms
  const ajaxForms = new AjaxForms();
  ajaxForms.init();

  // Smooth scroll for anchor links
  initSmoothScroll();

  // External links in new tab
  initExternalLinks();

  // Skip link focus fix for webkit
  initSkipLinkFocusFix();
});

/**
 * Smooth scroll for anchor links
 */
function initSmoothScroll() {
  const links = document.querySelectorAll('a[href^="#"]:not([href="#"])');
  
  links.forEach(link => {
    link.addEventListener('click', function(e) {
      const targetId = this.getAttribute('href').substring(1);
      const targetElement = document.getElementById(targetId);
      
      if (targetElement) {
        e.preventDefault();
        targetElement.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
        
        // Update URL
        if (history.pushState) {
          history.pushState(null, null, `#${targetId}`);
        }
      }
    });
  });
}

/**
 * Open external links in new tab
 */
function initExternalLinks() {
  const links = document.querySelectorAll('a[href^="http"]');
  const currentDomain = window.location.hostname;
  
  links.forEach(link => {
    const linkDomain = new URL(link.href).hostname;
    
    if (linkDomain !== currentDomain) {
      link.setAttribute('target', '_blank');
      link.setAttribute('rel', 'noopener noreferrer');
    }
  });
}

/**
 * Skip link focus fix for webkit browsers
 */
function initSkipLinkFocusFix() {
  const skipLink = document.querySelector('.skip-link');
  
  if (skipLink) {
    skipLink.addEventListener('click', function(e) {
      const target = document.querySelector(this.getAttribute('href'));
      
      if (target) {
        target.setAttribute('tabindex', '-1');
        target.focus();
      }
    });
  }
}

// Make theme data available globally if needed
window.BaseTheme = window.baseTheme || {};
