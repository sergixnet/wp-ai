/**
 * Navigation Module
 * 
 * Handles mobile menu toggle, keyboard navigation, and accessibility.
 * 
 * @package Base_Theme
 */

export default class Navigation {
  constructor() {
    this.menuToggle = document.querySelector('.menu-toggle');
    this.primaryMenu = document.querySelector('.main-navigation .menu');
    this.menuItems = document.querySelectorAll('.main-navigation .menu-item');
    this.isMenuOpen = false;
  }

  /**
   * Initialize navigation
   */
  init() {
    if (!this.menuToggle || !this.primaryMenu) {
      return;
    }

    this.setupMenuToggle();
    this.setupKeyboardNavigation();
    this.setupSubmenuToggles();
    this.handleResize();
  }

  /**
   * Setup mobile menu toggle
   */
  setupMenuToggle() {
    this.menuToggle.addEventListener('click', () => {
      this.toggleMenu();
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
      if (this.isMenuOpen && 
          !this.primaryMenu.contains(e.target) && 
          !this.menuToggle.contains(e.target)) {
        this.closeMenu();
      }
    });

    // Close menu on ESC key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && this.isMenuOpen) {
        this.closeMenu();
        this.menuToggle.focus();
      }
    });
  }

  /**
   * Toggle menu open/close
   */
  toggleMenu() {
    this.isMenuOpen = !this.isMenuOpen;
    
    this.menuToggle.classList.toggle('toggled');
    this.primaryMenu.classList.toggle('toggled');
    
    this.menuToggle.setAttribute('aria-expanded', this.isMenuOpen);
    
    // Trap focus in menu when open
    if (this.isMenuOpen) {
      this.trapFocus();
    }
  }

  /**
   * Close menu
   */
  closeMenu() {
    this.isMenuOpen = false;
    this.menuToggle.classList.remove('toggled');
    this.primaryMenu.classList.remove('toggled');
    this.menuToggle.setAttribute('aria-expanded', 'false');
  }

  /**
   * Setup keyboard navigation for accessibility
   */
  setupKeyboardNavigation() {
    this.menuItems.forEach(item => {
      const link = item.querySelector('a');
      const submenu = item.querySelector('.sub-menu');
      
      if (!link) return;

      // Handle keyboard navigation
      link.addEventListener('keydown', (e) => {
        // Open submenu on Enter or Space
        if (submenu && (e.key === 'Enter' || e.key === ' ')) {
          if (window.innerWidth < 768) {
            e.preventDefault();
            this.toggleSubmenu(item);
          }
        }

        // Navigate with arrow keys
        if (e.key === 'ArrowDown') {
          e.preventDefault();
          this.focusNextMenuItem(item);
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          this.focusPreviousMenuItem(item);
        } else if (e.key === 'ArrowRight' && submenu) {
          e.preventDefault();
          this.openSubmenu(item);
          this.focusFirstSubmenuItem(submenu);
        } else if (e.key === 'ArrowLeft' && item.closest('.sub-menu')) {
          e.preventDefault();
          this.closeSubmenu(item.closest('.menu-item'));
        }
      });
    });
  }

  /**
   * Setup submenu toggles for mobile
   */
  setupSubmenuToggles() {
    const itemsWithSubmenu = document.querySelectorAll('.main-navigation .menu-item-has-children');
    
    itemsWithSubmenu.forEach(item => {
      const link = item.querySelector('a');
      
      if (window.innerWidth < 768) {
        link.addEventListener('click', (e) => {
          e.preventDefault();
          this.toggleSubmenu(item);
        });
      }
    });
  }

  /**
   * Toggle submenu
   */
  toggleSubmenu(item) {
    const submenu = item.querySelector('.sub-menu');
    if (!submenu) return;

    const isOpen = submenu.style.display === 'block';
    submenu.style.display = isOpen ? 'none' : 'block';
    item.classList.toggle('submenu-open');
  }

  /**
   * Open submenu
   */
  openSubmenu(item) {
    const submenu = item.querySelector('.sub-menu');
    if (submenu) {
      submenu.style.display = 'block';
      item.classList.add('submenu-open');
    }
  }

  /**
   * Close submenu
   */
  closeSubmenu(item) {
    const submenu = item.querySelector('.sub-menu');
    if (submenu) {
      submenu.style.display = 'none';
      item.classList.remove('submenu-open');
      item.querySelector('a').focus();
    }
  }

  /**
   * Focus next menu item
   */
  focusNextMenuItem(currentItem) {
    const nextItem = currentItem.nextElementSibling;
    if (nextItem) {
      nextItem.querySelector('a').focus();
    }
  }

  /**
   * Focus previous menu item
   */
  focusPreviousMenuItem(currentItem) {
    const prevItem = currentItem.previousElementSibling;
    if (prevItem) {
      prevItem.querySelector('a').focus();
    }
  }

  /**
   * Focus first submenu item
   */
  focusFirstSubmenuItem(submenu) {
    const firstItem = submenu.querySelector('.menu-item a');
    if (firstItem) {
      firstItem.focus();
    }
  }

  /**
   * Trap focus inside menu when open (mobile)
   */
  trapFocus() {
    const focusableElements = this.primaryMenu.querySelectorAll(
      'a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])'
    );
    
    if (focusableElements.length === 0) return;

    const firstElement = focusableElements[0];
    const lastElement = focusableElements[focusableElements.length - 1];

    this.primaryMenu.addEventListener('keydown', (e) => {
      if (e.key !== 'Tab') return;

      if (e.shiftKey) {
        if (document.activeElement === firstElement) {
          e.preventDefault();
          lastElement.focus();
        }
      } else {
        if (document.activeElement === lastElement) {
          e.preventDefault();
          firstElement.focus();
        }
      }
    });
  }

  /**
   * Handle window resize
   */
  handleResize() {
    window.addEventListener('resize', () => {
      if (window.innerWidth >= 768 && this.isMenuOpen) {
        this.closeMenu();
      }
    });
  }
}
