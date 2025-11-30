# Base Theme

A modern WordPress starter theme built with SCSS and ES6+ JavaScript, following WordPress coding standards and best practices.

## Features

- 🎨 **Modern Architecture**: SCSS with 7-1 architecture pattern
- ⚡ **ES6+ JavaScript**: Modular JavaScript with Vite bundler
- 🎯 **Full Site Editing**: Full support for WordPress block editor
- 📱 **Responsive**: Mobile-first responsive design
- ♿ **Accessible**: WCAG 2.1 AA compliant
- 🔧 **Developer Tools**: ESLint, Stylelint, Prettier
- 🚀 **Performance**: Optimized assets with lazy loading
- 🌐 **i18n Ready**: Fully translatable

## Requirements

- WordPress 6.0+
- PHP 8.0+
- Node.js 18+
- npm 9+

## Installation

1. **Clone or download** the theme into `wp-content/themes/`:
   ```bash
   cd wp-content/themes
   git clone <repository-url> base-theme
   ```

2. **Install dependencies**:
   ```bash
   cd base-theme
   npm install
   ```

3. **Build assets**:
   ```bash
   npm run build
   ```

4. **Activate** the theme in WordPress Admin → Appearance → Themes

## Development

### Available Scripts

- `npm run dev` - Start Vite development server with hot reload
- `npm run build` - Build production-ready assets
- `npm run watch` - Watch files and rebuild on changes
- `npm run lint` - Lint JavaScript and SCSS files
- `npm run lint:js` - Lint JavaScript only
- `npm run lint:css` - Lint SCSS only
- `npm run format` - Format code with Prettier

### Development Workflow

1. Start the development server:
   ```bash
   npm run dev
   ```

2. Make changes to SCSS files in `assets/src/scss/`
3. Make changes to JavaScript files in `assets/src/js/`
4. Assets will be automatically compiled to `assets/dist/`

### Directory Structure

```
base-theme/
├── assets/
│   ├── src/
│   │   ├── scss/
│   │   │   ├── abstracts/     # Variables, mixins, functions
│   │   │   ├── base/          # Reset, typography
│   │   │   ├── components/    # Buttons, forms, cards
│   │   │   ├── layout/        # Header, footer, grid
│   │   │   └── main.scss      # Main entry point
│   │   └── js/
│   │       ├── modules/       # JS modules
│   │       └── main.js        # Main entry point
│   └── dist/                  # Compiled assets (auto-generated)
├── inc/
│   ├── class-assets-manager.php
│   ├── class-component-loader.php
│   ├── theme-setup.php
│   ├── template-functions.php
│   └── template-tags.php
├── template-parts/
│   ├── components/            # Reusable components
│   ├── content.php
│   ├── content-none.php
│   ├── content-single.php
│   └── content-search.php
├── patterns/                  # Block patterns
├── languages/                 # Translation files
├── functions.php              # Theme functions
├── style.css                  # Theme header
├── theme.json                 # FSE configuration
└── README.md
```

## Theme Configuration

### Colors

Edit color variables in `assets/src/scss/abstracts/_variables.scss`:

```scss
$color-primary: #0066cc;
$color-secondary: #ff6b35;
$color-accent: #f7931e;
```

### Typography

Font settings are in `assets/src/scss/abstracts/_variables.scss`:

```scss
$font-primary: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto;
$font-size-base: 1rem;
```

### Breakpoints

Responsive breakpoints in `assets/src/scss/abstracts/_variables.scss`:

```scss
$breakpoint-sm: 576px;
$breakpoint-md: 768px;
$breakpoint-lg: 992px;
$breakpoint-xl: 1200px;
```

## JavaScript Modules

### Navigation

Mobile menu with keyboard navigation and accessibility features.

```javascript
import Navigation from './modules/navigation.js';
const navigation = new Navigation();
navigation.init();
```

### Lazy Loading

Intersection Observer API for lazy loading images.

```javascript
import LazyLoading from './modules/lazy-loading.js';
const lazyLoading = new LazyLoading();
lazyLoading.init();
```

### AJAX Forms

Handle form submissions via AJAX with validation.

```javascript
import AjaxForms from './modules/ajax-forms.js';
const ajaxForms = new AjaxForms();
ajaxForms.init();
```

## Block Patterns

The theme includes ready-to-use block patterns:

- **Hero Section**: Full-width hero with heading, description, and CTA
- **Card Grid**: Three-column card layout with icons and text

Add patterns in the `patterns/` directory following WordPress pattern format.

## Customization

### Adding Custom Styles

1. Create a new partial in `assets/src/scss/components/`
2. Import it in `assets/src/scss/main.scss`
3. Run `npm run build`

### Adding Custom JavaScript

1. Create a new module in `assets/src/js/modules/`
2. Import it in `assets/src/js/main.js`
3. Initialize in the DOMContentLoaded event
4. Run `npm run build`

### Creating Components

Use the Component Loader to create reusable template parts:

```php
Base_Theme_Component_Loader::load('button', array(
    'text' => 'Click Me',
    'url' => '#',
    'class' => 'btn-primary'
));
```

## Translation

The theme is translation-ready. Generate a POT file:

```bash
wp i18n make-pot . languages/base-theme.pot
```

## Browser Support

- Chrome (last 2 versions)
- Firefox (last 2 versions)
- Safari (last 2 versions)
- Edge (last 2 versions)

## Performance

- Minified CSS and JavaScript
- Lazy loading for images
- Efficient asset loading with version hashing
- No jQuery dependency

## Accessibility

- WCAG 2.1 AA compliant
- Keyboard navigation support
- Screen reader friendly
- Skip links and ARIA labels
- Focus management

## License

This theme is licensed under the GPL v2 or later.

## Credits

Built with modern WordPress development practices and tools.

## Support

For issues and questions, please open an issue on the repository.

## Changelog

### 1.0.0
- Initial release
- Full Site Editing support
- SCSS with 7-1 architecture
- ES6+ JavaScript modules
- Block patterns
- Responsive design
- Accessibility features
