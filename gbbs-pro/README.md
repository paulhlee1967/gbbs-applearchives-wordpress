# GBBS Pro WordPress Block Theme

A retro Apple II-inspired WordPress block theme that authentically recreates the classic Apple II computer interface with modern WordPress functionality.

## Features

### 🖥️ Authentic Apple II Design
- **16-Color Lo-Res Graphics Palette**: Complete Apple II lo-res graphics color scheme with all 16 authentic colors
- **Monospace Typography**: Uses authentic Apple II font (PrintChar21 by Kreative Software)
- **ASCII Art Elements**: Decorative separators using `----------------------------------------`
- **Retro UI Components**: Terminal-style buttons, forms, and navigation with colorful Apple II styling

### 🎨 Modern WordPress Features
- **Full Site Editing (FSE) Compatible**: Complete block theme with template parts
- **Responsive Design**: Mobile-first approach with Apple II aesthetic
- **Accessibility Ready**: WCAG compliant with screen reader support

### ⚡ Performance & Quality
- **Optimized Font Loading**: WOFF2 with fallbacks and `font-display: swap`
- **Elegant Font Implementation**: Clean CSS using `:not(.wp-admin)` for frontend-only font application
- **CSS Custom Properties**: Maintainable styling system
- **Error Handling**: Graceful font loading fallbacks
- **Clean Code**: Well-organized, commented CSS and PHP

## Requirements

- **WordPress**: 6.8 or higher
- **PHP**: 7.4 or higher
- **Browser Support**: Modern browsers with CSS Grid and Flexbox support

## Installation

1. Download the theme files
2. Upload to `/wp-content/themes/gbbs-pro/`
3. Activate the theme in WordPress Admin → Appearance → Themes
4. Customize using the Site Editor (Appearance → Editor)

## Customization

### Using the Site Editor

1. **Navigate to Appearance → Editor**
2. **Customize Templates**: Edit header, footer, and page templates
3. **Style Settings**: Modify colors, typography, and spacing

### Font Implementation Details

The theme uses a clean, elegant approach to font loading:

```css
/* Frontend only - no !important needed, no admin conflicts */
body:not(.wp-admin) {
    font-family: 'Apple II', monospace;
}

body:not(.wp-admin) * {
    font-family: inherit;
}
```

This approach:
- **Preserves WordPress admin interface** with default system fonts
- **Applies Apple II fonts** only to frontend content
- **Uses clean CSS** without complex selectors or `!important` declarations
- **Maintains performance** with minimal CSS overhead

### Custom CSS Classes

#### Basic Elements
```css
/* ASCII art separator */
.apple-ii-separator

/* Terminal cursor effect */
.apple-ii-cursor

/* Font fallback for compatibility */
.apple-ii-font-fallback
```

#### Color Utility Classes
```css
/* Text colors */
.gbbs-pro-color-red
.gbbs-pro-color-blue
.gbbs-pro-color-purple
.gbbs-pro-color-orange
.gbbs-pro-color-yellow
.gbbs-pro-color-pink
.gbbs-pro-color-aqua
.gbbs-pro-color-green
.gbbs-pro-color-dark-green
/* ... and all 16 Apple II colors */

/* Background colors */
.gbbs-pro-bg-red
.gbbs-pro-bg-blue
.gbbs-pro-bg-purple
.gbbs-pro-bg-orange
.gbbs-pro-bg-yellow
.gbbs-pro-bg-pink
.gbbs-pro-bg-aqua
.gbbs-pro-bg-green
/* ... and all 16 Apple II colors */

/* Border colors */
.gbbs-pro-border-red
.gbbs-pro-border-blue
.gbbs-pro-border-purple
/* ... and all 16 Apple II colors */

/* Colorful boxes */
.gbbs-pro-colorful-box
.gbbs-pro-colorful-box.red
.gbbs-pro-colorful-box.blue
.gbbs-pro-colorful-box.purple
.gbbs-pro-colorful-box.orange
.gbbs-pro-colorful-box.yellow
```

### Color Palette

The theme includes the complete 16-color Apple II lo-res graphics palette:

#### Core Colors
- **Black**: `#000000` (Background)
- **White**: `#FFFFFF` (Text highlights)
- **Gray**: `#9C9C9C` (Secondary text)
- **Light Gray**: `#9C9C9C` (Alternative gray)
- **Dark Grey**: `#4A4A4A` (Dark background)

#### Primary Colors
- **Red**: `#E31E60`
- **Dark Blue**: `#604EBD`
- **Purple**: `#FF44FD`
- **Dark Green**: `#00A360`
- **Medium Blue**: `#14CFFD`
- **Light Blue**: `#D0C3FF`
- **Brown**: `#607203`
- **Orange**: `#FF6A3C`
- **Pink**: `#FFA0D0`
- **Light Green**: `#14F53C` (Primary text)
- **Yellow**: `#D0DD8D` (Bright highlights)
- **Aqua**: `#72FFD0`

#### Theme Assignments
- **Background**: Black (`#000000`)
- **Primary Text**: Light Green (`#14F53C`)
- **Bright Text**: Yellow (`#D0DD8D`)
- **Dim Text**: Dark Green (`#00A360`)
- **Secondary Text**: Gray (`#9C9C9C`)

## Shortcodes

### Copyright Shortcode
Display dynamic copyright information:

```
[copyright]
[copyright start_year="2020"]
```

**Parameters:**
- `start_year` (optional): The starting year for copyright range. If provided and different from current year, displays as "start_year-current_year". If not provided or same as current year, displays just the current year.

**Examples:**
- `[copyright]` → "© 2025 Your Site Name"
- `[copyright start_year="2020"]` → "© 2020-2025 Your Site Name"

## Template Structure

```
templates/
├── index.html          # Blog home page
├── page.html           # Static pages
├── page-wide.html      # Wide static pages
├── blank-page.html     # Blank page template
├── blank-page-wide.html # Wide blank page template
├── archive.html        # Archive pages
├── search.html         # Search results
├── single.html         # Single post template
└── 404.html           # Error page

parts/
├── header.html         # Site header
├── footer.html         # Site footer
├── loop.html          # Post loop
├── comments.html      # Comments section
└── sidebar.html       # Sidebar template part
```

## Accessibility Features

- **Skip Links**: "Skip to main content" for keyboard navigation
- **Screen Reader Support**: Proper ARIA labels and semantic HTML
- **Focus Indicators**: Clear focus states for keyboard users
- **High Contrast Mode**: Enhanced colors for better visibility
- **Reduced Motion**: Respects user motion preferences

## Browser Support

- **Chrome**: 90+
- **Firefox**: 88+
- **Safari**: 14+
- **Edge**: 90+

## Performance

- **Font Optimization**: WOFF2 with fallbacks
- **CSS Optimization**: Minified and organized
- **Image Optimization**: Responsive images with lazy loading
- **Critical CSS**: Above-the-fold styles prioritized

## Troubleshooting

### Fonts Not Loading
If Apple II fonts don't appear:
1. Check file permissions on `/fonts/` directory
2. Verify font files are present
3. Check browser console for 404 errors

### Mobile Layout Issues
The theme uses mobile-first responsive design. If you experience layout issues:
1. Clear any custom CSS that might conflict
2. Check for plugin conflicts
3. Verify theme is up to date

### Block Editor Issues
If the block editor doesn't show Apple II styling:
1. Clear browser cache
2. Check for plugin conflicts
3. Verify `editor-style.css` is loading

## Development

### File Structure
```
gbbs-pro/
├── style.css              # Main stylesheet
├── theme.json             # Theme configuration
├── functions.php          # Theme functions
├── editor-style.css       # Block editor styles
├── fonts/                 # Font files
│   ├── PrintChar21.ttf    # Apple II font (TTF)
│   ├── PrintChar21.woff2  # Apple II font (WOFF2)
│   ├── PRNumber3.ttf      # Apple II 80-column font (TTF)
│   ├── PRNumber3.woff2    # Apple II 80-column font (WOFF2)
│   └── FreeLicense.txt    # Font license information
├── templates/             # Block templates
├── parts/                 # Template parts
├── screenshot.png         # Theme screenshot
├── README.md             # This file
├── readme.txt            # WordPress theme readme
├── CHANGELOG.md          # Version history
├── CONTRIBUTING.md       # Contribution guidelines
└── LICENSE               # GPL v2 license
```

### Customizing Colors
Edit `theme.json` to modify the color palette:

```json
{
  "settings": {
    "color": {
      "palette": [
        {
          "color": "#33ff33",
          "name": "Apple II Green",
          "slug": "primary"
        }
      ]
    }
  }
}
```


## Changelog

### Version 1.0.0
- Initial release
- Complete Apple II theme implementation
- Authentic 16-color lo-res graphics palette
- Full Site Editing (FSE) compatible
- Responsive design with mobile-first approach
- Accessibility features and performance optimizations
- Block theme structure
- Font integration

## Support

For support and feature requests:
1. Check this documentation first
2. Search existing issues
3. Create a new issue with detailed information

## Contributing

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) for details on how to contribute to this project.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a complete list of changes and version history.

## License

This theme is licensed under the [GNU General Public License v2 or later](LICENSE).

## Credits

- **Apple II Font**: PrintChar21 by [Kreative Software](https://www.kreativekorporation.com/) (used under Kreative Software Relay Fonts Free Use License v1.2f)
- **WordPress**: Block theme architecture
- **Inspiration**: Classic Apple II computer interface

---

**Made with ❤️ for retro computing enthusiasts**
