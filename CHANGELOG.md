# Changelog

All notable changes to the iTop Portal Personal Tokens Extension will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-10-04

### Added
- ✅ **Complete Portal Integration** via User Profile Tab Extension
- ✅ **Token Creation** with custom application name, scope (REST/JSON, REST/JSON+Export), and expiration (30/90/180/365 days)
- ✅ **Token Regeneration** with one-time display of new token value
- ✅ **Token Deletion** with confirmation dialog
- ✅ **Token Viewing** with usage statistics (count, last used date, expiration)
- ✅ **Copy to Clipboard** functionality for generated tokens
- ✅ **CSRF Protection** using iTop's built-in transaction ID validation
- ✅ **Double-Submission Prevention** via static flag mechanism
- ✅ **User Isolation** - users can only see and manage their own tokens
- ✅ **Permission Extensions** for Portal User profile to manage PersonalToken objects
- ✅ **Bootstrap 3 UI** with modal dialogs and responsive design
- ✅ **Multi-language Support** (English and German dictionaries included)
- ✅ **Comprehensive Documentation** (README, deployment guide, architecture notes)

### Technical Implementation
- User Profile Tab Extension (`iUserProfileTabContentExtension`) instead of ManageBricks
- Direct OQL queries with `user_id` filtering for security
- Integration with iTop's `AuthentTokenService` for token generation
- Twig templates for HTML, JavaScript, and CSS
- Static state management to prevent double execution

### Security
- Tokens stored hashed in database (never plain text)
- Transaction ID validation prevents CSRF attacks
- User ID filtering enforced at query level
- AllowWrite/AllowDelete permission bypasses for portal users
- All operations logged via IssueLog (errors only)

### Fixed
- Double token creation on form double-click/double-submit
- Transaction ID consumption and validation flow
- Form data persistence across method calls
- Success/error message display logic
- Debug logging removed from production code

## [0.2.0] - 2025-09-29 (Development)

### Changed
- Switched from ManageBrick approach to User Profile Tab Extension
- Simplified OQL queries - removed complex JOINs
- Direct form handling via POST instead of brick actions

### Removed
- ManageBrick configuration (moved to User Profile Tab Extension)
- Complex OQL JOINs with User class
- Brick-based navigation rules

### Issues Resolved
- ManageBrick "No item" display issue - not applicable with new approach
- Scope filtering complications - resolved with direct user_id queries
- "Show token once" workflow - easily handled with custom UI

## [0.1.0] - 2025-09-28 (Initial Development)

### Added
- Initial project structure
- ManageBrick configuration (later replaced)
- PersonalToken class scope
- Basic permission extensions
- English translations

### Known Issues (Resolved in 1.0.0)
- ManageBrick showed count but not items
- CRUD actions not functional in portal
- Complex OQL queries required

---

## Migration Notes

### From 0.x to 1.0.0
- Complete architectural change from ManageBricks to User Profile Tab Extension
- No data migration needed - uses same PersonalToken class
- Clear cache after upgrade: `rm -rf /path/to/itop/data/cache-production/*`

### Configuration
```php
// Required in config-itop.php
'allow_rest_services_via_tokens' => true,
'portal_personal_tokens' => array(
    'max_tokens_per_user' => 5,
),
```

## Upgrade Path

### From iTop 3.1.x to 3.2.x
- Extension is compatible with both versions
- No changes required
- Leverages standard iTop extension hooks

---

## Version History Summary

- **1.0.0** (2025-10-04) - Production release with full functionality
- **0.2.0** (2025-09-29) - Architectural pivot to User Profile Tab Extension
- **0.1.0** (2025-09-28) - Initial development with ManageBricks
