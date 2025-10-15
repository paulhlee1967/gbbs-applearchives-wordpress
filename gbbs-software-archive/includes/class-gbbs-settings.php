<?php
/**
 * GBBS Software Archive Settings Management
 * 
 * This class handles all settings management for the GBBS Software Archive plugin.
 * It provides methods for getting, setting, and validating plugin options.
 * 
 * @package GBBS_Software_Archive
 * @version 1.0.0
 * @author Paul H. Lee
 */

if (!defined('ABSPATH')) {
    exit;
}

class GBBS_Settings {
    
    /**
     * Settings option name
     * 
     * @var string
     */
    private $option_name = 'gbbs_archive_settings';
    
    /**
     * Default settings
     * 
     * @var array
     */
    private $defaults = array(
        // General Settings
        'download_endpoint' => 'gbbs-download',
        'endpoint_type' => 'id', // 'id' or 'slug'
        'role_permissions' => array('administrator', 'editor'),
        'archive_title' => 'GBBS Pro Software Archive',
        'items_per_page' => 20,
        
        // Display Settings
        'show_download_counts' => true,
        'show_file_sizes' => true,
        'show_upload_dates' => true,
        'enable_search' => true,
        'enable_volume_filter' => true,
        
        // Download Settings
        'require_login' => false,
        'track_downloads' => true,
        'download_timeout' => 300, // 5 minutes
        'download_logging' => true,
        'download_counter' => true,
        'rate_limiting' => true,
        'rate_limit_requests' => 10, // requests per minute
        'rate_limit_window' => 60, // seconds
        
        // Archive Settings
        'archive_description' => 'Your gateway to GBBS Pro and GBBS II software',
        'show_archive_stats' => true,
        'enable_sorting' => true,
        'default_sorting' => 'name', // 'name', 'date', 'downloads', 'size'
        
        // URL Settings
        'post_type_endpoint' => 'gbbs-archive',
        'volume_endpoint' => 'gbbs-volume',
        
        // Upload Settings
        'upload_folder_structure' => 'gbbs_dedicated', // 'wordpress_default' or 'gbbs_dedicated'
        'file_organization' => 'by_archive', // 'by_archive', 'by_volume', 'flat'
        'max_file_size' => 50, // MB
        'restrict_file_types' => true, // Whether to restrict file types
        'allowed_file_types' => array(
            // Apple II Disk Images
            'dsk', 'po', 'do', 'nib', 'woz', '2mg',
            // Apple II File Formats
            'bas', 'int', 'asm', 's', 'bin', 'a2s', 'a2d',
            // Archive Formats
            'shk', 'bny', 'sea', 'bxy', 'bqy', 'zip',
            // Documentation
            'txt', 'doc', 'pdf'
        ),
        
    );
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_init', array($this, 'handle_settings_save'));
    }
    
    /**
     * Register settings with WordPress
     */
    public function register_settings() {
        register_setting(
            'gbbs_archive_settings_group',
            $this->option_name,
            array(
                'sanitize_callback' => array($this, 'sanitize_settings'),
                'default' => $this->defaults
            )
        );
    }
    
    /**
     * Handle settings form submission
     */
    public function handle_settings_save() {
        
        if (!isset($_POST['gbbs_settings_nonce'])) {
            return;
        }
        
        if (!wp_verify_nonce($_POST['gbbs_settings_nonce'], 'gbbs_save_settings')) {
            return;
        }
        
        if (!current_user_can('manage_options')) {
            return;
        }
        
        if (isset($_POST['gbbs_save_settings'])) {
            $settings = $this->sanitize_settings($_POST);
            $result = update_option($this->option_name, $settings);
            
            // Add admin notice
            add_action('admin_notices', function() use ($result) {
                if ($result) {
                    echo '<div class="notice notice-success is-dismissible"><p>' . 
                         __('Settings saved successfully!', 'gbbs-software-archive') . 
                         '</p></div>';
                } else {
                    echo '<div class="notice notice-warning is-dismissible"><p>' . 
                         __('Settings were not changed (same values).', 'gbbs-software-archive') . 
                         '</p></div>';
                }
            });
            
            // Flush rewrite rules if endpoint changed
            if (isset($_POST['download_endpoint'])) {
                flush_rewrite_rules();
            }
        }
    }
    
    /**
 * Sanitize settings data
 * 
 * @param array $input Raw input data
 * @return array Sanitized settings
 */
public function sanitize_settings($input) {
    // Start with current settings to preserve values not in the form
    $current_settings = get_option($this->option_name, $this->defaults);
    $sanitized = $current_settings;
    
    // General Settings
    if (isset($input['download_endpoint'])) {
        $sanitized['download_endpoint'] = sanitize_title($input['download_endpoint']);
    }
    
    if (isset($input['endpoint_type'])) {
        $sanitized['endpoint_type'] = in_array($input['endpoint_type'], array('id', 'slug')) ? 
                                     $input['endpoint_type'] : 'id';
    }
    
    if (isset($input['role_permissions']) && is_array($input['role_permissions'])) {
        $sanitized['role_permissions'] = array_map('sanitize_text_field', $input['role_permissions']);
    }
    
    if (isset($input['archive_title'])) {
        $sanitized['archive_title'] = sanitize_text_field($input['archive_title']);
    }
    
    if (isset($input['items_per_page'])) {
        $sanitized['items_per_page'] = max(1, intval($input['items_per_page']));
    }
    
    // Display Settings - explicitly set based on presence in input
    // These will be false if checkbox is unchecked (not in POST)
    $sanitized['show_download_counts'] = !empty($input['show_download_counts']);
    $sanitized['show_file_sizes'] = !empty($input['show_file_sizes']);
    $sanitized['show_upload_dates'] = !empty($input['show_upload_dates']);
    $sanitized['enable_search'] = !empty($input['enable_search']);
    $sanitized['enable_volume_filter'] = !empty($input['enable_volume_filter']);
    
    // Download Settings - explicitly set based on presence in input
    $sanitized['require_login'] = !empty($input['require_login']);
    $sanitized['track_downloads'] = !empty($input['track_downloads']);
    $sanitized['download_logging'] = !empty($input['download_logging']);
    $sanitized['download_counter'] = !empty($input['download_counter']);
    $sanitized['rate_limiting'] = !empty($input['rate_limiting']);
    
    if (isset($input['download_timeout'])) {
        $sanitized['download_timeout'] = max(30, intval($input['download_timeout']));
    }
    
    if (isset($input['rate_limit_requests'])) {
        $sanitized['rate_limit_requests'] = max(1, min(100, intval($input['rate_limit_requests'])));
    }
    
    if (isset($input['rate_limit_window'])) {
        $sanitized['rate_limit_window'] = max(10, min(3600, intval($input['rate_limit_window'])));
    }
    
    // Archive Settings
    if (isset($input['archive_description'])) {
        $sanitized['archive_description'] = sanitize_textarea_field($input['archive_description']);
    }
    
    $sanitized['show_archive_stats'] = !empty($input['show_archive_stats']);
    $sanitized['enable_sorting'] = !empty($input['enable_sorting']);
    
    if (isset($input['default_sorting'])) {
        $sanitized['default_sorting'] = in_array($input['default_sorting'], array('name', 'date', 'downloads', 'size')) ? 
                                       $input['default_sorting'] : 'name';
    }
    
    // URL Settings
    if (isset($input['post_type_endpoint'])) {
        $sanitized['post_type_endpoint'] = sanitize_title($input['post_type_endpoint']);
    }
    
    if (isset($input['volume_endpoint'])) {
        $sanitized['volume_endpoint'] = sanitize_title($input['volume_endpoint']);
    }
    
    // Upload Settings
    if (isset($input['upload_folder_structure'])) {
        $sanitized['upload_folder_structure'] = in_array($input['upload_folder_structure'], array('wordpress_default', 'gbbs_dedicated')) ? 
                                               $input['upload_folder_structure'] : 'gbbs_dedicated';
    }
    
    if (isset($input['file_organization'])) {
        $sanitized['file_organization'] = in_array($input['file_organization'], array('by_archive', 'by_volume', 'flat')) ? 
                                        $input['file_organization'] : 'by_archive';
    }
    
    if (isset($input['max_file_size'])) {
        $sanitized['max_file_size'] = max(1, min(1000, intval($input['max_file_size']))); // 1-1000 MB
    }
    
    $sanitized['restrict_file_types'] = !empty($input['restrict_file_types']);
    
    if (isset($input['allowed_file_types']) && is_array($input['allowed_file_types'])) {
        $sanitized['allowed_file_types'] = array_map('sanitize_text_field', $input['allowed_file_types']);
    }
    
    return $sanitized;
}
    
    /**
     * Get all settings
     * 
     * @return array Settings array
     */
    public function get_settings() {
        $settings = get_option($this->option_name, $this->defaults);
        $merged_settings = wp_parse_args($settings, $this->defaults);
        
        return $merged_settings;
    }
    
    /**
     * Get a specific setting
     * 
     * @param string $key Setting key
     * @param mixed $default Default value
     * @return mixed Setting value
     */
    public function get_setting($key, $default = null) {
        $settings = $this->get_settings();
        
        if (isset($settings[$key])) {
            return $settings[$key];
        }
        
        return $default !== null ? $default : (isset($this->defaults[$key]) ? $this->defaults[$key] : null);
    }
    
    /**
     * Update a specific setting
     * 
     * @param string $key Setting key
     * @param mixed $value Setting value
     * @return bool True if successful
     */
    public function update_setting($key, $value) {
        $settings = $this->get_settings();
        $settings[$key] = $value;
        return update_option($this->option_name, $settings);
    }
    
    /**
     * Get available user roles
     * 
     * @return array Array of user roles
     */
    public function get_available_roles() {
        global $wp_roles;
        
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }
        
        $roles = array();
        foreach ($wp_roles->roles as $role_key => $role_data) {
            $roles[$role_key] = $role_data['name'];
        }
        
        return $roles;
    }
    
    /**
     * Check if current user can edit GBBS archives
     * 
     * @return bool True if user can edit
     */
    public function can_user_edit_archives() {
        $allowed_roles = $this->get_setting('role_permissions', array('administrator', 'editor'));
        $user = wp_get_current_user();
        
        if (empty($user->roles)) {
            return false;
        }
        
        return !empty(array_intersect($user->roles, $allowed_roles));
    }
    
    /**
     * Get download endpoint URL
     * 
     * @param int $archive_id Archive ID
     * @param int $file_index File index
     * @return string Download URL
     */
    public function get_download_url($archive_id, $file_index) {
        $endpoint = $this->get_setting('download_endpoint', 'gbbs-download');
        $endpoint_type = $this->get_setting('endpoint_type', 'id');
        
        if ($endpoint_type === 'slug') {
            $archive = get_post($archive_id);
            if ($archive) {
                $download_id = $archive->post_name . '-' . $file_index;
            } else {
                $download_id = $archive_id . '-' . $file_index;
            }
        } else {
            $download_id = $archive_id . '-' . $file_index;
        }
        
        if (get_option('permalink_structure')) {
            return home_url('/' . $endpoint . '/' . $download_id . '/');
        } else {
            return add_query_arg($endpoint, $download_id, home_url());
        }
    }
    
    /**
     * Get archive title
     * 
     * @return string Archive title
     */
    public function get_archive_title() {
        return $this->get_setting('archive_title', 'GBBS Pro Software Archive');
    }
    
    /**
     * Get archive description
     * 
     * @return string Archive description
     */
    public function get_archive_description() {
        return $this->get_setting('archive_description', 'Your gateway to GBBS Pro and GBBS II software');
    }
    
    /**
     * Get items per page
     * 
     * @return int Items per page
     */
    public function get_items_per_page() {
        return $this->get_setting('items_per_page', 20);
    }
    
    /**
     * Check if downloads require login
     * 
     * @return bool True if login required
     */
    public function require_login_for_downloads() {
        return $this->get_setting('require_login', false);
    }
    
    /**
     * Check if downloads should be tracked
     * 
     * @return bool True if tracking enabled
     */
    public function track_downloads() {
        return $this->get_setting('track_downloads', true);
    }
    
    /**
     * Get download timeout
     * 
     * @return int Timeout in seconds
     */
    public function get_download_timeout() {
        return $this->get_setting('download_timeout', 300);
    }
    
    
    /**
     * Get default sorting order
     * 
     * @return string Default sorting order
     */
    public function get_default_sorting() {
        return $this->get_setting('default_sorting', 'name');
    }
    
    
    /**
     * Check if download logging is enabled
     * 
     * @return bool True if logging enabled
     */
    public function is_download_logging_enabled() {
        return $this->get_setting('download_logging', true);
    }
    
    /**
     * Check if download counter is enabled
     * 
     * @return bool True if counter enabled
     */
    public function is_download_counter_enabled() {
        return $this->get_setting('download_counter', true);
    }
    
    /**
     * Check if rate limiting is enabled
     * 
     * @return bool True if rate limiting enabled
     */
    public function is_rate_limiting_enabled() {
        return $this->get_setting('rate_limiting', true);
    }
    
    /**
     * Get rate limit requests per window
     * 
     * @return int Number of requests allowed per window
     */
    public function get_rate_limit_requests() {
        return $this->get_setting('rate_limit_requests', 10);
    }
    
    /**
     * Get rate limit window in seconds
     * 
     * @return int Window duration in seconds
     */
    public function get_rate_limit_window() {
        return $this->get_setting('rate_limit_window', 60);
    }
    
    /**
     * Get post type endpoint slug
     * 
     * @return string Post type endpoint slug
     */
    public function get_post_type_endpoint() {
        return $this->get_setting('post_type_endpoint', 'gbbs-archive');
    }
    
    /**
     * Get volume endpoint slug
     * 
     * @return string Volume endpoint slug
     */
    public function get_volume_endpoint() {
        return $this->get_setting('volume_endpoint', 'gbbs-volume');
    }
    
    /**
     * Check if user has exceeded rate limit
     * 
     * @param string $ip User IP address
     * @return bool True if rate limit exceeded
     */
    public function is_rate_limit_exceeded($ip) {
        if (!$this->is_rate_limiting_enabled()) {
            return false;
        }
        
        $transient_key = 'gbbs_rate_limit_' . md5($ip);
        $requests = get_transient($transient_key);
        
        if ($requests === false) {
            set_transient($transient_key, 1, $this->get_rate_limit_window());
            return false;
        }
        
        if ($requests >= $this->get_rate_limit_requests()) {
            return true;
        }
        
        set_transient($transient_key, $requests + 1, $this->get_rate_limit_window());
        return false;
    }
    
    /**
     * Get upload folder structure setting
     * 
     * @return string Upload folder structure
     */
    public function get_upload_folder_structure() {
        return $this->get_setting('upload_folder_structure', 'gbbs_dedicated');
    }
    
    /**
     * Get file organization setting
     * 
     * @return string File organization method
     */
    public function get_file_organization() {
        return $this->get_setting('file_organization', 'by_archive');
    }
    
    /**
     * Get maximum file size in MB
     * 
     * @return int Maximum file size in MB
     */
    public function get_max_file_size() {
        return $this->get_setting('max_file_size', 50);
    }
    
    /**
     * Get maximum file size in bytes
     * 
     * @return int Maximum file size in bytes
     */
    public function get_max_file_size_bytes() {
        return $this->get_max_file_size() * 1024 * 1024;
    }
    
    /**
     * Get allowed file types
     * 
     * @return array Array of allowed file extensions
     */
    public function get_allowed_file_types() {
        return $this->get_setting('allowed_file_types', array());
    }
    
    /**
     * Check if file type is allowed
     * 
     * @param string $filename File name to check
     * @return bool True if file type is allowed
     */
    public function is_file_type_allowed($filename) {
        $allowed_types = $this->get_allowed_file_types();
        $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        return in_array($file_extension, $allowed_types);
    }
    
    /**
     * Get all available file types with descriptions and enable/disable status
     * 
     * @return array File types with descriptions and status
     */
    public function get_file_type_definitions() {
        return array(
            // Apple II Disk Images
            'dsk' => array(
                'name' => 'Disk Image',
                'description' => 'Apple II disk image files',
                'category' => 'disk_images',
                'mime_type' => 'application/octet-stream'
            ),
            'po' => array(
                'name' => 'ProDOS Order',
                'description' => 'ProDOS disk image files',
                'category' => 'disk_images',
                'mime_type' => 'application/octet-stream'
            ),
            'do' => array(
                'name' => 'DOS Order',
                'description' => 'DOS disk image files',
                'category' => 'disk_images',
                'mime_type' => 'application/octet-stream'
            ),
            'nib' => array(
                'name' => 'Nibble',
                'description' => 'Nibble disk image files',
                'category' => 'disk_images',
                'mime_type' => 'application/octet-stream'
            ),
            'woz' => array(
                'name' => 'WOZ',
                'description' => 'WOZ disk image files',
                'category' => 'disk_images',
                'mime_type' => 'application/octet-stream'
            ),
            '2mg' => array(
                'name' => '2MG',
                'description' => '2MG disk image files',
                'category' => 'disk_images',
                'mime_type' => 'application/octet-stream'
            ),
            
            // Apple II File Formats
            'bas' => array(
                'name' => 'BASIC',
                'description' => 'Apple II BASIC program files',
                'category' => 'programs',
                'mime_type' => 'text/plain'
            ),
            'int' => array(
                'name' => 'Integer BASIC',
                'description' => 'Apple II Integer BASIC program files',
                'category' => 'programs',
                'mime_type' => 'text/plain'
            ),
            'asm' => array(
                'name' => 'Assembly',
                'description' => 'Assembly language source files',
                'category' => 'programs',
                'mime_type' => 'text/plain'
            ),
            's' => array(
                'name' => 'Source Code',
                'description' => 'Source code files',
                'category' => 'programs',
                'mime_type' => 'text/plain'
            ),
            'bin' => array(
                'name' => 'Binary',
                'description' => 'Binary executable files',
                'category' => 'programs',
                'mime_type' => 'application/octet-stream'
            ),
            'a2s' => array(
                'name' => 'Apple II Source',
                'description' => 'Apple II source code files',
                'category' => 'programs',
                'mime_type' => 'application/octet-stream'
            ),
            'a2d' => array(
                'name' => 'Apple II Data',
                'description' => 'Apple II data files',
                'category' => 'programs',
                'mime_type' => 'application/octet-stream'
            ),
            'bxy' => array(
                'name' => 'Binary XY',
                'description' => 'Binary XY format files',
                'category' => 'programs',
                'mime_type' => 'application/octet-stream'
            ),
            'bqy' => array(
                'name' => 'Binary QY',
                'description' => 'Binary QY format files',
                'category' => 'programs',
                'mime_type' => 'application/octet-stream'
            ),
            
            // Archive Formats
            'shk' => array(
                'name' => 'ShrinkIt',
                'description' => 'ShrinkIt archive files',
                'category' => 'archives',
                'mime_type' => 'application/octet-stream'
            ),
            'bny' => array(
                'name' => 'Binary NY',
                'description' => 'Binary NY archive files',
                'category' => 'archives',
                'mime_type' => 'application/octet-stream'
            ),
            'sea' => array(
                'name' => 'Self-Extracting Archive',
                'description' => 'Self-extracting archive files',
                'category' => 'archives',
                'mime_type' => 'application/octet-stream'
            ),
            'zip' => array(
                'name' => 'ZIP Archive',
                'description' => 'ZIP archive files',
                'category' => 'archives',
                'mime_type' => 'application/zip'
            ),
            
            // Documentation
            'txt' => array(
                'name' => 'Text File',
                'description' => 'Plain text files',
                'category' => 'documentation',
                'mime_type' => 'text/plain'
            ),
            'doc' => array(
                'name' => 'Word Document',
                'description' => 'Microsoft Word document files',
                'category' => 'documentation',
                'mime_type' => 'application/msword'
            ),
            'pdf' => array(
                'name' => 'PDF Document',
                'description' => 'PDF document files',
                'category' => 'documentation',
                'mime_type' => 'application/pdf'
            )
        );
    }
    
    /**
     * Get file types grouped by category
     * 
     * @return array File types grouped by category
     */
    public function get_file_types_by_category() {
        $definitions = $this->get_file_type_definitions();
        $grouped = array();
        
        foreach ($definitions as $extension => $info) {
            $category = $info['category'];
            if (!isset($grouped[$category])) {
                $grouped[$category] = array();
            }
            $grouped[$category][$extension] = $info;
        }
        
        return $grouped;
    }
    
    /**
     * Get enabled file types with their definitions
     * 
     * @return array Enabled file types with definitions
     */
    public function get_enabled_file_types_with_definitions() {
        $enabled_types = $this->get_allowed_file_types();
        $definitions = $this->get_file_type_definitions();
        $enabled_with_definitions = array();
        
        foreach ($enabled_types as $extension) {
            if (isset($definitions[$extension])) {
                $enabled_with_definitions[$extension] = $definitions[$extension];
            }
        }
        
        return $enabled_with_definitions;
    }
    
    /**
     * Get file type statistics
     * 
     * @return array File type statistics
     */
    public function get_file_type_stats() {
        $definitions = $this->get_file_type_definitions();
        $enabled_types = $this->get_allowed_file_types();
        
        $stats = array(
            'total_types' => count($definitions),
            'enabled_types' => count($enabled_types),
            'disabled_types' => count($definitions) - count($enabled_types),
            'by_category' => array()
        );
        
        // Count by category
        foreach ($definitions as $extension => $info) {
            $category = $info['category'];
            if (!isset($stats['by_category'][$category])) {
                $stats['by_category'][$category] = array(
                    'total' => 0,
                    'enabled' => 0,
                    'disabled' => 0
                );
            }
            
            $stats['by_category'][$category]['total']++;
            if (in_array($extension, $enabled_types)) {
                $stats['by_category'][$category]['enabled']++;
            } else {
                $stats['by_category'][$category]['disabled']++;
            }
        }
        
        return $stats;
    }
    
    /**
     * Enable all file types in a category
     * 
     * @param string $category Category name
     * @return bool True if successful
     */
    public function enable_category_file_types($category) {
        $definitions = $this->get_file_type_definitions();
        $enabled_types = $this->get_allowed_file_types();
        
        foreach ($definitions as $extension => $info) {
            if ($info['category'] === $category && !in_array($extension, $enabled_types)) {
                $enabled_types[] = $extension;
            }
        }
        
        return $this->set_setting('allowed_file_types', $enabled_types);
    }
    
    /**
     * Disable all file types in a category
     * 
     * @param string $category Category name
     * @return bool True if successful
     */
    public function disable_category_file_types($category) {
        $definitions = $this->get_file_type_definitions();
        $enabled_types = $this->get_allowed_file_types();
        
        foreach ($definitions as $extension => $info) {
            if ($info['category'] === $category) {
                $key = array_search($extension, $enabled_types);
                if ($key !== false) {
                    unset($enabled_types[$key]);
                }
            }
        }
        
        return $this->set_setting('allowed_file_types', array_values($enabled_types));
    }
    
    /**
     * Get upload directory path based on settings
     * 
     * @param int $archive_id Archive ID (for organization)
     * @param string $volume_slug Volume slug (for organization)
     * @return string Upload directory path
     */
    public function get_upload_directory($archive_id = null, $volume_slug = null) {
        $upload_dir = wp_upload_dir();
        
        if ($this->get_upload_folder_structure() === 'wordpress_default') {
            return $upload_dir['basedir'];
        }
        
        // GBBS dedicated folder
        $gbbs_dir = $upload_dir['basedir'] . '/gbbs-archive';
        
        $organization = $this->get_file_organization();
        
        if ($organization === 'by_archive') {
            if ($archive_id) {
                return $gbbs_dir . '/' . $archive_id;
            } else {
                // If no archive ID, create a temporary folder for new uploads
                return $gbbs_dir . '/temp';
            }
        } elseif ($organization === 'by_volume') {
            if ($volume_slug) {
                return $gbbs_dir . '/volumes/' . $volume_slug;
            } else {
                // If no volume slug, create a temporary folder for new uploads
                return $gbbs_dir . '/volumes/temp';
            }
        } else {
            // Flat organization
            return $gbbs_dir . '/files';
        }
    }
    
    /**
     * Get upload URL based on settings
     * 
     * @param int $archive_id Archive ID (for organization)
     * @param string $volume_slug Volume slug (for organization)
     * @return string Upload URL
     */
    public function get_upload_url($archive_id = null, $volume_slug = null) {
        $upload_dir = wp_upload_dir();
        
        if ($this->get_upload_folder_structure() === 'wordpress_default') {
            return $upload_dir['baseurl'];
        }
        
        // GBBS dedicated folder
        $gbbs_url = $upload_dir['baseurl'] . '/gbbs-archive';
        
        $organization = $this->get_file_organization();
        
        if ($organization === 'by_archive') {
            if ($archive_id) {
                return $gbbs_url . '/' . $archive_id;
            } else {
                // If no archive ID, use temporary folder for new uploads
                return $gbbs_url . '/temp';
            }
        } elseif ($organization === 'by_volume') {
            if ($volume_slug) {
                return $gbbs_url . '/volumes/' . $volume_slug;
            } else {
                // If no volume slug, use temporary folder for new uploads
                return $gbbs_url . '/volumes/temp';
            }
        } else {
            // Flat organization
            return $gbbs_url . '/files';
        }
    }
    
    /**
     * Move files from temp folder to proper archive folder
     * 
     * @param int $archive_id Archive ID
     * @return bool True if successful
     */
    public function organize_temp_files($archive_id) {
        if ($this->get_file_organization() !== 'by_archive') {
            return true; // Only needed for by_archive organization
        }
        
        $upload_dir = wp_upload_dir();
        $gbbs_dir = $upload_dir['basedir'] . '/gbbs-archive';
        $temp_dir = $gbbs_dir . '/temp';
        $archive_dir = $gbbs_dir . '/' . $archive_id;
        
        if (!file_exists($temp_dir)) {
            return true; // No temp files to move
        }
        
        // Create archive directory if it doesn't exist
        if (!file_exists($archive_dir)) {
            wp_mkdir_p($archive_dir);
        }
        
        // Move files from temp to archive directory
        $files = glob($temp_dir . '/*');
        $moved = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $filename = basename($file);
                $destination = $archive_dir . '/' . $filename;
                
                if (rename($file, $destination)) {
                    $moved++;
                }
            }
        }
        
        // Remove temp directory if empty
        if (count(glob($temp_dir . '/*')) === 0) {
            rmdir($temp_dir);
        }
        
        return $moved > 0;
    }
    
    /**
     * Reset settings to defaults
     * 
     * @return bool True if successful
     */
    public function reset_to_defaults() {
        return update_option($this->option_name, $this->defaults);
    }
    
    /**
     * Export settings
     * 
     * @return array Settings array for export
     */
    public function export_settings() {
        return $this->get_settings();
    }
    
    /**
     * Import settings
     * 
     * @param array $settings Settings to import
     * @return bool True if successful
     */
    public function import_settings($settings) {
        if (!is_array($settings)) {
            return false;
        }
        
        $sanitized = $this->sanitize_settings($settings);
        return update_option($this->option_name, $sanitized);
    }
}