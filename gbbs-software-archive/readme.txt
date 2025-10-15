=== GBBS Software Archive ===
Contributors: paulhlee1967
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

A WordPress plugin designed to archive and manage GBBS Pro and GBBS II software packages in a classic 80's BBS-style interface.

== Description ==

The GBBS Software Archive plugin creates a complete software archive system specifically designed for vintage Apple II BBS software, particularly GBBS Pro and GBBS II. It mimics the classic BBS file area structure with "Volumes" containing complete software packages, preserving the nostalgic experience of 1980s bulletin board systems.

**Key Features:**

* **BBS-Style Organization**: Classic BBS file areas (GBBS Pro, Games, Mods, Segs, Utilities, etc.)
* **Complete Archives**: Each archive contains all files - no separate patches or install disks
* **File Management**: Multiple files per archive with Apple II format support
* **Retro Interface**: ASCII art headers, classic BBS terminology, and responsive design
* **Security Features**: Download rate limiting, file type validation, and secure upload handling
* **Custom Post Types**: `gbbs_archive` for software packages and `gbbs_volume` for organization
* **Admin Interface**: Complete management system with meta boxes and settings
* **Download Tracking**: Statistics and analytics for file downloads
* **Search & Filter**: Find archives by volume, version, author, etc.
* **Gutenberg Integration**: Custom block for easy archive insertion in posts/pages
* **Individual Archive Links**: Link to specific archives with modal popup display
* **WordPress Admin Styling**: Clean, professional interface using WordPress fonts

**Perfect for:**
* Vintage Apple II software archives
* BBS software preservation projects
* Retro computing communities
* Historical software documentation
* Educational resources for classic computing

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/gbbs-software-archive/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure settings in **GBBS Archive > Settings**
4. Create volumes and add your first archive

== Frequently Asked Questions ==

= Does this plugin work with any theme? =
Yes! The plugin is designed to work with any WordPress theme, though it works particularly well with retro-themed sites.

= What file types are supported? =
The plugin supports all Apple II file formats including .dsk, .po, .do, .woz, .bas, .asm, .bin, .shk, .bny, .sea, and more.

= Can I organize files by different categories? =
Yes! The plugin uses a hierarchical taxonomy system called "Volumes" where you can create categories like "GBBS Pro", "Games", "Utilities", etc.

= Is there a limit on file sizes? =
File size limits are configurable in the plugin settings. By default, it follows WordPress upload limits.

= Does the plugin track downloads? =
Yes! The plugin includes comprehensive download tracking and statistics for all files.

= Can I restrict access to certain archives? =
Yes! The plugin includes user permission controls and can require login for downloads.

= Is the plugin mobile-friendly? =
Absolutely! The plugin uses responsive design and works well on all devices.

== Screenshots ==

1. BBS-style directory listing with retro ASCII art
2. Archive detail page showing complete package information
3. Admin interface for managing archives and files
4. Settings page with comprehensive configuration options
5. File upload interface with drag-and-drop support
6. Download statistics and analytics dashboard

== Changelog ==

= 1.0.1 =
* Fixed Volume column display issue in archive directory table
* Improved BBS header layout and alignment
* Enhanced statistics loading mechanism
* Fixed SQL query for proper volume name retrieval

= 1.0.0 =
* Initial release
* Complete BBS-style software archive system
* Custom post type and taxonomy support
* File upload and management system
* Download tracking and statistics
* Admin interface with meta boxes
* Settings management system
* Apple II file type detection
* Download rate limiting and security features
* Responsive design with retro styling
* Search and filtering capabilities
* Shortcode support for easy integration
* Gutenberg block editor integration
* Individual archive modal display
* Archive search and selection interface
* WordPress admin font integration

== Upgrade Notice ==

= 1.0.0 =
Initial release of the GBBS Software Archive plugin.

== Copyright ==

GBBS Software Archive WordPress Plugin, (C) 2025 Paul H. Lee
GBBS Software Archive is distributed under the terms of the GNU GPL.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
