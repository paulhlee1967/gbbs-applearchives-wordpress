<?php
/**
 * Plugin Name: GBBS Software Archive
 * Description: Archive and management system for GBBS Pro and GBBS II software archives.
 * Version: 1.0.0
 * Author: Paul H. Lee
 * Text Domain: gbbs-software-archive
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('GBBS_SOFTWARE_ARCHIVE_VERSION', '1.0.0');
define('GBBS_SOFTWARE_ARCHIVE_DIR', plugin_dir_path(__FILE__));
define('GBBS_SOFTWARE_ARCHIVE_URL', plugin_dir_url(__FILE__));
define('GBBS_SOFTWARE_ARCHIVE_BASENAME', plugin_basename(__FILE__));

// Include required files
require_once GBBS_SOFTWARE_ARCHIVE_DIR . 'includes/class-gbbs-software-archive.php';
require_once GBBS_SOFTWARE_ARCHIVE_DIR . 'includes/class-gbbs-settings.php';

// Initialize the plugin
function gbbs_software_archive_init() {
    $gbbs_archive = new GBBS_Software_Archive();
    $gbbs_archive->init();
}
add_action('plugins_loaded', 'gbbs_software_archive_init');

// Activation hook
register_activation_hook(__FILE__, 'gbbs_software_archive_activate');
function gbbs_software_archive_activate() {
    // Create custom post types and taxonomies
    $gbbs_archive = new GBBS_Software_Archive();
    $gbbs_archive->create_post_types();
    $gbbs_archive->create_taxonomies();
    $gbbs_archive->create_download_logs_table();
    
    // Flush rewrite rules
    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'gbbs_software_archive_deactivate');
function gbbs_software_archive_deactivate() {
    // Flush rewrite rules
    flush_rewrite_rules();
}