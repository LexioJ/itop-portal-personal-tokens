# iTop Portal Personal Tokens Extension

Enable Portal Users to create and manage personal API tokens for REST API access in iTop.

## Overview

This extension adds personal token management capabilities to the iTop Portal, allowing Portal Users to:
- Create personal API tokens for REST API access
- Manage token lifecycle (create, regenerate, delete)
- Set token expiration dates and scopes
- Use tokens for API authentication without sharing passwords

## Features

- **Portal Integration**: Seamlessly integrated into the iTop Portal UI
- **Token Security**: Secure token generation and storage using iTop's existing authentication framework
- **Scope Management**: Control API access permissions per token
- **Expiration Control**: Set token expiration dates for security
- **Usage Tracking**: Monitor token usage and last access times
- **Upgrade Safe**: Survives iTop upgrades as a standard extension

## Requirements

- iTop 3.1.0 or higher
- `authent-token` module enabled
- Portal module installed and configured
- PHP 7.4 or higher

## Installation

1. Download the latest release
2. Extract to your iTop `extensions/` directory
3. Run the iTop setup wizard
4. Select "Portal Personal Tokens" extension
5. Complete the installation

For detailed installation instructions, see the documentation in the `docs/` folder

## Configuration

Add to your `config-itop.php`:

```php
'allow_rest_services_via_tokens' => true,
'portal_personal_tokens' => array(
    'enabled' => true,
    'max_tokens_per_user' => 5,
    'default_expiry_days' => 90,
),
```

## Usage

### For Portal Users

1. Log into the iTop Portal
2. Navigate to "My Profile" or "Personal Tokens" section
3. Click "Create New Token"
4. Provide an application name and select scope
5. Copy the generated token (it won't be shown again!)
6. Use the token in API calls

### API Authentication

Use the token in REST API calls:

```bash
curl -X POST https://your-itop.com/webservices/rest.php?version=1.3 \
  -d "auth_token=YOUR_TOKEN_HERE" \
  -d 'json_data={"operation":"core/get","class":"Ticket","key":"SELECT Ticket"}'
```

## Security Considerations

- Tokens are stored hashed in the database
- Each token has a unique scope limiting its permissions
- Tokens expire automatically based on configuration
- Users can only manage their own tokens
- All token operations are logged

## Architecture

The extension leverages iTop's existing `PersonalToken` class from the `authent-token` module and adds:
- Portal UI components for token management
- Permission extensions for Portal Users
- AJAX handlers for token operations
- Twig templates for Portal integration

## Development

### Project Structure

```
itop-portal-personal-tokens/
├── extension.xml                    # Extension manifest
├── module.*.php                      # Module definition
├── model.*.php                       # Business logic
├── datamodel.*.xml                   # Data model extensions
├── en.dict.*.php                     # Translations
├── portal/                           # Portal UI components
│   ├── views/                        # Twig templates
│   └── public/                       # JS/CSS assets
└── docs/                             # Documentation
```

### Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## Support

For issues, questions, or contributions, please use the GitHub issue tracker.

## License

This extension is released under the [AGPL-3.0 License](LICENSE).

## Credits

Developed for the iTop community to enable Portal Users to leverage REST API capabilities securely.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history and release notes.

---

## Current Status (2025-09-29)

- Portal menu entry “Personal Tokens” is visible for Portal Users
- OQL updated to filter by current portal contact: `u.contactid = :current_contact_id`
- PersonalToken class scope added for portal context
- ManageBrick registered under module_design itop-portal with ui_version v3
- Counts display correctly (eg. “Personal Tokens (1)”), but list area shows “No item.”
- CRUD actions (create/edit/delete) are not yet functional in the portal; backend My Account CRUD works
- Setup wizard needs permission fixes occasionally (see .warp.md) before running

Next steps to resume:
1) Diagnose why ManageBrick renders “No item” despite returning one row
   - Compare to itop-tickets ManageBrick; verify columns and data_loading
   - Try pure class <class>PersonalToken</class> vs OQL JOIN
   - Enable portal debug logs; check ScopeValidatorHelper and BrickCollection
2) Wire up create/edit/delete using standard ManageBrick actions and forms
3) Re-run setup, verify with Boris user, and add simple e2e test notes in docs.

See last screenshot in the issue log for current UI state.
