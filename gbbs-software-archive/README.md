# GBBS Software Archive Plugin

[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net/)

A WordPress plugin designed to archive and manage GBBS Pro and GBBS II software packages in a classic 80's BBS-style interface.

## 📋 Overview

This plugin creates a complete software archive system specifically designed for vintage Apple II BBS software, particularly GBBS Pro and GBBS II. It mimics the classic BBS file area structure with "Volumes" containing complete software packages, preserving the nostalgic experience of 1980s bulletin board systems.

## ✨ Key Features

### 🗂️ BBS-Style Organization

- **Volumes**: Classic BBS file areas (GBBS Pro, Games, Mods, Segs, Utilities, etc.)
- **Archives**: Complete software sets within each volume
- **No Fragmentation**: Each archive is complete - no separate patches or install disks

### 📁 File Management

- Multiple files per archive (main program, docs, source, configs, etc.)
- Apple II file format support (.dsk, .po, .do, .woz, .bas, .asm, etc.)
- File categorization within archives (main, documentation, source, config, utility, other)
- Download tracking and statistics
- File type validation and restrictions
- Upload directory organization (by archive, by volume, or flat structure)

### 🎨 User Interface

- Retro BBS-style directory listing
- ASCII art headers and styling
- Classic BBS terminology and layout
- Responsive design for modern browsers
- Real-time clock display
- Sortable table columns
- Modal file detail views
- Apple II monospace font styling

### 🔒 Security Features

- Download rate limiting per IP address
- File type validation and restrictions
- Secure file upload handling
- User permission controls
- Nonce verification for all forms
- Input sanitization and validation
- Secure directory structure with .htaccess protection

## 🏗️ Plugin Structure

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
│   │   ├── gbbs-admin.css               # Admin styles
│   │   └── gbbs-block-editor.css        # Gutenberg block editor styles
│   └── js/
│       ├── gbbs-archive.js              # Public JavaScript
│       ├── gbbs-admin.js                # Admin JavaScript
│       └── gbbs-block-editor.js         # Gutenberg block editor JavaScript
├── README.md                             # This file
├── readme.txt                            # WordPress plugin readme
├── LICENSE                               # GPL v3 License
├── CONTRIBUTING.md                       # Contribution guidelines
├── CHANGELOG.md                          # Version history
├── FOLDER-STRUCTURE.md                   # Upload directory structure documentation
└── SECURITY.md                           # Security reporting
```

## 🗄️ Database Structure

### Custom Post Type: `gbbs_archive`

- **Title**: Archive name (e.g., "GBBS Pro v2.1 Complete System")
- **Content**: Archive description
- **Excerpt**: Short description
- **Thumbnail**: Archive screenshot/icon
- **Meta Fields**: See below

### Custom Taxonomy: `gbbs_volume`

- **Hierarchical**: Yes (allows sub-volumes)
- **Examples**: GBBS Pro, Games, Mods, Segs, Utilities, Documentation

### Meta Fields for Archives

- `gbbs_archive_version`: Software version
- `gbbs_archive_author`: Original author/publisher
- `gbbs_archive_release_year`: Release year
- `gbbs_archive_requirements`: System requirements
- `gbbs_archive_installation_notes`: Installation instructions
- `gbbs_archive_historical_notes`: Historical context
- `gbbs_archive_files`: Array of file attachments with metadata

## 📄 File Types Supported

### Apple II Disk Images

- `.dsk` - Standard Apple II disk image
- `.po` - ProDOS disk image
- `.do` - DOS disk image
- `.nib` - Raw nibble data
- `.woz` - Modern WOZ format
- `.2mg` - 2MG disk image

### Apple II File Formats

- `.bas` - AppleSoft BASIC
- `.int` - Integer BASIC
- `.asm` - Assembly source
- `.s` - Assembly source (alternative)
- `.bin` - Binary files
- `.a2s` - AppleSingle
- `.a2d` - AppleDouble

### Archive Formats

- `.shk` - ShrinkIt archive
- `.bny` - Binary NY archive
- `.bxy` - Binary XY archive
- `.bqy` - Binary QY archive
- `.sea` - Self-extracting archive
- `.zip` - ZIP archive

### Documentation

- `.txt` - Text files
- `.doc` - Documentation
- `.pdf` - PDF manuals

## 🚀 Usage

### Admin Interface

1. **Add New Archive**: Create a new GBBS archive
2. **Assign Volume**: Choose which volume the archive belongs to
3. **Upload Files**: Add all files for the archive
4. **Set Metadata**: Fill in version, author, requirements, etc.
5. **Publish**: Make the archive available to users

### Frontend Display

- **Directory Listing**: BBS-style file area listing
- **Archive Details**: Complete archive information
- **Download Options**: Download complete archive or individual files
- **Search/Filter**: Find archives by volume, version, etc.

### Shortcodes

- `[gbbs_directory]` - Display BBS-style directory listing
- `[gbbs_directory limit="10"]` - Display with custom limit
- `[gbbs_directory volume="games"]` - Show only archives from specific volume
- `[gbbs_archive id="123"]` - Display specific archive by ID
- `[gbbs_archive id="123" button_text="View Archive"]` - Display individual archive with modal popup
- `[gbbs_archive id="123" show_files="true"]` - Show file list in archive display

### Gutenberg Block Editor Integration

- **GBBS Archive Link Block**: Custom block for easy archive insertion
- **Search Interface**: Type to search archives with real-time results
- **Volume Filtering**: Filter archives by volume categories
- **Visual Selection**: Click to select archives with visual indicators
- **One-Click Insertion**: Insert archive shortcodes directly into posts/pages

### Settings & Configuration

The plugin includes comprehensive settings organized into logical tabs:

- **General Settings**: Role permissions, URL slugs for archives and volumes
- **Display Settings**: Archive title, description, information visibility, interactive features, sorting options, items per page
- **Upload Settings**: Folder structure, file organization, size limits, file type restrictions
- **Download Settings**: Login requirements, tracking, timeouts, rate limiting

**Key Features:**
- **Tab Persistence**: Settings remember your current tab when saving
- **Upload Directory Widget**: Shows current file storage location with copy-to-clipboard functionality
- **Responsive Design**: Settings interface works on all device sizes
- **Real-time Validation**: Form validation with helpful error messages

Access settings via **GBBS Archive > Settings** in the WordPress admin.

## 📊 Development Status

### ✅ Completed

- [x] Basic plugin structure
- [x] Custom post type and taxonomy (`gbbs_archive`, `gbbs_volume`)
- [x] Complete admin interface with meta boxes
- [x] File upload and management system
- [x] Download tracking and statistics
- [x] BBS-style frontend with retro styling
- [x] Archive file management (multiple files per archive)
- [x] Download system with individual file downloads
- [x] Search and filtering functionality
- [x] Volume-based organization
- [x] Settings management system
- [x] Shortcode support (`gbbs_directory`, `gbbs_archive`)
- [x] Apple II file type detection
- [x] Download rate limiting
- [x] File type restrictions
- [x] Upload directory organization
- [x] Gutenberg block editor integration
- [x] Individual archive modal display
- [x] Archive search and selection interface
- [x] WordPress admin font integration
- [x] Settings page reorganization and improved UX
- [x] Tab persistence for settings interface
- [x] Enhanced upload directory widget with copy functionality
- [x] Improved rate limiting UI alignment

### 🔄 In Progress

- [ ] Advanced search features
- [ ] Enhanced statistics reporting
- [ ] Bulk archive management

### 📋 Planned

- [ ] Archive import/export
- [ ] Advanced user permissions
- [ ] Archive dependencies
- [ ] Version history tracking
- [ ] API endpoints

## 📦 Installation

1. Download the plugin files
2. Upload to `/wp-content/plugins/gbbs-software-archive/`
3. Activate the plugin through WordPress admin
4. Configure settings in **GBBS Archive > Settings**
5. Create volumes and add archives

## ⚙️ Requirements

- **WordPress**: 5.0 or higher
- **PHP**: 7.4 or higher
- **MySQL**: 5.6 or higher

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) for details on how to contribute to this project.

## 📄 License

This plugin is licensed under the [GNU General Public License v3.0](LICENSE).

**Author**: Paul H. Lee

## 📞 Support

For support and questions, please open an issue on GitHub or contact the plugin author.

## 📝 Changelog

See [CHANGELOG.md](CHANGELOG.md) for a complete list of changes and version history.

## 🎨 Fonts

This plugin now uses the WordPress theme's default fonts for a cleaner, more integrated appearance. The retro BBS styling is maintained through colors and layout while respecting the theme's typography choices.

---

**Made with ❤️ for the Apple II and BBS community**
