/**
 * Lazy Loading Module
 * 
 * Implements lazy loading for images and iframes using Intersection Observer API.
 * 
 * @package Base_Theme
 */

export default class LazyLoading {
  constructor() {
    this.lazyElements = document.querySelectorAll('[data-lazy-src], [loading="lazy"]');
    this.observerOptions = {
      root: null,
      rootMargin: '50px',
      threshold: 0.01
    };
  }

  /**
   * Initialize lazy loading
   */
  init() {
    if (!this.lazyElements.length) {
      return;
    }

    // Check if browser supports Intersection Observer
    if ('IntersectionObserver' in window) {
      this.setupIntersectionObserver();
    } else {
      // Fallback: load all images immediately
      this.loadAllImages();
    }
  }

  /**
   * Setup Intersection Observer
   */
  setupIntersectionObserver() {
    const observer = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          this.loadElement(entry.target);
          observer.unobserve(entry.target);
        }
      });
    }, this.observerOptions);

    this.lazyElements.forEach(element => {
      observer.observe(element);
    });
  }

  /**
   * Load lazy element
   */
  loadElement(element) {
    const lazySrc = element.dataset.lazySrc;
    const lazySrcset = element.dataset.lazySrcset;
    const lazySizes = element.dataset.lazySizes;

    // Handle images
    if (element.tagName === 'IMG') {
      if (lazySrc) {
        element.src = lazySrc;
      }
      if (lazySrcset) {
        element.srcset = lazySrcset;
      }
      if (lazySizes) {
        element.sizes = lazySizes;
      }

      element.addEventListener('load', () => {
        element.classList.add('loaded');
        element.classList.remove('loading');
      });

      element.addEventListener('error', () => {
        element.classList.add('error');
        element.classList.remove('loading');
      });

      element.classList.add('loading');
    }

    // Handle background images
    if (element.dataset.lazyBg) {
      element.style.backgroundImage = `url(${element.dataset.lazyBg})`;
      element.classList.add('loaded');
    }

    // Handle iframes
    if (element.tagName === 'IFRAME' && lazySrc) {
      element.src = lazySrc;
      element.classList.add('loaded');
    }

    // Remove data attributes
    delete element.dataset.lazySrc;
    delete element.dataset.lazySrcset;
    delete element.dataset.lazySizes;
    delete element.dataset.lazyBg;
  }

  /**
   * Load all images immediately (fallback)
   */
  loadAllImages() {
    this.lazyElements.forEach(element => {
      this.loadElement(element);
    });
  }

  /**
   * Add lazy loading to dynamically added elements
   */
  static observe(element) {
    const instance = new LazyLoading();
    instance.lazyElements = [element];
    instance.init();
  }
}
