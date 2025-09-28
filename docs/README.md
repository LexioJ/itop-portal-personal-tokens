# iTop Portal Users Personal Token Extension

Enable Portal Users to create and manage personal API tokens for REST API authentication in iTop.

## Overview

This extension addresses a critical limitation in iTop where only backend users can create personal API tokens. With this extension, Portal Users gain the ability to:

- Create personal API tokens through the Portal interface
- Manage (view, revoke) their existing tokens
- Use tokens for REST API authentication
- Integrate with external applications (like Nextcloud)

## Features

✅ **Secure Token Management**
- Cryptographically secure token generation
- Bcrypt hashing for storage
- One-time token display after creation
- Automatic expiration (configurable)

✅ **Portal Integration**
- New "API Tokens" tab in user profile
- Intuitive UI matching iTop design
- Bootstrap-based responsive interface
- CSRF protection on all operations

✅ **REST API Compatibility**
- Seamless integration with existing REST API
- Bearer token authentication support
- Respects existing user permissions
- Compatible with REST API v1.3+

✅ **Security & Audit**
- Token usage logging
- Failed authentication tracking
- Optional IP restriction
- Rate limiting support

## Requirements

- iTop 3.2.x or higher
- PHP 7.4 or higher
- Portal module enabled
- REST API enabled

## Installation

### Method 1: Extension Installation

1. Download the latest release from the releases page
2. Extract the ZIP file to your iTop `extensions/` directory
3. Navigate to iTop setup page (http://your-itop/setup/)
4. Select "Upgrade an existing iTop instance"
5. Check "Portal User Tokens Extension" in the extensions list
6. Complete the installation wizard

### Method 2: Manual Installation

1. Clone this repository:
```bash
git clone https://github.com/yourusername/itop-portal-tokens-extension.git
cd itop-portal-tokens-extension
```

2. Copy to iTop extensions directory:
```bash
cp -r itop-portal-tokens-extension /path/to/itop/extensions/
```

3. Run iTop setup to install the extension

## Configuration

The extension can be configured through iTop's configuration file:

```php
// In conf/production/config-itop.php
'itop-portal-tokens' => array(
    'token_lifetime_days' => 90,        // Token expiration in days
    'max_tokens_per_user' => 10,        // Maximum active tokens per user
    'enable_ip_restriction' => false,    // Enable IP-based restrictions
    'token_length' => 32,                // Token length in bytes
),
```

## Usage

### For Portal Users

1. **Access Token Management**
   - Log into the Portal
   - Navigate to your profile (click your name in top-right)
   - Click on "API Tokens" tab

2. **Create a Token**
   - Click "Create Token" button
   - Enter a description (e.g., "Nextcloud Integration")
   - Click "Generate"
   - **Important**: Copy the token immediately - it won't be shown again!

3. **Manage Tokens**
   - View all your active tokens
   - See creation date, last usage, and expiration
   - Revoke tokens that are no longer needed

### For Developers

#### Using Tokens with REST API

```bash
# Example: Get user's tickets
curl -X POST https://your-itop.com/webservices/rest.php?version=1.3 \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "operation": "core/get",
    "class": "UserRequest",
    "key": "SELECT UserRequest WHERE caller_id = :current_contact_id",
    "output_fields": "ref, title, status, priority"
  }'
```

#### PHP Example

```php
$token = 'YOUR_TOKEN_HERE';
$itopUrl = 'https://your-itop.com/webservices/rest.php?version=1.3';

$data = [
    'operation' => 'core/get',
    'class' => 'UserRequest',
    'key' => 'SELECT UserRequest WHERE status NOT IN ("closed", "resolved")',
    'output_fields' => 'ref, title, status'
];

$options = [
    'http' => [
        'header' => [
            "Authorization: Bearer $token",
            "Content-Type: application/json",
        ],
        'method' => 'POST',
        'content' => json_encode($data),
    ],
];

$context = stream_context_create($options);
$result = file_get_contents($itopUrl, false, $context);
$response = json_decode($result, true);
```

#### JavaScript Example

```javascript
const token = 'YOUR_TOKEN_HERE';
const itopUrl = 'https://your-itop.com/webservices/rest.php?version=1.3';

fetch(itopUrl, {
    method: 'POST',
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        operation: 'core/get',
        class: 'UserRequest',
        key: 'SELECT UserRequest WHERE caller_id = :current_contact_id',
        output_fields: 'ref, title, status'
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

## Security Considerations

### Token Security
- Tokens are never stored in plain text
- Each token is hashed using bcrypt
- Tokens are shown only once after creation
- Automatic expiration after configured period

### Best Practices
1. **Never share your tokens** - Treat them like passwords
2. **Use descriptive names** - Makes it easier to identify tokens later
3. **Revoke unused tokens** - Remove tokens you no longer need
4. **Set appropriate expiration** - Balance security with convenience
5. **Monitor token usage** - Check the "Last Used" column regularly

### For Administrators
- Review token configuration settings
- Monitor token usage through audit logs
- Set appropriate `max_tokens_per_user` limit
- Consider enabling IP restrictions for sensitive environments
- Regularly review and clean up expired tokens

## Troubleshooting

### Common Issues

**Token not working with REST API**
- Verify token hasn't expired
- Check if token status is "active"
- Ensure REST API is enabled in iTop
- Verify user has appropriate permissions

**Cannot create new token**
- Check if you've reached the maximum token limit
- Verify Portal User profile has token creation permission
- Check browser console for JavaScript errors

**Token disappeared after creation**
- Tokens are shown only once for security
- If you didn't copy it, you'll need to create a new one
- Revoke the unused token to keep things clean

### Debug Mode

Enable debug logging in iTop configuration:
```php
'log_level' => 'Debug',
'debug_report' => true,
```

Check logs at: `/log/error.log`

## Development

### Project Structure
```
itop-portal-tokens-extension/
├── datamodel.itop-portal-tokens.xml    # Data model definition
├── module.itop-portal-tokens.php       # Module configuration
├── en.dict.itop-portal-tokens.php      # English translations
├── model/
│   ├── PortalTokenService.class.php    # Token service logic
│   └── PortalTokenAuthProvider.class.php # REST auth provider
├── portal/
│   ├── src/Controller/              # Portal controllers
│   ├── templates/                   # Twig templates
│   └── config/                      # Portal configuration
├── docs/                            # Documentation
└── tests/                           # Test suite
```

### Contributing

We welcome contributions! Please:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add/update tests
5. Submit a pull request

### Running Tests

```bash
# Unit tests
php vendor/bin/phpunit tests/

# Integration tests
php tests/integration/run.php
```

## Roadmap

### Version 1.1 (Planned)
- [ ] Token usage statistics dashboard
- [ ] Bulk token management for administrators
- [ ] Token templates for common use cases
- [ ] Webhook support for token events

### Version 2.0 (Future)
- [ ] OAuth2 support
- [ ] Token scoping (fine-grained permissions)
- [ ] API rate limiting per token
- [ ] Token rotation mechanism

## Support

### Documentation
- [Installation Guide](docs/INSTALL.md)
- [API Usage Guide](docs/API_USAGE.md)
- [Security Guide](docs/SECURITY.md)
- [Developer Guide](docs/DEVELOPER.md)

### Getting Help
- **Issues**: [GitHub Issues](https://github.com/yourusername/itop-portal-tokens-extension/issues)
- **Discussions**: [GitHub Discussions](https://github.com/yourusername/itop-portal-tokens-extension/discussions)
- **Wiki**: [Project Wiki](https://github.com/yourusername/itop-portal-tokens-extension/wiki)

## License

This extension is licensed under the AGPL v3 license. See [LICENSE](LICENSE) file for details.

## Credits

Developed by [Your Organization]

Special thanks to:
- The iTop community for feedback and testing
- Combodo for the iTop framework
- Contributors and testers

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history and release notes.

---

**Note**: This extension is specifically designed to address the limitation where Portal Users cannot create personal API tokens, which is essential for integrations with external applications like Nextcloud.