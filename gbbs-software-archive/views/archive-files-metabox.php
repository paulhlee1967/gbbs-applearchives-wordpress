<?php defined( 'ABSPATH' ) or die(); ?>

<div class="gbbs-archive-files">
    <?php
    // Show general file type information at the top
    $gbbs_settings = new GBBS_Settings();
    $restrict_file_types = $gbbs_settings->get_setting('restrict_file_types', true);
    if ($restrict_file_types) {
        $allowed_types = $gbbs_settings->get_allowed_file_types();
        if (!empty($allowed_types)) {
            echo '<div class="gbbs-file-types-notice" style="background: #f0f0f1; padding: 10px; margin-bottom: 15px; border-left: 4px solid #00a0d2;">';
            echo '<strong>' . __('Allowed file types for this archive:', 'gbbs-software-archive') . '</strong> ';
            echo esc_html(implode(', ', array_map('strtoupper', $allowed_types)));
            echo '</div>';
        }
    } else {
        echo '<div class="gbbs-file-types-notice" style="background: #f0f0f1; padding: 10px; margin-bottom: 15px; border-left: 4px solid #00a0d2;">';
        echo '<strong>' . __('File types:', 'gbbs-software-archive') . '</strong> ' . __('Any file type that WordPress allows', 'gbbs-software-archive');
        echo '</div>';
    }
    ?>
    <div class="gbbs-files-list">
        <?php
        $archive_files = get_post_meta( $post->ID, '_gbbs_archive_files', true );
        if ( ! is_array( $archive_files ) ) {
            $archive_files = array();
        }
        
        if ( ! empty( $archive_files ) ) :
            foreach ( $archive_files as $index => $file ) :
                $file_id = isset( $file['id'] ) ? $file['id'] : '';
                $file_url = isset( $file['url'] ) ? $file['url'] : '';
                $file_name = isset( $file['name'] ) ? $file['name'] : '';
                $file_description = isset( $file['description'] ) ? $file['description'] : '';
                $file_category = isset( $file['category'] ) ? $file['category'] : '';
                ?>
                <div class="gbbs-file-item" data-index="<?php echo esc_attr( $index ); ?>">
                    <div class="gbbs-file-header">
                        <h4><?php _e( 'File', 'gbbs-software-archive' ); ?> #<?php echo esc_html( $index + 1 ); ?></h4>
                        <button type="button" class="button gbbs-remove-file"><?php _e( 'Remove', 'gbbs-software-archive' ); ?></button>
                    </div>
                    
                    <table class="form-table">
                        <tr>
                            <th>
                                <label><?php _e( 'File:', 'gbbs-software-archive' ); ?></label>
                            </th>
                            <td>
                                <input type="text" name="gbbs_archive_files[<?php echo esc_attr( $index ); ?>][url]" 
                                       class="gbbs-file-url regular-text" 
                                       value="<?php echo esc_attr( $file_url ); ?>" readonly/>
                                <input type="hidden" name="gbbs_archive_files[<?php echo esc_attr( $index ); ?>][id]" 
                                       class="gbbs-file-id" 
                                       value="<?php echo esc_attr( $file_id ); ?>"/>
                                <button type="button" class="button gbbs-upload-file-button"
                                        data-dialog-title="<?php esc_attr_e( 'Choose a file', 'gbbs-software-archive' ); ?>"
                                        data-dialog-button="<?php esc_attr_e( 'Insert file URL', 'gbbs-software-archive' ); ?>">
                                    <?php _e( 'Upload File', 'gbbs-software-archive' ); ?>
                                </button>
                            </td>
                        </tr>
                        
                        <tr>
                            <th>
                                <label><?php _e( 'File Name:', 'gbbs-software-archive' ); ?></label>
                            </th>
                            <td>
                                <input type="text" name="gbbs_archive_files[<?php echo esc_attr( $index ); ?>][name]" 
                                       class="gbbs-file-name regular-text" 
                                       value="<?php echo esc_attr( $file_name ); ?>"
                                       placeholder="<?php esc_attr_e( 'Display name for this file', 'gbbs-software-archive' ); ?>"/>
                            </td>
                        </tr>
                        
                        <tr>
                            <th>
                                <label><?php _e( 'Category:', 'gbbs-software-archive' ); ?></label>
                            </th>
                            <td>
                                <select name="gbbs_archive_files[<?php echo esc_attr( $index ); ?>][category]" class="gbbs-file-category">
                                    <option value=""><?php _e( 'Select Category', 'gbbs-software-archive' ); ?></option>
                                    <option value="main" <?php selected( $file_category, 'main' ); ?>><?php _e( 'Main Program', 'gbbs-software-archive' ); ?></option>
                                    <option value="documentation" <?php selected( $file_category, 'documentation' ); ?>><?php _e( 'Documentation', 'gbbs-software-archive' ); ?></option>
                                    <option value="source" <?php selected( $file_category, 'source' ); ?>><?php _e( 'Source Code', 'gbbs-software-archive' ); ?></option>
                                    <option value="config" <?php selected( $file_category, 'config' ); ?>><?php _e( 'Configuration', 'gbbs-software-archive' ); ?></option>
                                    <option value="utility" <?php selected( $file_category, 'utility' ); ?>><?php _e( 'Utility', 'gbbs-software-archive' ); ?></option>
                                    <option value="other" <?php selected( $file_category, 'other' ); ?>><?php _e( 'Other', 'gbbs-software-archive' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <th>
                                <label><?php _e( 'Description:', 'gbbs-software-archive' ); ?></label>
                            </th>
                            <td>
                                <textarea name="gbbs_archive_files[<?php echo esc_attr( $index ); ?>][description]" 
                                          class="gbbs-file-description large-text" 
                                          rows="2" 
                                          placeholder="<?php esc_attr_e( 'Brief description of this file', 'gbbs-software-archive' ); ?>"><?php echo esc_textarea( $file_description ); ?></textarea>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
            endforeach;
        endif;
        ?>
    </div>
    
    <div class="gbbs-add-file-section">
        <button type="button" class="button button-primary gbbs-add-file">
            <?php _e( 'Add File', 'gbbs-software-archive' ); ?>
        </button>
        <p class="description">
            <?php _e( 'Add multiple files to this archive. Each file can be categorized and described.', 'gbbs-software-archive' ); ?>
        </p>
    </div>
</div>

<?php wp_nonce_field( 'gbbs_archive_files_nonce', 'gbbs_archive_files_nonce_field' ); ?>

<script type="text/template" id="gbbs-file-template">
    <div class="gbbs-file-item" data-index="{{index}}">
        <div class="gbbs-file-header">
            <h4><?php _e( 'File', 'gbbs-software-archive' ); ?> #{{number}}</h4>
            <button type="button" class="button gbbs-remove-file"><?php _e( 'Remove', 'gbbs-software-archive' ); ?></button>
        </div>
        
        <table class="form-table">
            <tr>
                <th>
                    <label><?php _e( 'File:', 'gbbs-software-archive' ); ?></label>
                </th>
                <td>
                    <input type="text" name="gbbs_archive_files[{{index}}][url]" 
                           class="gbbs-file-url regular-text" 
                           value="" readonly/>
                    <input type="hidden" name="gbbs_archive_files[{{index}}][id]" 
                           class="gbbs-file-id" 
                           value=""/>
                    <button type="button" class="button gbbs-upload-file-button"
                            data-dialog-title="<?php esc_attr_e( 'Choose a file', 'gbbs-software-archive' ); ?>"
                            data-dialog-button="<?php esc_attr_e( 'Insert file URL', 'gbbs-software-archive' ); ?>">
                        <?php _e( 'Upload File', 'gbbs-software-archive' ); ?>
                    </button>
                </td>
            </tr>
            
            <tr>
                <th>
                    <label><?php _e( 'File Name:', 'gbbs-software-archive' ); ?></label>
                </th>
                <td>
                    <input type="text" name="gbbs_archive_files[{{index}}][name]" 
                           class="gbbs-file-name regular-text" 
                           value=""
                           placeholder="<?php esc_attr_e( 'Display name for this file', 'gbbs-software-archive' ); ?>"/>
                </td>
            </tr>
            
            <tr>
                <th>
                    <label><?php _e( 'Category:', 'gbbs-software-archive' ); ?></label>
                </th>
                <td>
                    <select name="gbbs_archive_files[{{index}}][category]" class="gbbs-file-category">
                        <option value=""><?php _e( 'Select Category', 'gbbs-software-archive' ); ?></option>
                        <option value="main"><?php _e( 'Main Program', 'gbbs-software-archive' ); ?></option>
                        <option value="documentation"><?php _e( 'Documentation', 'gbbs-software-archive' ); ?></option>
                        <option value="source"><?php _e( 'Source Code', 'gbbs-software-archive' ); ?></option>
                        <option value="config"><?php _e( 'Configuration', 'gbbs-software-archive' ); ?></option>
                        <option value="utility"><?php _e( 'Utility', 'gbbs-software-archive' ); ?></option>
                        <option value="other"><?php _e( 'Other', 'gbbs-software-archive' ); ?></option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th>
                    <label><?php _e( 'Description:', 'gbbs-software-archive' ); ?></label>
                </th>
                <td>
                    <textarea name="gbbs_archive_files[{{index}}][description]" 
                              class="gbbs-file-description large-text" 
                              rows="2" 
                              placeholder="<?php esc_attr_e( 'Brief description of this file', 'gbbs-software-archive' ); ?>"></textarea>
                </td>
            </tr>
        </table>
    </div>
</script>
