/**
 * AJAX Forms Module
 * 
 * Handles form submissions via AJAX with proper validation and feedback.
 * 
 * @package Base_Theme
 */

export default class AjaxForms {
  constructor() {
    this.forms = document.querySelectorAll('[data-ajax-form]');
  }

  /**
   * Initialize AJAX forms
   */
  init() {
    if (!this.forms.length) {
      return;
    }

    this.forms.forEach(form => {
      this.setupForm(form);
    });
  }

  /**
   * Setup individual form
   */
  setupForm(form) {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      this.handleSubmit(form);
    });
  }

  /**
   * Handle form submission
   */
  async handleSubmit(form) {
    // Validate form
    if (!this.validateForm(form)) {
      return;
    }

    // Get form data
    const formData = new FormData(form);
    const action = form.dataset.ajaxAction || 'submit_form';
    
    // Add WordPress nonce
    if (window.baseTheme && window.baseTheme.nonce) {
      formData.append('nonce', window.baseTheme.nonce);
    }
    
    formData.append('action', action);

    // Show loading state
    this.setLoadingState(form, true);

    try {
      const response = await fetch(window.baseTheme.ajaxUrl, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      });

      const data = await response.json();

      if (data.success) {
        this.handleSuccess(form, data);
      } else {
        this.handleError(form, data);
      }
    } catch (error) {
      this.handleError(form, {
        message: window.baseTheme.i18n.error || 'An error occurred'
      });
    } finally {
      this.setLoadingState(form, false);
    }
  }

  /**
   * Validate form
   */
  validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');

    // Remove previous error messages
    this.clearErrors(form);

    requiredFields.forEach(field => {
      if (!field.value.trim()) {
        this.showFieldError(field, 'This field is required');
        isValid = false;
      }

      // Email validation
      if (field.type === 'email' && field.value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(field.value)) {
          this.showFieldError(field, 'Please enter a valid email address');
          isValid = false;
        }
      }

      // URL validation
      if (field.type === 'url' && field.value) {
        try {
          new URL(field.value);
        } catch {
          this.showFieldError(field, 'Please enter a valid URL');
          isValid = false;
        }
      }

      // Number validation
      if (field.type === 'number' && field.value) {
        const min = field.getAttribute('min');
        const max = field.getAttribute('max');
        const value = parseFloat(field.value);

        if (min !== null && value < parseFloat(min)) {
          this.showFieldError(field, `Value must be at least ${min}`);
          isValid = false;
        }

        if (max !== null && value > parseFloat(max)) {
          this.showFieldError(field, `Value must be at most ${max}`);
          isValid = false;
        }
      }
    });

    return isValid;
  }

  /**
   * Show field error
   */
  showFieldError(field, message) {
    field.classList.add('is-invalid');
    
    const errorElement = document.createElement('div');
    errorElement.className = 'invalid-feedback';
    errorElement.textContent = message;
    
    field.parentNode.appendChild(errorElement);
  }

  /**
   * Clear all errors
   */
  clearErrors(form) {
    const errorElements = form.querySelectorAll('.invalid-feedback');
    errorElements.forEach(el => el.remove());
    
    const invalidFields = form.querySelectorAll('.is-invalid');
    invalidFields.forEach(field => field.classList.remove('is-invalid'));
  }

  /**
   * Set loading state
   */
  setLoadingState(form, isLoading) {
    const submitButton = form.querySelector('[type="submit"]');
    
    if (submitButton) {
      submitButton.disabled = isLoading;
      submitButton.classList.toggle('loading', isLoading);
      
      if (isLoading) {
        submitButton.dataset.originalText = submitButton.textContent;
        submitButton.textContent = window.baseTheme.i18n.loading || 'Loading...';
      } else {
        submitButton.textContent = submitButton.dataset.originalText;
      }
    }
  }

  /**
   * Handle successful submission
   */
  handleSuccess(form, data) {
    // Show success message
    this.showMessage(form, data.message || 'Form submitted successfully', 'success');
    
    // Reset form
    form.reset();
    
    // Trigger custom event
    form.dispatchEvent(new CustomEvent('ajaxFormSuccess', {
      detail: data
    }));

    // Redirect if specified
    if (data.redirect) {
      setTimeout(() => {
        window.location.href = data.redirect;
      }, 1500);
    }
  }

  /**
   * Handle submission error
   */
  handleError(form, data) {
    this.showMessage(form, data.message || 'An error occurred', 'error');
    
    // Trigger custom event
    form.dispatchEvent(new CustomEvent('ajaxFormError', {
      detail: data
    }));
  }

  /**
   * Show message
   */
  showMessage(form, message, type = 'info') {
    // Remove existing messages
    const existingMessage = form.querySelector('.form-message');
    if (existingMessage) {
      existingMessage.remove();
    }

    const messageElement = document.createElement('div');
    messageElement.className = `form-message alert alert-${type}`;
    messageElement.textContent = message;
    messageElement.setAttribute('role', 'alert');
    
    form.insertBefore(messageElement, form.firstChild);

    // Auto-hide after 5 seconds
    setTimeout(() => {
      messageElement.classList.add('fade-out');
      setTimeout(() => messageElement.remove(), 300);
    }, 5000);
  }
}
