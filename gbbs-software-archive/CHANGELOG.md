# Changelog

All notable changes to the GBBS Software Archive WordPress plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-01-27

### Added
- Initial release of GBBS Software Archive WordPress plugin
- Complete BBS-style software archive system
- Custom post type `gbbs_archive` for software packages
- Custom taxonomy `gbbs_volume` for hierarchical organization
- File upload and management system with multiple files per archive
- Download tracking and statistics system
- Admin interface with comprehensive meta boxes
- Settings management system with multiple configuration options
- Apple II file type detection and validation
- Download rate limiting and security features
- Responsive design with retro BBS styling
- Search and filtering capabilities
- Shortcode support for easy integration (`gbbs_directory`, `gbbs_archive`)
- Gutenberg block editor integration
- Individual archive modal display
- Archive search and selection interface
- WordPress admin font integration
- BBS-style directory listing with ASCII art headers
- Archive detail pages with complete package information
- File categorization within archives (main, documentation, source, config, utility, other)
- Upload directory organization options (by archive, by volume, or flat structure)
- User permission controls and login requirements
- File type restrictions and validation
- Secure file upload handling with .htaccess protection
- Real-time clock display in BBS interface
- Sortable table columns for archive listings
- Modal file detail views
- Apple II monospace font styling integration
- Comprehensive documentation and setup instructions
- Individual archive shortcode `[gbbs_archive id="123" button_text="View Archive"]`
- Gutenberg block editor integration with custom "GBBS Archive Link" block
- Archive search interface with real-time results
- Volume filtering in block editor
- Visual selection indicators for chosen archives
- Modal popup display for individual archives
- Copy-to-clipboard functionality for upload directory path in settings
- Tab persistence using localStorage to remember active tab after form submission

### Changed
- Enhanced shortcode system with individual archive support
- Improved block editor integration with professional styling
- Updated CSS to override BBS theme fonts in admin interface
- Replaced alert() calls with better user feedback in admin interface
- Improved error handling in JavaScript components
- **Settings Page Reorganization**: Moved display-related settings to Display tab for better organization
- **Upload Directory Widget**: Enhanced styling with proper word wrapping for long file paths
- **Rate Limiting UI**: Improved alignment and layout of configuration options

### Fixed
- React state management issues in Gutenberg block
- Search functionality timing problems
- Selection indicator display issues
- Font conflicts between BBS theme and WordPress admin
- **Settings Tab Persistence**: Users now stay on current tab when saving settings
- **Rate Limiting Alignment**: Fixed misaligned configuration box in settings
- **Upload Directory Overflow**: Long file paths now wrap properly instead of overflowing

### Removed
- Debug console.log statements for production readiness
- Debug error_log statements from download handler
- Alert() popups replaced with inline notifications
- **URLs Tab**: Consolidated URL settings into General tab for simplified interface

### Features
- **BBS-Style Organization**: Classic BBS file areas (GBBS Pro, Games, Mods, Segs, Utilities, etc.)
- **Complete Archives**: Each archive contains all files - no separate patches or install disks
- **File Management**: Multiple files per archive with Apple II format support
- **Retro Interface**: ASCII art headers, classic BBS terminology, and responsive design
- **Security Features**: Download rate limiting, file type validation, and secure upload handling
- **Custom Post Types**: `gbbs_archive` for software packages and `gbbs_volume` for organization
- **Admin Interface**: Complete management system with meta boxes and settings
- **Download Tracking**: Statistics and analytics for file downloads
- **Search & Filter**: Find archives by volume, version, author, etc.

### Supported File Types
- **Apple II Disk Images**: .dsk, .po, .do, .nib, .woz, .2mg
- **Apple II File Formats**: .bas, .int, .asm, .s, .bin, .a2s, .a2d
- **Archive Formats**: .shk, .bny, .bxy, .bqy, .sea, .zip
- **Documentation**: .txt, .doc, .pdf

### Technical Details
- **WordPress Requirements**: 5.0 or higher
- **PHP Requirements**: 7.4 or higher
- **MySQL Requirements**: 5.6 or higher
- **License**: GNU General Public License v3 or later

### Database Structure
- Custom post type `gbbs_archive` with meta fields for version, author, requirements, etc.
- Custom taxonomy `gbbs_volume` for hierarchical organization
- Download logs table for tracking and statistics
- File metadata storage for each archive

### Admin Features
- Archive management with drag-and-drop file uploads
- Volume organization and management
- Comprehensive settings page with multiple configuration options
- Download statistics and analytics dashboard
- File type restrictions and validation settings
- User permission controls
- Upload directory organization options

### Frontend Features
- BBS-style directory listing with retro ASCII art
- Archive detail pages with complete package information
- Download system with individual file downloads
- Search and filtering by volume, version, author, etc.
- Responsive design for all devices
- Real-time clock display
- Sortable table columns
- Modal file detail views

---

## Version History Summary

- **v1.0.0**: Initial release with complete BBS-style software archive system

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines on contributing to this project.

## License

This plugin is licensed under the [GNU General Public License v3 or later](LICENSE).

## Credits

- **Author**: Paul H. Lee
- **WordPress**: Plugin architecture and development standards
- **Inspiration**: Classic Apple II BBS systems and retro computing community
