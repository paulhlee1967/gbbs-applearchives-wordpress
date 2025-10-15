<?php
/**
 * Main GBBS Software Archive class
 * 
 * This class handles the core functionality of the GBBS Software Archive plugin.
 * It manages custom post types, taxonomies, meta boxes, and provides the foundation
 * for the BBS-style software archive system.
 * 
 * @package GBBS_Software_Archive
 * @version 1.0.0
 * @author Paul H. Lee
 */

if (!defined('ABSPATH')) {
    exit;
}

class GBBS_Software_Archive {
    
    /**
     * Plugin version
     * 
     * @var string
     */
    public $version = GBBS_SOFTWARE_ARCHIVE_VERSION;
    
    /**
     * Plugin directory path
     * 
     * @var string
     */
    public $plugin_dir;
    
    /**
     * Plugin URL
     * 
     * @var string
     */
    public $plugin_url;
    
    /**
     * Settings instance
     * 
     * @var GBBS_Settings
     */
    public $settings;
    
    /**
     * Constructor
     * 
     * Initializes the plugin by setting up directory paths and URLs.
     */
    public function __construct() {
        $this->plugin_dir = GBBS_SOFTWARE_ARCHIVE_DIR;
        $this->plugin_url = GBBS_SOFTWARE_ARCHIVE_URL;
        $this->settings = new GBBS_Settings();
    }
    
    /**
     * Initialize the plugin
     * 
     * Sets up all WordPress hooks and actions needed for the plugin to function.
     * This includes creating post types, taxonomies, enqueuing scripts/styles,
     * adding admin menus, and registering shortcodes.
     */
    public function init() {
        // Load text domain for translations
        add_action('init', array($this, 'load_textdomain'));
        
        // Create post types and taxonomies
        add_action('init', array($this, 'create_post_types'));
        add_action('init', array($this, 'create_taxonomies'));
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // Add archive helper for Gutenberg block editor
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
        
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Add meta boxes
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        
        // Save meta data
        add_action('save_post', array($this, 'save_meta_data'));
        
        // Handle archive deletion
        add_action('before_delete_post', array($this, 'cleanup_archive_files'));
        
        // Add shortcodes
        add_action('init', array($this, 'register_shortcodes'));
        
        // Add explicit block editor support for gbbs_archive post type
        add_action('init', array($this, 'add_block_editor_support'));
        
        // Add AJAX handlers
        add_action('wp_ajax_gbbs_get_archives', array($this, 'ajax_get_archives'));
        add_action('wp_ajax_gbbs_get_volumes', array($this, 'ajax_get_volumes'));
        
        // Add upload directory filter
        add_filter('upload_dir', array($this, 'gbbs_upload_dir'));
        
        // Allow additional file types for uploads
        add_filter('upload_mimes', array($this, 'allow_gbbs_file_types'));
        
        // Handle files with multiple dots in filename
        // add_filter('wp_handle_upload_prefilter', array($this, 'handle_multiple_dots_upload'));
        
        // Allow files with multiple dots
        add_filter('wp_check_filetype_and_ext', array($this, 'allow_multiple_dots_filetype'), 10, 4);
        
        // Preserve multiple dots in filenames for GBBS archives
        add_filter('sanitize_file_name', array($this, 'preserve_multiple_dots_filename'), 10, 2);
        
        // Fix attachment URLs to use original filenames for GBBS archives
        add_filter('wp_get_attachment_url', array($this, 'fix_attachment_url_for_gbbs'), 10, 2);
        
        // Make GBBS Volume taxonomy single select
        add_action('admin_init', array($this, 'make_volume_single_select'));
        
        // Hide default custom fields meta box for GBBS archives
        add_action('add_meta_boxes', array($this, 'hide_default_meta_boxes'));
        
        // Add template filter for GBBS archives
        add_filter('single_template', array($this, 'gbbs_archive_template'));
        
        // Add AJAX handlers
        add_action('wp_ajax_gbbs_get_archive_info', array($this, 'ajax_get_archive_info'));
        add_action('wp_ajax_nopriv_gbbs_get_archive_info', array($this, 'ajax_get_archive_info'));
        
        // Add download tracking
        add_action('init', array($this, 'add_download_endpoint'));
        add_action('template_redirect', array($this, 'handle_download_request'));
        
        // Ensure rewrite rules are flushed on init
        add_action('init', array($this, 'maybe_flush_rewrite_rules'), 20);
        
        // Add admin notices for missing files
        add_action('admin_notices', array($this, 'check_archive_files_exist'));
        
        // Add custom Quick Edit for volume selection
        add_action('quick_edit_custom_box', array($this, 'add_volume_quick_edit'), 10, 2);
        
        // Rename the taxonomy column to "Volume" and remove category columns
        add_filter('manage_gbbs_archive_posts_columns', array($this, 'rename_taxonomy_column'));
        
        // Additional filter to ensure category columns are removed (runs after other plugins)
        add_filter('manage_gbbs_archive_posts_columns', array($this, 'remove_category_columns'), 20);
        
        // Add custom column content display
        add_action('manage_gbbs_archive_posts_custom_column', array($this, 'display_custom_columns'), 10, 2);
        
    }
    
    /**
     * Load text domain for translations
     * 
     * Loads the plugin's text domain for internationalization support.
     */
    public function load_textdomain() {
        load_plugin_textdomain('gbbs-software-archive', false, dirname(GBBS_SOFTWARE_ARCHIVE_BASENAME) . '/languages');
    }
    
    /**
     * Create custom post types
     * 
     * Registers the 'gbbs_archive' custom post type for managing GBBS software archives.
     * Each archive represents a complete software set (no fragmented downloads).
     */
    public function create_post_types() {
        // GBBS Archive post type
        register_post_type('gbbs_archive', array(
            'labels' => array(
                'name' => __('GBBS Archives', 'gbbs-software-archive'),
                'singular_name' => __('GBBS Archive', 'gbbs-software-archive'),
                'add_new' => __('Add Archive', 'gbbs-software-archive'),
                'add_new_item' => __('Add Archive', 'gbbs-software-archive'),
                'edit_item' => __('Edit GBBS Archive', 'gbbs-software-archive'),
                'new_item' => __('New GBBS Archive', 'gbbs-software-archive'),
                'view_item' => __('View GBBS Archive', 'gbbs-software-archive'),
                'search_items' => __('Search GBBS Archives', 'gbbs-software-archive'),
                'not_found' => __('No GBBS archives found', 'gbbs-software-archive'),
                'not_found_in_trash' => __('No GBBS archives found in Trash', 'gbbs-software-archive'),
            ),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_admin_bar' => true,
            'show_in_rest' => true, // Required for block editor support
            'menu_position' => 25,
            'menu_icon' => 'dashicons-archive',
            'capability_type' => 'post',
            'hierarchical' => false,
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'has_archive' => true,
            'rewrite' => array('slug' => $this->settings->get_post_type_endpoint()),
            'query_var' => true,
        ));
    }
    
    /**
     * Add explicit block editor support for gbbs_archive post type
     * 
     * Ensures the block editor is used for gbbs_archive posts
     */
    public function add_block_editor_support() {
        // Add block editor support for gbbs_archive post type
        add_post_type_support('gbbs_archive', 'editor');
        
        // Force block editor for gbbs_archive post type
        add_filter('use_block_editor_for_post_type', array($this, 'force_block_editor_for_archive'), 10, 2);
    }
    
    /**
     * Force block editor for gbbs_archive post type
     * 
     * @param bool $use_block_editor Whether to use block editor
     * @param string $post_type The post type
     * @return bool Whether to use block editor
     */
    public function force_block_editor_for_archive($use_block_editor, $post_type) {
        if ($post_type === 'gbbs_archive') {
            return true;
        }
        return $use_block_editor;
    }
    
    /**
     * Create custom taxonomies
     * 
     * Registers the 'gbbs_volume' taxonomy for organizing archives into BBS-style
     * file areas. Volumes represent different categories of software (GBBS Pro,
     * Games, Mods, Segs, Utilities, etc.).
     */
    public function create_taxonomies() {
        // GBBS Volume taxonomy (like BBS file areas)
        register_taxonomy('gbbs_volume', 'gbbs_archive', array(
            'labels' => array(
                'name' => __('GBBS Volumes', 'gbbs-software-archive'),
                'singular_name' => __('GBBS Volume', 'gbbs-software-archive'),
                'search_items' => __('Search Volumes', 'gbbs-software-archive'),
                'all_items' => __('All Volumes', 'gbbs-software-archive'),
                'parent_item' => __('Parent Volume', 'gbbs-software-archive'),
                'parent_item_colon' => __('Parent Volume:', 'gbbs-software-archive'),
                'edit_item' => __('Edit Volume', 'gbbs-software-archive'),
                'update_item' => __('Update Volume', 'gbbs-software-archive'),
                'add_new_item' => __('Add New Volume', 'gbbs-software-archive'),
                'new_item_name' => __('New Volume Name', 'gbbs-software-archive'),
                'menu_name' => __('Volumes', 'gbbs-software-archive'),
            ),
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_in_quick_edit' => false, // Disable default Quick Edit to use custom dropdown
            'rewrite' => array('slug' => $this->settings->get_volume_endpoint()),
        ));
    }
    
    /**
     * Enqueue public scripts and styles
     * 
     * Loads CSS and JavaScript files for the frontend display.
     * Includes BBS-style styling and interactive functionality.
     */
    public function enqueue_public_scripts() {
        // Always load CSS and JS for shortcode compatibility
        wp_enqueue_style(
            'gbbs-archive-style',
            $this->plugin_url . 'assets/css/gbbs-archive.css',
            array(),
            $this->version
        );
        
        wp_enqueue_script(
            'gbbs-archive-script',
            $this->plugin_url . 'assets/js/gbbs-archive.js',
            array('jquery'),
            $this->version,
            true
        );
        
        // Custom CSS and JS functionality can be added here in the future
    }
    
    /**
     * Enqueue admin scripts and styles
     * 
     * Loads CSS and JavaScript files for the WordPress admin interface.
     * Loads on all admin pages like lana-downloads-manager does.
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on GBBS archive pages and post edit screens
        if (strpos($hook, 'gbbs_archive') === false && !in_array($hook, array('post.php', 'post-new.php'))) {
            return;
        }
        
        // Enqueue media library
        wp_enqueue_media();
        
        // Register and enqueue admin script
        wp_register_script(
            'gbbs-admin-script',
            $this->plugin_url . 'assets/js/gbbs-admin.js',
            array('jquery'),
            $this->version,
            true
        );
        wp_enqueue_script('gbbs-admin-script');
        
        // Register and enqueue admin style
        wp_register_style(
            'gbbs-admin-style',
            $this->plugin_url . 'assets/css/gbbs-admin.css',
            array(),
            $this->version
        );
        wp_enqueue_style('gbbs-admin-style');
        
        // Add localization for JavaScript
        wp_localize_script('gbbs-admin-script', 'gbbsAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gbbs_admin_nonce'),
            'strings' => array(
                'confirmRemove' => __('Are you sure you want to remove this file?', 'gbbs-software-archive'),
                'selectFile' => __('Choose a file', 'gbbs-software-archive'),
                'insertFile' => __('Insert file URL', 'gbbs-software-archive')
            )
        ));
    }
    
    /**
     * Add admin menu
     * 
     * Creates the admin menu structure for managing GBBS archives and settings.
     */
    public function add_admin_menu() {
        // Change the first submenu item from "GBBS Archives" to "All Archives"
        global $submenu;
        if (isset($submenu['edit.php?post_type=gbbs_archive'])) {
            $submenu['edit.php?post_type=gbbs_archive'][5][0] = __('All Archives', 'gbbs-software-archive');
        }
        
        add_submenu_page(
            'edit.php?post_type=gbbs_archive',
            __('Download Statistics', 'gbbs-software-archive'),
            __('Statistics', 'gbbs-software-archive'),
            'manage_options',
            'gbbs-download-stats',
            array($this, 'download_stats_page')
        );
        
        add_submenu_page(
            'edit.php?post_type=gbbs_archive',
            __('GBBS Archive Settings', 'gbbs-software-archive'),
            __('Settings', 'gbbs-software-archive'),
            'manage_options',
            'gbbs-archive-settings',
            array($this, 'settings_page')
        );
    }
    
    /**
     * Add meta boxes
     * 
     * Registers meta boxes for the GBBS archive edit screen.
     * Includes archive information and file management interfaces.
     */
    public function add_meta_boxes() {
        add_meta_box(
            'gbbs-archive-info',
            __('GBBS Archive Information', 'gbbs-software-archive'),
            array($this, 'archive_info_meta_box'),
            'gbbs_archive',
            'normal',
            'high'
        );
        
        add_meta_box(
            'gbbs-archive-files',
            __('Archive Files', 'gbbs-software-archive'),
            array($this, 'archive_files_meta_box'),
            'gbbs_archive',
            'normal',
            'high'
        );
    }
    
    /**
     * Hide default meta boxes for GBBS archives
     * 
     * Removes the default WordPress Custom Fields meta box to prevent
     * duplicate display of archive metadata.
     */
    public function hide_default_meta_boxes() {
        global $post_type;
        
        if ($post_type == 'gbbs_archive') {
            // Remove default custom fields meta box
            remove_meta_box('postcustom', 'gbbs_archive', 'normal');
            
            // Remove other default meta boxes that might interfere
            remove_meta_box('postexcerpt', 'gbbs_archive', 'normal');
            remove_meta_box('commentstatusdiv', 'gbbs_archive', 'normal');
            remove_meta_box('commentsdiv', 'gbbs_archive', 'normal');
            remove_meta_box('trackbacksdiv', 'gbbs_archive', 'normal');
            remove_meta_box('authordiv', 'gbbs_archive', 'normal');
            remove_meta_box('slugdiv', 'gbbs_archive', 'normal');
            remove_meta_box('revisionsdiv', 'gbbs_archive', 'normal');
        }
    }
    
    /**
     * Archive info meta box
     * 
     * Displays the archive information meta box on the edit screen.
     * Includes fields for version, author, requirements, etc.
     * 
     * @param WP_Post $post The current post object
     */
    public function archive_info_meta_box($post) {
        include $this->plugin_dir . 'views/archive-info-metabox.php';
    }
    
    /**
     * Archive files meta box
     * 
     * Displays the archive files meta box on the edit screen.
     * Allows management of multiple files per archive.
     * 
     * @param WP_Post $post The current post object
     */
    public function archive_files_meta_box($post) {
        include $this->plugin_dir . 'views/archive-files-metabox.php';
    }
    
    /**
     * Cleanup archive files when archive is deleted
     * 
     * Removes all files associated with an archive when it's deleted.
     * This includes both WordPress media attachments and custom directory files.
     * 
     * @param int $post_id The ID of the post being deleted
     */
    public function cleanup_archive_files($post_id) {
        // Only handle GBBS archives
        if (get_post_type($post_id) !== 'gbbs_archive') {
            return;
        }
        
        // Get archive files meta data
        $archive_files = get_post_meta($post_id, '_gbbs_archive_files', true);
        
        // Delete WordPress media attachments
        if (is_array($archive_files)) {
            foreach ($archive_files as $file) {
                if (isset($file['id']) && !empty($file['id'])) {
                    $attachment_id = intval($file['id']);
                    if ($attachment_id > 0) {
                        // Check if this attachment is only used by this archive
                        $attachment_posts = get_posts(array(
                            'post_type' => 'attachment',
                            'meta_query' => array(
                                array(
                                    'key' => '_wp_attachment_metadata',
                                    'value' => $attachment_id,
                                    'compare' => 'LIKE'
                                )
                            ),
                            'posts_per_page' => -1
                        ));
                        
                        // Also check if the attachment is referenced in other GBBS archives
                        $other_archives = get_posts(array(
                            'post_type' => 'gbbs_archive',
                            'post_status' => 'any',
                            'post__not_in' => array($post_id),
                            'meta_query' => array(
                                array(
                                    'key' => '_gbbs_archive_files',
                                    'value' => '"id":"' . $attachment_id . '"',
                                    'compare' => 'LIKE'
                                )
                            ),
                            'posts_per_page' => -1
                        ));
                        
                        // Only delete if not used by other archives
                        if (empty($other_archives)) {
                            wp_delete_attachment($attachment_id, true); // true = force delete, bypass trash
                        }
                    }
                }
            }
        }
        
        // Get the upload directory for this archive
        $upload_dir = $this->settings->get_upload_directory($post_id);
        
        if ($upload_dir && file_exists($upload_dir)) {
            // Recursively delete the archive directory and all its contents
            $this->recursive_rmdir($upload_dir);
        }
        
        // Also clean up any orphaned files in the general GBBS directory
        $this->cleanup_orphaned_files($post_id);
    }
    
    /**
     * Recursively remove directory and all contents
     * 
     * @param string $dir Directory path to remove
     * @return bool True on success, false on failure
     */
    private function recursive_rmdir($dir) {
        if (!is_dir($dir)) {
            return false;
        }
        
        $files = array_diff(scandir($dir), array('.', '..'));
        
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                $this->recursive_rmdir($path);
            } else {
                unlink($path);
            }
        }
        
        return rmdir($dir);
    }
    
    /**
     * Clean up orphaned files
     * 
     * Removes any files that might be orphaned after archive deletion.
     * This includes both custom directory files and WordPress media attachments.
     * 
     * @param int $deleted_archive_id The ID of the deleted archive
     */
    private function cleanup_orphaned_files($deleted_archive_id) {
        // Get the base GBBS upload directory
        $upload_dir = wp_upload_dir();
        $base_dir = $upload_dir['basedir'] . '/gbbs-archive';
        
        if (!$base_dir || !file_exists($base_dir)) {
            return;
        }
        
        // Look for any remaining files in the general GBBS directory
        $files = glob($base_dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                // Check if this file belongs to the deleted archive
                $file_content = file_get_contents($file);
                if (strpos($file_content, 'archive_id=' . $deleted_archive_id) !== false) {
                    unlink($file);
                }
            }
        }
        
        // Also check for orphaned WordPress media attachments
        // Find all attachments that might be orphaned
        $orphaned_attachments = get_posts(array(
            'post_type' => 'attachment',
            'post_status' => 'any',
            'meta_query' => array(
                array(
                    'key' => '_wp_attachment_metadata',
                    'value' => 'gbbs_archive',
                    'compare' => 'LIKE'
                )
            ),
            'posts_per_page' => -1
        ));
        
        foreach ($orphaned_attachments as $attachment) {
            // Check if this attachment is still referenced in any GBBS archive
            $referenced_archives = get_posts(array(
                'post_type' => 'gbbs_archive',
                'post_status' => 'any',
                'meta_query' => array(
                    array(
                        'key' => '_gbbs_archive_files',
                        'value' => '"id":"' . $attachment->ID . '"',
                        'compare' => 'LIKE'
                    )
                ),
                'posts_per_page' => -1
            ));
            
            // If not referenced in any archive, delete it
            if (empty($referenced_archives)) {
                wp_delete_attachment($attachment->ID, true); // true = force delete, bypass trash
            }
        }
    }
    
    /**
     * Save meta data
     * 
     * Handles saving of custom meta data when an archive is saved.
     * Validates and sanitizes all input data.
     * 
     * @param int $post_id The ID of the post being saved
     */
    public function save_meta_data($post_id) {
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check if this is a GBBS archive
        if (get_post_type($post_id) !== 'gbbs_archive') {
            return;
        }
        
        // Check user permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Check if user has permission to edit GBBS archives
        if (!$this->settings->can_user_edit_archives()) {
            return;
        }
        
        // Save archive information
        if (isset($_POST['gbbs_archive_info_nonce_field']) && 
            wp_verify_nonce($_POST['gbbs_archive_info_nonce_field'], 'gbbs_archive_info_nonce')) {
            
            $fields = array(
                'gbbs_archive_version',
                'gbbs_archive_author',
                'gbbs_archive_release_year',
                'gbbs_archive_requirements',
                'gbbs_archive_installation_notes',
                'gbbs_archive_historical_notes'
            );
            
            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    $value = sanitize_textarea_field($_POST[$field]);
                    update_post_meta($post_id, $field, $value);
                }
            }
        }
        
        // Save archive files
        if (isset($_POST['gbbs_archive_files_nonce_field']) && 
            wp_verify_nonce($_POST['gbbs_archive_files_nonce_field'], 'gbbs_archive_files_nonce')) {
            
            if (isset($_POST['gbbs_archive_files']) && is_array($_POST['gbbs_archive_files'])) {
                $archive_files = array();
                
                foreach ($_POST['gbbs_archive_files'] as $file_data) {
                    if (!empty($file_data['url'])) {
                        $file = array(
                            'id' => sanitize_text_field($file_data['id']),
                            'url' => esc_url_raw($file_data['url']),
                            'name' => sanitize_text_field($file_data['name']),
                            'category' => sanitize_text_field($file_data['category']),
                            'description' => sanitize_textarea_field($file_data['description'])
                        );
                        
                        // Validate file URL
                        if (wp_http_validate_url($file['url'])) {
                            // Check if file type is allowed (only if restrictions are enabled)
                            if ($this->settings->get_setting('restrict_file_types', true)) {
                                $allowed_types = $this->settings->get_allowed_file_types();
                                if (!empty($allowed_types) && !$this->validate_file_type($file['name'])) {
                                    // File type not allowed, show warning
                                    add_action('admin_notices', function() use ($file, $allowed_types) {
                                        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                                        echo '<div class="notice notice-warning is-dismissible"><p>' . 
                                             sprintf(__('File "%s" was not added because the file type "%s" is not allowed. Allowed types: %s', 'gbbs-software-archive'), 
                                                    esc_html($file['name']), 
                                                    esc_html($file_extension),
                                                    esc_html(implode(', ', $allowed_types))) . 
                                             '</p></div>';
                                    });
                                    continue;
                                }
                            }
                            
                            // Validate file size (if it's a local file)
                            $upload_dir = wp_upload_dir();
                            if (strpos($file['url'], $upload_dir['baseurl']) !== false) {
                                $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $file['url']);
                                if (file_exists($file_path)) {
                                    $file_size = filesize($file_path);
                                    if (!$this->validate_file_size($file_size)) {
                                        // File too large, skip it
                                        add_action('admin_notices', function() use ($file) {
                                            echo '<div class="notice notice-warning is-dismissible"><p>' . 
                                                 sprintf(__('File "%s" was not added because it exceeds the maximum file size limit.', 'gbbs-software-archive'), esc_html($file['name'])) . 
                                                 '</p></div>';
                                        });
                                        continue;
                                    }
                                }
                            }
                            
                            // If we get here, the file is valid
                            $archive_files[] = $file;
                        }
                    }
                }
                
                update_post_meta($post_id, '_gbbs_archive_files', $archive_files);
            } else {
                // Clear files if none provided
                delete_post_meta($post_id, '_gbbs_archive_files');
            }
        }
        
        // Save volume selection
        if (isset($_POST['gbbs_volume'])) {
            $volume_id = intval($_POST['gbbs_volume']);
            if ($volume_id > 0) {
                wp_set_post_terms($post_id, array($volume_id), 'gbbs_volume');
            } else {
                wp_set_post_terms($post_id, array(), 'gbbs_volume');
            }
        }
        
        // Save volume selection from Quick Edit
        if (isset($_POST['gbbs_volume_quick_edit'])) {
            $volume_id = intval($_POST['gbbs_volume_quick_edit']);
            if ($volume_id > 0) {
                wp_set_post_terms($post_id, array($volume_id), 'gbbs_volume');
            } else {
                // If empty value, don't change the volume (— No Change — option)
                // This allows users to keep existing volume when using Quick Edit
            }
        }
        
        // Organize any temp files to the proper archive folder
        $this->settings->organize_temp_files($post_id);
        
        // Clean up any files that were removed from this archive
        $this->cleanup_removed_files($post_id);
    }
    
    /**
     * Clean up files that were removed from an archive
     * 
     * Compares the current files with previously saved files and removes
     * any WordPress media attachments that are no longer referenced.
     * 
     * @param int $post_id The ID of the post being saved
     */
    private function cleanup_removed_files($post_id) {
        // Only handle GBBS archives
        if (get_post_type($post_id) !== 'gbbs_archive') {
            return;
        }
        
        // Get current files
        $current_files = get_post_meta($post_id, '_gbbs_archive_files', true);
        if (!is_array($current_files)) {
            $current_files = array();
        }
        
        // Get current attachment IDs
        $current_attachment_ids = array();
        foreach ($current_files as $file) {
            if (isset($file['id']) && !empty($file['id'])) {
                $current_attachment_ids[] = intval($file['id']);
            }
        }
        
        // Get previously saved files from post meta (before this save)
        $previous_files = get_post_meta($post_id, '_gbbs_archive_files_previous', true);
        if (!is_array($previous_files)) {
            $previous_files = array();
        }
        
        // Get previous attachment IDs
        $previous_attachment_ids = array();
        foreach ($previous_files as $file) {
            if (isset($file['id']) && !empty($file['id'])) {
                $previous_attachment_ids[] = intval($file['id']);
            }
        }
        
        // Find removed attachment IDs
        $removed_attachment_ids = array_diff($previous_attachment_ids, $current_attachment_ids);
        
        // Delete removed attachments if they're not used by other archives
        foreach ($removed_attachment_ids as $attachment_id) {
            // Check if this attachment is still referenced in other GBBS archives
            $other_archives = get_posts(array(
                'post_type' => 'gbbs_archive',
                'post_status' => 'any',
                'post__not_in' => array($post_id),
                'meta_query' => array(
                    array(
                        'key' => 'gbbs_archive_files',
                        'value' => '"id":"' . $attachment_id . '"',
                        'compare' => 'LIKE'
                    )
                ),
                'posts_per_page' => -1
            ));
            
            // If not referenced in other archives, delete it
            if (empty($other_archives)) {
                wp_delete_attachment($attachment_id, true); // true = force delete, bypass trash
            }
        }
        
        // Store current files as previous for next comparison
        update_post_meta($post_id, '_gbbs_archive_files_previous', $current_files);
    }
    
    /**
     * Register shortcodes
     * 
     * Registers shortcodes for displaying GBBS archives and directories
     * on the frontend.
     */
    public function register_shortcodes() {
        add_shortcode('gbbs_directory', array($this, 'gbbs_directory_shortcode'));
        add_shortcode('gbbs_archive', array($this, 'gbbs_archive_shortcode'));
        
        // Register AJAX handlers for async stats loading
        add_action('wp_ajax_gbbs_load_stats', array($this, 'ajax_load_stats'));
        add_action('wp_ajax_nopriv_gbbs_load_stats', array($this, 'ajax_load_stats'));
        
        // Clear cache when archives are updated
        add_action('save_post', array($this, 'clear_cache_on_archive_update'));
        add_action('delete_post', array($this, 'clear_cache_on_archive_update'));
        add_action('wp_trash_post', array($this, 'clear_cache_on_archive_update'));
    }
    
    /**
     * GBBS Directory shortcode
     * 
     * Displays a BBS-style directory listing of all archives.
     * 
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function gbbs_directory_shortcode($atts) {
        // Get URL parameters for volume, search, pagination, and sorting
        $url_volume = isset($_GET['volume']) ? sanitize_text_field($_GET['volume']) : '';
        $url_search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
        $url_page = isset($_GET['gbbs_page']) ? max(1, intval($_GET['gbbs_page'])) : 1;
        $url_sort = isset($_GET['gbbs_sort']) ? sanitize_text_field($_GET['gbbs_sort']) : '';
        $url_sort_dir = isset($_GET['gbbs_sort_dir']) ? sanitize_text_field($_GET['gbbs_sort_dir']) : '';
        
        $atts = shortcode_atts(array(
            'volume' => $url_volume,
            'search' => $url_search,
            'limit' => $this->settings->get_items_per_page(),
            'hide_title' => false
        ), $atts);
        
        $per_page = intval($atts['limit']);
        $offset = ($url_page - 1) * $per_page;
        
        // Add body class for styling
        add_filter('body_class', function($classes) {
            $classes[] = 'gbbs-archive';
            return $classes;
        });
        
        // Query archives with pagination
        $args = array(
            'post_type' => 'gbbs_archive',
            'post_status' => 'publish',
            'posts_per_page' => $per_page,
            'offset' => $offset,
            'paged' => $url_page
        );
        
        // Apply sorting - use URL parameters if available, otherwise use default
        $sort_field = !empty($url_sort) ? $url_sort : $this->settings->get_default_sorting();
        $sort_direction = !empty($url_sort_dir) ? $url_sort_dir : 'asc';
        
        // Validate sort direction
        if (!in_array($sort_direction, ['asc', 'desc'])) {
            $sort_direction = 'asc';
        }
        
        switch ($sort_field) {
            case 'date':
            case 'uploaded':
                $args['orderby'] = 'date';
                $args['order'] = strtoupper($sort_direction);
                break;
            case 'name':
                $args['orderby'] = 'title';
                $args['order'] = strtoupper($sort_direction);
                break;
            case 'downloads':
                // This will be handled after query with custom sorting
                break;
            case 'size':
                // This will be handled after query with custom sorting
                break;
            case 'version':
                // This will be handled after query with custom sorting
                break;
            case 'volume':
                // This will be handled after query with custom sorting
                break;
            default:
                // Fallback to default sorting
                $default_sort = $this->settings->get_default_sorting();
                switch ($default_sort) {
                    case 'date':
                        $args['orderby'] = 'date';
                        $args['order'] = 'DESC';
                        break;
                    case 'name':
                        $args['orderby'] = 'title';
                        $args['order'] = 'ASC';
                        break;
                }
                break;
        }
        
        // Filter by volume if specified
        if (!empty($atts['volume'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'gbbs_volume',
                    'field' => 'slug',
                    'terms' => $atts['volume']
                )
            );
        }
        
        // Search functionality - search only by archive name (title)
        if (!empty($atts['search'])) {
            $args['s'] = $atts['search'];
        }
        
        // Get archives and total count for pagination using WP_Query
        $query = new WP_Query($args);
        $archives = $query->posts;
        $total_archives = $query->found_posts;
        $total_pages = $query->max_num_pages;
        
        // Handle custom sorting for fields that can't be sorted by WP_Query
        if (in_array($sort_field, ['downloads', 'size', 'version', 'volume'])) {
            // Get all archives for custom sorting (without pagination)
            $all_args = $args;
            unset($all_args['posts_per_page']);
            unset($all_args['offset']);
            unset($all_args['paged']);
            $all_args['numberposts'] = -1;
            
            $all_query = new WP_Query($all_args);
            $all_archives = $all_query->posts;
            
            // Sort all archives
            usort($all_archives, function($a, $b) use ($sort_field, $sort_direction) {
                $a_value = $this->get_archive_sort_value($a, $sort_field);
                $b_value = $this->get_archive_sort_value($b, $sort_field);
                
                $comparison = 0;
                if ($a_value < $b_value) {
                    $comparison = -1;
                } elseif ($a_value > $b_value) {
                    $comparison = 1;
                }
                
                return $sort_direction === 'desc' ? -$comparison : $comparison;
            });
            
            // Apply pagination to sorted results
            $archives = array_slice($all_archives, $offset, $per_page);
            $total_archives = count($all_archives);
            $total_pages = ceil($total_archives / $per_page);
        }
        
        // Get all volumes for navigation
        $volumes = get_terms(array(
            'taxonomy' => 'gbbs_volume',
            'hide_empty' => false
        ));
        
        ob_start();
        ?>
        <!-- GBBS Directory Shortcode -->
        <style>
        #gbbs-loading-spinner {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            font-family: var(--wp--preset--font-family--apple-ii, monospace);
        }
        #gbbs-loading-spinner .gbbs-loading-spinner {
            background: var(--wp--preset--color--black, #000);
            border: 2px solid var(--wp--preset--color--light-green, #0f0);
            border-radius: 4px;
            padding: 20px;
            text-align: center;
            color: var(--wp--preset--color--light-green, #0f0);
            font-size: 14px;
            min-width: 200px;
        }
        #gbbs-loading-spinner .gbbs-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid var(--wp--preset--color--dark-grey, #333);
            border-radius: 50%;
            border-top-color: var(--wp--preset--color--light-green, #0f0);
            animation: gbbs-spin 1s ease-in-out infinite;
            margin-right: 10px;
        }
        @keyframes gbbs-spin {
            to { transform: rotate(360deg); }
        }
        #gbbs-loading-spinner .gbbs-loading-text {
            display: inline-block;
            vertical-align: middle;
        }
        </style>
        
        <div class="gbbs-bbs-directory" data-style="bbs">
            <!-- Loading Spinner -->
            <div id="gbbs-loading-spinner">
                <div class="gbbs-loading-spinner">
                    <div class="gbbs-spinner"></div>
                    <span class="gbbs-loading-text">Loading Archives...</span>
                </div>
            </div>
            
            <!-- Content Container (initially hidden) -->
            <div id="gbbs-directory-content" style="display: none;">
            
            <!-- BBS Intro Header -->
            <div class="bbs-intro-header">
                <div class="bbs-system-info">
                    <div class="bbs-welcome">
                        <p class="bbs-welcome-text"><?php echo esc_html($this->settings->get_archive_title()); ?></p>
                        <p class="bbs-subtitle"><?php echo esc_html($this->settings->get_archive_description()); ?></p>
                    </div>
                    
                    <?php if ($this->settings->get_setting('show_archive_stats', true)): ?>
                    <div class="bbs-stats" id="gbbs-stats-container">
                        <div class="bbs-stat-item">
                            <span class="bbs-stat-label">Archives Available:</span>
                            <span class="bbs-stat-value"><?php echo $total_archives; ?></span>
                        </div>
                        <div class="bbs-stat-item">
                            <span class="bbs-stat-label">Loading additional stats...</span>
                            <span class="bbs-stat-value" id="gbbs-loading-stats">⏳</span>
                        </div>
                    </div>
                    <script>
                    // Load statistics asynchronously to improve initial page load
                    document.addEventListener('DOMContentLoaded', function() {
                        // Load stats via AJAX after page load
                        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'action=gbbs_load_stats&search=<?php echo esc_js($atts['search']); ?>&volume=<?php echo esc_js($atts['volume']); ?>&nonce=<?php echo wp_create_nonce('gbbs_stats_nonce'); ?>'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Remove the loading stat item and insert the new stats
                                var loadingElement = document.getElementById('gbbs-loading-stats').parentElement;
                                loadingElement.remove();
                                
                                // Insert the new stats into the stats container
                                var statsContainer = document.getElementById('gbbs-stats-container');
                                statsContainer.insertAdjacentHTML('beforeend', data.data);
                            }
                        })
                        .catch(error => {
                            console.log('Stats loading failed:', error);
                            document.getElementById('gbbs-loading-stats').parentElement.innerHTML = '<span class="bbs-stat-label">Stats unavailable</span><span class="bbs-stat-value">-</span>';
                        });
                    });
                    </script>
                    <?php endif; ?>
                </div>
            </div>
        
        <div class="gbbs-directory-listing">
            
            <div class="bbs-header">
                <?php if ($this->settings->get_setting('enable_search', true)): ?>
                <div class="bbs-search">
                    <label for="gbbs-search-input" class="screen-reader-text">Search archives by name</label>
                    <input type="text" 
                           id="gbbs-search-input" 
                           placeholder="Search archives..." 
                           class="bbs-search-input"
                           value="<?php echo esc_attr($atts['search']); ?>"
                           aria-describedby="search-help"
                           onkeypress="if(event.key==='Enter') gbbsSearchArchives()">
                    <button type="button" 
                            id="gbbs-search-btn" 
                            class="bbs-search-btn" 
                            onclick="gbbsSearchArchives()"
                            aria-label="Search archives">
                        Search
                    </button>
                    <button type="button" 
                            id="gbbs-reset-btn" 
                            class="bbs-reset-btn" 
                            onclick="gbbsResetSearch()"
                            aria-label="Clear search and filters">
                        Reset
                    </button>
                    <div id="search-help" class="screen-reader-text">Press Enter to search or click the Search button</div>
                </div>
                <?php endif; ?>
                
                <?php if ($this->settings->get_setting('enable_volume_filter', true)): ?>
                <div class="bbs-volume-filter">
                    <label for="gbbs-volume-filter">Filter by Volume:</label>
                    <select id="gbbs-volume-filter" 
                            class="bbs-volume-select" 
                            onchange="gbbsFilterByVolume(this.value)"
                            aria-label="Filter archives by volume">
                        <option value="" <?php selected(empty($atts['volume']), true); ?>>All Volumes</option>
                        <?php foreach ($volumes as $volume): ?>
                            <option value="<?php echo esc_attr($volume->slug); ?>"
                                    <?php selected($atts['volume'], $volume->slug); ?>>
                                <?php echo esc_html($volume->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
            </div>
            
                    <div class="gbbs-directory-container">
                        <div class="gbbs-directory-table" role="table" aria-label="GBBS Software Archives Directory">
                            <div class="gbbs-table-header" role="rowgroup">
                                <div class="gbbs-col-num" role="columnheader" aria-label="Row number">###</div>
                                <div class="gbbs-col-filename <?php echo $this->settings->get_setting('enable_sorting', true) ? 'gbbs-sortable' : ''; ?>" role="columnheader" aria-label="Archive name">
                                    <?php if ($this->settings->get_setting('enable_sorting', true)): ?>
                                        <a href="<?php echo add_query_arg(array('gbbs_sort' => 'name', 'gbbs_sort_dir' => ($sort_field === 'name' && $sort_direction === 'asc') ? 'desc' : 'asc', 'gbbs_page' => 1)); ?>" class="gbbs-sort-link">
                                            Name <?php echo ($sort_field === 'name') ? ($sort_direction === 'asc' ? '↑' : '↓') : ''; ?>
                                        </a>
                                    <?php else: ?>
                                        Name
                                    <?php endif; ?>
                                </div>
                                <div class="gbbs-col-version <?php echo $this->settings->get_setting('enable_sorting', true) ? 'gbbs-sortable' : ''; ?>" role="columnheader" aria-label="Archive version">
                                    <?php if ($this->settings->get_setting('enable_sorting', true)): ?>
                                        <a href="<?php echo add_query_arg(array('gbbs_sort' => 'version', 'gbbs_sort_dir' => ($sort_field === 'version' && $sort_direction === 'asc') ? 'desc' : 'asc', 'gbbs_page' => 1)); ?>" class="gbbs-sort-link">
                                            Version <?php echo ($sort_field === 'version') ? ($sort_direction === 'asc' ? '↑' : '↓') : ''; ?>
                                        </a>
                                    <?php else: ?>
                                        Version
                                    <?php endif; ?>
                                </div>
                                <?php if ($this->settings->get_setting('show_file_sizes', true)): ?>
                                <div class="gbbs-col-size <?php echo $this->settings->get_setting('enable_sorting', true) ? 'gbbs-sortable' : ''; ?>" role="columnheader" aria-label="Total file size">
                                    <?php if ($this->settings->get_setting('enable_sorting', true)): ?>
                                        <a href="<?php echo add_query_arg(array('gbbs_sort' => 'size', 'gbbs_sort_dir' => ($sort_field === 'size' && $sort_direction === 'asc') ? 'desc' : 'asc', 'gbbs_page' => 1)); ?>" class="gbbs-sort-link">
                                            Size <?php echo ($sort_field === 'size') ? ($sort_direction === 'asc' ? '↑' : '↓') : ''; ?>
                                        </a>
                                    <?php else: ?>
                                        Size
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                <?php if ($this->settings->get_setting('show_upload_dates', true)): ?>
                                <div class="gbbs-col-uploaded <?php echo $this->settings->get_setting('enable_sorting', true) ? 'gbbs-sortable' : ''; ?>" role="columnheader" aria-label="Upload date">
                                    <?php if ($this->settings->get_setting('enable_sorting', true)): ?>
                                        <a href="<?php echo add_query_arg(array('gbbs_sort' => 'uploaded', 'gbbs_sort_dir' => ($sort_field === 'uploaded' && $sort_direction === 'asc') ? 'desc' : 'asc', 'gbbs_page' => 1)); ?>" class="gbbs-sort-link">
                                            Uploaded <?php echo ($sort_field === 'uploaded') ? ($sort_direction === 'asc' ? '↑' : '↓') : ''; ?>
                                        </a>
                                    <?php else: ?>
                                        Uploaded
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                <div class="gbbs-col-packer <?php echo $this->settings->get_setting('enable_sorting', true) ? 'gbbs-sortable' : ''; ?>" role="columnheader" aria-label="Volume">
                                    <?php if ($this->settings->get_setting('enable_sorting', true)): ?>
                                        <a href="<?php echo add_query_arg(array('gbbs_sort' => 'volume', 'gbbs_sort_dir' => ($sort_field === 'volume' && $sort_direction === 'asc') ? 'desc' : 'asc', 'gbbs_page' => 1)); ?>" class="gbbs-sort-link">
                                            Volume <?php echo ($sort_field === 'volume') ? ($sort_direction === 'asc' ? '↑' : '↓') : ''; ?>
                                        </a>
                                    <?php else: ?>
                                        Volume
                                    <?php endif; ?>
                                </div>
                                <?php if ($this->settings->get_setting('show_download_counts', true) && $this->settings->is_download_counter_enabled()): ?>
                                <div class="gbbs-col-downloads <?php echo $this->settings->get_setting('enable_sorting', true) ? 'gbbs-sortable' : ''; ?>" role="columnheader" aria-label="Download count">
                                    <?php if ($this->settings->get_setting('enable_sorting', true)): ?>
                                        <a href="<?php echo add_query_arg(array('gbbs_sort' => 'downloads', 'gbbs_sort_dir' => ($sort_field === 'downloads' && $sort_direction === 'asc') ? 'desc' : 'asc', 'gbbs_page' => 1)); ?>" class="gbbs-sort-link">
                                            Downloads <?php echo ($sort_field === 'downloads') ? ($sort_direction === 'asc' ? '↑' : '↓') : ''; ?>
                                        </a>
                                    <?php else: ?>
                                        Downloads
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="gbbs-table-body">
                                <?php if (!empty($archives)): ?>
                                    <?php
                                    // Pre-fetch all metadata to avoid N+1 queries
                                    $archive_ids = wp_list_pluck($archives, 'ID');
                                    $all_metadata = $this->get_bulk_archive_metadata($archive_ids);
                                    $all_volumes = $this->get_bulk_archive_volumes($archive_ids);
                                    $all_download_counts = $this->get_bulk_download_counts($archive_ids);
                                    ?>
                                    <?php foreach ($archives as $index => $archive): ?>
                                        <?php
                                        $archive_id = $archive->ID;
                                        $archive_files = $all_metadata[$archive_id]['files'] ?? array();
                                        $archive_version = $all_metadata[$archive_id]['version'] ?? '';
                                        $archive_author = $all_metadata[$archive_id]['author'] ?? '';
                                        $archive_release_year = $all_metadata[$archive_id]['release_year'] ?? '';

                                        // Calculate file stats
                                        $total_size = 0;
                                        $file_count = 0;
                                        if (is_array($archive_files)) {
                                            $file_count = count($archive_files);
                                            foreach ($archive_files as $file) {
                                                if (!empty($file['url'])) {
                                                    // Try to get file size
                                                    $file_size = $this->get_file_size($file['url']);
                                                    $total_size += $file_size;
                                                }
                                            }
                                        }

                                        // Format file size
                                        $formatted_size = $this->format_file_size($total_size);

                                        // Get volume info from pre-fetched data
                                        $volume_name = $all_volumes[$archive_id] ?? 'Uncategorized';
                                        
                                        // Get total download count for all files in archive
                                        $download_count = $all_download_counts[$archive_id] ?? 0;
                                        ?>
                                        <div class="gbbs-table-row" 
                                             role="row"
                                             data-archive-id="<?php echo $archive->ID; ?>" 
                                             data-sort-name="<?php echo esc_attr(strtolower($archive->post_title)); ?>"
                                             data-sort-version="<?php echo esc_attr(strtolower($archive_version ?: 'zzz')); ?>"
                                             data-sort-size="<?php echo $total_size; ?>"
                                             data-sort-uploaded="<?php echo get_the_date('Y-m-d', $archive); ?>"
                                             data-sort-volume="<?php echo esc_attr(strtolower($volume_name)); ?>"
                                             data-sort-downloads="<?php echo $download_count; ?>"
                                             tabindex="0"
                                             aria-label="Archive: <?php echo esc_attr($archive->post_title); ?>, Version: <?php echo esc_attr($archive_version ?: 'N/A'); ?>, Size: <?php echo esc_attr($formatted_size); ?>, Downloads: <?php echo $download_count; ?>"
                                             onclick="gbbsShowArchiveInfo(<?php echo $archive->ID; ?>)"
                                             onkeydown="if(event.key==='Enter'||event.key===' ') { event.preventDefault(); gbbsShowArchiveInfo(<?php echo $archive->ID; ?>); }">
                                            <div class="gbbs-col-num" role="cell"><?php echo str_pad($offset + $index + 1, 3, '0', STR_PAD_LEFT); ?></div>
                                            <div class="gbbs-col-filename" role="cell"><?php echo esc_html($archive->post_title); ?></div>
                                            <div class="gbbs-col-version" role="cell"><?php echo esc_html($archive_version ?: 'N/A'); ?></div>
                                            <?php if ($this->settings->get_setting('show_file_sizes', true)): ?>
                                            <div class="gbbs-col-size" role="cell"><?php echo $formatted_size; ?></div>
                                            <?php endif; ?>
                                            <?php if ($this->settings->get_setting('show_upload_dates', true)): ?>
                                            <div class="gbbs-col-uploaded" role="cell"><?php echo get_the_date('d-M-y', $archive); ?></div>
                                            <?php endif; ?>
                                            <div class="gbbs-col-packer" role="cell"><?php echo esc_html($volume_name); ?></div>
                                            <?php if ($this->settings->get_setting('show_download_counts', true) && $this->settings->is_download_counter_enabled()): ?>
                                            <div class="gbbs-col-downloads" role="cell"><?php echo $download_count; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="gbbs-empty">No archives found</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pagination Controls -->
                    <?php 
                    // Debug information (remove in production)
                    if (current_user_can('manage_options') && isset($_GET['debug'])) {
                        echo '<div style="background: #f0f0f0; padding: 10px; margin: 10px 0; border: 1px solid #ccc;">';
                        echo '<strong>Debug Info:</strong><br>';
                        echo 'Total Archives: ' . $total_archives . '<br>';
                        echo 'Per Page: ' . $per_page . '<br>';
                        echo 'Total Pages: ' . $total_pages . '<br>';
                        echo 'Current Page: ' . $url_page . '<br>';
                        echo 'Show Pagination: ' . ($total_pages > 1 ? 'YES' : 'NO') . '<br>';
                        echo 'Next Page URL: ' . add_query_arg('gbbs_page', $url_page + 1) . '<br>';
                        echo 'Prev Page URL: ' . add_query_arg('gbbs_page', $url_page - 1) . '<br>';
                        echo 'Current URL: ' . $_SERVER['REQUEST_URI'] . '<br>';
                        echo '</div>';
                    }
                    ?>
                    <?php if ($total_pages > 1 || (current_user_can('manage_options') && isset($_GET['debug']))): ?>
                        <nav class="gbbs-pagination" role="navigation" aria-label="Archive pagination">
                            <div class="gbbs-pagination-info" aria-live="polite">
                                Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $per_page, $total_archives); ?> of <?php echo $total_archives; ?> archives
                            </div>
                            <div class="gbbs-pagination-controls">
                                <?php if ($url_page > 1): ?>
                                    <a href="<?php echo add_query_arg(array('gbbs_page' => 1, 'gbbs_sort' => $sort_field, 'gbbs_sort_dir' => $sort_direction)); ?>" 
                                       class="gbbs-pagination-link gbbs-pagination-first"
                                       aria-label="Go to first page"><< First</a>
                                    <a href="<?php echo add_query_arg(array('gbbs_page' => $url_page - 1, 'gbbs_sort' => $sort_field, 'gbbs_sort_dir' => $sort_direction)); ?>" 
                                       class="gbbs-pagination-link gbbs-pagination-prev"
                                       aria-label="Go to previous page">< Prev</a>
                                <?php endif; ?>
                                
                                <span class="gbbs-pagination-current" aria-current="page">
                                    Page <?php echo $url_page; ?> of <?php echo $total_pages; ?>
                                </span>
                                
                                <?php if ($url_page < $total_pages): ?>
                                    <a href="<?php echo add_query_arg(array('gbbs_page' => $url_page + 1, 'gbbs_sort' => $sort_field, 'gbbs_sort_dir' => $sort_direction)); ?>" 
                                       class="gbbs-pagination-link gbbs-pagination-next"
                                       aria-label="Go to next page">Next ></a>
                                    <a href="<?php echo add_query_arg(array('gbbs_page' => $total_pages, 'gbbs_sort' => $sort_field, 'gbbs_sort_dir' => $sort_direction)); ?>" 
                                       class="gbbs-pagination-link gbbs-pagination-last"
                                       aria-label="Go to last page">Last >></a>
                                <?php endif; ?>
                            </div>
                        </nav>
                    <?php endif; ?>
        </div>
        
        <!-- Modal for archive info -->
        <div id="gbbs-modal-overlay" class="gbbs-modal-overlay" onclick="gbbsCloseModal()">
            <div id="gbbs-modal" class="gbbs-modal" onclick="event.stopPropagation()">
                <div class="gbbs-modal-header">
                    <div class="gbbs-modal-title" id="gbbs-modal-title">Archive Information</div>
                    <button class="gbbs-modal-close" onclick="gbbsCloseModal()">X</button>
                </div>
                <div class="gbbs-modal-content" id="gbbs-modal-content">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
        
        <script>
        function gbbsShowLoading(message) {
            // Remove any existing loading overlay
            gbbsHideLoading();
            
            var overlay = document.createElement('div');
            overlay.className = 'gbbs-loading-overlay';
            overlay.id = 'gbbs-loading-overlay';
            overlay.innerHTML = '<div class="gbbs-loading-spinner"><div class="gbbs-spinner"></div><span class="gbbs-loading-text">' + (message || 'Loading...') + '</span></div>';
            document.body.appendChild(overlay);
        }
        
        function gbbsHideLoading() {
            var overlay = document.getElementById('gbbs-loading-overlay');
            if (overlay) {
                overlay.remove();
            }
        }
        
        function gbbsFilterByVolume(volume) {
            gbbsShowLoading('Filtering archives...');
            var url = new URL(window.location);
            if (volume) {
                url.searchParams.set('volume', volume);
            } else {
                url.searchParams.delete('volume');
            }
            // Reset to page 1 when changing volume filter
            url.searchParams.delete('gbbs_page');
            window.location.href = url.toString();
        }
        
        // Add loading to pagination and sort links
        document.addEventListener('DOMContentLoaded', function() {
            var paginationLinks = document.querySelectorAll('.gbbs-pagination-link');
            paginationLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    gbbsShowLoading('Loading page...');
                    // Let the default behavior happen
                });
            });
            
            var sortLinks = document.querySelectorAll('.gbbs-sort-link');
            sortLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    gbbsShowLoading('Sorting archives...');
                    // Let the default behavior happen
                });
            });
        });
        
                    function gbbsSearchArchives() {
                        gbbsShowLoading('Searching archives...');
                        var searchInput = document.getElementById('gbbs-search-input');
                        var search = searchInput.value.trim();
                        var url = new URL(window.location);
                        
                        if (search.length > 0) {
                            url.searchParams.set('search', search);
                        } else {
                            url.searchParams.delete('search');
                        }
                        // Reset to page 1 when searching
                        url.searchParams.delete('gbbs_page');
                        window.location.href = url.toString();
                    }

                    function gbbsResetSearch() {
                        gbbsShowLoading('Resetting filters...');
                        document.getElementById('gbbs-search-input').value = '';
                        document.getElementById('gbbs-volume-filter').value = '';
                        var url = new URL(window.location);
                        url.searchParams.delete('search');
                        url.searchParams.delete('volume');
                        url.searchParams.delete('gbbs_page');
                        window.location.href = url.toString();
                    }
        
        function gbbsDownloadArchive(archiveId) {
            // This will be implemented for actual download functionality
            // For now, this function is a placeholder
        }
        
        function gbbsShowArchiveInfo(archiveId) {
            // Load archive info via AJAX
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        document.getElementById('gbbs-modal-title').textContent = response.data.title;
                        document.getElementById('gbbs-modal-content').innerHTML = response.data.content;
                        document.getElementById('gbbs-modal-overlay').classList.add('show');
                    }
                }
            };
            xhr.send('action=gbbs_get_archive_info&archive_id=' + archiveId + '&nonce=<?php echo wp_create_nonce('gbbs_archive_info'); ?>');
        }
        
        function gbbsCloseModal() {
            document.getElementById('gbbs-modal-overlay').classList.remove('show');
            // Refresh the page to show updated download counts
            window.location.reload();
        }
        
        // Hide loading spinner and show content when page is ready
        document.addEventListener('DOMContentLoaded', function() {
            const loadingSpinner = document.getElementById('gbbs-loading-spinner');
            const content = document.getElementById('gbbs-directory-content');
            
            if (loadingSpinner && content) {
                // Hide spinner and show content
                loadingSpinner.style.display = 'none';
                content.style.display = 'block';
            }
        });
        </script>
        <?php
        
        // Close the content container
        echo '</div>'; // Close gbbs-directory-content
        
        return ob_get_clean();
    }
    
    /**
     * GBBS Archive shortcode
     * 
     * Displays a specific archive with download options in a modal or inline.
     * 
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function gbbs_archive_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
            'button_text' => 'View Archive',
            'show_files' => true,
            'show_description' => true,
            'show_requirements' => true,
            'show_installation' => true,
            'show_historical' => true
        ), $atts);
        
        $archive_id = intval($atts['id']);
        if (!$archive_id) {
            return '<p style="color: #d63638;">Error: Archive ID required. Use [gbbs_archive id="123"]</p>';
        }
        
        $archive = get_post($archive_id);
        if (!$archive || $archive->post_type !== 'gbbs_archive') {
            return '<p style="color: #d63638;">Error: Archive not found.</p>';
        }
        
        if ($archive->post_status !== 'publish') {
            return '<p style="color: #d63638;">Error: Archive is not published.</p>';
        }
        
        // Always use modal display
        return $this->generate_archive_modal($archive_id, $atts);
    }
    
    /**
     * Generate archive modal HTML
     * 
     * @param int $archive_id Archive post ID
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    private function generate_archive_modal($archive_id, $atts) {
        $archive = get_post($archive_id);
        $unique_id = uniqid(); // Generate unique ID for this shortcode instance
        $modal_id = 'gbbs-archive-modal-' . $archive_id . '-' . $unique_id;
        $button_id = 'gbbs-archive-button-' . $archive_id . '-' . $unique_id;
        
        // Get archive metadata
        $archive_version = get_post_meta($archive_id, 'gbbs_archive_version', true);
        $archive_author = get_post_meta($archive_id, 'gbbs_archive_author', true);
        $archive_release_year = get_post_meta($archive_id, 'gbbs_archive_release_year', true);
        $archive_requirements = get_post_meta($archive_id, 'gbbs_archive_requirements', true);
        $archive_installation_notes = get_post_meta($archive_id, 'gbbs_archive_installation_notes', true);
        $archive_historical_notes = get_post_meta($archive_id, 'gbbs_archive_historical_notes', true);
        $archive_files = get_post_meta($archive_id, '_gbbs_archive_files', true);
        
        // Get volume information
        $volumes = get_the_terms($archive_id, 'gbbs_volume');
        $volume_name = !empty($volumes) ? $volumes[0]->name : 'Uncategorized';
        
        // Ensure archive_files is an array
        if (!is_array($archive_files)) {
            $archive_files = array();
        }
        
        ob_start();
        ?>
        <!-- Archive Button -->
        <button type="button" id="<?php echo esc_attr($button_id); ?>" class="gbbs-archive-button">
            <?php echo esc_html($atts['button_text']); ?>
        </button>
        
        <!-- Archive Modal - Using same structure as gbbs_directory modal -->
        <div id="<?php echo esc_attr($modal_id); ?>" class="gbbs-modal-overlay" onclick="gbbsCloseArchiveModal('<?php echo esc_js($modal_id); ?>')">
            <div class="gbbs-modal" onclick="event.stopPropagation()">
                <div class="gbbs-modal-header">
                    <div class="gbbs-modal-title"><?php echo esc_html($archive->post_title); ?></div>
                    <button class="gbbs-modal-close" onclick="gbbsCloseArchiveModal('<?php echo esc_js($modal_id); ?>')">X</button>
                </div>
                <div class="gbbs-modal-content">
                    <div class="gbbs-modal-section">
                        <h3>Archive Details</h3>
                        <p><strong>Title:</strong> <?php echo esc_html($archive->post_title); ?></p>
                        <?php if ($archive_version): ?>
                            <p><strong>Version:</strong> <?php echo esc_html($archive_version); ?></p>
                        <?php endif; ?>
                        <?php if ($archive_author): ?>
                            <p><strong>Author:</strong> <?php echo esc_html($archive_author); ?></p>
                        <?php endif; ?>
                        <?php if ($archive_release_year): ?>
                            <p><strong>Release Year:</strong> <?php echo esc_html($archive_release_year); ?></p>
                        <?php endif; ?>
                        <p><strong>Volume:</strong> <?php echo esc_html($volume_name); ?></p>
                    </div>
                    
                    <?php if ($atts['show_description'] && $archive->post_content): ?>
                        <div class="gbbs-modal-section">
                            <h3>Description</h3>
                            <p><?php echo wp_kses_post($archive->post_content); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($atts['show_requirements'] && $archive_requirements): ?>
                        <div class="gbbs-modal-section">
                            <h3>System Requirements</h3>
                            <p><?php echo nl2br(esc_html($archive_requirements)); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($atts['show_installation'] && $archive_installation_notes): ?>
                        <div class="gbbs-modal-section">
                            <h3>Installation Notes</h3>
                            <p><?php echo nl2br(esc_html($archive_installation_notes)); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($atts['show_historical'] && $archive_historical_notes): ?>
                        <div class="gbbs-modal-section">
                            <h3>Historical Notes</h3>
                            <p><?php echo nl2br(esc_html($archive_historical_notes)); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($atts['show_files'] && !empty($archive_files)): ?>
                        <div class="gbbs-modal-section">
                            <h3>Archive Files</h3>
                            <div class="gbbs-modal-files">
                                <table class="gbbs-modal-file-table">
                                    <thead>
                                        <tr>
                                            <th class="gbbs-modal-file-name-header">Name</th>
                                            <th class="gbbs-modal-file-type-header">Type</th>
                                            <th class="gbbs-modal-file-size-header">Size</th>
                                            <th class="gbbs-modal-file-category-header">Category</th>
                                            <th class="gbbs-modal-file-downloads-header">Downloads</th>
                                            <th class="gbbs-modal-file-action-header">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($archive_files as $file_index => $file): ?>
                                            <?php
                                            // Get file size
                                            $file_size = 0;
                                            if (!empty($file['url'])) {
                                                $file_size = $this->get_file_size($file['url']);
                                            }
                                            $formatted_size = $this->format_file_size($file_size);
                                            
                                            // Get download count for this file
                                            $file_download_count = $this->get_file_download_count($archive_id, $file['url']);
                                            
                                            // Get download URL
                                            $download_url = $this->get_download_url($archive_id, $file_index);
                                            
                                            // Get Apple II file type
                                            $filename = $file['name'] ?: basename($file['url']);
                                            $file_type_info = $this->get_apple_ii_file_type($filename);
                                            $file_type = $file_type_info['type'];
                                            $file_type_css = $this->get_file_type_css_class($file_type);
                                            ?>
                                            <tr class="gbbs-modal-file-row">
                                                <td class="gbbs-modal-file-name"><?php echo esc_html($filename); ?></td>
                                                <td class="gbbs-modal-file-type">
                                                    <span class="gbbs-file-type-indicator <?php echo esc_attr($file_type_css); ?>" title="<?php echo esc_attr($file_type_info['description']); ?>">
                                                        <?php echo esc_html($file_type); ?>
                                                    </span>
                                                </td>
                                                <td class="gbbs-modal-file-size"><?php echo esc_html($formatted_size); ?></td>
                                                <td class="gbbs-modal-file-category"><?php echo esc_html($this->get_category_display_name($file['category'] ?: 'other')); ?></td>
                                                <td class="gbbs-modal-file-downloads"><?php echo $file_download_count; ?></td>
                                                <td class="gbbs-modal-file-action">
                                                    <a href="<?php echo esc_url($download_url); ?>" class="gbbs-modal-file-download">
                                                        Download
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php if ($file['description']): ?>
                                                <tr class="gbbs-modal-file-description-row">
                                                    <td colspan="6" class="gbbs-modal-file-description">
                                                        <?php echo esc_html($file['description']); ?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            // Open modal
            $('#<?php echo esc_js($button_id); ?>').on('click', function(e) {
                e.preventDefault();
                $('#<?php echo esc_js($modal_id); ?>').addClass('show');
                $('body').addClass('gbbs-modal-open');
            });
        });
        
        // Global function to close archive modal (matches gbbs_directory modal)
        function gbbsCloseArchiveModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
            document.body.classList.remove('gbbs-modal-open');
        }
        </script>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Generate inline archive display
     * 
     * @param int $archive_id Archive post ID
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    private function generate_archive_inline($archive_id, $atts) {
        // For now, just return a simple inline display
        // This can be enhanced later if needed
        $archive = get_post($archive_id);
        $archive_version = get_post_meta($archive_id, 'gbbs_archive_version', true);
        $archive_author = get_post_meta($archive_id, 'gbbs_archive_author', true);
        
        ob_start();
        ?>
        <div class="gbbs-archive-inline">
            <h3><?php echo esc_html($archive->post_title); ?></h3>
            <p><strong>Version:</strong> <?php echo esc_html($archive_version ?: 'N/A'); ?></p>
            <p><strong>Author:</strong> <?php echo esc_html($archive_author ?: 'Unknown'); ?></p>
            <?php if ($archive->post_content): ?>
                <div class="gbbs-archive-description">
                    <?php echo wp_kses_post($archive->post_content); ?>
                </div>
            <?php endif; ?>
            <p><a href="<?php echo get_permalink($archive_id); ?>" class="gbbs-archive-link">View Full Archive</a></p>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * AJAX handler for getting archives
     * 
     * Used by the admin helper to load archives for selection
     */
    public function ajax_get_archives() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'gbbs_admin_nonce')) {
            wp_die('Security check failed');
        }
        
        // Check user capabilities
        if (!current_user_can('edit_posts')) {
            wp_die('Insufficient permissions');
        }
        
        $search = sanitize_text_field($_POST['search']);
        $volume = sanitize_text_field($_POST['volume']);
        
        $args = array(
            'post_type' => 'gbbs_archive',
            'post_status' => 'publish',
            'posts_per_page' => 50,
            'orderby' => 'title',
            'order' => 'ASC'
        );
        
        if (!empty($search)) {
            $args['s'] = $search;
        }
        
        if (!empty($volume)) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'gbbs_volume',
                    'field'    => 'slug',
                    'terms'    => $volume,
                ),
            );
        }
        
        $archives = get_posts($args);
        $results = array();
        
        foreach ($archives as $archive) {
            $archive_files = get_post_meta($archive->ID, '_gbbs_archive_files', true);
            $file_count = is_array($archive_files) ? count($archive_files) : 0;
            
            // Get volume information
            $volumes = get_the_terms($archive->ID, 'gbbs_volume');
            $volume_name = !empty($volumes) ? $volumes[0]->name : 'Uncategorized';
            
            $results[] = array(
                'ID' => $archive->ID,
                'post_title' => $archive->post_title,
                'version' => get_post_meta($archive->ID, 'gbbs_archive_version', true),
                'author' => get_post_meta($archive->ID, 'gbbs_archive_author', true),
                'file_count' => $file_count,
                'volume' => $volume_name
            );
        }
        
        wp_send_json_success($results);
    }
    
    /**
     * AJAX handler for getting volumes
     * 
     * Used by the admin helper to load volumes for filtering
     */
    public function ajax_get_volumes() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'gbbs_admin_nonce')) {
            wp_die('Security check failed');
        }
        
        // Check user capabilities
        if (!current_user_can('edit_posts')) {
            wp_die('Insufficient permissions');
        }
        
        $volumes = get_terms(array(
            'taxonomy' => 'gbbs_volume',
            'hide_empty' => false,
            'orderby' => 'name',
            'order' => 'ASC'
        ));
        
        $results = array();
        foreach ($volumes as $volume) {
            $results[] = array(
                'slug' => $volume->slug,
                'name' => $volume->name,
                'count' => $volume->count
            );
        }
        
        wp_send_json_success($results);
    }
    
    /**
     * Enqueue block editor assets
     * 
     * Loads JavaScript and CSS for the Gutenberg block editor
     */
    public function enqueue_block_editor_assets() {
        // Check if user can edit posts
        if (!current_user_can('edit_posts')) {
            return;
        }
        
        // Enqueue block editor script
        wp_enqueue_script(
            'gbbs-block-editor-script',
            $this->plugin_url . 'assets/js/gbbs-block-editor.js',
            array('wp-blocks', 'wp-element', 'wp-components', 'wp-i18n'),
            $this->version,
            true
        );
        
        // Enqueue block editor style - load after WordPress core and theme styles
        wp_enqueue_style(
            'gbbs-block-editor-style',
            $this->plugin_url . 'assets/css/gbbs-block-editor.css',
            array('wp-edit-blocks', 'wp-components', 'wp-editor'),
            $this->version
        );
        
        // Localize script with AJAX data
        wp_localize_script('gbbs-block-editor-script', 'gbbsBlockEditor', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gbbs_admin_nonce'),
            'strings' => array(
                'insertArchive' => __('Insert Archive Link', 'gbbs-software-archive'),
                'selectArchive' => __('Select Archive to Link', 'gbbs-software-archive'),
                'searchArchives' => __('Search archives by name, author, or version...', 'gbbs-software-archive'),
                'filterByVolume' => __('Filter by Volume:', 'gbbs-software-archive'),
                'allVolumes' => __('All Volumes', 'gbbs-software-archive'),
                'displayInModal' => __('Display in modal', 'gbbs-software-archive'),
                'buttonText' => __('Button text:', 'gbbs-software-archive'),
                'insertArchiveLink' => __('Insert Archive Link', 'gbbs-software-archive'),
                'cancel' => __('Cancel', 'gbbs-software-archive'),
                'loadingArchives' => __('Loading archives...', 'gbbs-software-archive'),
                'noArchivesFound' => __('No archives found.', 'gbbs-software-archive'),
                'errorLoadingArchives' => __('Error loading archives:', 'gbbs-software-archive'),
                'pleaseSelectArchive' => __('Please select an archive first.', 'gbbs-software-archive')
            )
        ));
    }
    
    /**
     * Download statistics page
     * 
     * Displays download statistics and analytics in the WordPress admin.
     */
    public function download_stats_page() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'gbbs_download_logs';
        
        // Get date range filter
        $date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : date('Y-m-01');
        $date_to = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : date('Y-m-d');
        
        // Get total downloads
        $total_downloads = $this->get_total_download_count();
        
        // Get downloads in date range
        $range_downloads = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE download_date >= %s AND download_date <= %s",
            $date_from . ' 00:00:00',
            $date_to . ' 23:59:59'
        ));
        
        // Get top files
        $top_files = $wpdb->get_results($wpdb->prepare(
            "SELECT archive_id, file_name, file_url, COUNT(*) as download_count 
             FROM $table_name 
             WHERE download_date >= %s AND download_date <= %s 
             GROUP BY archive_id, file_url 
             ORDER BY download_count DESC 
             LIMIT 10",
            $date_from . ' 00:00:00',
            $date_to . ' 23:59:59'
        ));
        
        // Get recent downloads
        $recent_downloads = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name 
             WHERE download_date >= %s AND download_date <= %s 
             ORDER BY download_date DESC 
             LIMIT 20",
            $date_from . ' 00:00:00',
            $date_to . ' 23:59:59'
        ));
        
        // Get daily download counts for chart
        $daily_downloads = $wpdb->get_results($wpdb->prepare(
            "SELECT DATE(download_date) as download_date, COUNT(*) as count 
             FROM $table_name 
             WHERE download_date >= %s AND download_date <= %s 
             GROUP BY DATE(download_date) 
             ORDER BY download_date ASC",
            $date_from . ' 00:00:00',
            $date_to . ' 23:59:59'
        ));
        
        ?>
        <div class="wrap">
            <h1><?php _e('GBBS Download Statistics', 'gbbs-software-archive'); ?></h1>
            
            <!-- Date Range Filter -->
            <div class="gbbs-stats-filter">
                <form method="get" action="<?php echo admin_url('edit.php'); ?>">
                    <input type="hidden" name="post_type" value="gbbs_archive">
                    <input type="hidden" name="page" value="gbbs-download-stats">
                    
                    <label for="date_from"><?php _e('From:', 'gbbs-software-archive'); ?></label>
                    <input type="date" id="date_from" name="date_from" value="<?php echo esc_attr($date_from); ?>">
                    
                    <label for="date_to"><?php _e('To:', 'gbbs-software-archive'); ?></label>
                    <input type="date" id="date_to" name="date_to" value="<?php echo esc_attr($date_to); ?>">
                    
                    <input type="submit" class="button" value="<?php _e('Filter', 'gbbs-software-archive'); ?>">
                </form>
            </div>
            
            <!-- Statistics Overview -->
            <div class="gbbs-stats-overview">
                <div class="gbbs-stat-card">
                    <h3><?php _e('Total File Downloads', 'gbbs-software-archive'); ?></h3>
                    <div class="gbbs-stat-number"><?php echo number_format($total_downloads); ?></div>
                </div>
                
                <div class="gbbs-stat-card">
                    <h3><?php _e('File Downloads in Range', 'gbbs-software-archive'); ?></h3>
                    <div class="gbbs-stat-number"><?php echo number_format($range_downloads); ?></div>
                </div>
                
                <div class="gbbs-stat-card">
                    <h3><?php _e('Date Range', 'gbbs-software-archive'); ?></h3>
                    <div class="gbbs-stat-number"><?php echo esc_html($date_from . ' to ' . $date_to); ?></div>
                </div>
            </div>
            
            <!-- Top Files -->
            <div class="gbbs-stats-section">
                <h2><?php _e('Top Downloaded Files', 'gbbs-software-archive'); ?></h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Archive', 'gbbs-software-archive'); ?></th>
                            <th><?php _e('File', 'gbbs-software-archive'); ?></th>
                            <th><?php _e('Downloads', 'gbbs-software-archive'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($top_files)): ?>
                            <?php foreach ($top_files as $file): ?>
                                <?php $archive_post = get_post($file->archive_id); ?>
                                <tr>
                                    <td>
                                        <?php if ($archive_post): ?>
                                            <a href="<?php echo get_edit_post_link($file->archive_id); ?>">
                                                <?php echo esc_html($archive_post->post_title); ?>
                                            </a>
                                        <?php else: ?>
                                            <?php printf(__('Archive #%d (deleted)', 'gbbs-software-archive'), $file->archive_id); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo esc_html($file->file_name); ?></td>
                                    <td><?php echo number_format($file->download_count); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3"><?php _e('No downloads in this date range.', 'gbbs-software-archive'); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Recent Downloads -->
            <div class="gbbs-stats-section">
                <h2><?php _e('Recent Downloads', 'gbbs-software-archive'); ?></h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Date', 'gbbs-software-archive'); ?></th>
                            <th><?php _e('Archive', 'gbbs-software-archive'); ?></th>
                            <th><?php _e('File', 'gbbs-software-archive'); ?></th>
                            <th><?php _e('User', 'gbbs-software-archive'); ?></th>
                            <th><?php _e('IP Address', 'gbbs-software-archive'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recent_downloads)): ?>
                            <?php foreach ($recent_downloads as $download): ?>
                                <?php $archive_post = get_post($download->archive_id); ?>
                                <tr>
                                    <td><?php echo esc_html(date('Y-m-d H:i:s', strtotime($download->download_date))); ?></td>
                                    <td>
                                        <?php if ($archive_post): ?>
                                            <a href="<?php echo get_edit_post_link($download->archive_id); ?>">
                                                <?php echo esc_html($archive_post->post_title); ?>
                                            </a>
                                        <?php else: ?>
                                            <?php printf(__('Archive #%d (deleted)', 'gbbs-software-archive'), $download->archive_id); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo esc_html($download->file_name); ?></td>
                                    <td>
                                        <?php if ($download->user_id): ?>
                                            <?php $user = get_user_by('id', $download->user_id); ?>
                                            <?php echo $user ? esc_html($user->display_name) : __('User #' . $download->user_id, 'gbbs-software-archive'); ?>
                                        <?php else: ?>
                                            <?php _e('Guest', 'gbbs-software-archive'); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo esc_html($download->user_ip); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5"><?php _e('No downloads in this date range.', 'gbbs-software-archive'); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <style>
        .gbbs-stats-filter {
            background: #fff;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
        }
        
        .gbbs-stats-filter label {
            margin-right: 10px;
            font-weight: bold;
        }
        
        .gbbs-stats-filter input[type="date"] {
            margin-right: 20px;
        }
        
        .gbbs-stats-overview {
            display: flex;
            gap: 20px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        
        .gbbs-stat-card {
            background: #fff;
            padding: 20px;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            flex: 1;
            text-align: center;
            min-width: 200px;
        }
        
        .gbbs-stat-card h3 {
            margin: 0 0 10px 0;
            color: #23282d;
            font-size: 14px;
        }
        
        .gbbs-stat-number {
            font-size: 1.5em;
            font-weight: bold;
            color: #0073aa;
            word-break: break-all;
        }
        
        @media (max-width: 768px) {
            .gbbs-stats-overview {
                flex-direction: column;
                gap: 10px;
            }
            
            .gbbs-stat-card {
                min-width: unset;
                padding: 15px;
            }
            
            .gbbs-stat-card h3 {
                font-size: 13px;
            }
            
            .gbbs-stat-number {
                font-size: 1.2em;
            }
        }
        
        .gbbs-stats-section {
            background: #fff;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
        }
        
        .gbbs-stats-section h2 {
            margin-top: 0;
        }
        </style>
        <?php
    }
    
    /**
     * Settings page
     * 
     * Displays the plugin settings page in the WordPress admin.
     */
    public function settings_page() {
        // Handle reset settings
        if (isset($_POST['gbbs_reset_settings']) && 
            wp_verify_nonce($_POST['gbbs_reset_nonce'], 'gbbs_reset_settings') &&
            current_user_can('manage_options')) {
            
            $this->settings->reset_to_defaults();
            
            echo '<div class="notice notice-success is-dismissible"><p>' . 
                 __('Settings have been reset to defaults.', 'gbbs-software-archive') . 
                 '</p></div>';
        }
        
        
        // Make settings available to the view
        $gbbs_settings = $this->settings;
        
        // Include the settings page view
        include $this->plugin_dir . 'views/settings-page.php';
    }
    
    
    /**
     * Get upload directory .htaccess content
     * 
     * @return string .htaccess content for upload directory
     */
    public function get_upload_directory_htaccess() {
        $htaccess = 'deny from all' . PHP_EOL;
        $htaccess .= '<FilesMatch "\.(jpg|jpeg|png|gif)$">' . PHP_EOL;
        $htaccess .= '  allow from all' . PHP_EOL;
        $htaccess .= '  RewriteEngine On' . PHP_EOL;
        $htaccess .= '  RewriteRule .*$ ' . get_bloginfo('url') . '/wp-includes/images/media/default.png [L]' . PHP_EOL;
        $htaccess .= '</FilesMatch>' . PHP_EOL;
        
        return $htaccess;
    }
    
    /**
     * Filter upload directory for GBBS files
     * 
     * @param array $param Upload directory parameters
     * @return array Modified upload directory parameters
     */
    public function gbbs_upload_dir($param) {
        // Prevent infinite loops
        static $processing_upload_dir = false;
        if ($processing_upload_dir) {
            return $param;
        }
        
        $processing_upload_dir = true;
        
        try {
            // Check if this is a GBBS archive upload
            $is_gbbs_upload = false;
            $archive_id = null;
            $volume_slug = null;
            
            // Check various ways the upload might be triggered
            if (isset($_POST['type']) && 'gbbs_archive' === $_POST['type']) {
                $is_gbbs_upload = true;
                $archive_id = isset($_POST['post_ID']) ? intval($_POST['post_ID']) : null;
            } elseif (isset($_POST['post_id']) && get_post_type($_POST['post_id']) === 'gbbs_archive') {
                $is_gbbs_upload = true;
                $archive_id = intval($_POST['post_id']);
            } elseif (isset($_GET['post']) && get_post_type($_GET['post']) === 'gbbs_archive') {
                $is_gbbs_upload = true;
                $archive_id = intval($_GET['post']);
            } elseif (isset($_REQUEST['post']) && get_post_type($_REQUEST['post']) === 'gbbs_archive') {
                $is_gbbs_upload = true;
                $archive_id = intval($_REQUEST['post']);
            }
            
            // Also check if we're on a GBBS archive edit page
            if (!$is_gbbs_upload && is_admin()) {
                global $pagenow, $post;
                if (($pagenow === 'post.php' || $pagenow === 'post-new.php') && 
                    isset($post) && $post->post_type === 'gbbs_archive') {
                    $is_gbbs_upload = true;
                    $archive_id = $post->ID;
                }
            }
            
            if ($is_gbbs_upload) {
                // Get volume for organization
                if ($archive_id) {
                    $volumes = get_the_terms($archive_id, 'gbbs_volume');
                    if (!empty($volumes) && !is_wp_error($volumes)) {
                        $volume_slug = $volumes[0]->slug;
                    }
                }
                
                // Get upload directory based on settings
                $upload_path = $this->settings->get_upload_directory($archive_id, $volume_slug);
                $upload_url = $this->settings->get_upload_url($archive_id, $volume_slug);
                
                // Ensure the directory exists (simplified)
                if (!file_exists($upload_path)) {
                    wp_mkdir_p($upload_path);
                }
                
                // Calculate subdir relative to uploads directory
                $upload_dir = wp_upload_dir();
                $subdir = str_replace($upload_dir['basedir'], '', $upload_path);
                
                $param['path'] = $upload_path;
                $param['url'] = $upload_url;
                $param['subdir'] = $subdir;
            }
            
            $processing_upload_dir = false;
            return $param;
        } catch (Exception $e) {
            $processing_upload_dir = false;
            return $param;
        }
    }
    
    /**
     * Get allowed file types
     * 
     * @return array Array of allowed file extensions
     */
    public function get_allowed_file_types() {
        return $this->settings->get_allowed_file_types();
    }
    
    /**
     * Validate file type
     * 
     * @param string $filename The filename to validate
     * @return bool True if file type is allowed
     */
    public function validate_file_type($filename) {
        return $this->settings->is_file_type_allowed($filename);
    }
    
    /**
     * Validate file size
     * 
     * @param int $file_size File size in bytes
     * @return bool True if file size is allowed
     */
    public function validate_file_size($file_size) {
        $max_size = $this->settings->get_max_file_size_bytes();
        return $file_size <= $max_size;
    }
    
    /**
     * Allow GBBS file types in WordPress uploads
     * 
     * @param array $mimes Existing mime types
     * @return array Modified mime types
     */
    public function allow_gbbs_file_types($mimes) {
        // If file type restrictions are disabled, allow all WordPress file types
        if (!$this->settings->get_setting('restrict_file_types', true)) {
            return $mimes;
        }
        
        $allowed_types = $this->settings->get_allowed_file_types();
        
        // If no specific file types are configured, allow all WordPress file types
        if (empty($allowed_types)) {
            return $mimes;
        }
        
        // Define mime types for each file extension
        $mime_map = array(
            // Apple II Disk Images
            'dsk' => 'application/octet-stream',
            'po' => 'application/octet-stream',
            'do' => 'application/octet-stream',
            'nib' => 'application/octet-stream',
            'woz' => 'application/octet-stream',
            '2mg' => 'application/octet-stream',
            
            // Apple II File Formats
            'bas' => 'text/plain',
            'int' => 'text/plain',
            'asm' => 'text/plain',
            's' => 'text/plain',
            'bin' => 'application/octet-stream',
            'a2s' => 'application/octet-stream',
            'a2d' => 'application/octet-stream',
            'bxy' => 'application/octet-stream',
            'bqy' => 'application/octet-stream',
            
            // Archive Formats
            'shk' => 'application/octet-stream',
            'bny' => 'application/octet-stream',
            'sea' => 'application/octet-stream',
            'zip' => 'application/zip',
            
            // Documentation
            'txt' => 'text/plain',
            'doc' => 'application/msword',
            'pdf' => 'application/pdf'
        );
        
        // Add mime types for allowed file types
        foreach ($allowed_types as $extension) {
            if (isset($mime_map[$extension])) {
                $mimes[$extension] = $mime_map[$extension];
            } else {
                // For any other file types, use a generic MIME type
                $mimes[$extension] = 'application/octet-stream';
            }
        }
        
        return $mimes;
    }
    
    /**
     * Ensure upload directory exists
     * 
     * Creates upload directory structure if it doesn't exist
     * 
     * @param int $archive_id Archive ID
     * @param string $volume_slug Volume slug
     * @return bool True if directory exists or was created
     */
    public function ensure_upload_directory($archive_id = null, $volume_slug = null) {
        // Prevent infinite loops by checking if we're already in the middle of creating directories
        static $creating_directories = false;
        if ($creating_directories) {
            return true;
        }
        
        $creating_directories = true;
        
        try {
            // If called as AJAX hook, try to determine archive ID from request
            if (is_null($archive_id) && isset($_REQUEST['post_id'])) {
                $post_id = intval($_REQUEST['post_id']);
                if (get_post_type($post_id) === 'gbbs_archive') {
                    $archive_id = $post_id;
                }
            }
            
            $upload_path = $this->settings->get_upload_directory($archive_id, $volume_slug);
            
            if (!file_exists($upload_path)) {
                $created = wp_mkdir_p($upload_path);
                
                if ($created) {
                    // Create .htaccess file for security
                    $htaccess_file = $upload_path . '/.htaccess';
                    if (!file_exists($htaccess_file)) {
                        file_put_contents($htaccess_file, $this->get_upload_directory_htaccess());
                    }
                    
                    // Create index.php file
                    $index_file = $upload_path . '/index.php';
                    if (!file_exists($index_file)) {
                        file_put_contents($index_file, '<?php // Silence is golden');
                    }
                }
                
                $creating_directories = false;
                return $created;
            }
            
            $creating_directories = false;
            return true;
        } catch (Exception $e) {
            $creating_directories = false;
            return false;
        }
    }
    
    /**
     * Make GBBS Volume taxonomy single select
     * 
     * Forces the GBBS Volume taxonomy to display as a single select dropdown
     * instead of checkboxes, since archives should only belong to one volume.
     */
    public function make_volume_single_select() {
        // Remove the default metabox
        remove_meta_box('gbbs_volumediv', 'gbbs_archive', 'side');
        
        // Add a custom metabox with single select
        add_meta_box(
            'gbbs_volume_single',
            __('GBBS Volume', 'gbbs-software-archive'),
            array($this, 'volume_single_select_meta_box'),
            'gbbs_archive',
            'side',
            'high'
        );
    }
    
    /**
     * Volume single select meta box
     * 
     * Displays a single select dropdown for GBBS Volume selection.
     * 
     * @param WP_Post $post The current post object
     */
    public function volume_single_select_meta_box($post) {
        $terms = get_terms(array(
            'taxonomy' => 'gbbs_volume',
            'hide_empty' => false,
        ));
        
        $selected_term = wp_get_post_terms($post->ID, 'gbbs_volume', array('fields' => 'ids'));
        $selected_id = !empty($selected_term) ? $selected_term[0] : 0;
        
        echo '<select name="gbbs_volume" id="gbbs_volume">';
        echo '<option value="">' . __('Select a Volume', 'gbbs-software-archive') . '</option>';
        
        foreach ($terms as $term) {
            $selected = selected($selected_id, $term->term_id, false);
            echo '<option value="' . esc_attr($term->term_id) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
        }
        
        echo '</select>';
        echo '<p class="description">' . __('Choose which volume this archive belongs to.', 'gbbs-software-archive') . '</p>';
    }
    
    /**
     * Add custom volume dropdown to Quick Edit
     * 
     * Adds a single-select dropdown for volume selection in the Quick Edit interface.
     * This ensures archives can only be assigned to one volume even in Quick Edit.
     * 
     * @param string $column_name The name of the column being edited
     * @param string $post_type The post type being edited
     */
    public function add_volume_quick_edit($column_name, $post_type) {
        // Only show for gbbs_archive post type and volume column
        if ($post_type !== 'gbbs_archive' || $column_name !== 'taxonomy-gbbs_volume') {
            return;
        }
        
        // Get all volumes
        $terms = get_terms(array(
            'taxonomy' => 'gbbs_volume',
            'hide_empty' => false,
            'orderby' => 'name',
            'order' => 'ASC'
        ));
        
        if (empty($terms) || is_wp_error($terms)) {
            return;
        }
        ?>
        <fieldset class="inline-edit-col-right">
            <div class="inline-edit-col">
                <label>
                    <span class="title"><?php _e('Volume', 'gbbs-software-archive'); ?></span>
                    <select name="gbbs_volume_quick_edit" id="gbbs_volume_quick_edit">
                        <option value=""><?php _e('— No Change —', 'gbbs-software-archive'); ?></option>
                        <?php foreach ($terms as $term): ?>
                            <option value="<?php echo esc_attr($term->term_id); ?>">
                                <?php echo esc_html($term->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>
        </fieldset>
        <?php
    }
    
    /**
     * Rename taxonomy column to "Volume" and remove category columns
     * 
     * @param array $columns Existing columns
     * @return array Modified columns
     */
    public function rename_taxonomy_column($columns) {
        // Remove all possible category-related columns
        unset($columns['categories']);
        unset($columns['category']);
        unset($columns['taxonomy-category']);
        
        // Add new columns after the title column
        $new_columns = array();
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            
            // Add Version and Release Year columns after the title
            if ($key === 'title') {
                $new_columns['gbbs_version'] = __('Version', 'gbbs-software-archive');
                $new_columns['gbbs_release_year'] = __('Release Year', 'gbbs-software-archive');
            }
        }
        
        // Rename the gbbs_volume taxonomy column to "Volume"
        if (isset($new_columns['taxonomy-gbbs_volume'])) {
            $new_columns['taxonomy-gbbs_volume'] = __('Volume', 'gbbs-software-archive');
        }
        
        return $new_columns;
    }
    
    /**
     * Additional filter to remove category columns (runs after other plugins)
     * 
     * This method runs with priority 20 to ensure it removes any category columns
     * that might have been added by other plugins or themes.
     * 
     * @param array $columns Existing columns
     * @return array Modified columns
     */
    public function remove_category_columns($columns) {
        // Remove any remaining category-related columns
        unset($columns['categories']);
        unset($columns['category']);
        unset($columns['taxonomy-category']);
        
        // Also check for any columns that might contain "category" in the key
        foreach ($columns as $key => $value) {
            if (strpos(strtolower($key), 'category') !== false) {
                unset($columns[$key]);
            }
        }
        
        return $columns;
    }
    
    /**
     * Display custom column content
     * 
     * @param string $column_name The name of the column
     * @param int $post_id The post ID
     */
    public function display_custom_columns($column_name, $post_id) {
        switch ($column_name) {
            case 'gbbs_version':
                $version = get_post_meta($post_id, 'gbbs_archive_version', true);
                echo $version ? esc_html($version) : '—';
                break;
                
            case 'gbbs_release_year':
                $release_year = get_post_meta($post_id, 'gbbs_archive_release_year', true);
                echo $release_year ? esc_html($release_year) : '—';
                break;
        }
    }
    
    /**
     * Template filter for GBBS archives
     * 
     * Uses a custom template for displaying GBBS archives instead of the default post template.
     * 
     * @param string $template The current template path
     * @return string The template path to use
     */
    public function gbbs_archive_template($template) {
        global $post;
        
        if ($post && $post->post_type == 'gbbs_archive') {
            $custom_template = $this->plugin_dir . 'views/single-gbbs-archive.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        
        return $template;
    }
    
    /**
     * Get file size from URL with caching
     * 
     * @param string $url File URL
     * @return int File size in bytes
     */
    public function get_file_size($url) {
        // Use cache key based on URL
        $cache_key = 'gbbs_file_size_' . md5($url);
        $cached_size = get_transient($cache_key);
        
        if ($cached_size !== false) {
            return intval($cached_size);
        }
        
        $file_size = 0;
        
        // Try to get file size from WordPress uploads first (faster)
        $upload_dir = wp_upload_dir();
        $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $url);
        
        if (file_exists($file_path)) {
            $file_size = filesize($file_path);
        } else {
            // Fallback: try to get file size from headers (slower)
            $headers = get_headers($url, true);
            if (isset($headers['Content-Length'])) {
                $file_size = intval($headers['Content-Length']);
            }
        }
        
        // Cache the result for 24 hours
        set_transient($cache_key, $file_size, DAY_IN_SECONDS);
        
        return $file_size;
    }

    /**
     * Format file size in human readable format
     * 
     * @param int $bytes File size in bytes
     * @return string Formatted file size
     */
    public function format_file_size($bytes) {
        if ($bytes == 0) {
            return '0 B';
        }
        
        $units = array('B', 'K', 'MB', 'GB', 'TB');
        $power = floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);
        
        $size = $bytes / pow(1024, $power);
        
        if ($power == 0) {
            return $size . ' ' . $units[$power];
        } else {
            return round($size) . ' ' . $units[$power];
        }
    }
    
    /**
     * AJAX handler for getting archive info
     */
    public function ajax_get_archive_info() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'gbbs_archive_info')) {
            wp_die('Security check failed');
        }
        
        $archive_id = intval($_POST['archive_id']);
        $archive = get_post($archive_id);
        
        if (!$archive || $archive->post_type !== 'gbbs_archive') {
            wp_send_json_error('Archive not found');
        }
        
        // Get archive metadata
        $archive_version = get_post_meta($archive_id, 'gbbs_archive_version', true);
        $archive_author = get_post_meta($archive_id, 'gbbs_archive_author', true);
        $archive_release_year = get_post_meta($archive_id, 'gbbs_archive_release_year', true);
        $archive_requirements = get_post_meta($archive_id, 'gbbs_archive_requirements', true);
        $archive_installation_notes = get_post_meta($archive_id, 'gbbs_archive_installation_notes', true);
        $archive_historical_notes = get_post_meta($archive_id, 'gbbs_archive_historical_notes', true);
        $archive_files = get_post_meta($archive_id, '_gbbs_archive_files', true);
        
        // Get volume info
        $volumes = get_the_terms($archive_id, 'gbbs_volume');
        $volume_name = !empty($volumes) ? $volumes[0]->name : 'Uncategorized';
        
        // Ensure archive_files is an array
        if (!is_array($archive_files)) {
            $archive_files = array();
        }
        
        ob_start();
        ?>
        <div class="gbbs-modal-section">
            <h3>Archive Details</h3>
            <p><strong>Title:</strong> <?php echo esc_html($archive->post_title); ?></p>
            <?php if ($archive_version): ?>
                <p><strong>Version:</strong> <?php echo esc_html($archive_version); ?></p>
            <?php endif; ?>
            <?php if ($archive_author): ?>
                <p><strong>Author:</strong> <?php echo esc_html($archive_author); ?></p>
            <?php endif; ?>
            <?php if ($archive_release_year): ?>
                <p><strong>Release Year:</strong> <?php echo esc_html($archive_release_year); ?></p>
            <?php endif; ?>
            <p><strong>Volume:</strong> <?php echo esc_html($volume_name); ?></p>
        </div>
        
        <?php if ($archive->post_content): ?>
            <div class="gbbs-modal-section">
                <h3>Description</h3>
                <p><?php echo wp_kses_post($archive->post_content); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($archive_requirements): ?>
            <div class="gbbs-modal-section">
                <h3>System Requirements</h3>
                <p><?php echo nl2br(esc_html($archive_requirements)); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($archive_installation_notes): ?>
            <div class="gbbs-modal-section">
                <h3>Installation Notes</h3>
                <p><?php echo nl2br(esc_html($archive_installation_notes)); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($archive_historical_notes): ?>
            <div class="gbbs-modal-section">
                <h3>Historical Notes</h3>
                <p><?php echo nl2br(esc_html($archive_historical_notes)); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($archive_files)): ?>
            <div class="gbbs-modal-section">
                <h3>Archive Files</h3>
                <div class="gbbs-modal-files">
                    <table class="gbbs-modal-file-table">
                        <thead>
                            <tr>
                                <th class="gbbs-modal-file-name-header">Name</th>
                                <th class="gbbs-modal-file-type-header">Type</th>
                                <th class="gbbs-modal-file-size-header">Size</th>
                                <th class="gbbs-modal-file-category-header">Category</th>
                                <th class="gbbs-modal-file-downloads-header">Downloads</th>
                                <th class="gbbs-modal-file-action-header">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($archive_files as $file_index => $file): ?>
                                <?php
                                // Get file size
                                $file_size = 0;
                                if (!empty($file['url'])) {
                                    $file_size = $this->get_file_size($file['url']);
                                }
                                $formatted_size = $this->format_file_size($file_size);
                                
                                // Get download count for this file
                                $file_download_count = $this->get_file_download_count($archive_id, $file['url']);
                                
                                // Get download URL
                                $download_url = $this->get_download_url($archive_id, $file_index);
                                
                                // Get Apple II file type
                                $filename = $file['name'] ?: basename($file['url']);
                                $gbbs_instance = new GBBS_Software_Archive();
                                $file_type_info = $gbbs_instance->get_apple_ii_file_type($filename);
                                $file_type = $file_type_info['type'];
                                $file_type_css = $gbbs_instance->get_file_type_css_class($file_type);
                                ?>
                                <tr class="gbbs-modal-file-row">
                                    <td class="gbbs-modal-file-name"><?php echo esc_html($filename); ?></td>
                                    <td class="gbbs-modal-file-type">
                                        <span class="gbbs-file-type-indicator <?php echo esc_attr($file_type_css); ?>" title="<?php echo esc_attr($file_type_info['description']); ?>">
                                            <?php echo esc_html($file_type); ?>
                                        </span>
                                    </td>
                                    <td class="gbbs-modal-file-size"><?php echo esc_html($formatted_size); ?></td>
                                    <td class="gbbs-modal-file-category"><?php echo esc_html($this->get_category_display_name($file['category'] ?: 'other')); ?></td>
                                    <td class="gbbs-modal-file-downloads"><?php echo $file_download_count; ?></td>
                                    <td class="gbbs-modal-file-action">
                                        <a href="<?php echo esc_url($download_url); ?>" class="gbbs-modal-file-download">
                                            Download
                                        </a>
                                    </td>
                                </tr>
                                <?php if ($file['description']): ?>
                                    <tr class="gbbs-modal-file-description-row">
                                        <td colspan="6" class="gbbs-modal-file-description">
                                            <?php echo esc_html($file['description']); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        
        <?php
        
        $content = ob_get_clean();
        
        wp_send_json_success(array(
            'title' => $archive->post_title,
            'content' => $content
        ));
    }
    
    /**
     * Create download logs table
     * 
     * Creates a database table to track all file downloads.
     */
    public function create_download_logs_table() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'gbbs_download_logs';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            archive_id bigint(20) NOT NULL,
            file_url varchar(500) NOT NULL,
            file_name varchar(255) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            user_ip varchar(45) NOT NULL,
            user_agent text NOT NULL,
            download_date datetime DEFAULT CURRENT_TIMESTAMP,
            referer varchar(500) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY archive_id (archive_id),
            KEY user_ip (user_ip),
            KEY download_date (download_date)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Add download endpoint
     * 
     * Adds a rewrite endpoint for handling file downloads.
     */
    public function add_download_endpoint() {
        $endpoint = $this->settings->get_setting('download_endpoint', 'gbbs-download');
        add_rewrite_endpoint($endpoint, EP_ALL);
    }
    
    /**
     * Maybe flush rewrite rules
     * 
     * Flushes rewrite rules if needed to ensure endpoints are registered.
     */
    public function maybe_flush_rewrite_rules() {
        $endpoint = $this->settings->get_setting('download_endpoint', 'gbbs-download');
        $option_name = 'gbbs_rewrite_rules_flushed_' . $endpoint;
        
        // Check if we've already flushed rules for this endpoint
        if (!get_option($option_name)) {
            flush_rewrite_rules();
            update_option($option_name, true);
        }
    }
    
    /**
     * Handle download request
     * 
     * Processes file download requests and logs them.
     */
    public function handle_download_request() {
        global $wp_query;
        
        $endpoint = $this->settings->get_setting('download_endpoint', 'gbbs-download');
        
        // Check if this is a download request
        if (!isset($wp_query->query_vars[$endpoint])) {
            return;
        }
        
        // Get the download ID from the URL
        $download_id = $wp_query->query_vars[$endpoint];
        
        if (empty($download_id)) {
            wp_die(__('Invalid download request.', 'gbbs-software-archive'));
        }
        
        // Check if login is required
        if ($this->settings->require_login_for_downloads() && !is_user_logged_in()) {
            wp_die(__('You must be logged in to download files.', 'gbbs-software-archive'));
        }
        
        // Check rate limiting
        $user_ip = $this->get_user_ip();
        if ($this->settings->is_rate_limit_exceeded($user_ip)) {
            wp_die(__('Download rate limit exceeded. Please try again later.', 'gbbs-software-archive'));
        }
        
        // Parse the download ID (format: archive_id-file_index)
        $parts = explode('-', $download_id);
        if (count($parts) < 2) {
            wp_die(__('Invalid download format.', 'gbbs-software-archive'));
        }
        
        $archive_id = intval($parts[0]);
        $file_index = intval($parts[1]);
        
        // Get the archive
        $archive = get_post($archive_id);
        
        if (!$archive || $archive->post_type !== 'gbbs_archive' || $archive->post_status !== 'publish') {
            wp_die(__('Archive not found or not available.', 'gbbs-software-archive'));
        }
        
        // Get archive files
        $archive_files = get_post_meta($archive_id, '_gbbs_archive_files', true);
        
        if (!is_array($archive_files) || !isset($archive_files[$file_index])) {
            wp_die(__('File not found in archive.', 'gbbs-software-archive'));
        }
        
        $file = $archive_files[$file_index];
        $file_url = $file['url'];
        $file_name = $file['name'] ?: basename($file_url);
        
        // Check if file exists before serving (similar to Lana Downloads Manager addon)
        if (!$this->check_file_exists($file_url)) {
            status_header(404);
            nocache_headers();
            wp_die(__('File not found. The requested file may have been moved or deleted.', 'gbbs-software-archive'), __('File Not Found', 'gbbs-software-archive'), array('response' => 404));
        }
        
        // Log the download if logging is enabled
        if ($this->settings->is_download_logging_enabled()) {
            $this->log_download($archive_id, $file_url, $file_name);
        }
        
        // Serve the file
        $this->serve_file($file_url, $file_name);
    }
    
    /**
     * Log download
     * 
     * Records a download in the database.
     * 
     * @param int $archive_id Archive ID
     * @param string $file_url File URL
     * @param string $file_name File name
     */
    public function log_download($archive_id, $file_url, $file_name) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'gbbs_download_logs';
        
        $wpdb->insert(
            $table_name,
            array(
                'archive_id' => $archive_id,
                'file_url' => $file_url,
                'file_name' => $file_name,
                'user_id' => get_current_user_id() ?: null,
                'user_ip' => $this->get_user_ip(),
                'user_agent' => $this->get_user_agent(),
                'referer' => wp_get_referer()
            ),
            array(
                '%d', '%s', '%s', '%d', '%s', '%s', '%s'
            )
        );
    }
    
    /**
     * Serve file
     * 
     * Serves the file for download with proper headers.
     * 
     * @param string $file_url File URL
     * @param string $file_name File name
     */
    public function serve_file($file_url, $file_name) {
        // Prevent caching
        define('DONOTCACHEPAGE', true);
        
        // Set time limit for large files
        if (!ini_get('safe_mode')) {
            @set_time_limit(0);
        }
        
        // Disable compression
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', 1);
        }
        
        @session_write_close();
        
        if (ini_get('zlib.output_compression')) {
            @ini_set('zlib.output_compression', 'Off');
        }
        
        @ob_end_clean();
        
        while (ob_get_level() > 0) {
            @ob_end_clean();
        }
        
        // Check if it's a local file
        $upload_dir = wp_upload_dir();
        $local_file = false;
        $file_path = $file_url;
        
        if (strpos($file_url, $upload_dir['baseurl']) !== false) {
            $local_file = true;
            $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $file_url);
            
            // If the file doesn't exist with the exact path, check for GBBS files with multiple dots
            if (!file_exists($file_path)) {
                $filename = basename($file_path);
                $directory = dirname($file_path);
                
                // Check if this might be a sanitized filename (contains underscores)
                if (strpos($filename, '_') !== false) {
                    // Try to find a file with dots instead of underscores
                    $original_filename = str_replace('_', '.', $filename);
                    $original_path = $directory . '/' . $original_filename;
                    
                    if (file_exists($original_path)) {
                        $file_path = $original_path;
                    }
                }
            }
        }
        
        if ($local_file && file_exists($file_path)) {
            // Serve local file
            $file_size = filesize($file_path);
            
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $file_name . '"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . $file_size);
            header('Connection: Keep-Alive');
            header('Expires: 0');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            
            readfile($file_path);
        } else {
            // Redirect to remote file
            wp_redirect($file_url);
        }
        
        exit;
    }
    
    /**
     * Get user IP address
     * 
     * @return string User IP address
     */
    public function get_user_ip() {
        $client = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = $_SERVER['REMOTE_ADDR'];
        
        if (filter_var($client, FILTER_VALIDATE_IP)) {
            return $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            return $forward;
        } else {
            return $remote;
        }
    }
    
    /**
     * Get user agent
     * 
     * @return string User agent string
     */
    public function get_user_agent() {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }
    
    /**
     * Get total download count for all files in an archive
     * 
     * @param int $archive_id Archive ID
     * @return int Total download count for all files in archive
     */
    public function get_archive_total_download_count($archive_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'gbbs_download_logs';
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE archive_id = %d",
            $archive_id
        ));
        
        return intval($count);
    }
    
    /**
     * Get download count for specific file
     * 
     * @param int $archive_id Archive ID
     * @param string $file_url File URL
     * @return int Download count
     */
    public function get_file_download_count($archive_id, $file_url) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'gbbs_download_logs';
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE archive_id = %d AND file_url = %s",
            $archive_id,
            $file_url
        ));
        
        return intval($count);
    }
    
    /**
     * Get total download count with caching
     * 
     * @return int Total download count
     */
    public function get_total_download_count() {
        $cache_key = 'gbbs_total_downloads';
        $cached_count = get_transient($cache_key);
        
        if ($cached_count !== false) {
            return intval($cached_count);
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'gbbs_download_logs';
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        
        // Cache for 1 hour
        set_transient($cache_key, $count, HOUR_IN_SECONDS);
        
        return intval($count);
    }
    
    /**
     * Get download URL for file
     * 
     * @param int $archive_id Archive ID
     * @param int $file_index File index in archive
     * @return string Download URL
     */
    public function get_download_url($archive_id, $file_index) {
        return $this->settings->get_download_url($archive_id, $file_index);
    }
    
    /**
     * Get file type indicator for a filename (simple extension-based)
     * 
     * @param string $filename The filename to analyze
     * @return array Array with 'type' and 'description'
     */
    public function get_apple_ii_file_type($filename) {
        $extension = strtoupper(pathinfo($filename, PATHINFO_EXTENSION));
        
        // If no extension, return UNK
        if (empty($extension)) {
            return array('type' => 'UNK', 'description' => 'Unknown File Type');
        }
        
        // Return the extension as the file type
        return array('type' => $extension, 'description' => $extension . ' File');
    }
    
    /**
     * Get file type CSS class for styling
     * 
     * @param string $file_type The file type extension
     * @return string CSS class name
     */
    public function get_file_type_css_class($file_type) {
        // All file types use the same styling - simple and clean
        return 'gbbs-file-default';
    }
    
    /**
     * Check if file exists (local or remote)
     * 
     * Similar to Lana Downloads Manager Check Local File Addon functionality.
     * Checks if a file exists before serving it to prevent broken download links.
     * 
     * @param string $file_url File URL to check
     * @return bool True if file exists, false otherwise
     */
    public function check_file_exists($file_url) {
        if (empty($file_url)) {
            return false;
        }
        
        // Check if it's a local file (WordPress uploads)
        $upload_dir = wp_upload_dir();
        if (strpos($file_url, $upload_dir['baseurl']) !== false) {
            // Convert URL to file path
            $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $file_url);
            
            // First check if the file exists with the exact path
            if (file_exists($file_path)) {
                return true;
            }
            
            // If not found, check if it's a GBBS file with multiple dots that was sanitized
            // Look for files with underscores that should have dots
            $filename = basename($file_path);
            $directory = dirname($file_path);
            
            // Check if this might be a sanitized filename (contains underscores)
            if (strpos($filename, '_') !== false) {
                // Try to find a file with dots instead of underscores
                $original_filename = str_replace('_', '.', $filename);
                $original_path = $directory . '/' . $original_filename;
                
                if (file_exists($original_path)) {
                    return true;
                }
            }
            
            return false;
        }
        
        // For remote files, we can do a basic URL validation
        // Note: We don't actually check remote files as that would be slow
        // and could cause timeouts. Remote files are assumed to exist.
        if (filter_var($file_url, FILTER_VALIDATE_URL)) {
            return true; // Assume remote files exist
        }
        
        return false;
    }
    
    
    /**
     * Check upload directory and show admin notice if needed
     */
    public function check_upload_directory() {
        // Only show on GBBS archive pages and avoid infinite loops
        global $post_type, $pagenow;
        
        // Skip if we're already in an admin notice context
        if (did_action('admin_notices') > 1) {
            return;
        }
        
        if ($post_type !== 'gbbs_archive' && !($pagenow === 'post.php' && isset($_GET['post']) && get_post_type($_GET['post']) === 'gbbs_archive')) {
            return;
        }
        
        // Simple check without complex operations
        $upload_dir = wp_upload_dir();
        $gbbs_base_dir = $upload_dir['basedir'] . '/gbbs-archive';
        
        if (!file_exists($gbbs_base_dir)) {
            echo '<div class="notice notice-warning is-dismissible">';
            echo '<p><strong>GBBS Software Archive:</strong> Upload directory not found. ';
            echo '<a href="' . admin_url('edit.php?post_type=gbbs_archive&page=gbbs-archive-settings') . '">Go to Settings</a> to fix this issue.</p>';
            echo '</div>';
        }
    }
    
    /**
     * Check if archive files exist and show admin notice
     * 
     * Similar to Lana Downloads Manager Check Local File Addon functionality.
     * Warns administrators about missing files in the admin interface.
     */
    public function check_archive_files_exist() {
        global $post, $pagenow;
        
        // Only check on GBBS archive edit pages
        if ($pagenow !== 'post.php' || !$post || $post->post_type !== 'gbbs_archive') {
            return;
        }
        
        // Skip if we're already in an admin notice context to avoid infinite loops
        if (did_action('admin_notices') > 1) {
            return;
        }
        
        $archive_files = get_post_meta($post->ID, '_gbbs_archive_files', true);
        if (!is_array($archive_files) || empty($archive_files)) {
            return;
        }
        
        $missing_files = array();
        
        foreach ($archive_files as $index => $file) {
            if (!empty($file['url']) && !$this->check_file_exists($file['url'])) {
                $file_name = $file['name'] ?: basename($file['url']);
                $missing_files[] = $file_name;
            }
        }
        
        if (!empty($missing_files)) {
            echo '<div class="notice notice-warning is-dismissible">';
            echo '<p><strong>GBBS Software Archive:</strong> Some files in this archive are missing or inaccessible:</p>';
            echo '<ul style="margin-left: 20px;">';
            foreach ($missing_files as $missing_file) {
                echo '<li>' . esc_html($missing_file) . '</li>';
            }
            echo '</ul>';
            echo '<p>Please check the file URLs or re-upload the missing files.</p>';
            echo '</div>';
        }
    }
    
    /**
     * Get total file count across all archives with caching
     * 
     * @param string $search Search term (optional)
     * @param string $volume Volume slug (optional)
     * @return int Total number of files
     */
    public function get_total_file_count($search = '', $volume = '') {
        $cache_key = 'gbbs_total_files_' . md5($search . $volume);
        $cached_count = get_transient($cache_key);
        
        if ($cached_count !== false) {
            return intval($cached_count);
        }
        
        $args = array(
            'post_type' => 'gbbs_archive',
            'post_status' => 'publish',
            'numberposts' => -1
        );
        
        // Apply search filter if provided
        if (!empty($search)) {
            $args['s'] = $search;
        }
        
        // Apply volume filter if provided
        if (!empty($volume)) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'gbbs_volume',
                    'field' => 'slug',
                    'terms' => $volume
                )
            );
        }
        
        $archives = get_posts($args);
        
        $total_files = 0;
        foreach ($archives as $archive) {
            $archive_files = get_post_meta($archive->ID, '_gbbs_archive_files', true);
            if (is_array($archive_files)) {
                $total_files += count($archive_files);
            }
        }
        
        // Cache for 1 hour
        set_transient($cache_key, $total_files, HOUR_IN_SECONDS);
        
        return $total_files;
    }
    
    /**
     * Get total size of all archives with caching
     * 
     * @param string $search Search term (optional)
     * @param string $volume Volume slug (optional)
     * @return string Formatted total size
     */
    public function get_total_archive_size($search = '', $volume = '') {
        $cache_key = 'gbbs_total_size_' . md5($search . $volume);
        $cached_size = get_transient($cache_key);
        
        if ($cached_size !== false) {
            return $cached_size;
        }
        
        $args = array(
            'post_type' => 'gbbs_archive',
            'post_status' => 'publish',
            'numberposts' => -1
        );
        
        // Apply search filter if provided
        if (!empty($search)) {
            $args['s'] = $search;
        }
        
        // Apply volume filter if provided
        if (!empty($volume)) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'gbbs_volume',
                    'field' => 'slug',
                    'terms' => $volume
                )
            );
        }
        
        $archives = get_posts($args);
        
        $total_size = 0;
        foreach ($archives as $archive) {
            $archive_files = get_post_meta($archive->ID, '_gbbs_archive_files', true);
            if (is_array($archive_files)) {
                foreach ($archive_files as $file) {
                    if (!empty($file['url'])) {
                        $file_size = $this->get_file_size($file['url']);
                        $total_size += $file_size;
                    }
                }
            }
        }
        
        $formatted_size = $this->format_file_size($total_size);
        
        // Cache for 1 hour
        set_transient($cache_key, $formatted_size, HOUR_IN_SECONDS);
        
        return $formatted_size;
    }
    
    /**
     * Get number of volumes with caching
     * 
     * @return int Number of volumes
     */
    public function get_volume_count() {
        $cache_key = 'gbbs_volume_count';
        $cached_count = get_transient($cache_key);
        
        if ($cached_count !== false) {
            return intval($cached_count);
        }
        
        $volumes = get_terms(array(
            'taxonomy' => 'gbbs_volume',
            'hide_empty' => true
        ));
        
        $count = count($volumes);
        
        // Cache for 1 hour
        set_transient($cache_key, $count, HOUR_IN_SECONDS);
        
        return $count;
    }
    
    /**
     * Get sort value for an archive based on sort field
     * 
     * @param WP_Post $archive Archive post object
     * @param string $sort_field Sort field name
     * @return mixed Sort value
     */
    private function get_archive_sort_value($archive, $sort_field) {
        switch ($sort_field) {
            case 'downloads':
                return intval(get_post_meta($archive->ID, '_gbbs_download_count', true));
            case 'size':
                $archive_files = get_post_meta($archive->ID, '_gbbs_archive_files', true);
                $total_size = 0;
                if (is_array($archive_files)) {
                    foreach ($archive_files as $file) {
                        if (!empty($file['url'])) {
                            $total_size += $this->get_file_size($file['url']);
                        }
                    }
                }
                return $total_size;
            case 'version':
                $version = get_post_meta($archive->ID, 'gbbs_archive_version', true);
                return $version ?: '';
            case 'volume':
                $volumes = get_the_terms($archive->ID, 'gbbs_volume');
                return !empty($volumes) ? $volumes[0]->name : 'Uncategorized';
            default:
                return '';
        }
    }
    
    /**
     * Get date of newest archive with caching
     * 
     * @return string Formatted date of newest archive
     */
    public function get_newest_archive_date() {
        $cache_key = 'gbbs_newest_archive_date';
        $cached_date = get_transient($cache_key);
        
        if ($cached_date !== false) {
            return $cached_date;
        }
        
        $archives = get_posts(array(
            'post_type' => 'gbbs_archive',
            'post_status' => 'publish',
            'numberposts' => 1,
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        
        $date = 'N/A';
        if (!empty($archives)) {
            $date = get_the_date('M j, Y', $archives[0]->ID);
        }
        
        // Cache for 1 hour
        set_transient($cache_key, $date, HOUR_IN_SECONDS);
        
        return $date;
    }
    
    /**
     * AJAX handler for loading statistics asynchronously
     * 
     * @return void
     */
    public function ajax_load_stats() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'], 'gbbs_stats_nonce')) {
            wp_die('Security check failed');
        }
        
        $search = sanitize_text_field($_POST['search']);
        $volume = sanitize_text_field($_POST['volume']);
        
        $stats_html = '';
        
        // Download count
        if ($this->settings->get_setting('show_download_counts', true) && $this->settings->is_download_counter_enabled()) {
            $download_count = $this->get_total_download_count();
            $stats_html .= '<div class="bbs-stat-item">
                <span class="bbs-stat-label">Total File Downloads:</span>
                <span class="bbs-stat-value">' . $download_count . '</span>
            </div>';
        }
        
        // File count
        $file_count = $this->get_total_file_count($search, $volume);
        $stats_html .= '<div class="bbs-stat-item">
            <span class="bbs-stat-label">Total Files:</span>
            <span class="bbs-stat-value">' . $file_count . '</span>
        </div>';
        
        // Total size
        $total_size = $this->get_total_archive_size($search, $volume);
        $stats_html .= '<div class="bbs-stat-item">
            <span class="bbs-stat-label">Total Size:</span>
            <span class="bbs-stat-value">' . $total_size . '</span>
        </div>';
        
        // Volume count
        $volume_count = $this->get_volume_count();
        $stats_html .= '<div class="bbs-stat-item">
            <span class="bbs-stat-label">Volumes:</span>
            <span class="bbs-stat-value">' . $volume_count . '</span>
        </div>';
        
        // Newest archive date
        $newest_date = $this->get_newest_archive_date();
        $stats_html .= '<div class="bbs-stat-item">
            <span class="bbs-stat-label">Latest Archive:</span>
            <span class="bbs-stat-value">' . $newest_date . '</span>
        </div>';
        
        wp_send_json_success($stats_html);
    }
    
    /**
     * Clear all cached statistics
     * 
     * @return void
     */
    public function clear_stats_cache() {
        delete_transient('gbbs_total_downloads');
        delete_transient('gbbs_total_files_');
        delete_transient('gbbs_total_size_');
        delete_transient('gbbs_volume_count');
        delete_transient('gbbs_newest_archive_date');
        
        // Clear file size cache
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_gbbs_file_size_%'");
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_gbbs_file_size_%'");
    }
    
    /**
     * Clear cache when archive is updated
     * 
     * @param int $post_id Post ID
     * @return void
     */
    public function clear_cache_on_archive_update($post_id) {
        // Only clear cache for gbbs_archive posts
        if (get_post_type($post_id) === 'gbbs_archive') {
            $this->clear_stats_cache();
        }
    }
    
    /**
     * Get bulk archive metadata to avoid N+1 queries
     * 
     * @param array $archive_ids Array of archive IDs
     * @return array Associative array of metadata by archive ID
     */
    private function get_bulk_archive_metadata($archive_ids) {
        if (empty($archive_ids)) {
            return array();
        }
        
        global $wpdb;
        $ids_placeholder = implode(',', array_fill(0, count($archive_ids), '%d'));
        
        $query = $wpdb->prepare("
            SELECT post_id, meta_key, meta_value 
            FROM {$wpdb->postmeta} 
            WHERE post_id IN ($ids_placeholder) 
            AND meta_key IN ('_gbbs_archive_files', 'gbbs_archive_version', 'gbbs_archive_author', 'gbbs_archive_release_year')
        ", $archive_ids);
        
        $results = $wpdb->get_results($query);
        
        $metadata = array();
        foreach ($archive_ids as $id) {
            $metadata[$id] = array(
                'files' => array(),
                'version' => '',
                'author' => '',
                'release_year' => ''
            );
        }
        
        foreach ($results as $row) {
            $value = $row->meta_value;
            if ($row->meta_key === '_gbbs_archive_files') {
                $metadata[$row->post_id]['files'] = maybe_unserialize($value);
            } elseif ($row->meta_key === 'gbbs_archive_version') {
                $metadata[$row->post_id]['version'] = $value;
            } elseif ($row->meta_key === 'gbbs_archive_author') {
                $metadata[$row->post_id]['author'] = $value;
            } elseif ($row->meta_key === 'gbbs_archive_release_year') {
                $metadata[$row->post_id]['release_year'] = $value;
            }
        }
        
        return $metadata;
    }
    
    /**
     * Get bulk archive volumes to avoid N+1 queries
     * 
     * @param array $archive_ids Array of archive IDs
     * @return array Associative array of volume names by archive ID
     */
    private function get_bulk_archive_volumes($archive_ids) {
        if (empty($archive_ids)) {
            return array();
        }
        
        global $wpdb;
        $ids_placeholder = implode(',', array_fill(0, count($archive_ids), '%d'));
        
        $query = $wpdb->prepare("
            SELECT tr.object_id, t.name 
            FROM {$wpdb->term_relationships} tr
            INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
            WHERE tr.object_id IN ($ids_placeholder) 
            AND tt.taxonomy = 'gbbs_volume'
        ", $archive_ids);
        
        $results = $wpdb->get_results($query);
        
        $volumes = array();
        foreach ($archive_ids as $id) {
            $volumes[$id] = 'Uncategorized';
        }
        
        foreach ($results as $row) {
            $volumes[$row->object_id] = $row->name;
        }
        
        return $volumes;
    }
    
    /**
     * Get bulk download counts to avoid N+1 queries
     * 
     * @param array $archive_ids Array of archive IDs
     * @return array Associative array of download counts by archive ID
     */
    private function get_bulk_download_counts($archive_ids) {
        if (empty($archive_ids)) {
            return array();
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'gbbs_download_logs';
        $ids_placeholder = implode(',', array_fill(0, count($archive_ids), '%d'));
        
        $query = $wpdb->prepare("
            SELECT archive_id, COUNT(*) as download_count 
            FROM $table_name 
            WHERE archive_id IN ($ids_placeholder) 
            GROUP BY archive_id
        ", $archive_ids);
        
        $results = $wpdb->get_results($query);
        
        $download_counts = array();
        foreach ($archive_ids as $id) {
            $download_counts[$id] = 0;
        }
        
        foreach ($results as $row) {
            $download_counts[$row->archive_id] = intval($row->download_count);
        }
        
        return $download_counts;
    }
    
    /**
     * Get display name for category key
     * 
     * @param string $category_key The category key (e.g., 'main', 'documentation')
     * @return string The display name (e.g., 'Main Program', 'Documentation')
     */
    public function get_category_display_name($category_key) {
        $category_map = array(
            'main' => 'Main Program',
            'documentation' => 'Documentation',
            'source' => 'Source Code',
            'config' => 'Configuration',
            'utility' => 'Utility',
            'other' => 'Other'
        );
        
        return isset($category_map[$category_key]) ? $category_map[$category_key] : ucfirst($category_key);
    }
    
    /**
     * Handle files with multiple dots in filename during upload
     * 
     * WordPress sometimes rejects files with multiple dots. This function
     * ensures that files with valid extensions are allowed even with multiple dots.
     * 
     * @param array $file The file array from WordPress
     * @return array Modified file array
     */
    public function handle_multiple_dots_upload($file) {
        // Only process if this is a GBBS archive upload
        if (!isset($_POST['type']) || $_POST['type'] !== 'gbbs_archive') {
            return $file;
        }
        
        // If there's already an error, don't interfere
        if (!empty($file['error'])) {
            return $file;
        }
        
        $filename = $file['name'];
        $allowed_types = $this->settings->get_allowed_file_types();
        
        // Get the file extension (everything after the last dot)
        $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Check if the extension is allowed
        if (in_array($file_extension, $allowed_types)) {
            // File has a valid extension, allow it
            return $file;
        }
        
        // If we get here, the file extension is not allowed
        // Set an error message
        $file['error'] = sprintf(
            __('File type "%s" is not allowed. Allowed types: %s', 'gbbs-software-archive'),
            $file_extension,
            implode(', ', $allowed_types)
        );
        
        return $file;
    }
    
    /**
     * Allow files with multiple dots to be uploaded
     * 
     * WordPress sometimes rejects files with multiple dots. This function
     * ensures that files with valid extensions are allowed even with multiple dots.
     * 
     * @param array $data File data array
     * @param string $file Full path to the file
     * @param string $filename The name of the file
     * @param array $mimes Array of mime types
     * @return array Modified file data
     */
    public function allow_multiple_dots_filetype($data, $file, $filename, $mimes) {
        // Only process if this is a GBBS archive upload
        if (!isset($_POST['type']) || $_POST['type'] !== 'gbbs_archive') {
            return $data;
        }
        
        $allowed_types = $this->settings->get_allowed_file_types();
        
        // Get the file extension (everything after the last dot)
        $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Check if the extension is allowed
        if (in_array($file_extension, $allowed_types)) {
            // File has a valid extension, allow it
            $data['ext'] = $file_extension;
            $data['type'] = 'application/octet-stream'; // Generic type for Apple II files
            $data['proper_filename'] = $filename;
        }
        
        return $data;
    }
    
    /**
     * Preserve multiple dots in filenames for GBBS archives
     * 
     * WordPress sanitizes filenames by replacing dots (except the last one) with underscores
     * for security reasons. This function preserves the original filename structure for
     * GBBS archive uploads while maintaining security for other uploads.
     * 
     * @param string $filename The sanitized filename
     * @param string $filename_raw The original filename before sanitization
     * @return string The filename with multiple dots preserved
     */
    public function preserve_multiple_dots_filename($filename, $filename_raw) {
        // Only process if this is a GBBS archive upload
        if (!isset($_POST['type']) || $_POST['type'] !== 'gbbs_archive') {
            return $filename;
        }
        
        // Check if we're in an admin context and on a GBBS archive page
        if (is_admin()) {
            global $pagenow, $post;
            if (($pagenow === 'post.php' || $pagenow === 'post-new.php') && 
                isset($post) && $post->post_type === 'gbbs_archive') {
                // This is a GBBS archive upload, preserve the original filename
                return $filename_raw;
            }
        }
        
        // For other contexts, check if this is a GBBS archive upload via POST data
        if (isset($_POST['post_id']) && get_post_type($_POST['post_id']) === 'gbbs_archive') {
            return $filename_raw;
        }
        
        // If we can't determine the context, return the sanitized filename
        return $filename;
    }
    
    /**
     * Fix attachment URLs to use original filenames for GBBS archives
     * 
     * When files with multiple dots are uploaded, WordPress generates URLs with
     * sanitized filenames (underscores), but the actual files are saved with
     * original names. This method fixes the URL to match the actual file.
     * 
     * @param string $url The attachment URL
     * @param int $attachment_id The attachment ID
     * @return string The corrected URL
     */
    public function fix_attachment_url_for_gbbs($url, $attachment_id) {
        // Only process if this is a GBBS archive context
        if (!isset($_POST['type']) || $_POST['type'] !== 'gbbs_archive') {
            // Check if we're in an admin context and on a GBBS archive page
            if (is_admin()) {
                global $pagenow, $post;
                if (!(($pagenow === 'post.php' || $pagenow === 'post-new.php') && 
                      isset($post) && $post->post_type === 'gbbs_archive')) {
                    return $url;
                }
            } else {
                return $url;
            }
        }
        
        // Get the attachment metadata
        $attachment_meta = get_post_meta($attachment_id, '_wp_attachment_metadata', true);
        if (!$attachment_meta || !isset($attachment_meta['file'])) {
            return $url;
        }
        
        // Get the original filename from the attachment
        $original_filename = get_post_meta($attachment_id, '_wp_attached_file', true);
        if (!$original_filename) {
            return $url;
        }
        
        // Check if the URL contains sanitized filename (with underscores)
        $upload_dir = wp_upload_dir();
        $file_path = $upload_dir['basedir'] . '/' . $original_filename;
        
        // If the file exists with the original name, update the URL
        if (file_exists($file_path)) {
            $original_url = $upload_dir['baseurl'] . '/' . $original_filename;
            
            // Check if the current URL is different from the original
            if ($url !== $original_url) {
                // The URL is using the sanitized filename, fix it
                return $original_url;
            }
        }
        
        return $url;
    }
}