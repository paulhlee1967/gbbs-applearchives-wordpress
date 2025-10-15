<?php defined( 'ABSPATH' ) or die(); ?>

<table class="form-table gbbs-archive-info">
    <tr>
        <th>
                <label for="gbbs-archive-version">
                <?php _e( 'Version:', 'gbbs-software-archive' ); ?>
            </label>
        </th>
        <td>
            <input type="text" name="gbbs_archive_version" id="gbbs-archive-version" class="regular-text"
                   value="<?php echo esc_attr( get_post_meta( $post->ID, 'gbbs_archive_version', true ) ); ?>"/>
            <p class="description">
                <?php _e( 'Software version (e.g., 2.1, 1.0.3)', 'gbbs-software-archive' ); ?>
            </p>
        </td>
    </tr>
    
    <tr>
        <th>
                <label for="gbbs-archive-author">
                <?php _e( 'Author/Publisher:', 'gbbs-software-archive' ); ?>
            </label>
        </th>
        <td>
            <input type="text" name="gbbs_archive_author" id="gbbs-archive-author" class="regular-text"
                   value="<?php echo esc_attr( get_post_meta( $post->ID, 'gbbs_archive_author', true ) ); ?>"/>
            <p class="description">
                <?php _e( 'Original author or publisher of the software', 'gbbs-software-archive' ); ?>
            </p>
        </td>
    </tr>
    
    <tr>
        <th>
                <label for="gbbs-archive-release-year">
                <?php _e( 'Release Year:', 'gbbs-software-archive' ); ?>
            </label>
        </th>
        <td>
            <input type="number" name="gbbs_archive_release_year" id="gbbs-archive-release-year" class="regular-text"
                   min="1977" max="2025" step="1" placeholder="1985"
                   value="<?php echo esc_attr( get_post_meta( $post->ID, 'gbbs_archive_release_year', true ) ); ?>"/>
            <p class="description">
                <?php _e( 'Year the software was originally released (e.g., 1985)', 'gbbs-software-archive' ); ?>
            </p>
        </td>
    </tr>
    
    <tr>
        <th>
                <label for="gbbs-archive-requirements">
                <?php _e( 'System Requirements:', 'gbbs-software-archive' ); ?>
            </label>
        </th>
        <td>
            <textarea name="gbbs_archive_requirements" id="gbbs-archive-requirements" rows="3" cols="50" class="large-text"><?php echo esc_textarea( get_post_meta( $post->ID, 'gbbs_archive_requirements', true ) ); ?></textarea>
            <p class="description">
                <?php _e( 'System requirements (e.g., Apple IIe, 64K RAM, ProDOS)', 'gbbs-software-archive' ); ?>
            </p>
        </td>
    </tr>
    
    <tr>
        <th>
                <label for="gbbs-archive-installation-notes">
                <?php _e( 'Installation Notes:', 'gbbs-software-archive' ); ?>
            </label>
        </th>
        <td>
            <textarea name="gbbs_archive_installation_notes" id="gbbs-archive-installation-notes" rows="4" cols="50" class="large-text"><?php echo esc_textarea( get_post_meta( $post->ID, 'gbbs_archive_installation_notes', true ) ); ?></textarea>
            <p class="description">
                <?php _e( 'Installation instructions and setup notes', 'gbbs-software-archive' ); ?>
            </p>
        </td>
    </tr>
    
    <tr>
        <th>
                <label for="gbbs-archive-historical-notes">
                <?php _e( 'Historical Notes:', 'gbbs-software-archive' ); ?>
            </label>
        </th>
        <td>
            <textarea name="gbbs_archive_historical_notes" id="gbbs-archive-historical-notes" rows="4" cols="50" class="large-text"><?php echo esc_textarea( get_post_meta( $post->ID, 'gbbs_archive_historical_notes', true ) ); ?></textarea>
            <p class="description">
                <?php _e( 'Historical context, significance, or background information', 'gbbs-software-archive' ); ?>
            </p>
        </td>
    </tr>
    
</table>

<?php wp_nonce_field( 'gbbs_archive_info_nonce', 'gbbs_archive_info_nonce_field' ); ?>
