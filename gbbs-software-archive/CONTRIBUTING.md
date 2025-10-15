# Contributing to GBBS Software Archive

Thank you for your interest in contributing to the GBBS Software Archive plugin! This document provides guidelines for contributing to this WordPress plugin.

## 🎯 How to Contribute

### Reporting Issues

Before creating an issue, please:
1. Check if the issue has already been reported
2. Search the existing issues and discussions
3. Make sure you're using the latest version

When reporting an issue, please include:
- WordPress version
- Plugin version
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
4. Consider if it fits with the BBS/retro computing aesthetic

### Code Contributions

#### Development Setup

1. **Fork the repository**
2. **Clone your fork locally**
   ```bash
   git clone https://github.com/yourusername/gbbs-software-archive.git
   cd gbbs-software-archive
   ```

3. **Set up a local WordPress development environment**
   - Use WordPress 5.0 or higher
   - PHP 7.4 or higher
   - MySQL 5.6 or higher
   - Enable debug mode for development

4. **Install the plugin**
   - Copy the plugin folder to your WordPress plugins directory
   - Activate the plugin in WordPress admin

#### Coding Standards

We follow WordPress coding standards:

- **PHP**: [WordPress PHP Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)
- **CSS**: [WordPress CSS Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/css/)
- **JavaScript**: [WordPress JavaScript Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/)

#### Plugin-Specific Guidelines

1. **Maintain BBS Aesthetic**
   - Preserve retro BBS-style interface
   - Use ASCII art elements appropriately
   - Maintain classic BBS terminology
   - Keep terminal-style UI components

2. **WordPress Plugin Standards**
   - Follow WordPress plugin development best practices
   - Use proper sanitization and validation
   - Implement proper nonce verification
   - Follow WordPress database interaction patterns

3. **Security**
   - Validate all user inputs
   - Sanitize output data
   - Use proper capability checks
   - Implement rate limiting where appropriate

4. **Performance**
   - Optimize database queries
   - Use proper caching where appropriate
   - Minimize file operations
   - Test with large file sets

#### File Structure

```
gbbs-software-archive/
├── gbbs-software-archive.php    # Main plugin file
├── includes/
│   ├── class-gbbs-software-archive.php  # Core functionality
│   └── class-gbbs-settings.php          # Settings management
├── views/
│   ├── archive-files-metabox.php        # File management metabox
│   ├── archive-info-metabox.php         # Package info metabox
│   ├── settings-page.php                # Admin settings page
│   └── single-gbbs-archive.php          # Single package display
├── assets/
│   ├── css/
│   │   ├── gbbs-archive.css             # Public styles
│   │   └── gbbs-admin.css               # Admin styles
│   └── js/
│       ├── gbbs-archive.js              # Public JavaScript
│       └── gbbs-admin.js                # Admin JavaScript
└── README.md                             # Documentation
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
   - With and without other plugins

3. **Test file operations**
   - Upload various file types
   - Test with large files
   - Verify download functionality
   - Check file organization

4. **Test security features**
   - Rate limiting
   - File type validation
   - User permissions
   - Input sanitization

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
- [ ] Tested with different WordPress versions
- [ ] Tested with various plugins
- [ ] Tested file upload/download functionality
- [ ] Tested security features

### Screenshots
Include before/after screenshots if applicable.

### Checklist
- [ ] Code follows WordPress coding standards
- [ ] Code is self-documenting
- [ ] No console errors
- [ ] Security best practices followed
- [ ] Performance impact considered
- [ ] Database queries optimized

## 🎨 Design Guidelines

### BBS Aesthetic
- Use ASCII art for separators and headers
- Maintain terminal-style interface elements
- Preserve classic BBS terminology
- Keep retro computing feel

### File Management
- Support Apple II file formats
- Maintain organized file structure
- Provide clear file information
- Ensure easy navigation

### User Interface
- Responsive design for all devices
- Clear visual hierarchy
- Intuitive navigation
- Accessible to all users

## 🐛 Bug Reports

When reporting bugs, please include:

1. **Environment Details**
   - WordPress version
   - Plugin version
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
   - Fits BBS aesthetic
   - Maintains performance
   - Preserves security
   - Aligns with plugin goals

## 📞 Getting Help

- **Documentation**: Check the README.md file
- **Issues**: Search existing issues first
- **Discussions**: Use GitHub Discussions for questions
- **Email**: Contact the maintainer directly

## 📄 License

By contributing to GBBS Software Archive, you agree that your contributions will be licensed under the GNU General Public License v3 or later.

## 🙏 Recognition

Contributors will be recognized in:
- README.md contributors section
- Release notes
- Plugin credits (if applicable)

Thank you for contributing to GBBS Software Archive! Your efforts help preserve the legacy of Apple II BBS software and retro computing.
