<?php
/**
 * Template for displaying a single GBBS Archive
 * 
 * This template is used when viewing a single GBBS archive post.
 * It displays all archive information and file attachments in a BBS-style format.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Add body class for styling
add_filter('body_class', function($classes) {
    $classes[] = 'gbbs-archive';
    return $classes;
});

// Get archive metadata
$archive_version = get_post_meta(get_the_ID(), 'gbbs_archive_version', true);
$archive_author = get_post_meta(get_the_ID(), 'gbbs_archive_author', true);
$archive_release_year = get_post_meta(get_the_ID(), 'gbbs_archive_release_year', true);
$archive_requirements = get_post_meta(get_the_ID(), 'gbbs_archive_requirements', true);
$archive_installation_notes = get_post_meta(get_the_ID(), 'gbbs_archive_installation_notes', true);
$archive_historical_notes = get_post_meta(get_the_ID(), 'gbbs_archive_historical_notes', true);
$archive_files = get_post_meta(get_the_ID(), '_gbbs_archive_files', true);

// Get volume information
$volumes = get_the_terms(get_the_ID(), 'gbbs_volume');
$volume_name = !empty($volumes) ? $volumes[0]->name : 'Uncategorized';

// Ensure archive_files is an array
if (!is_array($archive_files)) {
    $archive_files = array();
}
?>

<div class="gbbs-archive-single">
    <div class="gbbs-supertac-header">
        <div class="gbbs-supertac-title">
            ..................: The Aerodrome - GBBS Software Archive:
        </div>
        
        <div class="gbbs-supertac-params">
            <div class="gbbs-supertac-params-title">
                GBBS Archive Information:
            </div>
            <div class="gbbs-supertac-params-grid">
                <div class="gbbs-supertac-param">Archive: <?php echo esc_html(get_the_title()); ?></div>
                <div class="gbbs-supertac-param">Version: <?php echo esc_html($archive_version ?: 'N/A'); ?></div>
                <div class="gbbs-supertac-param">Author: <?php echo esc_html($archive_author ?: 'Unknown'); ?></div>
                <div class="gbbs-supertac-param">Year: <?php echo esc_html($archive_release_year ?: 'N/A'); ?></div>
                <div class="gbbs-supertac-param">Volume: <?php echo esc_html($volume_name); ?></div>
                <div class="gbbs-supertac-param">Files: <?php echo count($archive_files); ?></div>
            </div>
        </div>
        
        <div class="gbbs-supertac-access">
            Access to Archive: Complete Download
        </div>
        
        <div class="gbbs-supertac-prompt">
            [::][<span class="gbbs-inverse">GBBS</span>] Archive View Mode:
        </div>
    </div>
    
    <div class="gbbs-directory-listing">
        <div class="gbbs-directory-header">
            <div class="gbbs-directory-title">Archive Files Directory</div>
            <div class="gbbs-directory-subtitle">Complete File Listing</div>
        </div>
        
        <?php if (get_the_content()): ?>
            <div class="gbbs-modal-section">
                <h3>Description</h3>
                <p><?php the_content(); ?></p>
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
            <table class="gbbs-file-table">
                <thead>
                    <tr>
                        <th>###</th>
                        <th>Filename</th>
                        <th>Typ</th>
                        <th>Length</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($archive_files as $index => $file): ?>
                        <?php
                        $file_size = 0;
                        if (!empty($file['url'])) {
                            // Try to get file size
                            $upload_dir = wp_upload_dir();
                            $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $file['url']);
                            if (file_exists($file_path)) {
                                $file_size = filesize($file_path);
                            }
                        }
                        
                        // Get Apple II file type
                        $filename = $file['name'] ?: basename($file['url']);
                        $gbbs_instance = new GBBS_Software_Archive();
                        $file_type_info = $gbbs_instance->get_apple_ii_file_type($filename);
                        $file_type = $file_type_info['type'];
                        $file_type_css = $gbbs_instance->get_file_type_css_class($file_type);
                        ?>
                        <tr>
                            <td><?php echo str_pad($index + 1, 3, '0', STR_PAD_LEFT); ?></td>
                            <td class="gbbs-filename"><?php echo esc_html($filename); ?></td>
                            <td class="gbbs-file-type <?php echo esc_attr($file_type_css); ?>" title="<?php echo esc_attr($file_type_info['description']); ?>"><?php echo esc_html($file_type); ?></td>
                            <td class="gbbs-file-length"><?php echo number_format($file_size); ?></td>
                            <td class="gbbs-file-packer"><?php echo esc_html(ucfirst($file['category'] ?: 'Other')); ?></td>
                            <td class="gbbs-file-actions">
                                <a href="<?php echo esc_url($file['url']); ?>" 
                                   class="gbbs-action-link" 
                                   download>
                                    D
                                </a>
                            </td>
                        </tr>
                        <?php if ($file['description']): ?>
                            <tr>
                                <td colspan="6" style="font-size: 10px; color: #cccccc; padding-left: 20px; font-style: italic;">
                                    <?php echo esc_html($file['description']); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; color: #cccccc; padding: 20px;">
                No files available for this archive.
            </div>
        <?php endif; ?>
    </div>
</div>
