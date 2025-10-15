<?php
/**
 * GBBS Pro Theme Functions
 *
 * This file contains all the theme's functionality and is the main entry point
 * for the GBBS Pro WordPress theme. It handles font loading, theme support,
 * and various utility functions.
 *
 * @package GBBSPro
 * @since 1.0.0
 * @version 1.0.0
 * @author Paul Lee
 * @link https://github.com/paulhlee1967/gbbs-pro-wordpress-theme
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get font CSS definitions
 *
 * Centralized function that generates all @font-face declarations for the Apple II fonts.
 * This makes it easy to override font loading by simply overriding this function.
 *
 * @since 1.0.0
 * @return string CSS containing @font-face declarations for Apple II fonts
 */
function gbbs_pro_get_font_css() {
    $template_uri = get_template_directory_uri();

    return "
    @font-face {
        font-family: 'Apple II';
        src: url('{$template_uri}/fonts/PrintChar21.woff2') format('woff2'),
             url('{$template_uri}/fonts/PrintChar21.ttf') format('truetype');
        font-weight: normal;
        font-style: normal;
        font-display: swap;
    }

    @font-face {
        font-family: 'Apple II 80';
        src: url('{$template_uri}/fonts/PRNumber3.woff2') format('woff2'),
             url('{$template_uri}/fonts/PRNumber3.ttf') format('truetype');
        font-weight: normal;
        font-style: normal;
        font-display: swap;
    }
    ";
}

/**
 * Get font application CSS
 *
 * Centralized function that generates CSS rules for applying Apple II fonts.
 * Uses a clean approach: only target frontend and editor canvas areas.
 *
 * @since 1.0.0
 * @return string CSS containing font application rules
 */
function gbbs_pro_get_font_application_css() {
    return "
    /* Frontend only - no !important needed, no admin conflicts */
    body:not(.wp-admin) {
        font-family: 'Apple II', monospace;
    }

    body:not(.wp-admin) * {
        font-family: inherit;
    }
    ";
}

/**
 * Get editor font application CSS
 *
 * Centralized function that generates CSS rules for applying Apple II fonts in the editor.
 * This ensures fonts are available in both frontend and editor contexts.
 *
 * @since 1.0.0
 * @return string CSS containing font application rules for editor
 */
function gbbs_pro_get_editor_font_application_css() {
    return "
    /* Editor context - make fonts available for selection */
    .editor-styles-wrapper {
        font-family: 'Apple II', monospace;
    }

    .editor-styles-wrapper * {
        font-family: inherit;
    }
    ";
}

/**
 * Enqueue frontend styles and fonts
 *
 * Handles all frontend asset loading including the main stylesheet and font CSS.
 * This is the single entry point for frontend font loading, making it easy to maintain.
 *
 * @since 1.0.0
 * @return void
 */
function gbbs_pro_enqueue_frontend_assets() {
    // Enqueue main stylesheet
    wp_enqueue_style(
        'gbbs-pro-style',
        get_stylesheet_uri(),
        array(),
        wp_get_theme()->get('Version')
    );

    // Add font CSS
    wp_add_inline_style('gbbs-pro-style', gbbs_pro_get_font_css());

    // Add font application CSS
    wp_add_inline_style('gbbs-pro-style', gbbs_pro_get_font_application_css());
}
add_action('wp_enqueue_scripts', 'gbbs_pro_enqueue_frontend_assets');

/**
 * Enqueue editor assets and fonts
 *
 * Handles all editor asset loading for both Block Editor and Site Editor.
 * The font application CSS uses :not(.wp-admin) to ensure fonts only apply
 * to frontend content, preserving the WordPress admin interface.
 *
 * @since 1.0.0
 * @return void
 */
function gbbs_pro_enqueue_editor_assets() {
    // Get font CSS
    $font_css = gbbs_pro_get_font_css();

    // Get editor font application CSS
    $editor_font_application_css = gbbs_pro_get_editor_font_application_css();

    // Combine both
    $combined_css = $font_css . $editor_font_application_css;

    // Add to all relevant editor stylesheets
    $editor_handles = array(
        'wp-block-library',
        'wp-edit-blocks',
        'wp-block-editor',
        'wp-components',
        'wp-edit-site',
        'wp-edit-post'
    );

    foreach ($editor_handles as $handle) {
        wp_add_inline_style($handle, $combined_css);
    }
}
add_action('enqueue_block_editor_assets', 'gbbs_pro_enqueue_editor_assets');

/**
 * Add editor styles support
 *
 * Registers editor stylesheets for the Block Editor and Site Editor.
 * This ensures the theme's styling is applied in the editor context.
 *
 * @since 1.0.0
 * @return void
 */
function gbbs_pro_add_editor_styles() {
    add_editor_style('editor-style.css');
    add_editor_style('style.css');
}
add_action('after_setup_theme', 'gbbs_pro_add_editor_styles');

/**
 * Copyright shortcode
 *
 * Displays a copyright notice with optional start year.
 * Usage: [copyright] or [copyright start_year="2020"]
 *
 * @since 1.0.0
 * @param array $atts Shortcode attributes
 * @return string Copyright notice HTML
 */
function gbbs_pro_copyright_shortcode($atts) {
    $atts = shortcode_atts(array(
        'start_year' => '',
    ), $atts);

    $current_year = date('Y');
    $site_name = get_bloginfo('name');

    // If start year is provided and different from current year, show range
    if (!empty($atts['start_year']) && $atts['start_year'] != $current_year) {
        $year_display = $atts['start_year'] . '-' . $current_year;
    } else {
        $year_display = $current_year;
    }

    return '© ' . $year_display . ' ' . $site_name;
}
add_shortcode('copyright', 'gbbs_pro_copyright_shortcode');


/**
 * Add theme support for various features
 *
 * Registers support for various WordPress theme features including
 * post thumbnails, HTML5 markup, responsive embeds, and more.
 *
 * @since 1.0.0
 * @return void
 */
function gbbs_pro_theme_support() {
    // Add support for post thumbnails
    add_theme_support('post-thumbnails');

    // Add support for HTML5 markup
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // Add support for responsive embedded content
    add_theme_support('responsive-embeds');

    // Add support for wide and full alignment
    add_theme_support('align-wide');

    // Add support for editor styles
    add_theme_support('editor-styles');

    // Add support for custom line height
    add_theme_support('custom-line-height');

    // Add support for custom units
    add_theme_support('custom-units');

    // Add support for post navigation
    add_theme_support('post-navigation');
}
add_action('after_setup_theme', 'gbbs_pro_theme_support');

/**
 * Add skip link for accessibility
 *
 * Outputs a skip link for screen readers to jump to main content.
 * This improves accessibility for keyboard and screen reader users.
 *
 * @since 1.0.0
 * @return void
 */
function gbbs_pro_skip_link() {
    echo '<a class="screen-reader-text" href="#main">Skip to main content</a>';
}
add_action('wp_body_open', 'gbbs_pro_skip_link');

/**
 * Add GBBS Pro specific body classes
 *
 * Adds theme-specific body classes for styling and identification.
 *
 * @since 1.0.0
 * @param array $classes Existing body classes
 * @return array Modified body classes
 */
function gbbs_pro_body_classes($classes) {
    $classes[] = 'gbbs-pro-theme';

    // Add search results class for JavaScript functionality
    if (is_search()) {
        $classes[] = 'search-results';
    }

    return $classes;
}
add_filter('body_class', 'gbbs_pro_body_classes');

/**
 * Add GBBS Pro specific admin styles
 *
 * Outputs custom styles for the WordPress admin area to maintain
 * the Apple II aesthetic in admin notices and other elements.
 *
 * @since 1.0.0
 * @return void
 */
function gbbs_pro_admin_styles() {
    echo '<style>
        .gbbs-pro-admin-notice {
            background: #000;
            color: #33ff33;
            border: 1px solid #33ff33;
            padding: 1rem;
            font-family: monospace;
            margin: 1rem 0;
        }
    </style>';
}
add_action('admin_head', 'gbbs_pro_admin_styles');

/**
 * Add GBBS Pro specific JavaScript
 *
 * Enqueues JavaScript for theme-specific functionality including
 * the terminal cursor effect for elements with the gbbs-pro-cursor class
 * and search loading states with spinning cursor.
 *
 * @since 1.0.0
 * @return void
 */
function gbbs_pro_enqueue_scripts() {
    // Add terminal cursor effect and search loading script
    wp_add_inline_script('wp-block-library', '
        document.addEventListener("DOMContentLoaded", function() {
            // Add terminal cursor effect to elements with gbbs-pro-cursor class
            const cursorElements = document.querySelectorAll(".gbbs-pro-cursor");
            cursorElements.forEach(function(element) {
                element.classList.add("gbbs-pro-cursor");
            });

            // Add search loading functionality
            const searchForms = document.querySelectorAll(".wp-block-search");
            searchForms.forEach(function(form) {
                const searchButton = form.querySelector(".wp-block-search__button");
                const searchInput = form.querySelector(".wp-block-search__input");

                if (searchButton && searchInput) {
                    searchButton.addEventListener("click", function() {
                        // Show spinning cursor while searching
                        const originalText = searchButton.textContent;
                        searchButton.innerHTML = \'<span class="gbbs-pro-spinner">Searching...</span>\';
                        searchButton.disabled = true;

                        // Re-enable after a short delay (search will redirect)
                        setTimeout(function() {
                            searchButton.textContent = originalText;
                            searchButton.disabled = false;
                        }, 2000);
                    });
                }
            });

            // Add loading state to search results page
            if (document.body.classList.contains("search-results")) {
                // Show loading indicator while page loads
                const searchResults = document.querySelector(".wp-block-query");
                if (searchResults) {
                    const loadingDiv = document.createElement("div");
                    loadingDiv.className = "gbbs-pro-spinner";
                    loadingDiv.textContent = "Loading search results...";
                    loadingDiv.style.textAlign = "center";
                    loadingDiv.style.margin = "2rem 0";
                    searchResults.insertBefore(loadingDiv, searchResults.firstChild);

                    // Hide loading indicator once content is loaded
                    window.addEventListener("load", function() {
                        loadingDiv.style.display = "none";
                    });
                }
            }

            // GBBS Pro Page Header - Dynamic dash length
            function adjustDashLength() {
                const pageHeaders = document.querySelectorAll(".gbbs-pro-page-header");

                pageHeaders.forEach(function(header, index) {
                    const title = header.querySelector(".gbbs-pro-page-title");
                    const topDash = header.querySelector(".gbbs-pro-header-top-dash");
                    const bottomDash = header.querySelector(".gbbs-pro-header-bottom-dash");


                    if (title && topDash && bottomDash) {
                        // Get the title text
                        const titleText = title.textContent.trim();

                        // Calculate dash length to match the bracketed title
                        // The bracketed title will be "[ " + title + " ]"
                        // Account for the brackets and spaces: "[ " = 2 chars, " ]" = 2 chars
                        const bracketedLength = titleText.length + 4; // "[ " + title + " ]"

                        // Use the bracketed length as the dash count for perfect alignment
                        const dashCount = Math.max(15, Math.min(60, bracketedLength));

                        // Create dash string
                        const dashString = "-".repeat(dashCount);

                        // Update the dash elements
                        topDash.textContent = dashString;
                        bottomDash.textContent = dashString;
                    } else {
                    }
                });
            }

            // Run on page load
            adjustDashLength();

            // Run on window resize (in case of responsive changes)
            window.addEventListener("resize", function() {
                setTimeout(adjustDashLength, 100);
            });

            // GBBS Pro Single Category Enforcement
            // Note: Single category enforcement is now handled by the dropdown interface
            // No additional JavaScript needed since dropdown naturally prevents multiple selection

        });
    ');
}
add_action('wp_enqueue_scripts', 'gbbs_pro_enqueue_scripts');

/**
 * Add GBBS Pro page header JavaScript
 *
 * Enqueues JavaScript specifically for the page header dash length adjustment.
 * This ensures the JavaScript runs on the frontend.
 *
 * @since 1.0.0
 * @return void
 */
function gbbs_pro_enqueue_page_header_script() {
    wp_add_inline_script('wp-block-library', '
        document.addEventListener("DOMContentLoaded", function() {

            // GBBS Pro Page Header - Dynamic dash length
            function adjustDashLength() {
                const pageHeaders = document.querySelectorAll(".gbbs-pro-page-header");

                pageHeaders.forEach(function(header, index) {
                    const title = header.querySelector(".gbbs-pro-page-title");
                    const topDash = header.querySelector(".gbbs-pro-header-top-dash");
                    const bottomDash = header.querySelector(".gbbs-pro-header-bottom-dash");


                    if (title && topDash && bottomDash) {
                        // Get the title text
                        const titleText = title.textContent.trim();

                        // Calculate dash length to match the bracketed title
                        // The bracketed title will be "[ " + title + " ]"
                        // Account for the brackets and spaces: "[ " = 2 chars, " ]" = 2 chars
                        const bracketedLength = titleText.length + 4; // "[ " + title + " ]"

                        // Use the bracketed length as the dash count for perfect alignment
                        const dashCount = Math.max(15, Math.min(60, bracketedLength));

                        // Create dash string
                        const dashString = "-".repeat(dashCount);

                        // Update the dash elements
                        topDash.textContent = dashString;
                        bottomDash.textContent = dashString;
                    } else {
                    }
                });
            }

            // Run on page load
            adjustDashLength();

            // Run on window resize (in case of responsive changes)
            window.addEventListener("resize", function() {
                setTimeout(adjustDashLength, 100);
            });
        });
    ');
}
add_action('wp_enqueue_scripts', 'gbbs_pro_enqueue_page_header_script');


/**
 * Replace category checkboxes with dropdown selector
 *
 * This function replaces the default category checkboxes with a dropdown
 * selector that naturally enforces single category selection.
 *
 * @since 1.0.0
 * @return void
 */
function gbbs_pro_replace_category_metabox() {
    // Add our custom category dropdown metabox
    add_meta_box(
        'gbbs_pro_category_dropdown',
        'Category (Select One)',
        'gbbs_pro_category_dropdown_callback',
        'post',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'gbbs_pro_replace_category_metabox');

/**
 * Custom category dropdown callback
 *
 * Creates a dropdown selector for categories instead of checkboxes.
 *
 * @since 1.0.0
 * @param WP_Post $post Current post object
 * @return void
 */
function gbbs_pro_category_dropdown_callback($post) {
    // Get all categories
    $categories = get_categories(array(
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC'
    ));

    // Get current post categories
    $post_categories = wp_get_post_categories($post->ID);
    $selected_category = !empty($post_categories) ? $post_categories[0] : 0;

    // Add nonce for security
    wp_nonce_field('gbbs_pro_category_dropdown', 'gbbs_pro_category_nonce');

    echo '<div class="gbbs-pro-category-dropdown">';
    echo '<select name="gbbs_pro_category" id="gbbs_pro_category" style="width: 100%; font-family: monospace;">';
    echo '<option value="0">-- Select Category --</option>';

    foreach ($categories as $category) {
        $selected = ($category->term_id == $selected_category) ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($category->term_id) . '" ' . $selected . '>' . esc_html($category->name) . '</option>';
    }

    echo '</select>';
    echo '<p class="description" style="font-family: monospace; font-size: 12px; color: #666; margin-top: 5px;">';
    echo 'GBBS Pro Theme: Select only one category for this post.';
    echo '</p>';
    echo '</div>';
}

/**
 * Save custom category selection
 *
 * Handles saving the selected category from our custom dropdown.
 *
 * @since 1.0.0
 * @param int $post_id Post ID
 * @return void
 */
function gbbs_pro_save_category_selection($post_id) {
    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Check nonce
    if (!isset($_POST['gbbs_pro_category_nonce']) || !wp_verify_nonce($_POST['gbbs_pro_category_nonce'], 'gbbs_pro_category_dropdown')) {
        return;
    }

    // Only process for posts
    if (get_post_type($post_id) !== 'post') {
        return;
    }

    // Get selected category
    $selected_category = isset($_POST['gbbs_pro_category']) ? intval($_POST['gbbs_pro_category']) : 0;

    // Remove all existing categories first
    wp_set_post_categories($post_id, array());

    // Set the selected category (only if one is selected)
    if ($selected_category > 0) {
        wp_set_post_categories($post_id, array($selected_category));
    }

    // Handle Quick Edit category selection
    if (isset($_POST['gbbs_pro_category_quick_edit'])) {
        $quick_edit_category = intval($_POST['gbbs_pro_category_quick_edit']);
        if ($quick_edit_category > 0) {
            // Remove all existing categories first
            wp_set_post_categories($post_id, array());
            // Set the selected category
            wp_set_post_categories($post_id, array($quick_edit_category));
        }
        // If empty value, don't change the category (— No Change — option)
    }
}
add_action('save_post', 'gbbs_pro_save_category_selection');

/**
 * Add admin styles for the category dropdown
 *
 * Styles the custom category dropdown to match the GBBS Pro theme.
 *
 * @since 1.0.0
 * @return void
 */
function gbbs_pro_category_dropdown_styles() {
    $screen = get_current_screen();

    // Only add to post edit screens
    if ($screen && $screen->id === 'post') {
        ?>
        <style>
        .gbbs-pro-category-dropdown {
            font-family: monospace;
        }

        .gbbs-pro-category-dropdown select {
            background-color: #fff;
            color: #1d2327;
            border: 1px solid #8c8f94;
            padding: 8px;
            font-family: monospace;
            font-size: 12px;
            border-radius: 3px;
        }

        .gbbs-pro-category-dropdown select:focus {
            outline: 2px solid #2271b1;
            outline-offset: 2px;
            border-color: #2271b1;
        }

        .gbbs-pro-category-dropdown option {
            background-color: #fff;
            color: #1d2327;
            padding: 4px;
        }

        .gbbs-pro-category-dropdown .description {
            font-family: monospace;
            font-size: 11px;
            color: #646970;
            margin-top: 8px;
            padding: 4px;
            background-color: #f6f7f7;
            border-left: 3px solid #8c8f94;
        }

        /* Hide Gutenberg category panel */
        .components-panel__body:has(button:contains("Categories")) {
            display: none !important;
        }

        /* Hide category checkboxes in Gutenberg */
        .components-checkbox-control__input[name*="post_category"] {
            display: none !important;
        }

        .components-base-control:has(.components-checkbox-control__input[name*="post_category"]) {
            display: none !important;
        }
        </style>
        <?php
    }

    // Add JavaScript for Quick Edit functionality
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Quick Edit functionality for category selection
        function initQuickEditCategory() {
            // Handle Quick Edit form opening
            $(document).on('click', '.editinline', function() {
                var $row = $(this).closest('tr');
                var postId = $row.attr('id').replace('post-', '');
                var $categoryCell = $row.find('.column-gbbs_pro_category');
                var currentCategoryId = $categoryCell.find('.gbbs-pro-category-id').data('category-id');

                // Set the current category in the Quick Edit dropdown
                if (currentCategoryId) {
                    $('#gbbs_pro_category_quick_edit').val(currentCategoryId);
                } else {
                    $('#gbbs_pro_category_quick_edit').val('');
                }
            });
        }

        // Initialize Quick Edit functionality
        initQuickEditCategory();

        // Hide Gutenberg category panel
        function hideGutenbergCategoryPanel() {
            // Hide the category panel
            $('button:contains("Categories")').each(function() {
                var $button = $(this);
                if ($button.text().trim() === 'Categories') {
                    var $panel = $button.closest('.components-panel__body');
                    if ($panel.length > 0) {
                        $panel.hide();
                    }
                }
            });

            // Hide category checkboxes
            $('.components-checkbox-control__input[name*="post_category"]').each(function() {
                var $checkbox = $(this);
                var $control = $checkbox.closest('.components-base-control');
                var $panel = $checkbox.closest('.components-panel__body');

                if ($control.length > 0) $control.hide();
                if ($panel.length > 0) $panel.hide();
            });
        }

        // Hide immediately and on any DOM changes
        hideGutenbergCategoryPanel();

        // Use multiple event listeners
        $(document).on('DOMNodeInserted', function() {
            hideGutenbergCategoryPanel();
        });

        $(document).on('DOMSubtreeModified', function() {
            hideGutenbergCategoryPanel();
        });

        // Also run periodically
        setInterval(hideGutenbergCategoryPanel, 1000);
    });
    </script>
    <?php
}
add_action('admin_head', 'gbbs_pro_category_dropdown_styles');

/**
 * Disable default category Quick Edit and add custom dropdown
 *
 * This function disables the default category Quick Edit checkboxes
 * and replaces them with a single-select dropdown.
 *
 * @since 1.0.0
 * @return void
 */
function gbbs_pro_disable_category_quick_edit() {
    // Remove the default category metabox from Quick Edit
    remove_meta_box('categorydiv', 'post', 'side');

    // Add custom Quick Edit for category selection
    add_action('quick_edit_custom_box', 'gbbs_pro_add_category_quick_edit', 10, 2);

    // Add custom admin column handling for category display
    add_filter('manage_posts_columns', 'gbbs_pro_add_category_column');
    add_action('manage_posts_custom_column', 'gbbs_pro_display_category_column', 10, 2);

    // Add CSS to hide default category checkboxes in Quick Edit
    add_action('admin_head', 'gbbs_pro_hide_default_category_quick_edit');
}
add_action('admin_init', 'gbbs_pro_disable_category_quick_edit');

/**
 * Hide default category checkboxes in Quick Edit
 *
 * @since 1.0.0
 * @return void
 */
function gbbs_pro_hide_default_category_quick_edit() {
    $screen = get_current_screen();
    if ($screen && $screen->id === 'edit-post') {
        ?>
        <style>
        /* Hide default category checkboxes in Quick Edit */
        .inline-edit-row .category-checklist {
            display: none !important;
        }

        /* Hide the category label in Quick Edit */
        .inline-edit-row .inline-edit-categories-label {
            display: none !important;
        }

        /* Hide the category div wrapper in Quick Edit */
        .inline-edit-row .inline-edit-categories {
            display: none !important;
        }
        </style>
        <?php
    }
}

/**
 * Add custom category dropdown to Quick Edit
 *
 * Adds a single-select dropdown for category selection in the Quick Edit interface.
 * This ensures posts can only be assigned to one category even in Quick Edit.
 *
 * @since 1.0.0
 * @param string $column_name The name of the column being edited
 * @param string $post_type The post type being edited
 * @return void
 */
function gbbs_pro_add_category_quick_edit($column_name, $post_type) {
    // Only show for posts and category column
    if ($post_type !== 'post' || $column_name !== 'gbbs_pro_category') {
        return;
    }

    // Get all categories
    $categories = get_categories(array(
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC'
    ));

    if (empty($categories) || is_wp_error($categories)) {
        return;
    }
    ?>
    <fieldset class="inline-edit-col-right">
        <div class="inline-edit-col">
            <label>
                <span class="title"><?php _e('Category', 'gbbs-pro'); ?></span>
                <select name="gbbs_pro_category_quick_edit" id="gbbs_pro_category_quick_edit">
                    <option value=""><?php _e('— No Change —', 'gbbs-pro'); ?></option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo esc_attr($category->term_id); ?>">
                            <?php echo esc_html($category->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
    </fieldset>
    <?php
}

/**
 * Add category column to admin list table
 *
 * @since 1.0.0
 * @param array $columns Existing columns
 * @return array Modified columns
 */
function gbbs_pro_add_category_column($columns) {
    // Insert category column before date column
    $new_columns = array();
    foreach ($columns as $key => $value) {
        if ($key === 'date') {
            $new_columns['gbbs_pro_category'] = __('Category', 'gbbs-pro');
        }
        $new_columns[$key] = $value;
    }
    return $new_columns;
}

/**
 * Display category column content
 *
 * @since 1.0.0
 * @param string $column_name The column name
 * @param int $post_id The post ID
 * @return void
 */
function gbbs_pro_display_category_column($column_name, $post_id) {
    if ($column_name === 'gbbs_pro_category') {
        $categories = get_the_category($post_id);
        if (!empty($categories) && !is_wp_error($categories)) {
            $category = $categories[0]; // Get first category (should only be one)
            echo '<span class="gbbs-pro-category-id" data-category-id="' . esc_attr($category->term_id) . '">';
            echo esc_html($category->name);
            echo '</span>';
        } else {
            echo '<span class="gbbs-pro-category-id" data-category-id="">';
            echo __('Uncategorized', 'gbbs-pro');
            echo '</span>';
        }
    }
}
