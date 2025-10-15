# Contributing to GBBS Pro

Thank you for your interest in contributing to GBBS Pro! This document provides guidelines for contributing to this WordPress block theme.

## 🎯 How to Contribute

### Reporting Issues

Before creating an issue, please:
1. Check if the issue has already been reported
2. Search the existing issues and discussions
3. Make sure you're using the latest version

When reporting an issue, please include:
- WordPress version
- Theme version
- PHP version
- Browser and version
- Steps to reproduce the issue
- Expected vs actual behavior
- Screenshots if applicable

### Suggesting Features

We welcome feature suggestions! Please:
1. Check if the feature has already been requested
2. Provide a clear description of the feature
3. Explain why it would be useful
4. Consider if it fits with the Apple II aesthetic

### Code Contributions

#### Development Setup

1. **Fork the repository**
2. **Clone your fork locally**
   ```bash
   git clone https://github.com/yourusername/gbbs-pro-wordpress-theme.git
   cd gbbs-pro-wordpress-theme
   ```

3. **Set up a local WordPress development environment**
   - Use WordPress 6.8 or higher
   - PHP 7.4 or higher
   - Enable debug mode for development

4. **Install the theme**
   - Copy the theme folder to your WordPress themes directory
   - Activate the theme in WordPress admin

#### Coding Standards

We follow WordPress coding standards:

- **PHP**: [WordPress PHP Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)
- **CSS**: [WordPress CSS Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/css/)
- **JavaScript**: [WordPress JavaScript Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/)

#### Theme-Specific Guidelines

1. **Maintain Apple II Aesthetic**
   - Use the 16-color lo-res graphics palette
   - Preserve monospace typography
   - Keep ASCII art elements authentic
   - Maintain terminal-style UI components

2. **Block Theme Compatibility**
   - Ensure all changes work with Full Site Editing
   - Test with the Site Editor
   - Maintain template part structure
   - Preserve theme.json configuration

3. **Accessibility**
   - Follow WCAG 2.1 AA guidelines
   - Test with screen readers
   - Ensure keyboard navigation works
   - Maintain high contrast ratios

4. **Performance**
   - Optimize CSS and JavaScript
   - Use efficient selectors
   - Minimize HTTP requests
   - Test loading times

#### File Structure

```
gbbs-pro/
├── style.css              # Main stylesheet
├── theme.json             # Theme configuration
├── functions.php          # Theme functions
├── editor-style.css       # Block editor styles
├── fonts/                 # Font files
├── templates/             # Block templates
├── parts/                 # Template parts
└── README.md             # Documentation
```

#### Testing

Before submitting a pull request:

1. **Test in multiple browsers**
   - Chrome, Firefox, Safari, Edge
   - Mobile browsers
   - Different screen sizes

2. **Test WordPress compatibility**
   - Latest WordPress version
   - Different PHP versions
   - With and without plugins

3. **Test accessibility**
   - Screen reader compatibility
   - Keyboard navigation
   - High contrast mode

4. **Test performance**
   - Page load times
   - Core Web Vitals
   - Mobile performance

#### Submitting Changes

1. **Create a feature branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make your changes**
   - Write clean, documented code
   - Follow coding standards
   - Test thoroughly

3. **Commit your changes**
   ```bash
   git add .
   git commit -m "Add: Brief description of changes"
   ```

4. **Push to your fork**
   ```bash
   git push origin feature/your-feature-name
   ```

5. **Create a Pull Request**
   - Provide a clear title and description
   - Reference any related issues
   - Include screenshots if applicable
   - List testing performed

## 📋 Pull Request Template

When creating a pull request, please include:

### Description
Brief description of changes made.

### Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update
- [ ] Performance improvement

### Testing
- [ ] Tested in multiple browsers
- [ ] Tested on mobile devices
- [ ] Tested with screen readers
- [ ] Tested with different WordPress versions
- [ ] Tested with various plugins

### Screenshots
Include before/after screenshots if applicable.

### Checklist
- [ ] Code follows WordPress coding standards
- [ ] Code is self-documenting
- [ ] No console errors
- [ ] Accessibility guidelines followed
- [ ] Performance impact considered

## 🎨 Design Guidelines

### Color Palette
Use only the 16-color Apple II lo-res graphics palette:
- Black: `#000000`
- White: `#FFFFFF`
- Gray: `#9C9C9C`
- Red: `#E31E60`
- Dark Blue: `#604EBD`
- Purple: `#FF44FD`
- Dark Green: `#00A360`
- Medium Blue: `#14CFFD`
- Light Blue: `#D0C3FF`
- Brown: `#607203`
- Orange: `#FF6A3C`
- Pink: `#FFA0D0`
- Light Green: `#14F53C`
- Yellow: `#D0DD8D`
- Aqua: `#72FFD0`

### Typography
- Primary font: PrintChar21 (Apple II font)
- Fallback: monospace system fonts
- Maintain monospace character spacing
- Preserve terminal-style appearance

### UI Elements
- Use ASCII art for separators
- Implement terminal-style buttons and forms
- Maintain consistent spacing and alignment
- Preserve retro computing aesthetic

## 🐛 Bug Reports

When reporting bugs, please include:

1. **Environment Details**
   - WordPress version
   - Theme version
   - PHP version
   - Browser and version
   - Operating system

2. **Issue Description**
   - What happened?
   - What did you expect to happen?
   - Steps to reproduce
   - Frequency of occurrence

3. **Additional Information**
   - Screenshots or videos
   - Console errors
   - Network errors
   - Related plugins

## 💡 Feature Requests

When suggesting features:

1. **Check Existing Issues**
   - Search for similar requests
   - Check if already planned

2. **Provide Details**
   - Clear description
   - Use case scenarios
   - Benefits to users
   - Implementation ideas

3. **Consider Scope**
   - Fits Apple II aesthetic
   - Maintains performance
   - Preserves accessibility
   - Aligns with theme goals

## 📞 Getting Help

- **Documentation**: Check the README.md file
- **Issues**: Search existing issues first
- **Discussions**: Use GitHub Discussions for questions
- **Email**: Contact the maintainer directly

## 📄 License

By contributing to GBBS Pro, you agree that your contributions will be licensed under the GNU General Public License v2 or later.

## 🙏 Recognition

Contributors will be recognized in:
- README.md contributors section
- Release notes
- Theme credits (if applicable)

Thank you for contributing to GBBS Pro! Your efforts help preserve the legacy of Apple II computing in the modern web.
