jQuery(document).ready(function ($) {
    
    var wpMediaGBBSFrame;
    var fileIndex = 0;
    
    // Check if we're on a GBBS archive page
    if (!$('.gbbs-archive-files').length) {
        return;
    }
    
    // Initialize file index based on existing files
    function initializeFileIndex() {
        var existingFiles = $('.gbbs-file-item').length;
        fileIndex = existingFiles;
    }
    
    // Initialize on page load
    initializeFileIndex();
    
    // Add new file
    $('.gbbs-add-file').on('click', function(e) {
        e.preventDefault();
        
        var template = $('#gbbs-file-template').html();
        if (!template) {
            return;
        }
        
        var number = fileIndex + 1;
        
        template = template.replace(/\{\{index\}\}/g, fileIndex);
        template = template.replace(/\{\{number\}\}/g, number);
        
        $('.gbbs-files-list').append(template);
        fileIndex++;
        
        // Re-bind events for the new file
        bindFileEvents();
    });
    
    // Remove file
    $(document).on('click', '.gbbs-remove-file', function(e) {
        e.preventDefault();
        
        var confirmMessage = (typeof gbbsAdmin !== 'undefined' && gbbsAdmin.strings.confirmRemove) ? 
            gbbsAdmin.strings.confirmRemove : 'Are you sure you want to remove this file?';
        
        if (confirm(confirmMessage)) {
            $(this).closest('.gbbs-file-item').remove();
            renumberFiles();
        }
    });
    
    // Renumber files after removal
    function renumberFiles() {
        $('.gbbs-file-item').each(function(index) {
            $(this).attr('data-index', index);
            $(this).find('h4').text('File #' + (index + 1));
            
            // Update all input names
            $(this).find('input, select, textarea').each(function() {
                var name = $(this).attr('name');
                if (name) {
                    var newName = name.replace(/gbbs_archive_files\[\d+\]/, 'gbbs_archive_files[' + index + ']');
                    $(this).attr('name', newName);
                }
            });
        });
        
        fileIndex = $('.gbbs-file-item').length;
    }
    
    // Bind events for file management
    function bindFileEvents() {
        // File upload button
        $('.gbbs-upload-file-button').off('click').on('click', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $fileItem = $button.closest('.gbbs-file-item');
            var $fileUrl = $fileItem.find('.gbbs-file-url');
            var $fileId = $fileItem.find('.gbbs-file-id');
            
            // Close existing frame
            if (wpMediaGBBSFrame) {
                wpMediaGBBSFrame.close();
            }
            
            // Get localized strings
            var dialogTitle = (typeof gbbsAdmin !== 'undefined' && gbbsAdmin.strings.selectFile) ? 
                gbbsAdmin.strings.selectFile : 'Choose a file';
            var dialogButton = (typeof gbbsAdmin !== 'undefined' && gbbsAdmin.strings.insertFile) ? 
                gbbsAdmin.strings.insertFile : 'Insert file URL';
            
            // Create new frame
            wpMediaGBBSFrame = wp.media.frames.gbbsArchive = wp.media({
                title: dialogTitle,
                library: {
                    type: ''
                },
                button: {
                    text: dialogButton
                },
                multiple: false,
                states: [
                    new wp.media.controller.Library({
                        library: wp.media.query(),
                        multiple: false,
                        title: dialogTitle
                    })
                ]
            });
            
            // Handle frame open
            wpMediaGBBSFrame.on('open', function() {
                var attachment = wp.media.attachment($fileId.val());
                attachment.fetch();
                
                var selection = wpMediaGBBSFrame.state().get('selection');
                selection.add(attachment ? [attachment] : []);
            });
            
            // Handle file selection
            wpMediaGBBSFrame.on('select', function() {
                var attachment = wpMediaGBBSFrame.state().get('selection').first().toJSON();
                
                $fileUrl.val(attachment.url);
                $fileId.val(attachment.id);
                
                // Auto-fill file name if empty
                if (!$fileItem.find('.gbbs-file-name').val()) {
                    $fileItem.find('.gbbs-file-name').val(attachment.filename || attachment.title);
                }
            });
            
            // Handle frame ready
            wpMediaGBBSFrame.on('ready', function() {
                // Set upload parameters for GBBS archive
                wpMediaGBBSFrame.uploader.options.uploader.params = {
                    type: 'gbbs_archive'
                };
                
                // Also set the post ID if available
                var postId = $('#post_ID').val();
                if (postId) {
                    wpMediaGBBSFrame.uploader.options.uploader.params.post_id = postId;
                }
            });
            
            wpMediaGBBSFrame.open();
        });
    }
    
    // Initialize file events
    bindFileEvents();
    
    // File type validation
    function validateFileType(fileUrl) {
        var allowedExtensions = [
            // Apple II Disk Images
            '.dsk', '.po', '.do', '.nib', '.woz', '.2mg',
            // Apple II File Formats
            '.bas', '.int', '.asm', '.s', '.bin', '.a2s', '.a2d', '.bxy', '.bqy',
            // Archive Formats
            '.shk', '.bny', '.sea', '.bxy', '.bqy', '.zip',
            // Documentation
            '.txt', '.doc', '.pdf'
        ];
        
        var fileExtension = fileUrl.toLowerCase().substring(fileUrl.lastIndexOf('.'));
        return allowedExtensions.indexOf(fileExtension) !== -1;
    }
    
    // Validate file when URL changes
    $(document).on('change', '.gbbs-file-url', function() {
        var fileUrl = $(this).val();
        var $fileItem = $(this).closest('.gbbs-file-item');
        
        if (fileUrl && !validateFileType(fileUrl)) {
            $fileItem.addClass('gbbs-invalid-file');
            if (!$fileItem.find('.gbbs-file-warning').length) {
                $(this).after('<p class="gbbs-file-warning" style="color: #d63638;">Warning: This file type may not be supported.</p>');
            }
        } else {
            $fileItem.removeClass('gbbs-invalid-file');
            $fileItem.find('.gbbs-file-warning').remove();
        }
    });
    
    // Auto-fill file name from URL
    $(document).on('change', '.gbbs-file-url', function() {
        var fileUrl = $(this).val();
        var $fileItem = $(this).closest('.gbbs-file-item');
        var $fileName = $fileItem.find('.gbbs-file-name');
        
        if (fileUrl) {
            var fileName = fileUrl.substring(fileUrl.lastIndexOf('/') + 1);
            $fileName.val(fileName);
        }
    });
    
    // Sortable files (optional enhancement)
    if ($.fn.sortable) {
        $('.gbbs-files-list').sortable({
            handle: '.gbbs-file-header',
            update: function() {
                renumberFiles();
            }
        });
    }
    
    // ========================================
    // Archive Link Insertion Helper
    // ========================================
    
    // Handle "Insert Archive Link" button click (button is added via PHP)
    $('#insert-gbbs-archive').on('click', function(e) {
        e.preventDefault();
        openArchiveSelector();
    });
    
    // Archive selector modal
    function openArchiveSelector() {
        var archiveModal = $('<div id="gbbs-archive-selector" class="gbbs-archive-selector-modal">' +
            '<div class="gbbs-archive-selector-content">' +
                '<div class="gbbs-archive-selector-header">' +
                    '<h2>Select Archive to Link</h2>' +
                    '<button type="button" class="gbbs-archive-selector-close">&times;</button>' +
                '</div>' +
                '<div class="gbbs-archive-selector-body">' +
                    '<div class="gbbs-archive-search">' +
                        '<input type="text" id="gbbs-archive-search" placeholder="Search archives by name, author, or version..." />' +
                    '</div>' +
                    '<div class="gbbs-archive-filters">' +
                        '<label>Filter by Volume: <select id="gbbs-archive-volume-filter"><option value="">All Volumes</option></select></label>' +
                    '</div>' +
                    '<div class="gbbs-archive-list">' +
                        '<div class="gbbs-archive-loading">Loading archives...</div>' +
                    '</div>' +
                    '<div class="gbbs-archive-options">' +
                        '<label><input type="checkbox" id="gbbs-archive-modal" checked /> Display in modal</label><br>' +
                        '<label>Button text: <input type="text" id="gbbs-archive-button-text" value="View Archive" /></label>' +
                    '</div>' +
                '</div>' +
                '<div class="gbbs-archive-selector-footer">' +
                    '<button type="button" class="button button-primary" id="gbbs-insert-archive">Insert Archive Link</button>' +
                    '<button type="button" class="button" id="gbbs-cancel-archive">Cancel</button>' +
                '</div>' +
            '</div>' +
        '</div>');
        
        $('body').append(archiveModal);
        $('#gbbs-archive-selector').fadeIn(300);
        
        // Load volumes for filter
        loadVolumes();
        
        // Load archives
        loadArchives();
        
        // Bind events
        bindArchiveSelectorEvents();
    }
    
    // Load archives via AJAX
    function loadArchives(searchTerm, volumeFilter) {
        var data = {
            action: 'gbbs_get_archives',
            search: searchTerm || '',
            volume: volumeFilter || '',
            nonce: gbbsAdmin.nonce
        };
        
        $.post(ajaxurl, data, function(response) {
            if (response.success) {
                displayArchives(response.data);
            } else {
                $('.gbbs-archive-list').html('<div class="gbbs-archive-error">Error loading archives: ' + response.data + '</div>');
            }
        });
    }
    
    // Load volumes via AJAX
    function loadVolumes() {
        var data = {
            action: 'gbbs_get_volumes',
            nonce: gbbsAdmin.nonce
        };
        
        $.post(ajaxurl, data, function(response) {
            if (response.success) {
                var $select = $('#gbbs-archive-volume-filter');
                $select.empty().append('<option value="">All Volumes</option>');
                
                response.data.forEach(function(volume) {
                    $select.append('<option value="' + volume.slug + '">' + volume.name + ' (' + volume.count + ')</option>');
                });
            }
        });
    }
    
    // Display archives in the selector
    function displayArchives(archives) {
        var html = '';
        if (archives.length === 0) {
            html = '<div class="gbbs-archive-empty">No archives found.</div>';
        } else {
            html = '<div class="gbbs-archive-items">';
            archives.forEach(function(archive) {
                html += '<div class="gbbs-archive-item" data-id="' + archive.ID + '">' +
                    '<div class="gbbs-archive-item-title">' + archive.post_title + '</div>' +
                    '<div class="gbbs-archive-item-meta">' +
                        'Version: ' + (archive.version || 'N/A') + ' | ' +
                        'Author: ' + (archive.author || 'Unknown') + ' | ' +
                        'Volume: ' + (archive.volume || 'Uncategorized') + ' | ' +
                        'Files: ' + (archive.file_count || 0) +
                    '</div>' +
                '</div>';
            });
            html += '</div>';
        }
        $('.gbbs-archive-list').html(html);
    }
    
    // Bind events for archive selector
    function bindArchiveSelectorEvents() {
        var $modal = $('#gbbs-archive-selector');
        var selectedArchiveId = null;
        
        // Close modal
        $modal.find('.gbbs-archive-selector-close, #gbbs-cancel-archive').on('click', function() {
            $modal.fadeOut(300, function() {
                $modal.remove();
            });
        });
        
        // Close on background click
        $modal.on('click', function(e) {
            if (e.target === this) {
                $modal.fadeOut(300, function() {
                    $modal.remove();
                });
            }
        });
        
        // Search functionality
        $('#gbbs-archive-search').on('input', function() {
            var searchTerm = $(this).val();
            var volumeFilter = $('#gbbs-archive-volume-filter').val();
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(function() {
                loadArchives(searchTerm, volumeFilter);
            }, 300);
        });
        
        // Volume filter functionality
        $('#gbbs-archive-volume-filter').on('change', function() {
            var searchTerm = $('#gbbs-archive-search').val();
            var volumeFilter = $(this).val();
            loadArchives(searchTerm, volumeFilter);
        });
        
        // Archive selection
        $(document).on('click', '.gbbs-archive-item', function() {
            $('.gbbs-archive-item').removeClass('selected');
            $(this).addClass('selected');
            selectedArchiveId = $(this).data('id');
        });
        
        // Insert archive link
        $('#gbbs-insert-archive').on('click', function() {
            if (!selectedArchiveId) {
                // Show error message in the interface instead of alert
                $('#gbbs-archive-message').html('<div class="notice notice-error"><p>Please select an archive first.</p></div>').show();
                return;
            }
            
            var modal = $('#gbbs-archive-modal').is(':checked') ? 'true' : 'false';
            var buttonText = $('#gbbs-archive-button-text').val() || 'View Archive';
            
            var shortcode = '[gbbs_archive id="' + selectedArchiveId + '" modal="' + modal + '" button_text="' + buttonText + '"]';
            
            // Insert into editor
            if (typeof wp !== 'undefined' && wp.media && wp.media.editor) {
                wp.media.editor.insert(shortcode);
            } else if (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor) {
                tinyMCE.activeEditor.insertContent(shortcode);
            } else {
                // Fallback for text editor
                var textarea = $('#content');
                if (textarea.length) {
                    var cursorPos = textarea.prop('selectionStart');
                    var textBefore = textarea.val().substring(0, cursorPos);
                    var textAfter = textarea.val().substring(cursorPos);
                    textarea.val(textBefore + shortcode + textAfter);
                }
            }
            
            $modal.fadeOut(300, function() {
                $modal.remove();
            });
        });
    }
    
    // Quick Edit functionality for volume selection
    function initQuickEditVolume() {
        // Handle Quick Edit form opening
        $(document).on('click', '.editinline', function() {
            var $row = $(this).closest('tr');
            var postId = $row.attr('id').replace('post-', '');
            var $volumeCell = $row.find('.column-gbbs_volume');
            var currentVolumeId = $volumeCell.find('.gbbs-volume-id').data('volume-id');
            
            // Set the current volume in the Quick Edit dropdown
            if (currentVolumeId) {
                $('#gbbs_volume_quick_edit').val(currentVolumeId);
            } else {
                $('#gbbs_volume_quick_edit').val('');
            }
        });
    }
    
    // Initialize Quick Edit functionality
    initQuickEditVolume();
    
});