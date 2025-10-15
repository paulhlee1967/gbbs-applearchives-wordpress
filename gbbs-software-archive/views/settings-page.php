<?php
/**
 * GBBS Software Archive Settings Page
 * 
 * This file contains the HTML structure for the plugin settings page.
 * It includes all the form fields for configuring the plugin options.
 * 
 * @package GBBS_Software_Archive
 * @version 1.0.0
 * @author Paul H. Lee
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get current settings
$settings = $gbbs_settings->get_settings();
$available_roles = $gbbs_settings->get_available_roles();

?>

<div class="wrap gbbs-settings-page">
    <h1><?php _e('GBBS Archive Settings', 'gbbs-software-archive'); ?></h1>
    
    <div class="gbbs-settings-container">
        <div class="gbbs-settings-main">
            <form method="post" action="" id="gbbs-settings-form">
                <?php wp_nonce_field('gbbs_save_settings', 'gbbs_settings_nonce'); ?>
                
                <!-- Tab Navigation -->
                <div class="gbbs-tab-nav">
                    <button type="button" class="gbbs-tab-button active" data-tab="general-settings">
                        <?php _e('General', 'gbbs-software-archive'); ?>
                    </button>
                    <button type="button" class="gbbs-tab-button" data-tab="display-settings">
                        <?php _e('Display', 'gbbs-software-archive'); ?>
                    </button>
                    <button type="button" class="gbbs-tab-button" data-tab="upload-settings">
                        <?php _e('Upload', 'gbbs-software-archive'); ?>
                    </button>
                    <button type="button" class="gbbs-tab-button" data-tab="download-settings">
                        <?php _e('Download', 'gbbs-software-archive'); ?>
                    </button>
                </div>
                
                <!-- General Settings Tab -->
                <div class="gbbs-tab-content active" id="general-settings">
                    <h2><?php _e('General Settings', 'gbbs-software-archive'); ?></h2>
                    <p class="description"><?php _e('Configure basic plugin settings and behavior.', 'gbbs-software-archive'); ?></p>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="download_endpoint"><?php _e('Download Endpoint', 'gbbs-software-archive'); ?></label>
                            </th>
                            <td>
                                <input type="text" 
                                       id="download_endpoint" 
                                       name="download_endpoint" 
                                       value="<?php echo esc_attr($settings['download_endpoint']); ?>" 
                                       class="regular-text"
                                       placeholder="gbbs-download">
                                <p class="description">
                                    <?php _e('Customize the download URL structure. Default: gbbs-download', 'gbbs-software-archive'); ?>
                                    <br>
                                    <strong><?php _e('Example URLs:', 'gbbs-software-archive'); ?></strong>
                                    <code><?php echo home_url('/gbbs-download/123-0/'); ?></code>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="endpoint_type"><?php _e('Endpoint Type', 'gbbs-software-archive'); ?></label>
                            </th>
                            <td>
                                <fieldset>
                                    <label>
                                        <input type="radio" 
                                               name="endpoint_type" 
                                               value="id" 
                                               <?php checked($settings['endpoint_type'], 'id'); ?>>
                                        <?php _e('ID-based URLs', 'gbbs-software-archive'); ?>
                                        <span class="description"><?php _e('(e.g., /gbbs-download/123-0/)', 'gbbs-software-archive'); ?></span>
                                    </label><br>
                                    <label>
                                        <input type="radio" 
                                               name="endpoint_type" 
                                               value="slug" 
                                               <?php checked($settings['endpoint_type'], 'slug'); ?>>
                                        <?php _e('Slug-based URLs', 'gbbs-software-archive'); ?>
                                        <span class="description"><?php _e('(e.g., /gbbs-download/archive-name-0/)', 'gbbs-software-archive'); ?></span>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="role_permissions"><?php _e('Role Permissions', 'gbbs-software-archive'); ?></label>
                            </th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text"><?php _e('Select user roles that can edit GBBS archives', 'gbbs-software-archive'); ?></legend>
                                    <?php foreach ($available_roles as $role_key => $role_name): ?>
                                        <label>
                                            <input type="checkbox" 
                                                   name="role_permissions[]" 
                                                   value="<?php echo esc_attr($role_key); ?>"
                                                   <?php checked(in_array($role_key, $settings['role_permissions'])); ?>>
                                            <?php echo esc_html($role_name); ?>
                                        </label><br>
                                    <?php endforeach; ?>
                                </fieldset>
                                <p class="description">
                                    <?php _e('Select which user roles can create and edit GBBS archives.', 'gbbs-software-archive'); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="post_type_endpoint"><?php _e('Archive URL Slug', 'gbbs-software-archive'); ?></label>
                            </th>
                            <td>
                                <input type="text" 
                                       id="post_type_endpoint" 
                                       name="post_type_endpoint" 
                                       value="<?php echo esc_attr($settings['post_type_endpoint']); ?>" 
                                       class="regular-text"
                                       placeholder="gbbs-archive">
                                <p class="description">
                                    <?php _e('URL slug for individual archive pages.', 'gbbs-software-archive'); ?>
                                    <br>
                                    <strong><?php _e('Example URLs:', 'gbbs-software-archive'); ?></strong>
                                    <code><?php echo home_url('/gbbs-archive/archive-name/'); ?></code>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="volume_endpoint"><?php _e('Volume URL Slug', 'gbbs-software-archive'); ?></label>
                            </th>
                            <td>
                                <input type="text" 
                                       id="volume_endpoint" 
                                       name="volume_endpoint" 
                                       value="<?php echo esc_attr($settings['volume_endpoint']); ?>" 
                                       class="regular-text"
                                       placeholder="gbbs-volume">
                                <p class="description">
                                    <?php _e('URL slug for volume category pages.', 'gbbs-software-archive'); ?>
                                    <br>
                                    <strong><?php _e('Example URLs:', 'gbbs-software-archive'); ?></strong>
                                    <code><?php echo home_url('/gbbs-volume/games/'); ?></code>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Display Settings Tab -->
                <div class="gbbs-tab-content" id="display-settings">
                    <h2><?php _e('Display Settings', 'gbbs-software-archive'); ?></h2>
                    <p class="description"><?php _e('Control what information is displayed in the archive.', 'gbbs-software-archive'); ?></p>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="archive_title"><?php _e('Archive Title', 'gbbs-software-archive'); ?></label>
                            </th>
                            <td>
                                <input type="text" 
                                       id="archive_title" 
                                       name="archive_title" 
                                       value="<?php echo esc_attr($settings['archive_title']); ?>" 
                                       class="regular-text">
                                <p class="description">
                                    <?php _e('The main title displayed in the BBS header.', 'gbbs-software-archive'); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="archive_description"><?php _e('Archive Description', 'gbbs-software-archive'); ?></label>
                            </th>
                            <td>
                                <textarea id="archive_description" 
                                          name="archive_description" 
                                          rows="3" 
                                          cols="50" 
                                          class="large-text"><?php echo esc_textarea($settings['archive_description']); ?></textarea>
                                <p class="description">
                                    <?php _e('Description text displayed below the archive title.', 'gbbs-software-archive'); ?>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Show Information', 'gbbs-software-archive'); ?></th>
                            <td>
                                <fieldset>
                                    <label>
                                        <input type="checkbox" 
                                               name="show_download_counts" 
                                               value="1"
                                               <?php checked($settings['show_download_counts'], true); ?>>
                                        <?php _e('Download counts', 'gbbs-software-archive'); ?>
                                    </label><br>
                                    <label>
                                        <input type="checkbox" 
                                               name="show_file_sizes" 
                                               value="1"
                                               <?php checked($settings['show_file_sizes'], true); ?>>
                                        <?php _e('File sizes', 'gbbs-software-archive'); ?>
                                    </label><br>
                                    <label>
                                        <input type="checkbox" 
                                               name="show_upload_dates" 
                                               value="1"
                                               <?php checked($settings['show_upload_dates'], true); ?>>
                                        <?php _e('Upload dates', 'gbbs-software-archive'); ?>
                                    </label><br>
                                    <label>
                                        <input type="checkbox" 
                                               name="show_archive_stats" 
                                               value="1"
                                               <?php checked($settings['show_archive_stats'], true); ?>>
                                        <?php _e('Archive statistics in header', 'gbbs-software-archive'); ?>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Interactive Features', 'gbbs-software-archive'); ?></th>
                            <td>
                                <fieldset>
                                    <label>
                                        <input type="checkbox" 
                                               name="enable_search" 
                                               value="1"
                                               <?php checked($settings['enable_search'], true); ?>>
                                        <?php _e('Enable search functionality', 'gbbs-software-archive'); ?>
                                    </label><br>
                                    <label>
                                        <input type="checkbox" 
                                               name="enable_volume_filter" 
                                               value="1"
                                               <?php checked($settings['enable_volume_filter'], true); ?>>
                                        <?php _e('Enable volume filtering', 'gbbs-software-archive'); ?>
                                    </label><br>
                                    <label>
                                        <input type="checkbox" 
                                               name="enable_sorting" 
                                               value="1"
                                               <?php checked($settings['enable_sorting'], true); ?>>
                                        <?php _e('Enable column sorting', 'gbbs-software-archive'); ?>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                        
                        
                        <tr>
                            <th scope="row">
                                <label for="default_sorting"><?php _e('Default Sorting', 'gbbs-software-archive'); ?></label>
                            </th>
                            <td>
                                <select id="default_sorting" name="default_sorting">
                                    <option value="name" <?php selected($settings['default_sorting'], 'name'); ?>><?php _e('Name (A-Z)', 'gbbs-software-archive'); ?></option>
                                    <option value="date" <?php selected($settings['default_sorting'], 'date'); ?>><?php _e('Upload Date (Newest First)', 'gbbs-software-archive'); ?></option>
                                    <option value="downloads" <?php selected($settings['default_sorting'], 'downloads'); ?>><?php _e('Download Count (Most Popular)', 'gbbs-software-archive'); ?></option>
                                    <option value="size" <?php selected($settings['default_sorting'], 'size'); ?>><?php _e('File Size (Largest First)', 'gbbs-software-archive'); ?></option>
                                </select>
                                <p class="description">
                                    <?php _e('Default sort order for the archive directory listing.', 'gbbs-software-archive'); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="items_per_page"><?php _e('Items Per Page', 'gbbs-software-archive'); ?></label>
                            </th>
                            <td>
                                <input type="number" 
                                       id="items_per_page" 
                                       name="items_per_page" 
                                       value="<?php echo esc_attr($settings['items_per_page']); ?>" 
                                       min="1" 
                                       max="100" 
                                       class="small-text">
                                <p class="description">
                                    <?php _e('Number of archives to display per page in directory listings.', 'gbbs-software-archive'); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Upload Settings Tab -->
                <div class="gbbs-tab-content" id="upload-settings">
                    <h2><?php _e('Upload Settings', 'gbbs-software-archive'); ?></h2>
                    <p class="description"><?php _e('Configure file upload behavior and organization.', 'gbbs-software-archive'); ?></p>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="upload_folder_structure"><?php _e('Upload Folder Structure', 'gbbs-software-archive'); ?></label>
                            </th>
                            <td>
                                <fieldset>
                                    <label>
                                        <input type="radio" 
                                               name="upload_folder_structure" 
                                               value="wordpress_default" 
                                               <?php checked($settings['upload_folder_structure'], 'wordpress_default'); ?>>
                                        <?php _e('WordPress Default', 'gbbs-software-archive'); ?>
                                        <span class="description"><?php _e('Use WordPress default uploads folder', 'gbbs-software-archive'); ?></span>
                                    </label><br>
                                    <label>
                                        <input type="radio" 
                                               name="upload_folder_structure" 
                                               value="gbbs_dedicated" 
                                               <?php checked($settings['upload_folder_structure'], 'gbbs_dedicated'); ?>>
                                        <?php _e('GBBS Dedicated Folder', 'gbbs-software-archive'); ?>
                                        <span class="description"><?php _e('Use dedicated /gbbs-archive/ folder', 'gbbs-software-archive'); ?></span>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="file_organization"><?php _e('File Organization', 'gbbs-software-archive'); ?></label>
                            </th>
                            <td>
                                <fieldset>
                                    <label>
                                        <input type="radio" 
                                               name="file_organization" 
                                               value="by_archive" 
                                               <?php checked($settings['file_organization'], 'by_archive'); ?>>
                                        <?php _e('By Archive', 'gbbs-software-archive'); ?>
                                        <span class="description"><?php _e('Organize files by archive ID', 'gbbs-software-archive'); ?></span>
                                    </label><br>
                                    <label>
                                        <input type="radio" 
                                               name="file_organization" 
                                               value="by_volume" 
                                               <?php checked($settings['file_organization'], 'by_volume'); ?>>
                                        <?php _e('By Volume', 'gbbs-software-archive'); ?></label>
                                        <span class="description"><?php _e('Organize files by volume category', 'gbbs-software-archive'); ?></span>
                                    </label><br>
                                    <label>
                                        <input type="radio" 
                                               name="file_organization" 
                                               value="flat" 
                                               <?php checked($settings['file_organization'], 'flat'); ?>>
                                        <?php _e('Flat Structure', 'gbbs-software-archive'); ?>
                                        <span class="description"><?php _e('All files in single folder', 'gbbs-software-archive'); ?></span>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="max_file_size"><?php _e('Maximum File Size', 'gbbs-software-archive'); ?></label>
                            </th>
                            <td>
                                <input type="number" 
                                       id="max_file_size" 
                                       name="max_file_size" 
                                       value="<?php echo esc_attr($settings['max_file_size']); ?>" 
                                       min="1" 
                                       max="1000" 
                                       class="small-text">
                                <span class="description"><?php _e('MB (1-1000)', 'gbbs-software-archive'); ?></span>
                                <p class="description">
                                    <?php _e('Maximum size for individual file uploads. Note: This may be limited by your server settings.', 'gbbs-software-archive'); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('File Type Restrictions', 'gbbs-software-archive'); ?></th>
                            <td>
                                <fieldset>
                                    <label>
                                        <input type="checkbox" 
                                               name="restrict_file_types" 
                                               value="1"
                                               <?php checked($settings['restrict_file_types'], true); ?>>
                                        <?php _e('Restrict file types to allowed list below', 'gbbs-software-archive'); ?>
                                    </label>
                                </fieldset>
                                <p class="description">
                                    <?php _e('If unchecked, any file type that WordPress allows will be accepted for archives.', 'gbbs-software-archive'); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="allowed_file_types"><?php _e('Allowed File Types', 'gbbs-software-archive'); ?></label>
                            </th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text"><?php _e('Select allowed file types for Apple II software', 'gbbs-software-archive'); ?></legend>
                                    
                                    <div class="gbbs-file-types-grid">
                                        <?php
                                        // Get file types grouped by category
                                        $file_types_by_category = $gbbs_settings->get_file_types_by_category();
                                        $category_names = array(
                                            'disk_images' => __('Apple II Disk Images', 'gbbs-software-archive'),
                                            'programs' => __('Apple II File Formats', 'gbbs-software-archive'),
                                            'archives' => __('Archive Formats', 'gbbs-software-archive'),
                                            'documentation' => __('Documentation', 'gbbs-software-archive')
                                        );
                                        
                                        foreach ($file_types_by_category as $category => $file_types): ?>
                                            <div class="gbbs-file-type-group">
                                                <h4><?php echo esc_html($category_names[$category]); ?></h4>
                                                <?php foreach ($file_types as $extension => $info): ?>
                                                    <label class="gbbs-file-type-label" title="<?php echo esc_attr($info['description']); ?>">
                                                        <input type="checkbox" 
                                                               name="allowed_file_types[]" 
                                                               value="<?php echo esc_attr($extension); ?>"
                                                               <?php checked(in_array($extension, $settings['allowed_file_types'])); ?>>
                                                        <span class="gbbs-file-type-name"><?php echo strtoupper($extension); ?></span>
                                                        <span class="gbbs-file-type-description"><?php echo esc_html($info['name']); ?></span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <div class="gbbs-file-type-actions">
                                        <button type="button" class="button button-secondary" id="select-all-file-types">
                                            <?php _e('Select All', 'gbbs-software-archive'); ?>
                                        </button>
                                        <button type="button" class="button button-secondary" id="deselect-all-file-types">
                                            <?php _e('Deselect All', 'gbbs-software-archive'); ?>
                                        </button>
                                        <button type="button" class="button button-secondary" id="select-disk-images">
                                            <?php _e('Select Disk Images', 'gbbs-software-archive'); ?>
                                        </button>
                                        <button type="button" class="button button-secondary" id="select-programs">
                                            <?php _e('Select Programs', 'gbbs-software-archive'); ?>
                                        </button>
                                        <button type="button" class="button button-secondary" id="select-archives">
                                            <?php _e('Select Archives', 'gbbs-software-archive'); ?>
                                        </button>
                                        <button type="button" class="button button-secondary" id="select-documentation">
                                            <?php _e('Select Documentation', 'gbbs-software-archive'); ?>
                                        </button>
                                    </div>
                                    
                                    <p class="description">
                                        <?php _e('Select which file types are allowed for upload. Uncheck types you want to disable.', 'gbbs-software-archive'); ?>
                                    </p>
                                </fieldset>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Download Settings Tab -->
                <div class="gbbs-tab-content" id="download-settings">
                    <h2><?php _e('Download Settings', 'gbbs-software-archive'); ?></h2>
                    <p class="description"><?php _e('Configure download behavior and security.', 'gbbs-software-archive'); ?></p>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Download Requirements', 'gbbs-software-archive'); ?></th>
                            <td>
                                <fieldset>
                                    <label>
                                        <input type="checkbox" 
                                               name="require_login" 
                                               value="1"
                                               <?php checked($settings['require_login'], true); ?>>
                                        <?php _e('Require user login for downloads', 'gbbs-software-archive'); ?>
                                    </label><br>
                                    <label>
                                        <input type="checkbox" 
                                               name="track_downloads" 
                                               value="1"
                                               <?php checked($settings['track_downloads'], true); ?>>
                                        <?php _e('Track download statistics', 'gbbs-software-archive'); ?>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="download_timeout"><?php _e('Download Timeout', 'gbbs-software-archive'); ?></label>
                            </th>
                            <td>
                                <input type="number" 
                                       id="download_timeout" 
                                       name="download_timeout" 
                                       value="<?php echo esc_attr($settings['download_timeout']); ?>" 
                                       min="30" 
                                       max="3600" 
                                       class="small-text">
                                <span class="description"><?php _e('seconds (30-3600)', 'gbbs-software-archive'); ?></span>
                                <p class="description">
                                    <?php _e('Maximum time allowed for file downloads. Increase for large files.', 'gbbs-software-archive'); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Download Logging', 'gbbs-software-archive'); ?></th>
                            <td>
                                <fieldset>
                                    <label>
                                        <input type="checkbox" 
                                               name="download_logging" 
                                               value="1"
                                               <?php checked($settings['download_logging'], true); ?>>
                                        <?php _e('Enable download logging', 'gbbs-software-archive'); ?>
                                    </label><br>
                                    <label>
                                        <input type="checkbox" 
                                               name="download_counter" 
                                               value="1"
                                               <?php checked($settings['download_counter'], true); ?>>
                                        <?php _e('Enable download counter display', 'gbbs-software-archive'); ?>
                                    </label>
                                </fieldset>
                                <p class="description">
                                    <?php _e('Control whether downloads are logged and counted for statistics.', 'gbbs-software-archive'); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><?php _e('Rate Limiting', 'gbbs-software-archive'); ?></th>
                            <td>
                                <fieldset>
                                    <label>
                                        <input type="checkbox" 
                                               name="rate_limiting" 
                                               value="1"
                                               <?php checked($settings['rate_limiting'], true); ?>>
                                        <?php _e('Enable rate limiting', 'gbbs-software-archive'); ?>
                                    </label>
                                </fieldset>
                                
                                <div class="gbbs-rate-limit-settings">
                                    <div class="gbbs-rate-limit-row">
                                        <label for="rate_limit_requests"><?php _e('Requests per window:', 'gbbs-software-archive'); ?></label>
                                        <input type="number" 
                                               id="rate_limit_requests" 
                                               name="rate_limit_requests" 
                                               value="<?php echo esc_attr($settings['rate_limit_requests']); ?>" 
                                               min="1" 
                                               max="100" 
                                               class="small-text">
                                    </div>
                                    
                                    <div class="gbbs-rate-limit-row">
                                        <label for="rate_limit_window"><?php _e('Window duration (seconds):', 'gbbs-software-archive'); ?></label>
                                        <input type="number" 
                                               id="rate_limit_window" 
                                               name="rate_limit_window" 
                                               value="<?php echo esc_attr($settings['rate_limit_window']); ?>" 
                                               min="10" 
                                               max="3600" 
                                               class="small-text">
                                    </div>
                                </div>
                                
                                <p class="description">
                                    <?php _e('Prevent download abuse by limiting requests per IP address.', 'gbbs-software-archive'); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                
                
                <!-- Action Buttons -->
                <div class="gbbs-settings-actions">
                    <?php submit_button(__('Save Settings', 'gbbs-software-archive'), 'primary', 'gbbs_save_settings', false); ?>
                    <button type="button" 
                            class="button" 
                            id="gbbs-reset-settings"
                            onclick="if(confirm('<?php _e('Are you sure you want to reset all settings to defaults?', 'gbbs-software-archive'); ?>')) { document.getElementById('gbbs-reset-form').submit(); }">
                        <?php _e('Reset to Defaults', 'gbbs-software-archive'); ?>
                    </button>
                </div>
            </form>
            
            <!-- Reset Form (Hidden) -->
            <form method="post" action="" id="gbbs-reset-form" style="display: none;">
                <?php wp_nonce_field('gbbs_reset_settings', 'gbbs_reset_nonce'); ?>
                <input type="hidden" name="gbbs_reset_settings" value="1">
            </form>
            
        </div>
        
        <!-- Sidebar -->
        <div class="gbbs-settings-sidebar">
            <div class="gbbs-settings-widget">
                <h3><?php _e('Quick Links', 'gbbs-software-archive'); ?></h3>
                <ul>
                    <li><a href="<?php echo admin_url('edit.php?post_type=gbbs_archive'); ?>"><?php _e('Manage Archives', 'gbbs-software-archive'); ?></a></li>
                    <li><a href="<?php echo admin_url('edit-tags.php?taxonomy=gbbs_volume&post_type=gbbs_archive'); ?>"><?php _e('Manage Volumes', 'gbbs-software-archive'); ?></a></li>
                    <li><a href="<?php echo admin_url('edit.php?post_type=gbbs_archive&page=gbbs-download-stats'); ?>"><?php _e('Download Statistics', 'gbbs-software-archive'); ?></a></li>
                </ul>
            </div>
            
                <div class="gbbs-settings-widget gbbs-upload-directory-widget">
                    <h3><?php _e('Upload Directory', 'gbbs-software-archive'); ?></h3>
                    <p><?php _e('The plugin automatically manages the upload directory structure. Directories are created when needed and cleaned up automatically.', 'gbbs-software-archive'); ?></p>
                    
                    <?php
                    // Show current directory structure
                    $upload_dir = wp_upload_dir();
                    $gbbs_dir = $upload_dir['basedir'] . '/gbbs-archive';
                    $organization = $settings['file_organization'];
                    
                    echo '<div style="background: #f9f9f9; padding: 10px; margin: 10px 0; border-left: 4px solid #0073aa; word-wrap: break-word;">';
                    echo '<strong>' . __('Current Structure:', 'gbbs-software-archive') . '</strong><br>';
                    echo '<div class="gbbs-directory-path" data-path="' . esc_attr($gbbs_dir) . '">' . esc_html($gbbs_dir) . '</div>';
                    echo '<button type="button" class="gbbs-copy-button" onclick="gbbsCopyDirectoryPath(this)">' . __('Copy', 'gbbs-software-archive') . '</button>';
                    echo '<div class="gbbs-copy-feedback">' . __('Copied!', 'gbbs-software-archive') . '</div>';
                    
                    if ($organization === 'by_archive') {
                        echo '<small>' . __('Files are organized by archive ID directly in the gbbs-archive folder.', 'gbbs-software-archive') . '</small>';
                    } elseif ($organization === 'by_volume') {
                        echo '<small>' . __('Files are organized by volume in the volumes subfolder.', 'gbbs-software-archive') . '</small>';
                    } else {
                        echo '<small>' . __('Files are stored in a flat structure in the files subfolder.', 'gbbs-software-archive') . '</small>';
                    }
                    echo '</div>';
                    ?>
                </div>
            
            <div class="gbbs-settings-widget">
                <h3><?php _e('Shortcode Usage', 'gbbs-software-archive'); ?></h3>
                <p><?php _e('Use these shortcodes to display archives on any page or post:', 'gbbs-software-archive'); ?></p>
                
                <h4><?php _e('Directory Listing', 'gbbs-software-archive'); ?></h4>
                <code>[gbbs_directory]</code>
                <p class="description"><?php _e('Display the full archive directory', 'gbbs-software-archive'); ?></p>
                
                <code>[gbbs_directory limit="10"]</code>
                <p class="description"><?php _e('Display with custom limit', 'gbbs-software-archive'); ?></p>
                
                <code>[gbbs_directory volume="games"]</code>
                <p class="description"><?php _e('Show only archives from specific volume', 'gbbs-software-archive'); ?></p>
                
                <h4><?php _e('Individual Archive', 'gbbs-software-archive'); ?></h4>
                <code>[gbbs_archive id="123"]</code>
                <p class="description"><?php _e('Display specific archive by ID', 'gbbs-software-archive'); ?></p>
                
                <code>[gbbs_archive id="123" button_text="View Archive"]</code>
                <p class="description"><?php _e('Custom button text for archive link', 'gbbs-software-archive'); ?></p>
                
                <code>[gbbs_archive id="123" show_files="true"]</code>
                <p class="description"><?php _e('Show file list in archive display', 'gbbs-software-archive'); ?></p>
            </div>
            
            <div class="gbbs-settings-widget">
                <h3><?php _e('System Information', 'gbbs-software-archive'); ?></h3>
                <p><strong><?php _e('Plugin Version:', 'gbbs-software-archive'); ?></strong> <?php echo GBBS_SOFTWARE_ARCHIVE_VERSION; ?></p>
                <p><strong><?php _e('WordPress Version:', 'gbbs-software-archive'); ?></strong> <?php echo get_bloginfo('version'); ?></p>
                <p><strong><?php _e('PHP Version:', 'gbbs-software-archive'); ?></strong> <?php echo PHP_VERSION; ?></p>
            </div>
        </div>
    </div>
</div>

<style>
.gbbs-settings-page {
    max-width: 1200px;
}

.gbbs-settings-container {
    display: flex;
    gap: 20px;
    margin-top: 20px;
}

.gbbs-settings-main {
    flex: 1;
    background: #fff;
    padding: 20px;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
}

.gbbs-settings-sidebar {
    width: 300px;
}

.gbbs-settings-widget {
    background: #fff;
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
}

.gbbs-settings-widget h3 {
    margin-top: 0;
    margin-bottom: 10px;
}

.gbbs-settings-widget ul {
    margin: 0;
    padding-left: 20px;
}

.gbbs-settings-widget code {
    background: #f1f1f1;
    padding: 8px 12px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
    display: block;
    margin: 8px 0;
    border: 1px solid #ddd;
    font-size: 13px;
    word-wrap: break-word;
    word-break: break-all;
    white-space: pre-wrap;
    overflow-wrap: break-word;
    max-width: 100%;
}

.gbbs-settings-widget h4 {
    margin: 15px 0 8px 0;
    color: #333;
    font-size: 14px;
    font-weight: 600;
}

/* Upload Directory Widget Specific Styles */
.gbbs-upload-directory-widget {
    position: relative;
}

.gbbs-upload-directory-widget .gbbs-directory-path {
    position: relative;
    background: #f1f1f1;
    padding: 8px 35px 8px 12px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    word-wrap: break-word;
    word-break: break-all;
    white-space: pre-wrap;
    overflow-wrap: break-word;
    max-width: 100%;
    border: 1px solid #ddd;
    margin: 8px 0;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.gbbs-upload-directory-widget .gbbs-directory-path:hover {
    background: #e8e8e8;
}

.gbbs-upload-directory-widget .gbbs-copy-button {
    position: absolute;
    top: 8px;
    right: 8px;
    background: #0073aa;
    color: white;
    border: none;
    border-radius: 3px;
    padding: 4px 8px;
    font-size: 11px;
    cursor: pointer;
    opacity: 0.8;
    transition: opacity 0.2s ease;
}

.gbbs-upload-directory-widget .gbbs-copy-button:hover {
    opacity: 1;
}

.gbbs-upload-directory-widget .gbbs-copy-button:active {
    background: #005a87;
}

.gbbs-upload-directory-widget .gbbs-copy-feedback {
    position: absolute;
    top: -25px;
    right: 0;
    background: #333;
    color: white;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 11px;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.gbbs-upload-directory-widget .gbbs-copy-feedback.show {
    opacity: 1;
}

/* Responsive adjustments for upload directory widget */
@media screen and (max-width: 782px) {
    .gbbs-upload-directory-widget .gbbs-directory-path {
        font-size: 11px;
        padding: 6px 30px 6px 10px;
    }
    
    .gbbs-upload-directory-widget .gbbs-copy-button {
        padding: 3px 6px;
        font-size: 10px;
    }
}

/* Tab Navigation */
.gbbs-tab-nav {
    display: flex;
    border-bottom: 1px solid #ccd0d4;
    margin-bottom: 20px;
    background: #f9f9f9;
    border-radius: 4px 4px 0 0;
}

.gbbs-tab-button {
    background: none;
    border: none;
    padding: 12px 20px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: #666;
    border-bottom: 3px solid transparent;
    transition: all 0.2s ease;
}

.gbbs-tab-button:hover {
    background: #fff;
    color: #333;
}

.gbbs-tab-button.active {
    background: #fff;
    color: #0073aa;
    border-bottom-color: #0073aa;
}

/* Tab Content */
.gbbs-tab-content {
    display: none;
    padding: 20px 0;
}

.gbbs-tab-content.active {
    display: block;
}

.gbbs-settings-section {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.gbbs-settings-section:last-child {
    border-bottom: none;
}

.gbbs-settings-actions {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.gbbs-settings-actions .button {
    margin-right: 10px;
}

@media (max-width: 768px) {
    .gbbs-settings-container {
        flex-direction: column;
    }
    
    .gbbs-settings-sidebar {
        width: 100%;
    }
}

/* File Types Grid Styling */
.gbbs-file-types-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 15px 0;
}

.gbbs-file-type-group {
    background: #f9f9f9;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.gbbs-file-type-group h4 {
    margin: 0 0 10px 0;
    color: #23282d;
    font-size: 14px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 5px;
}

.gbbs-file-type-label {
    display: block;
    margin: 5px 0;
    cursor: pointer;
}

.gbbs-file-type-label input[type="checkbox"] {
    margin-right: 8px;
}

.gbbs-file-type-name {
    font-family: monospace;
    font-weight: bold;
    color: #0073aa;
    margin-right: 8px;
}

.gbbs-file-type-description {
    color: #666;
    font-size: 12px;
    font-style: italic;
}

.gbbs-file-type-actions {
    margin: 15px 0;
    padding: 10px;
    background: #f0f0f1;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
}

.gbbs-file-type-actions .button {
    margin-right: 8px;
    margin-bottom: 5px;
}

@media (max-width: 600px) {
    .gbbs-file-types-grid {
        grid-template-columns: 1fr;
    }
}

/* Rate Limiting Settings */
.gbbs-rate-limit-settings {
    background: #f9f9f9;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-top: 10px;
}

.gbbs-rate-limit-settings label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.gbbs-rate-limit-settings input[type="number"] {
    margin-right: 20px;
    width: 80px;
    margin-bottom: 10px;
}

.gbbs-rate-limit-settings .gbbs-rate-limit-row {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.gbbs-rate-limit-settings .gbbs-rate-limit-row:last-child {
    margin-bottom: 0;
}

.gbbs-rate-limit-settings .gbbs-rate-limit-row label {
    display: inline-block;
    margin-right: 10px;
    margin-bottom: 0;
    min-width: 140px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Tab functionality
    $('.gbbs-tab-button').on('click', function() {
        var targetTab = $(this).data('tab');
        
        // Store active tab in localStorage
        localStorage.setItem('gbbs-active-tab', targetTab);
        
        // Remove active class from all buttons and content
        $('.gbbs-tab-button').removeClass('active');
        $('.gbbs-tab-content').removeClass('active');
        
        // Add active class to clicked button and corresponding content
        $(this).addClass('active');
        $('#' + targetTab).addClass('active');
    });
    
    // Restore active tab from localStorage on page load
    var activeTab = localStorage.getItem('gbbs-active-tab');
    if (activeTab && $('#' + activeTab).length) {
        // Remove active class from all buttons and content
        $('.gbbs-tab-button').removeClass('active');
        $('.gbbs-tab-content').removeClass('active');
        
        // Add active class to stored tab
        $('[data-tab="' + activeTab + '"]').addClass('active');
        $('#' + activeTab).addClass('active');
    }
    
    // Handle reset form submission
    $('#gbbs-reset-form').on('submit', function(e) {
        e.preventDefault();
        
        if (confirm('<?php _e('Are you sure you want to reset all settings to defaults? This action cannot be undone.', 'gbbs-software-archive'); ?>')) {
            // Submit the form
            this.submit();
        }
    });
    
    // Form validation
    $('#gbbs-settings-form').on('submit', function(e) {
        // Store current active tab before form submission
        var currentActiveTab = $('.gbbs-tab-button.active').data('tab');
        if (currentActiveTab) {
            localStorage.setItem('gbbs-active-tab', currentActiveTab);
        }
        
        var itemsPerPage = parseInt($('#items_per_page').val());
        var downloadTimeout = parseInt($('#download_timeout').val());
        
        if (itemsPerPage < 1 || itemsPerPage > 100) {
            alert('<?php _e('Items per page must be between 1 and 100.', 'gbbs-software-archive'); ?>');
            e.preventDefault();
            return false;
        }
        
        if (downloadTimeout < 30 || downloadTimeout > 3600) {
            alert('<?php _e('Download timeout must be between 30 and 3600 seconds.', 'gbbs-software-archive'); ?>');
            e.preventDefault();
            return false;
        }
        
        // Validate max file size
        var maxFileSize = parseInt($('#max_file_size').val());
        if (maxFileSize < 1 || maxFileSize > 1000) {
            alert('<?php _e('Maximum file size must be between 1 and 1000 MB.', 'gbbs-software-archive'); ?>');
            e.preventDefault();
            return false;
        }
        
        // Validate that at least one file type is selected
        var selectedFileTypes = $('input[name="allowed_file_types[]"]:checked').length;
        if (selectedFileTypes === 0) {
            alert('<?php _e('Please select at least one allowed file type.', 'gbbs-software-archive'); ?>');
            e.preventDefault();
            return false;
        }
        
        // Validate rate limiting settings
        var rateLimitRequests = parseInt($('#rate_limit_requests').val());
        var rateLimitWindow = parseInt($('#rate_limit_window').val());
        
        if (rateLimitRequests < 1 || rateLimitRequests > 100) {
            alert('<?php _e('Rate limit requests must be between 1 and 100.', 'gbbs-software-archive'); ?>');
            e.preventDefault();
            return false;
        }
        
        if (rateLimitWindow < 10 || rateLimitWindow > 3600) {
            alert('<?php _e('Rate limit window must be between 10 and 3600 seconds.', 'gbbs-software-archive'); ?>');
            e.preventDefault();
            return false;
        }
        
        // Validate URL slugs
        var postTypeEndpoint = $('#post_type_endpoint').val().trim();
        var volumeEndpoint = $('#volume_endpoint').val().trim();
        
        if (postTypeEndpoint === '') {
            alert('<?php _e('Archive URL slug cannot be empty.', 'gbbs-software-archive'); ?>');
            e.preventDefault();
            return false;
        }
        
        if (volumeEndpoint === '') {
            alert('<?php _e('Volume URL slug cannot be empty.', 'gbbs-software-archive'); ?>');
            e.preventDefault();
            return false;
        }
    });
    
    // File type management buttons
    $('#select-all-file-types').on('click', function() {
        $('input[name="allowed_file_types[]"]').prop('checked', true);
    });
    
    $('#deselect-all-file-types').on('click', function() {
        $('input[name="allowed_file_types[]"]').prop('checked', false);
    });
    
    $('#select-disk-images').on('click', function() {
        $('.gbbs-file-type-group').each(function() {
            if ($(this).find('h4').text().includes('Disk Images')) {
                $(this).find('input[name="allowed_file_types[]"]').prop('checked', true);
            }
        });
    });
    
    $('#select-programs').on('click', function() {
        $('.gbbs-file-type-group').each(function() {
            if ($(this).find('h4').text().includes('File Formats')) {
                $(this).find('input[name="allowed_file_types[]"]').prop('checked', true);
            }
        });
    });
    
    $('#select-archives').on('click', function() {
        $('.gbbs-file-type-group').each(function() {
            if ($(this).find('h4').text().includes('Archive')) {
                $(this).find('input[name="allowed_file_types[]"]').prop('checked', true);
            }
        });
    });
    
    $('#select-documentation').on('click', function() {
        $('.gbbs-file-type-group').each(function() {
            if ($(this).find('h4').text().includes('Documentation')) {
                $(this).find('input[name="allowed_file_types[]"]').prop('checked', true);
            }
        });
    });
});

// Copy directory path functionality
function gbbsCopyDirectoryPath(button) {
    var pathElement = button.previousElementSibling;
    var path = pathElement.getAttribute('data-path');
    var feedback = button.nextElementSibling;
    
    // Create a temporary textarea element
    var textarea = document.createElement('textarea');
    textarea.value = path;
    document.body.appendChild(textarea);
    textarea.select();
    
    try {
        // Copy the text
        document.execCommand('copy');
        
        // Show feedback
        feedback.classList.add('show');
        setTimeout(function() {
            feedback.classList.remove('show');
        }, 2000);
        
    } catch (err) {
        console.error('Failed to copy text: ', err);
        // Fallback: show the path in an alert
        alert('<?php _e('Path:', 'gbbs-software-archive'); ?> ' + path);
    }
    
    // Remove the temporary element
    document.body.removeChild(textarea);
}
</script>
