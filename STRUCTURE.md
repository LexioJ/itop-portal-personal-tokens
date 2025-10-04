# Project Structure

This document describes the file and directory structure of the iTop Portal Personal Tokens extension.

## Root Directory

```
itop-portal-personal-tokens/
├── src/                              # PHP source code
│   └── Hook/
│       └── PersonalTokensUserProfileExtension.php
├── templates/                        # Twig templates
│   ├── personal_tokens_tab.html.twig
│   ├── personal_tokens_tab.ready.js.twig
│   └── personal_tokens_tab.css.twig
├── docs/                             # Documentation
│   ├── screenshots/                  # UI screenshots
│   ├── DEPLOYMENT.md                 # Deployment guide
│   ├── DEV_STATUS.md                 # Development status
│   └── FILE_STATUS.md                # File status tracking
├── planning/                         # Architecture & planning docs
│   └── ARCHITECTURE_PLAN.md
├── maintenance/                      # Maintenance scripts
│   └── cleanup_expired_tokens.php
├── *.dict.itop-portal-personal-tokens.php  # Language dictionaries (17 files)
├── datamodel.itop-portal-personal-tokens.xml  # Permission extensions
├── module.itop-portal-personal-tokens.php     # Module definition
├── extension.xml                     # Extension manifest
├── LICENSE                           # AGPL-3.0 License
├── README.md                         # Main documentation
├── CHANGELOG.md                      # Version history
├── TRANSLATION.md                    # Translation guide
├── LANGUAGE_STATUS.md                # Translation status
└── .gitignore                        # Git ignore rules
```

## Directory Descriptions

### `/src/`
PHP source code implementing the extension logic.

- **Hook/PersonalTokensUserProfileExtension.php**: Main extension class implementing `iUserProfileTabContentExtension`

### `/templates/`
Twig templates for the portal UI.

- **personal_tokens_tab.html.twig**: Main HTML template
- **personal_tokens_tab.ready.js.twig**: JavaScript functionality
- **personal_tokens_tab.css.twig**: Custom styling

### `/docs/`
Documentation files.

- **screenshots/**: UI screenshots for documentation
- **DEPLOYMENT.md**: Production deployment guide
- **DEV_STATUS.md**: Current development status
- **FILE_STATUS.md**: File tracking

### `/planning/`
Architecture and planning documentation.

- **ARCHITECTURE_PLAN.md**: Original architecture decisions and implementation approach

### `/maintenance/`
Maintenance and utility scripts.

- **cleanup_expired_tokens.php**: Automated cleanup of expired tokens

### Language Dictionaries
17 dictionary files for iTop multi-language support:

- `en.dict.itop-portal-personal-tokens.php` (English - Complete)
- `de.dict.itop-portal-personal-tokens.php` (German - Complete)
- 15 other language files with English fallback

## Core Files

### `datamodel.itop-portal-personal-tokens.xml`
Extends Portal User profile permissions for PersonalToken management.

### `module.itop-portal-personal-tokens.php`
Module definition with metadata and autoloader setup.

### `extension.xml`
Extension manifest for iTop setup wizard.

## Documentation Files

### `README.md`
Main project documentation with:
- Features overview
- Installation instructions
- Usage guide
- Architecture description

### `CHANGELOG.md`
Complete version history and release notes.

### `TRANSLATION.md`
Guide for translators contributing language files.

### `LANGUAGE_STATUS.md`
Current status of all language translations.

## Development Files (Not in Production)

The following files are for development only and excluded via `.gitignore`:
- Cache files
- IDE configuration
- Temporary files
- Build artifacts

## Installation Structure

When installed in iTop, the extension resides at:
```
/path/to/itop/extensions/itop-portal-personal-tokens/
```

All files are copied to this location during installation.
