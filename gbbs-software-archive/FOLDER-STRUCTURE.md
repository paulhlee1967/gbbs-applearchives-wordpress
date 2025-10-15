# GBBS Software Archive - Folder Structure

## Overview
The plugin creates a consistent folder structure based on your settings. Here's how it works:

## Settings
- **Upload Folder Structure**: `gbbs_dedicated` (default)
- **File Organization**: `by_archive` (default)

## Folder Structure

### Base Directory
```
uploads/gbbs-archive/
├── .htaccess (security file)
├── index.php (security file)
├── temp/ (temporary uploads)
│   └── index.php (security file)
└── [archive-id]/ (individual archive folders)
    ├── .htaccess
    ├── index.php
    └── [uploaded files]
```

## How It Works

### 1. Plugin Activation
- Creates `uploads/gbbs-archive/` base directory
- Creates `uploads/gbbs-archive/temp/` for temporary uploads
- Adds security files (.htaccess, index.php)

### 2. File Uploads
- **New Archives**: Files go to `uploads/gbbs-archive/temp/`
- **Existing Archives**: Files go to `uploads/gbbs-archive/[archive-id]/`

### 3. Archive Save
- When an archive is saved, temp files are moved to the proper archive folder
- Temp directory is cleaned up if empty

## Alternative Organizations

### By Volume
```
uploads/gbbs-archive/
├── volumes/
│   ├── temp/ (temporary uploads)
│   └── [volume-slug]/ (volume folders)
```

### Flat Structure
```
uploads/gbbs-archive/
└── files/ (all files in one folder)
```

## Security
- Each directory has `.htaccess` and `index.php` files
- Prevents direct access to uploaded files
- Files are only accessible through WordPress

## File Management
- Files are automatically organized when archives are saved
- Orphaned files are cleaned up when archives are deleted
- Temporary files are moved to proper locations
