# iTop Portal Personal Tokens Extension

[![iTop Version](https://img.shields.io/badge/iTop-3.1.0+-blue.svg)](https://www.combodo.com/itop)
[![License](https://img.shields.io/badge/license-AGPL--3.0-green.svg)](LICENSE)
[![Version](https://img.shields.io/badge/version-1.1.0-orange.svg)](CHANGELOG.md)
[![Security](https://img.shields.io/badge/security-hardened-brightgreen.svg)](SECURITY.md)
[![Translations](https://img.shields.io/badge/translations-2%2F17-yellow.svg)](TRANSLATION.md)

Enable Portal Users to create and manage personal API tokens for REST API access in iTop.

## Overview

This extension adds personal token management capabilities to the iTop Portal, allowing Portal Users to:
- Create personal API tokens for REST API access
- Manage token lifecycle (create, regenerate, delete)
- Set token expiration dates and scopes
- Use tokens for API authentication without sharing passwords

## Features

- **Portal Integration**: Seamlessly integrated into the iTop Portal user profile
- **Token Security**: Secure token generation and storage using iTop's existing authentication framework
- **Scope Management**: Control API access permissions per token (REST/JSON, Export, etc.)
- **Expiration Control**: Set token expiration dates (30, 90, 180, or 365 days)
- **Usage Tracking**: Monitor token usage count and last access times
- **CSRF Protection**: Built-in transaction ID validation prevents duplicate submissions
- **Multi-Language Support**: Full support for all 17 iTop languages (translations needed)
- **Upgrade Safe**: Survives iTop upgrades as a standard extension

## Requirements

- iTop 3.1.0 or higher
- `authent-token` module enabled
- Portal module installed and configured
- PHP 7.4 or higher

## Installation

1. Download the latest release
2. Extract to your iTop `extensions/` directory as `itop-portal-personal-tokens`
3. Run the iTop setup wizard
4. Select "Portal Personal Tokens" extension
5. Complete the installation
6. **Important**: Enable REST token authentication (see Configuration below)

For detailed installation instructions, see [DEPLOYMENT.md](docs/DEPLOYMENT.md)

## Configuration

**Required**: Enable REST API token authentication by adding to your `config-itop.php`:

```php
'allow_rest_services_via_tokens' => true,
```

**Without this setting, the Personal API Tokens tab will not appear in the user profile.**

You can also configure in iTop Admin Console:
1. Go to **Admin Tools** > **Configuration**
2. Find **allow_rest_services_via_tokens**
3. Set to **Yes** or **true**
4. Click **APPLY**

### Optional Configuration

```php
'portal_personal_tokens' => array(
    'max_tokens_per_user' => 5,      // Maximum tokens per user (default: 5, range: 1-20)
    'default_expiry_days' => 90,     // Default expiration in days (default: 90)
),
```

#### Configuration Parameters

| Parameter | Type | Default | Valid Range | Description |
|-----------|------|---------|-------------|-------------|
| `max_tokens_per_user` | integer | `5` | `1-20` | Maximum number of personal tokens a user can create. Prevents token proliferation. |
| `default_expiry_days` | integer | `90` | `30-365` | Default token expiration period in days (users select this in UI dropdown). |

**Notes:**
- **max_tokens_per_user**: Values above 20 are not recommended for security reasons (increases attack surface if account is compromised)
- **default_expiry_days**: Shorter expiration periods improve security through regular token rotation
- Both parameters are optional; defaults are used if not specified
- Changes require clearing the iTop cache: `rm -rf /path/to/itop/data/cache-production/*`

## Screenshots

### Token Management Interface
![Token List View](docs/screenshots/token-list-view.png)
*Main interface showing personal tokens with usage statistics and management actions*

### Token Creation
![Token Creation Modal](docs/screenshots/token-creation-modal.png)
*Create new tokens with custom application name, scope, and expiration settings*

## Usage

### For Portal Users

1. Log into the iTop Portal
2. Navigate to "My Profile" → "Personal API Tokens" tab
3. Click "Create New Token"
4. Provide an application name and select:
   - Scope (REST/JSON or REST/JSON + Export)
   - Expiration (30, 90, 180, or 365 days)
5. Copy the generated token (it won't be shown again!)
6. Use the token in API calls

### API Authentication

Use the token in REST API calls:

```bash
curl -X POST https://your-itop.com/webservices/rest.php?version=1.3 \
  -d "auth_token=YOUR_TOKEN_HERE" \
  -d 'json_data={"operation":"core/get","class":"UserRequest","key":"SELECT UserRequest WHERE caller_id = :current_contact_id"}'
```

### Token Management

- **Regenerate**: Click the regenerate button to create a new token value (old token becomes invalid)
- **Delete**: Click the delete button to permanently remove a token
- **View Details**: See application name, scope, expiration date, usage count, and last use date

## Security Considerations

This extension implements multiple layers of security protection:

- **Token Storage**: Tokens are stored hashed in the database (never stored in plain text)
- **Scope Limitation**: Each token has a unique scope limiting its permissions
- **Automatic Expiration**: Tokens expire automatically based on configured expiration date
- **User Isolation**: Users can only manage their own tokens (enforced at database query level)
- **Audit Logging**: All token operations are logged via IssueLog
- **CSRF Protection**: Transaction ID validation prevents duplicate submissions and replay attacks
- **XSS Prevention** (v1.1.0+): All user inputs properly escaped in templates
- **SQL Injection Prevention**: Parameterized OQL queries with placeholder bindings
- **Input Validation**: Server-side validation for all user-provided data

For complete security documentation, see [SECURITY.md](SECURITY.md)

## Architecture

### Implementation Approach

The extension uses iTop's **User Profile Tab Extension** system rather than ManageBricks:

1. **Hook Implementation**: `PersonalTokensUserProfileExtension` implements `iUserProfileTabContentExtension`
2. **Custom UI**: Twig templates for HTML, JavaScript, and CSS
3. **Form Handling**: POST request processing with CSRF protection
4. **Security**: Direct OQL queries with user_id filtering

### Why Not ManageBricks?

Initial attempts used ManageBricks, but we switched to the User Profile Tab Extension approach because:
- Direct control over UI/UX for token display and "copy token" functionality
- Simpler implementation for view-only data with custom actions
- Better integration with the user profile page
- No complex OQL JOINs or scope filtering needed

### Project Structure

```
itop-portal-personal-tokens/
├── module.itop-portal-personal-tokens.php  # Module definition
├── datamodel.itop-portal-personal-tokens.xml  # Permission extensions
├── en.dict.itop-portal-personal-tokens.php    # English translations
├── de.dict.itop-portal-personal-tokens.php    # German translations
├── src/
│   └── Hook/
│       └── PersonalTokensUserProfileExtension.php  # Main logic
├── templates/
│   ├── personal_tokens_tab.html.twig       # UI template
│   ├── personal_tokens_tab.ready.js.twig   # JavaScript
│   └── personal_tokens_tab.css.twig        # Styling
└── docs/                                    # Documentation
```

### Key Components

1. **PersonalTokensUserProfileExtension**
   - Implements `iUserProfileTabContentExtension` interface
   - Handles form submissions (create/delete/regenerate)
   - Prevents double-submission via static flags
   - Uses iTop's `AuthentTokenService` for token generation

2. **Templates**
   - Bootstrap 3 compatible UI
   - Modal dialog for token creation
   - Real-time form validation
   - Copy-to-clipboard functionality

3. **Security**
   - Transaction ID validation (handled by iTop's UserProfileBrickController)
   - User ID filtering in all OQL queries
   - AllowWrite/AllowDelete permission bypasses for portal users

## Development

### Testing

Test the extension in OrbStack/Docker:

```bash
# Deploy to test environment
orb -m itop-dev sudo cp -r . /var/www/itop/web/extensions/itop-portal-personal-tokens/
orb -m itop-dev sudo rm -rf /var/www/itop/web/data/cache-production
orb -m itop-dev sudo chown -R www-data:www-data /var/www/itop/web/data

# Access portal
open http://itop-dev.orb.local/portal/
# Login as portal user, navigate to Profile → Personal API Tokens
```

### Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Create a Pull Request

### Translation Status

The extension supports all 17 iTop languages with dictionary files:

| Language | Code | Status | Completion |
|----------|------|--------|------------|
| English | `en` | ✅ Complete | 100% (native) |
| German | `de` | ✅ Complete | 100% (professional translation) |
| British English | `en_gb` | ⚠️ Partial | ~60% (English fallback with UK spellings) |
| Czech | `cs` | ⚠️ Fallback | 0% (English fallback with markers) |
| Danish | `da` | ⚠️ Fallback | 0% (English fallback with markers) |
| Spanish | `es_cr` | ⚠️ Fallback | 0% (English fallback with markers) |
| French | `fr` | ⚠️ Fallback | 0% (English fallback with markers) |
| Hungarian | `hu` | ⚠️ Fallback | 0% (English fallback with markers) |
| Italian | `it` | ⚠️ Fallback | 0% (English fallback with markers) |
| Japanese | `ja` | ⚠️ Fallback | 0% (English fallback with markers) |
| Dutch | `nl` | ⚠️ Fallback | 0% (English fallback with markers) |
| Polish | `pl` | ⚠️ Fallback | 0% (English fallback with markers) |
| Portuguese (BR) | `pt_br` | ⚠️ Fallback | 0% (English fallback with markers) |
| Russian | `ru` | ⚠️ Fallback | 0% (English fallback with markers) |
| Slovak | `sk` | ⚠️ Fallback | 0% (English fallback with markers) |
| Turkish | `tr` | ⚠️ Fallback | 0% (English fallback with markers) |
| Chinese | `zh_cn` | ⚠️ Fallback | 0% (English fallback with markers) |

**Translation Contributions Welcome!**

Native speakers are invited to contribute translations for their languages. All dictionary files include English fallback text with `TRANSLATE:` markers for easy identification of strings needing translation.

See [TRANSLATION.md](TRANSLATION.md) for detailed translation instructions.

## Troubleshooting

### Tokens not appearing
- Ensure `allow_rest_services_via_tokens` is `true` in config
- Check that Portal User profile has PersonalToken write permissions
- Verify authent-token module is installed and enabled

### "Form already submitted" errors
- This is a safety feature - refresh the page to get a new transaction ID
- Check that cache has been cleared after deployment

### Token generation fails
- Ensure `AuthentTokenService` is available
- Check PHP error logs for exceptions
- Verify PersonalToken class exists

## Support

For issues, questions, or contributions, please use the GitHub issue tracker.

## License

This extension is released under the [AGPL-3.0 License](LICENSE).

## Credits

Developed for the iTop community to enable Portal Users to leverage REST API capabilities securely.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history and release notes.
