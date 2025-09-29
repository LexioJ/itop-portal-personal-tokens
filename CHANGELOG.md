# Changelog

All notable changes to the iTop Portal Personal Tokens Extension will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Portal navigation menu entry "Personal Tokens" visible for Portal Users
- PersonalToken class scope with OQL filtering by current portal contact
- ManageBrick configuration with ui_version v3 for iTop 3.2+ compatibility
- Basic CRUD action definitions (create_from_this, edit, view) in ManageBrick
- Navigation rules and action rules for token management workflows

### Fixed
- OQL query parameter corrected from `:current_user_id` to `:current_contact_id`
- Simplified JOIN query from 3-table to 2-table for better ManageBrick compatibility
- UI version updated from `2025` to `v3` to match modern portal patterns

### Known Issues
- ManageBrick shows correct count but "No item" in list area (CRUD not yet wired in portal)
- Backend "My Account" PersonalToken management works and can serve as reference
- Initial development version
- Portal UI for personal token management
- Create, regenerate, and delete token functionality
- Token expiration and scope management
- Integration with existing iTop authent-token module
- Permission extensions for Portal Users
- English language translations
- Comprehensive documentation

### Security
- Tokens stored using secure hashing
- Automatic token expiration
- Per-user token limits
- Scope-based access control

## [1.0.0] - TBD

### Added
- First stable release
- Full Portal integration
- REST API authentication support
- Token usage tracking
- Responsive UI design
- AJAX-based token operations

### Documentation
- Installation guide
- Deployment guide
- API usage examples
- Security considerations

---

## Version History

- **1.0.0** - Initial release (TBD)
  - Core functionality for Portal Users to manage personal API tokens
  - Integration with iTop 3.1.0+
  - Leverages existing authent-token module
  - Upgrade-safe extension architecture