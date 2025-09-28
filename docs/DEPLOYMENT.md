# Deployment Guide

## Quick Installation

1. **Download** the latest release
2. **Extract** to your iTop `extensions/` directory
3. **Run** the iTop setup wizard
4. **Enable** the "Portal Personal Tokens" extension
5. **Configure** in `config-itop.php`

## Prerequisites

- iTop 3.1.0 or higher
- `authent-token` module enabled
- Portal module installed
- PHP 7.4+

## Installation Steps

### 1. Package the Extension

```bash
cd itop-portal-personal-tokens/
zip -r ../itop-portal-personal-tokens.zip . \
  -x "*.git*" -x "*.DS_Store" -x "planning/*" -x "*.warp.md"
```

### 2. Deploy to iTop

```bash
# Copy to extensions directory
cp itop-portal-personal-tokens.zip /path/to/itop/extensions/
cd /path/to/itop/extensions/
unzip itop-portal-personal-tokens.zip -d itop-portal-personal-tokens/
rm itop-portal-personal-tokens.zip

# Set permissions
chown -R www-data:www-data itop-portal-personal-tokens/
chmod -R 755 itop-portal-personal-tokens/
```

### 3. Run Setup Wizard

1. Navigate to `https://your-itop.com/setup/`
2. Choose "Upgrade an existing iTop instance"
3. Select "Portal Personal Tokens" from the extensions list
4. Complete the wizard

### 4. Configure

Add to your `conf/production/config-itop.php`:

```php
'allow_rest_services_via_tokens' => true,
'portal_personal_tokens' => array(
    'enabled' => true,
    'max_tokens_per_user' => 5,
    'default_expiry_days' => 90,
),
```

## Post-Installation

### Clear Cache

```bash
rm -rf /path/to/itop/data/cache-production/*
```

### Set up Token Cleanup (Optional)

Add to crontab:
```bash
0 2 * * * php /path/to/itop/extensions/itop-portal-personal-tokens/maintenance/cleanup_expired_tokens.php
```

## Verification

1. Log into the Portal as a Portal User
2. Look for "Personal Tokens" section
3. Create a test token
4. Test with REST API:

```bash
curl -X POST https://your-itop.com/webservices/rest.php?version=1.3 \
  -d "auth_token=YOUR_TOKEN" \
  -d 'json_data={"operation":"core/check_credentials"}'
```

## Upgrade Safety

This extension is designed to survive iTop upgrades because:
- It lives in the `extensions/` directory (preserved during upgrades)
- Uses only official extension APIs
- No core file modifications
- No custom database tables

During iTop upgrades:
1. The extension remains in place
2. Run setup wizard after upgrade to re-enable
3. Verify configuration is still present

## Troubleshooting

### Extension Not Visible in Portal
- Verify `authent-token` module is installed
- Check Portal User has correct profile
- Clear cache and re-run setup

### Tokens Not Authenticating
- Verify `allow_rest_services_via_tokens` is `true`
- Check token hasn't expired
- Ensure REST endpoint URL is correct

### Permission Denied
- Re-run setup wizard
- Check PersonalToken permissions in user profile
- Clear Portal cache

## Security Notes

- Always use HTTPS for Portal access
- Set reasonable token expiration periods
- Monitor token usage regularly
- Consider implementing IP restrictions for API access

## Support

For issues or questions, please use the GitHub issue tracker.