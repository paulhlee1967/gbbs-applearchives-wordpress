// GBBS Software Archive - Public JavaScript

jQuery(document).ready(function($) {
    'use strict';
    
    // Initialize GBBS Archive functionality
    const GBBSArchive = {
        init: function() {
            this.initRealTimeClock();
            this.initTableSorting();
        },
        
        initRealTimeClock: function() {
            const clockElement = document.getElementById('bbs-current-time');
            if (!clockElement) return;
            
            // Update clock immediately
            this.updateClock();
            
            // Update clock every second
            setInterval(this.updateClock.bind(this), 1000);
        },
        
        updateClock: function() {
            const clockElement = document.getElementById('bbs-current-time');
            if (!clockElement) return;
            
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', {
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            
            clockElement.textContent = timeString;
        },
        
        initTableSorting: function() {
            // Table sorting is now handled server-side via URL parameters
            // No client-side sorting needed
        }
    };
    
    // Initialize the GBBS Archive
    GBBSArchive.init();
});