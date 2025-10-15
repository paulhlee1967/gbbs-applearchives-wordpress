    (function() {
        'use strict';

        // Wait for WordPress and Gutenberg to load
        document.addEventListener('DOMContentLoaded', function() {
            var checkCount = 0;
            var checkInterval = setInterval(function() {
                checkCount++;
                if (typeof wp !== 'undefined' && wp.blocks && wp.components) {
                    clearInterval(checkInterval);
                    registerArchiveBlock();
                } else if (checkCount > 50) {
                    clearInterval(checkInterval);
                    // WordPress not loaded after 10 seconds - silently fail
                }
            }, 200);
        });
    
        function registerArchiveBlock() {
            // Register a custom block for archive insertion
        wp.blocks.registerBlockType('gbbs/archive-helper', {
            title: 'GBBS Archive Link',
            icon: 'archive',
            category: 'widgets',
            description: 'Insert a link to a GBBS software archive',
            keywords: ['archive', 'gbbs', 'software', 'download'],
            supports: {
                html: false,
            },
            edit: function(props) {
                return wp.element.createElement(ArchiveBlockEdit, props);
            },
            save: function() {
                return null; // This block is dynamic
            }
        });
    }
    
    // Archive Block Edit Component
    function ArchiveBlockEdit(props) {
        var useState = wp.element.useState;
        var useEffect = wp.element.useEffect;
        var useRef = wp.element.useRef;
        var el = wp.element.createElement;
        var Fragment = wp.element.Fragment;
        var Button = wp.components.Button;
        var TextControl = wp.components.TextControl;
        var SelectControl = wp.components.SelectControl;
        var CheckboxControl = wp.components.CheckboxControl;
        var Spinner = wp.components.Spinner;
        var Notice = wp.components.Notice;
        var Placeholder = wp.components.Placeholder;
        
        // Use refs for data that doesn't need to trigger re-renders
        var archivesRef = useRef([]);
        var volumesRef = useRef([]);
        var searchTimeoutRef = useRef(null);
        var selectedArchiveRef = useRef(null);
        var buttonTextRef = useRef('View Archive');
        var hasSearchedRef = useRef(false);
        var selectedArchiveStateRef = useRef(null);
        
        // Use state only for UI updates
        var archives = useState([])[0];
        var setArchives = useState([])[1];
        var volumes = useState([])[0];
        var setVolumes = useState([])[1];
        var loading = useState(false)[0];
        var setLoading = useState(false)[1];
        var error = useState(null)[0];
        var setError = useState(null)[1];
        var searchTerm = useState('')[0];
        var setSearchTerm = useState('')[1];
        var selectedVolume = useState('')[0];
        var setSelectedVolume = useState('')[1];
        var selectedArchive = useState(null)[0];
        var setSelectedArchive = useState(null)[1];
        var buttonText = useState('View Archive')[0];
        var setButtonText = useState('View Archive')[1];
        var hasSearched = useState(false)[0]; // Track if user has performed a search
        var setHasSearched = useState(false)[1];
        var forceUpdate = useState(0)[0];
        var setForceUpdate = useState(0)[1];
        
        // Refs for direct DOM access
        var searchInputRef = useRef(null);
        var buttonTextInputRef = useRef(null);
        var volumeSelectRef = useRef(null);
        
        // Load archives function
        function loadArchives(search, volume) {
            setLoading(true);
            setError(null);
            
            var data = new FormData();
            data.append('action', 'gbbs_get_archives');
            data.append('search', search || '');
            data.append('volume', volume || '');
            data.append('nonce', gbbsBlockEditor.nonce);
            
            fetch(gbbsBlockEditor.ajaxUrl, {
                method: 'POST',
                body: data
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(result) {
                if (result.success) {
                    archivesRef.current = result.data;
                    setArchives(result.data);
                    setForceUpdate(forceUpdate + 1); // Force re-render
                } else {
                    setError(result.data || 'Error loading archives');
                }
                setLoading(false);
            })
            .catch(function(err) {
                setError('Network error: ' + err.message);
                setLoading(false);
            });
        }
        
        // Load volumes function
        function loadVolumes() {
            var data = new FormData();
            data.append('action', 'gbbs_get_volumes');
            data.append('nonce', gbbsBlockEditor.nonce);
            
            fetch(gbbsBlockEditor.ajaxUrl, {
                method: 'POST',
                body: data
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(result) {
                if (result.success) {
                    volumesRef.current = result.data;
                    setVolumes(result.data);
                    setForceUpdate(forceUpdate + 1); // Force re-render
                }
            })
            .catch(function(err) {
                // Error loading volumes - silently fail
            });
        }
        
        // Load data on mount
        useEffect(function() {
            loadVolumes();
            // Don't load archives initially - wait for user to search
        }, []);
        
        // Handle insert
        function handleInsert() {
            var currentSelectedArchive = selectedArchiveRef.current;
            if (!currentSelectedArchive) {
                setError('Please select an archive first.');
                return;
            }

            var currentButtonText = buttonTextRef.current;
            var shortcode = '[gbbs_archive id="' + currentSelectedArchive.ID + '" button_text="' + currentButtonText + '"]';
            
            try {
                // Try different methods to insert the shortcode
                if (wp.data && wp.data.dispatch) {
                    wp.data.dispatch('core/block-editor').replaceBlocks(
                        props.clientId,
                        wp.blocks.createBlock('core/shortcode', {
                            text: shortcode
                        })
                    );
                } else if (props.replaceBlocks) {
                    props.replaceBlocks(
                        props.clientId,
                        wp.blocks.createBlock('core/shortcode', {
                            text: shortcode
                        })
                    );
                } else {
                    // Fallback: copy to clipboard
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(shortcode).then(function() {
                            setMessage('Shortcode copied to clipboard!');
                        });
                    } else {
                        setMessage('Please copy this shortcode: ' + shortcode);
                    }
                }
            } catch (error) {
                // Fallback: copy to clipboard
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(shortcode).then(function() {
                        setMessage('Shortcode copied to clipboard!');
                    });
                } else {
                    setMessage('Please copy this shortcode: ' + shortcode);
                }
            }
        }
        
        // Volume options - use ref data for stability
        var currentVolumes = volumesRef.current || [];
        var volumeOptions = [
            { label: 'All Volumes', value: '' }
        ].concat(currentVolumes.map(function(volume) {
            return {
                label: volume.name + ' (' + volume.count + ')',
                value: volume.slug
            };
        }));
        
        return el('div', { className: 'gbbs-archive-block-editor' }, [
            el(Placeholder, {
                icon: 'archive',
                label: 'GBBS Archive Link',
                instructions: 'Search and select an archive to create a link'
            }, [
                el('div', { style: { marginBottom: '16px' } }, [
                    el('label', { 
                        style: { display: 'block', marginBottom: '4px', fontWeight: '500' } 
                    }, 'Search Archives'),
                    el('input', {
                        ref: searchInputRef,
                        type: 'text',
                        placeholder: 'Search by name, author, or version...',
                        defaultValue: searchTerm,
                        style: {
                            width: '100%',
                            padding: '8px 12px',
                            border: '1px solid #8c8f94',
                            borderRadius: '4px',
                            fontSize: '14px',
                            marginBottom: '8px'
                        },
                        onInput: function(e) {
                            var value = e.target.value;
                            setSearchTerm(value);
                            // Mark as searched immediately when user types
                            if (value.length > 0) {
                                hasSearchedRef.current = true;
                                setHasSearched(true);
                                setForceUpdate(forceUpdate + 1); // Force re-render
                            }
                            // Debounce the search
                            if (searchTimeoutRef.current) {
                                clearTimeout(searchTimeoutRef.current);
                            }
                            searchTimeoutRef.current = setTimeout(function() {
                                loadArchives(value, selectedVolume);
                            }, 300);
                        }
                    }),
                    
                    el('label', { 
                        style: { display: 'block', marginBottom: '4px', fontWeight: '500' } 
                    }, 'Filter by Volume'),
                    el('select', {
                        ref: volumeSelectRef,
                        value: selectedVolume,
                        style: {
                            width: '100%',
                            padding: '8px 12px',
                            border: '1px solid #8c8f94',
                            borderRadius: '4px',
                            fontSize: '14px',
                            marginBottom: '8px'
                        },
                            onChange: function(e) {
                                var value = e.target.value;
                                setSelectedVolume(value);
                                setHasSearched(true); // Mark that a search has been performed
                                loadArchives(searchTerm, value);
                            }
                    }, volumeOptions.map(function(option) {
                        return el('option', { key: option.value, value: option.value }, option.label);
                    }))
                ]),
            
            loading ? el(Spinner) : null,
            
            error ? el(Notice, { status: 'error', isDismissible: false }, error) : null,
            
            (function() {
                var currentArchives = archivesRef.current || [];
                var currentHasSearched = hasSearched || hasSearchedRef.current;
                
                if (!currentHasSearched) {
                    return el('div', { style: { padding: '20px', textAlign: 'center', color: '#666' } }, 'Type in the search box to find archives.');
                }
                
                if (currentArchives.length > 0) {
                    return el('div', { style: { maxHeight: '200px', overflowY: 'auto', border: '1px solid #ddd', borderRadius: '4px', padding: '8px', marginBottom: '16px' } },
                        currentArchives.map(function(archive) {
                            var currentSelectedArchive = selectedArchive || selectedArchiveStateRef.current;
                            var isSelected = currentSelectedArchive && currentSelectedArchive.ID === archive.ID;
                            return el('div', {
                                key: archive.ID,
                                onClick: function() {
                                    selectedArchiveRef.current = archive;
                                    selectedArchiveStateRef.current = archive;
                                    setSelectedArchive(archive);
                                    setForceUpdate(forceUpdate + 1); // Force re-render to update button
                                    setError(null);
                                },
                                style: {
                                    padding: '8px',
                                    border: '2px solid ' + (isSelected ? '#0073aa' : '#eee'),
                                    borderRadius: '4px',
                                    margin: '4px 0',
                                    cursor: 'pointer',
                                    backgroundColor: isSelected ? '#0073aa' : 'transparent',
                                    color: isSelected ? 'white' : 'inherit',
                                    fontWeight: isSelected ? 'bold' : 'normal'
                                }
                            }, [
                                el('div', { style: { fontWeight: 'bold', marginBottom: '4px' } }, archive.post_title),
                                el('div', { style: { fontSize: '12px', opacity: 0.8 } },
                                    'Version: ' + (archive.version || 'N/A')
                                )
                            ]);
                        })
                    );
                } else {
                    return el('div', { style: { padding: '20px', textAlign: 'center', color: '#666' } }, 'No archives found matching your search.');
                }
            })(),
            
            el('div', { style: { marginBottom: '16px' } }, [
                el('label', { 
                    style: { display: 'block', marginBottom: '4px', fontWeight: '500' } 
                }, 'Button text'),
                el('input', {
                    ref: buttonTextInputRef,
                    type: 'text',
                    defaultValue: buttonTextRef.current,
                    style: {
                        width: '100%',
                        padding: '8px 12px',
                        border: '1px solid #8c8f94',
                        borderRadius: '4px',
                        fontSize: '14px',
                        marginBottom: '8px'
                    },
                    onInput: function(e) {
                        var value = e.target.value;
                        buttonTextRef.current = value;
                        setButtonText(value);
                    }
                })
            ]),
            
            (function() {
                var currentSelectedArchive = selectedArchiveRef.current;
                var isDisabled = !currentSelectedArchive;
                return el(Button, {
                    isPrimary: true,
                    onClick: handleInsert,
                    disabled: isDisabled,
                    style: { width: '100%' }
                }, 'Insert Archive Link');
            })()
            ])
        ]);
    }
    
})();